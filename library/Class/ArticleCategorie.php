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

class Class_ArticleCategorie extends Storm_Model_Abstract {
	protected $_table_name = 'cms_categorie';
	protected $_table_primary = 'ID_CAT';

	protected $_belongs_to = array('parent_categorie' => array('model' => 'Class_ArticleCategorie',
																														 'referenced_in' => 'id_cat_mere'),
																 'bib' => array('model' => 'Class_Bib',
																								'referenced_in' => 'id_site'));

	protected $_has_many = array('sous_categories' => array('model' => 'Class_ArticleCategorie',
																													 'role' => 'parent_categorie',
																													 'dependents' => 'delete',
																													 'order' => 'libelle'),

															  'articles' => array('model' => 'Class_Article',
																										'role' => 'categorie',
																										'dependents' => 'delete',
																										'order' => 'titre'));

	protected $_default_attribute_values = array(
																							 'id_site' => 0,
																							 'id_cat_mere' => 0,
																							 'libelle' => '');

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getRecursiveSousCategories() {
		$all_categories = $sous_categories = $this->getSousCategories();
		foreach ($sous_categories as $categorie)
			$all_categories = array_merge($all_categories, $categorie->getRecursiveSousCategories());
		return $all_categories;
	}


	public function toJSON($include_items = true) {
		$json_categories = array();
		$json_items = array();

		$categories = $this->getSousCategories();
		foreach($categories as $cat)
			$json_categories []= $cat->toJSON($include_items);

		$items = $include_items ? $this->getArticles() : array();
		foreach($items as $article)
			$json_items []= $article->toJSON();

		return  '{'.
			'"id":'.$this->getId().','.
			'"label": "'.htmlspecialchars($this->getLibelle()).'",'.
			'"categories": ['.implode(",", $json_categories).'],'.
			'"items": ['.implode(",", $json_items).']'.
			'}';
	}

	/**
	 * @return array
	 */
	public function getItems() {
		return $this->getArticles();
	}

	/**
	 * @return bool
	 */
	public function hasChildren() {
		return 0 < ($this->getChildrenCount());
	}

	/**
	 * @return bool
	 */
	public function hasNoChild() {
		return !$this->hasChildren();
	}

	/**
	 * @return int
	 */
	public function getChildrenCount() {
		return count($this->getArticles()) + count($this->getSousCategories());
	}

	/**
	 * @return Class_ArticleCategorie
	 */
	public function validate() {
		$this->check($this->getLibelle(), "Vous devez compléter le champ 'Libellé'");
		return $this;
	}


	/**
	 * @return Class_Bib
	 */
	public function getBib() {
		if ($bib = parent::_get('bib'))
			return $bib;

		if (!$this->hasParentCategorie())
			return Class_Bib::getLoader()->getPortail();

		return  $this->getParentCategorie()->getBib();
	}
}

?>
