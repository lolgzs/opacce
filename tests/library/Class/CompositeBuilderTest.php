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
require_once 'Class/CompositeBuilder.php';


class Class_ItemCategoryIdTest extends PHPUnit_Framework_TestCase {
	public function testWithIdFive(){
		$cat = new ItemCategory(5);
		$this->assertEquals(5, $cat->getId());
	}

	public function testWithIdSeven(){		
		$cat = new ItemCategory(7);
		$this->assertEquals(7, $cat->getId());
	}
}


class Class_ItemIdTest extends PHPUnit_Framework_TestCase {
	public function testWithIdFive(){
		$item = new BaseItem(5);
		$this->assertEquals(5, $item->getId());
	}

	public function testWithIdSeven(){		
		$item = new BaseItem(7);
		$this->assertEquals(7, $item->getId());
	}
}


class Class_EmptyItemCategoryTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$this->empty_cat = new ItemCategory(12);
	}

	public function testGetCategoriesReturnsEmptyArray() {
		$this->assertEquals(array(), $this->empty_cat->getCategories());
	}

	public function testGetItemsReturnsEmptyArray() {
		$this->assertEquals(array(), $this->empty_cat->getItems());
	}

	public function testGetCatWithIdReturnsNull() {
		$this->assertNull($this->empty_cat->getCategoryWithId(1));
	}

	public function testGetItemWithIdReturnsNull() {
		$this->assertNull($this->empty_cat->getItemWithId(1));
	}
}


class Class_CategoryWithOneSubCategoryAndItemTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->root_cat = new ItemCategory(12);
		$this->sub_cat = new ItemCategory(20);
		$this->sub_item = new BaseItem(30);
		$this->root_cat->addCategory($this->sub_cat);
		$this->root_cat->addItem($this->sub_item);
	}

	public function testGetCategoriesReturnsArrayWithSubcategory(){
		$this->assertEquals(array($this->sub_cat), $this->root_cat->getCategories());
	}

	public function testGetItemsReturnsArrayWithSubitems(){
		$this->assertEquals(array($this->sub_item), $this->root_cat->getItems());
	}


	public function testSubItemGetParentReturnsRootCat() {
		$this->assertEquals($this->root_cat, $this->sub_item->getParent());
	}

	public function testSubCatGetParentReturnsRootCat() {
		$this->assertEquals($this->root_cat, $this->sub_cat->getParent());
	}
}


class Class_CategoryTree extends PHPUnit_Framework_TestCase {
	public function setUp() {
		/*
			root id:0
			 |
			 +--- animation 1
			         |
							 +-- animation jeunesse 11
							       - pere noel 2
										 - carnaval 3
							 +-- animation adulte 12
							 - visite bib 4
			 + evenements 2
			      - fete internet 5
		 */
		$this->root_cat = new ItemCategory(0);

		$this->animation = new ItemCategory(1);
		$this->animation_jeunesse = new ItemCategory(11);
		$this->animation_jeunesse->setLabel('Animation "Jeunesse"');
		$this->animation_adulte = new ItemCategory(12);
		$this->animation_adulte->setLabel('Animation Adulte');
		$this->animation->addCategory($this->animation_jeunesse);
		$this->animation->addCategory($this->animation_adulte);
		$this->root_cat->addCategory($this->animation);

		$this->evenements = new ItemCategory(2);
		$this->root_cat->addCategory($this->evenements);


		$this->rencontre_pere_noel = new BaseItem(2);
		$this->carnaval = new BaseItem(3);
		$this->carnaval->setLabel('Le "Carnaval"');
		$this->animation_jeunesse->addItem($this->rencontre_pere_noel);
		$this->animation_jeunesse->addItem($this->carnaval);

		$this->visite_bib = new BaseItem(4);
		$this->animation->addItem($this->visite_bib);

		$this->fete_internet = new BaseItem(5);
		$this->evenements->addItem($this->fete_internet);
	}

	public function testRootGetCatWithId_12_ReturnsAnimationAdulte() {
		$this->assertEquals($this->animation_adulte, $this->root_cat->getCategoryWithId(12));
	}

	public function testRootGetCatWithId_11_ReturnsAnimationJeunesse() {
		$this->assertEquals($this->animation_jeunesse, $this->root_cat->getCategoryWithId(11));
	}

	public function testRootGetItemWithId_5_ReturnsFeteInternet() {
		$this->assertEquals($this->fete_internet, $this->root_cat->getItemWithId(5));
	}

	public function testRootGetItemWithId_2_ReturnsRencontrePereNoel() {
		$this->assertEquals($this->rencontre_pere_noel, $this->root_cat->getItemWithId(2));
	}


	public function testToJSON() {
		$this->assertEquals('{"id":0,"label": "unknown","categories": [{"id":1,"label": "unknown","categories": [{"id":11,"label": "Animation &quot;Jeunesse&quot;","categories": [],"items": [{"id":2,"label":"unknown"},{"id":3,"label":"Le &quot;Carnaval&quot;"}]},{"id":12,"label": "Animation Adulte","categories": [],"items": []}],"items": [{"id":4,"label":"unknown"}]},{"id":2,"label": "unknown","categories": [],"items": [{"id":5,"label":"unknown"}]}],"items": []}',
												$this->root_cat->toJSON());
	}
}

?>