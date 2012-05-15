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

abstract class Admin_OpdsControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_OpdsCatalog')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array(Class_OpdsCatalog::getLoader()->newInstanceWithId(1)
											->setLibelle('Ebooks gratuits')
											->setUrl('http://www.ebooksgratuits.com/opds/'),

											Class_OpdsCatalog::getLoader()->newInstanceWithId(2)
											->setLibelle('PragPub Magazine')
											->setUrl('http://pragprog.com/magazines.opds')))

			->whenCalled('save')
			->answers(true)

			->whenCalled('delete')
			->answers(true);
	}
}



class Admin_OpdsControllerIndexActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds');
	}


	/** @test */
	public function pageTitleShouldBeCataloguesOPDS() {
		$this->assertXPathContentContains('//h1', 'Catalogues OPDS');
	}


	/** @test */
	public function shouldHaveAddCatalogButton() {
		$this->assertXPath('//div[contains(@onclick, "opds/add")]');
	}


	/** @test */
	public function catalogEbooksGratuitsShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Ebooks gratuits');
	}


	/** @test */
	public function ebooksgratuitsUrlShouldBePresent() {
		$this->assertXPathContentContains('//td', 'http://www.ebooksgratuits.com/opds/');
	}



	/** @test */
	public function editLinkForEbooksGratuitsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "edit/id/1")]');
	}


	/** @test */
	public function deleteLinkForEbooksGratuitsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "delete/id/1")]');
	}


	/** @test */
	public function catalogPragPubMagazineShouldBePresent() {
		$this->assertXPathContentContains('//td', 'PragPub Magazine');
	}


	/** @test */
	public function editLinkForPragPubShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "edit/id/2")]');
	}


	/** @test */
	public function deleteLinkForPragPubShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "delete/id/2")]');
	}
}



class Admin_OpdsControllerAddActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds/add');
	}


	/** @test */
	public function titleShouldBeAjouterUnCatalogueOpds() {
		$this->assertXPathContentContains('//h1', 'Ajouter un catalogue OPDS');
	}


	/** @test */
	public function libelleInputShouldBePresent() {
		$this->assertXPath('//input[@name="libelle"]');
	}


	/** @test */
	public function urlInputShouldBePresent() {
		$this->assertXPath('//input[@name="url"]');
	}
}




class Admin_OpdsControllerAddPostActionTest extends Admin_OpdsControllerTestCase {
	protected $_wrapper;
	protected $_new_catalog;

	public function setUp() {
		parent::setUp();
		$this->_wrapper = Class_OpdsCatalog::getLoader()
			->whenCalled('save')
			->willDo(function($model) {
					$model->setId(99);
					return true;
				});
		$this->postDispatch('/admin/opds/add', array('libelle' => 'Freebooks',
																								 'url' => 'http://www.freebooks.org/opds'));

		$this->_new_catalog = $this->_wrapper->getFirstAttributeForLastCallOn('save');
	}

	
	/** @test */
	public function newCatalogLibelleShouldBeFreebooks() {
		$this->assertEquals('Freebooks', $this->_new_catalog->getLibelle());
	}


	/** @test */
	public function newCatalogUrlShouldFreebookDotOrg() {
		$this->assertEquals('http://www.freebooks.org/opds', 
												$this->_new_catalog->getUrl());
	}


	/** @test */
	public function responseShouldRedirectToEditCatalogId99() {
		$this->assertRedirectTo('/admin/opds/edit/id/99');
	}

}




class Admin_OpdsControllerInvalidPostActionTest extends Admin_OpdsControllerTestCase {
	protected $_new_catalog;

	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/opds/add', array('libelle' => '',
																								 'url' => 'zork'));
	}

	
	/** @test */
	public function errorsShouldContainsUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 'Une valeur est requise');
	}
	

	/** @test */
	public function errorsShouldContainsUrlNotValid() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 
																			"'zork' n'est pas une URL valide");
	}


	/** @test */
	public function responsShouldNotRedirect() {
		$this->assertNotRedirect();
	}
}




class Admin_OpdsControllerEditActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_OpdsCatalog::getLoader()->newInstanceWithId(1)
			->setLibelle('Ebooks gratuits')
			->setUrl('http://www.ebooksgratuits.com/opds/');
		$this->dispatch('/admin/opds/edit/id/1');
	}


	/** @test */
	public function titleShouldBeModifierUnCatalogueOpds() {
		$this->assertXPathContentContains('//h1', 'Modifier un catalogue OPDS');
	}


	/** @test */
	public function libelleInputShouldContainEbooksGratuits() {
		$this->assertXPath('//input[@name="libelle"][@value="Ebooks gratuits"]');
	}


	/** @test */
	public function urlInputShouldContainEbookGratuitDotCom() {
		$this->assertXPath('//input[@name="url"][contains(@value, "www.ebooksgratuits.com")]');
	}
}




class Admin_OpdsControllerEditPostActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/opds/edit/id/2', array('libelle' => 'Science et vie',
																											 'url' => 'http://sev.opds'));
	}

	
	/** @test */
	public function libelleShouldBeScienceEtVie() {
		$this->assertEquals('Science et vie', Class_OpdsCatalog::getLoader()->find(2)->getLibelle());
	}

	
	/** @test */
	public function saveShouldHaveBeenCalled() {
		$this->assertTrue(Class_OpdsCatalog::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function shouldRedirectToEdit() {
		$this->assertRedirectTo('/admin/opds/edit/id/2');
	}
}




class Admin_OpdsControllerDeleteActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds/delete/id/2');
	}


	/** @test */
	public function deleteShouldHaveBeenCalled() {
		$this->assertTrue(Class_OpdsCatalog::getLoader()->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/opds/index');
	}		
}



class Admin_OpdsControllerUnknownIdsActionErrorsTest extends Admin_OpdsControllerTestCase {
	/** @test */
	public function deleteShouldRedirectToIndex() {
		$this->dispatch('/admin/opds/delete/id/66666');
		$this->assertRedirectTo('/admin/opds/index');
	}


	/** @test */
	public function editShouldRedirectToIndex() {
		$this->dispatch('/admin/opds/edit/id/66666');
		$this->assertRedirectTo('/admin/opds/index');
	}
}
?>