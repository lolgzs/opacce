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
require_once 'AdminAbstractControllerTestCase.php';

abstract class Admin_FrbrLinktypeControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_LinkType')
			->whenCalled('findAllBy')
			->with(['order' => 'libelle'])
			->answers([
								 Class_FRBR_LinkType::newInstanceWithId(2)
								 ->setLibelle('Vous aimerez aussi')
								 ->setFromSource('vous aimerez aussi A')
								 ->setFromTarget('vous aimerez aussi B'),

								 Class_FRBR_LinkType::newInstanceWithId(3)
								 ->setLibelle('Suite')
								 ->setFromSource('a pour suite')
								 ->setFromTarget('est une suite de')
								 ]);
	}
}




class Admin_FrbrLinktypeControllerIndexTest extends Admin_FrbrLinktypeControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/frbr-linktype', true);
	}


	/** @test */
	public function pageTitleShouldBeTypesDeRelation() {
		$this->assertXPathContentContains('//h1', 'Types de relation');
	}


	/** @test */
	public function firstRowTDShouldContainsVousAimerezAussi() {
		$this->assertXPathContentContains('//tr[1]//td', 'Vous aimerez aussi');		
	}


	/** @test */
	public function firstRowTDShouldContainsVousAimerezAussiA() {
		$this->assertXPathContentContains('//tr[1]//td', 'vous aimerez aussi A');
	}


	/** @test */
	public function firstRowTDShouldContainsVousAimerezAussiB() {
		$this->assertXPathContentContains('//tr[1]//td', 'vous aimerez aussi B');		
	}


	/** @test */
	function firstRowTDShouldHaveLinkToEdit() {
		$this->assertXPath('//tr[1]//a[contains(@href, "frbr-linktype/edit/id/2")]');
	}


	/** @test */
	public function firstRowTDShouldHaveLinkToDelete() {
		$this->assertXPath('//tr[1]//a[contains(@href, "frbr-linktype/delete/id/2")]');
	}


	/** @test */
	public function secondRowTDShouldContainsSuite() {
		$this->assertXPathContentContains('//tr[2]//td', 'Suite');
	}


	/** @test */
	public function linkToAddNewShouldBePresent() {
		$this->assertXPath('//div[contains(@onclick, "frbr-linktype/add")]');
	}


	/** @test */
	public function linkToManageLinksShouldBePresent()  {
		$this->assertXPath('//div[contains(@onclick, "frbr-link\')")]');
	}
}




class Admin_FrbrLinktypeControllerEditSuiteTest extends Admin_FrbrLinktypeControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/frbr-linktype/edit/id/2', true);
	}


	/** @test */
	public function formShouldContainsInputForLibelle() {
		$this->assertXPath('//input[@name="libelle"]');
	}


	/** @test */
	public function formShouldContainsInputForFromAToB() {
		$this->assertXPath('//input[@name="from_source"]');
	}


	/** @test */
	public function formShouldContainsInputForFromBToA() {
		$this->assertXPath('//input[@name="from_target"]');
	}


	/** @test */
	public function labelForFromBToAInputShouldBeFromBToA() {
		$this->assertXPathContentContains('//label[@for="from_target"]', 'de l\'objet B vers l\'objet A');
	}
		
}



class Admin_FrbrLinktypeControllerEditSuitePostTest extends Admin_FrbrLinktypeControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_LinkType')
			->whenCalled('save')
			->answers(true);
	}


	/** @test */
	public function errorForEmptyLibelleShouldBeUnLibelleEstRequis() {
		$this->postDispatch('/admin/frbr-linktype/edit/id/2',
			                  ['libelle' => '', 'from_source' => '', 'from_target' => ''],
			                  true);
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 'Un libellé est requis');
	}


	/** @test */
	public function validPostShouldRedirect() {
		$this->postDispatch('/admin/frbr-linktype/edit/id/2',
			                  ['libelle' => 'Calcule', 'from_source' => 'calcule', 'from_target' => 'est calculé par'],
			                  true);

		$this->assertRedirect();
	}
}