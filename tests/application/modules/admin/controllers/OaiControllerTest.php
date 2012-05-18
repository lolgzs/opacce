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


abstract class Admin_OaiControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$gallica = Class_EntrepotOAI::getLoader()
			->newInstanceWithId(4)
			->setLibelle('Gallica')
			->setHandler('http://oai.bnf.fr/oai2/OAIHandler');

		$open_archive = Class_EntrepotOAI::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Open Archives')
			->setHandler('http://hal.archives-ouvertes.fr/oai/oai.php');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_EntrepotOAI')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array($gallica, $open_archive));
	}
}




class Admin_OaiControllerIndexActionTestCase extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/oai');
	}


	/** @test */
	public function titleShouldBeRessourcesOAI() {
		$this->assertXPathContentContains('//h1', 'Ressources OAI');
	}


	/** @test */
	public function selectOptionShouldContainsGallica() {
		$this->assertXPathContentContains('//select[@name="entrepot_id"]//option[@value="4"]', 'Gallica');
	}


	/** @test */
	public function selectOptionShouldContainsOpenArchive() {
		$this->assertXPathContentContains('//select[@name="entrepot_id"]//option[@value="5"]', 'Open Archives');
	}
}
?>