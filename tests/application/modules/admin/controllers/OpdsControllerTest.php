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
											->setUrl('http://pragprog.com/magazines.opds')));
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
