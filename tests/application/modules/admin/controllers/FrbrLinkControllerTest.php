<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README f ile).
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

abstract class Admin_FrbrLinkControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$type = Class_FRBR_LinkType::newInstanceWithId(3)
			->setLibelle('Suite')
			->setFromSource('a pour suite')
			->setFromTarget('est une suite de');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_Link')
			->whenCalled('findAllBy')
			->with(['order' => 'source'])
			->answers([
								 Class_FRBR_Link::newInstanceWithId(2)
								 ->setType($type)
								 ->setSource('http://localhost/afi-opac3-ce/recherche/viewnotice/clef/LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-1?id_profil=1&type_doc=4')
								 ->setSourceType(Class_FRBR_Link::TYPE_NOTICE)
								 ->setTarget('http://localhost/afi-opac3-ce/recherche/viewnotice/clef/LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-2?id_profil=1&type_doc=4')
								 ->setTargetType(Class_FRBR_Link::TYPE_NOTICE),

								 Class_FRBR_Link::newInstanceWithId(3)
								 ->setType($type)
								 ->setSource('http://localhost/afi-opac3-ce/recherche/viewnotice/clef/AMNESTYINTERNATIONAL--GRANTR--GAMMA-2002-1?id_profil=1&type_doc=4')
								 ->setSourceType(Class_FRBR_Link::TYPE_NOTICE)
								 ->setTarget('http://localhost/afi-opac3-ce/recherche/viewnotice/clef/AMNESTYINTERNATIONAL--GRANTR--GAMMA-2002-2?id_profil=1&type_doc=4')
								 ->setTargetType(Class_FRBR_Link::TYPE_NOTICE)
								 ]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('getNoticeByClefAlpha')
			->with('LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-1')
			->answers(Class_Notice::newInstanceWithId(33)
				          ->setTitrePrincipal('Les grands textes tome 1')
				          ->setAuteurPrincipal('Vidalio Nesti'))

			->whenCalled('getNoticeByClefAlpha')
			->with('LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-2')
			->answers(Class_Notice::newInstanceWithId(33)
				          ->setTitrePrincipal('Les grands textes tome 2')
				          ->setAuteurPrincipal('Vidalio Nesto'));
	}
}




class Admin_FrbrLinkControllerIndexTest extends Admin_FrbrLinkControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/frbr-link', true);
	}


	/** @test */
	public function pageTitleShouldBeTypesDeRelation() {
		$this->assertXPathContentContains('//h1', 'Notices liées');
	}


	/** @test */
	public function firstRowTDShouldContainsSourceTitle() {
		$this->assertXPathContentContains('//tr[1]//td', 'Les grands textes tome 1');
	}


	/** @test */
	public function firstRowTDShouldContainsSourceAuthor() {
		$this->assertXPathContentContains('//tr[1]//td', 'Vidalio Nesti');
	}


	/** @test */
	public function firstRowTDShouldContainsSourceLink() {
		$this->assertXPath('//tr[1]//td//a[contains(@href, "LESGRANDSTEXTESDEDROITINTERNATIONALPUBLIC--DUPUYP--DALLOZ-2010-1")]');
	}


	/** @test */
	public function firstRowTDShouldContainsTargetTitle() {
		$this->assertXPathContentContains('//tr[1]//td', 'Les grands textes tome 2');
	}


	/** @test */
	public function firstRowTDShouldContainsTargetAuthor() {
		$this->assertXPathContentContains('//tr[1]//td', 'Vidalio Nesto');
	}


	/** @test */
	public function firstRowTDShouldContainsTypeLabelFromSource() {
		$this->assertXPathContentContains('//tr[1]//td', 'a pour suite');		
	}


	/** @test */
	public function firstRowTDShouldContainsTypeLabelFromTarget() {
		$this->assertXPathContentContains('//tr[1]//td', 'est une suite de');
	}


	/** @test */
	function firstRowTDShouldHaveLinkToEdit() {
		$this->assertXPath('//tr[1]//a[contains(@href, "frbr-link/edit/id/2")]');
	}


	/** @test */
	public function firstRowTDShouldHaveLinkToDelete() {
		$this->assertXPath('//tr[1]//a[contains(@href, "frbr-link/delete/id/2")]');
	}


	/** @test */
	public function linkToAddNewShouldBePresent() {
		$this->assertXPath('//div[contains(@onclick, "frbr-link/add")]');
	}


	/** @test */
	public function linkToManageTypesShouldBePresent() {
		$this->assertXPath('//div[contains(@onclick, "frbr-linktype")]');
	}
}



class Admin_FrbrLinkControllerEditSuiteTest extends Admin_FrbrLinkControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/frbr-link/edit/id/2', true);
	}


	/** @test */
	public function inputForTypeShouldBePresent() {
		$this->assertXPath('//select[@name="type_id"]');
	}


	/** @test */
	public function inputForSourceShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@name="source"]');
	}


	/** @test */
	public function inputForTargetShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@name="target"]');
	}
}


abstract class Admin_FrbrLinkControllerEditSuiteValidPostTestCase extends Admin_FrbrLinkControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_Link')
				->whenCalled('save')
				->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_FRBR_LinkType')
				->whenCalled('getComboList')
				->answers([3 => 'Suite']);
	}


	/** @test */
	public function shouldSave() {
		$this->assertTrue(Class_FRBR_Link::methodHasBeenCalled('save'));
	}
	
	
	/** @test */
	public function shouldRedirect() {
		$this->assertRedirect();
	}
}



class Admin_FrbrLinkControllerEditSuiteValidWithLocalUrlsPostTest extends Admin_FrbrLinkControllerEditSuiteValidPostTestCase {
	protected $_link;
	
	public function setUp() {
		parent::setUp();

		$this->postDispatch('/admin/frbr-link/edit/id/2',
			                  ['type_id' => 3,
												 'source' => 'http://localhost/afi-opac3-ce/recherche/viewnotice/clef/LES1000MOTSDELINFO-POURMIEUXCOMPRENDREE-COMBRESE--GALLIMARDJEUNESSE-2003-1/type_doc/1/id/44275',
												 'target' => 'http://localhost/afi-opac3-ce/recherche/viewnotice/clef/1928--ELLINGTOND-VOLUME4-MEDIA7-1992-3?id_profil=1&type_doc=3'],
			                  true);

		$this->_link = Class_FRBR_Link::find(2);
	}

	
	/** @test */
	public function sourceTypeShouldBeNotice() {
		$this->assertEquals(Class_FRBR_Link::TYPE_NOTICE,
			                  $this->_link->getSourceType());
	}


	/** @test */
	public function targetTypeShouldBeNotice() {
		$this->assertEquals(Class_FRBR_Link::TYPE_NOTICE,
			                  $this->_link->getTargetType());
	}
}