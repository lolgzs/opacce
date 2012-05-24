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

class SystemeControllerWebServicesIndexActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/systeme/webservices');
	}


	/** @test */
	public function titleShouldBeTestDesWebServices() {
		$this->assertXPathContentContains('//h1', 'Test des Web Services');
	}


	/** @test */
	public function amazonRendAvisShouldBeVisible() {
		$this->assertXPathContentContains('//a[contains(@href, "webservices/id_service/Amazon/id_fonction/1")]', 
																			'rend_avis(isbn,page)');
	}
}




class SystemeControllerWebServicesActionTest extends Admin_AbstractControllerTestCase {
	/** 
	 * @group longtest
	 * @group integration
	 * @test 
	 */
	public function webServiceFnacGetResumeShouldWork() {
		$this->dispatch('/admin/systeme/webservices/id_service/Fnac/id_fonction/1');
		$this->assertXPathContentContains('//pre[@class="resultat"]', 
																			'Tandis que Lisbeth Salander');
	}
}

?>