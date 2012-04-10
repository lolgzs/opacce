<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

require_once "CompositeBuilder.php";

/*
* Implémentation d'un composite pour gérer l'arborescence des catégories/articles, catégories/rss
* et catégories/sites
*
* AbstractItem: class de base
* ItemCategory: le composite, maintient 2 listes items et categories
* Item: feuille
*/

class AbstractItem {
	protected $id;
	protected $parent;
	protected $builder;
	protected $label;

	public function __construct($id) {
		$this->id = $id;
		$this->parent = null;
		$this->label = 'unknown';
		$this->initialize();
	}

	public function initialize() {}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getId() {
		return $this->id;
	}

	public function getParent(){
		return $this->parent;
	}

	public function setParent($parent) {
		$this->parent = $parent;
		return $parent;
	}

	public function setBuilder($builder) {
		$this->builder = $builder;
	}

	public function getBuilder() {
		return $this->builder;
	}

	public function update_attributes($attributes){
		foreach($attributes as $key=>$value) {
			$attribute = strtolower($key);
			$this->$attribute = $value;
		}
		return $this;
	}

	public function acceptVisitor($visitor) {}

	public function __toString(){
		return get_class($this)."[".$this->getId()."] <-".$this->getParent();
	}

	public function toJSON(){
		return json_encode(array("id" => $this->getId(), "label" => htmlspecialchars($this->getLabel())));
	}

	protected function addAbstractItemsFromRows($rows, $id_field, $cat_field, $build_func){
		$builder = $this->getBuilder();
		foreach ($rows as $row) {
			$abs_item = $builder->$build_func($row[$id_field], 
																				$row[$cat_field]);
			if ($abs_item !== null) $abs_item->update_attributes($row);
		}
		return $this;
	}

	public function addItemsFromRows($rows, $id_field, $cat_field){
		return $this->addAbstractItemsFromRows($rows, 
																					 $id_field, 
																					 $cat_field, 
																					 "newItemIn");
	}

	public function addCategoriesFromRows($rows, $id_field, $cat_field){
		return $this->addAbstractItemsFromRows($rows, 
																					 $id_field,
																					 $cat_field, 
																					 "newSubcategoryIn");
	}
}


class BaseItem extends AbstractItem {
	public function acceptVisitor($visitor) {
		$visitor->visitItem($this);
	}
}

class ItemCategory extends AbstractItem {
	private $categories;
	private $items;

	public function initialize() {
		parent::initialize();
		$this->categories = array();
		$this->items = array();
	}

	public function getCategories() {
		return array_values($this->categories);
	}

	public function getItems() {
		return array_values($this->items);
	}

	public function addCategory($sub_category) {
		return $this->addChildToList($sub_category, $this->categories);
	}

	public function addAllCategories($categories) {
		foreach($categories as $cat)
			$this->addCategory($cat);
	}

	public function addItem($sub_item) {
		return $this->addChildToList($sub_item, $this->items);
	}

	public function getCategoryWithId($id) {
		if ($this->getId()==$id) return $this;
		return $this->getChildWithId($id, $this->categories, "getCategoryWithId");
	}

	public function getItemWithId($id) {
		return $this->getChildWithId($id, $this->items, "getItemWithId");
	}

	public function acceptVisitor($visitor) {
		$visitor->startVisitCategory($this);
		foreach($this->categories as $cat) $cat->acceptVisitor($visitor);
		foreach($this->items as $item) $item->acceptVisitor($visitor);
		$visitor->endVisitCategory($this);
	}

	public function toJSON(){
		$json_categories = array();
		$json_items = array();

		foreach($this->categories as $cat)
			$json_categories []= $cat->toJSON();

		foreach($this->items as $item) 
			$json_items []= $item->toJSON();

		return  '{'.
									'"id":'.$this->getId().','.
			            '"label": "'.htmlspecialchars($this->getLabel()).'",'.
									'"categories": ['.implode(",", $json_categories).'],'.
									'"items": ['.implode(",", $json_items).']'.
		         '}';
	}

	protected function getChildWithId($id, &$list, $get_func) {
		if (array_key_exists($id, $list))	return $list[$id];
		
		foreach($this->categories as $subcat) {
			$result = $subcat->$get_func($id);
			if ($result) return $result;
		}

		return null;
	}

	protected function addChildToList($child, &$list) {
		$list [$child->getId()]= $child;
	  $child->setParent($this);
		return $child;
	}
}


class CompositeBuilder {
	protected $category_class;
	protected $item_class;
	protected $root;
	protected $orphan_categories;

	public function __construct($category_class, $item_class){
		$this->category_class = $category_class;
		$this->item_class = $item_class;
		$this->root = $this->newCategory(0);
		$this->orphan_categories = array();
	}

	protected function newAbstractItem($from_class, $id) {
		$abs_item = new $from_class($id);
		$abs_item->setBuilder($this);
		return $abs_item;
	}

	public function newCategory($id) {
		return $this->newAbstractItem($this->category_class, $id);
	}

	public function newItem($id) {
		return $this->newAbstractItem($this->item_class, $id);
	}

	public function getRoot() {
		return $this->root;
	}

	public function getCategoryWithId($id) {
		return $this->root->getCategoryWithId($id);
	}

	public function newSubcategoryIn($id, $parent_id) {		
		$new_category = $this->newCategory($id);

		if (array_key_exists($parent_id, $this->orphan_categories)) {
			$parent = $this->orphan_categories[$parent_id]['category'];
			$parent->addCategory($new_category);
		} else {
			$this->orphan_categories [$id]= array('parent_id' => $parent_id, 
																						'category' => $new_category);
		}

		while ($this->tryToAddOrphans()) {}
		
		return $new_category;
	}

	protected function tryToAddOrphans(){
		$orphan_added = false;

		foreach($this->orphan_categories as $id => $pid_cat) {
			$parent = $this->getCategoryWithId($pid_cat['parent_id']);
			if ($parent==null) continue;

			$parent->addCategory($pid_cat['category']);
			unset($this->orphan_categories[$id]);
			$orphan_added = true;
		}

		return $orphan_added;
	}

	public function newItemIn($id, $parent_id) {
		$parent = $this->getCategoryWithId($parent_id);
		if ($parent==null) return;

		$item = $this->newItem($id);
		return $parent->addItem($item);
	}
}

?>