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

class CompositeBuilderTreeTest extends CompositeBuilderTreeTestCase {
	public function testPereNoelParentIsAnimationJeunesse() {
		$this->assertSame($this->animation_jeunesse, $this->rencontre_pere_noel->getParent());
	}

	public function testFeteInternetIsInEvenements() {
		$this->assertTrue(in_array($this->fete_internet, 
															 $this->evenements->getItems()));
	}

	public function testVisiteBibAsId_4() {
		$this->assertEquals(4, $this->visite_bib->getId());
	}

	public function testAnimationSubcategories() {		
		$this->assertEquals(array($this->animation_jeunesse, $this->animation_adulte),
												$this->animation->getCategories());
	}
}


class ItemVisitorTest extends CompositeBuilderTreeTestCase {
	public function setUp(){
		parent::setUp();
	}

	public function startVisitCategory($category) {
		$this->actual_visit []= 'start_cat:'.$category->getId();
	}

	public function endVisitCategory($category) {
		$this->actual_visit []= 'end_cat:'.$category->getId();
	}

	public function visitItem($item) {
		$this->actual_visit []= 'art:'.$item->getId();
	}

	public function testVisitor() {
		$this->actual_visit = array();
		$this->expected_visit = array('start_cat:0',
																		'start_cat:1',
																			'start_cat:11',
																				'art:2',
																				'art:3',
																			'end_cat:11',
																			'start_cat:12',
																			'end_cat:12',
																			'art:4',
																		'end_cat:1',
																		'start_cat:2',
																			'art:5',
																		'end_cat:2',
																	'end_cat:0');

		$this->root->acceptVisitor($this);
		$this->assertEquals($this->expected_visit, $this->actual_visit);
	}
}


?>