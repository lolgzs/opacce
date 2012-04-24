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
require_once 'AbstractControllerTestCase.php';

abstract class LieuControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur(1);

		$this->afi_annecy = Class_Lieu::getLoader()
			->newInstanceWithId(3)
			->setLibelle('AFI Annecy')
			->setAdresse('11, boulevard du fier')
			->setCodePostal('74000')
			->setVille('Annecy')
			->setPays('France');


		$this->afi_lognes = Class_Lieu::getLoader()
			->newInstanceWithId(5)
			->setLibelle('AFI Lognes')
			->setAdresse('35, rue de la Maison Rouge')
			->setCodePostal('77185')
			->setVille('Lognes')
			->setPays('France');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Lieu')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array($this->afi_annecy, $this->afi_lognes))
			->beStrict();
	}
}




class LieuControllerListTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/lieu');
	}

	
	/** @test */
	public function aListItemShouldContainsAnnecy() {
		$this->assertXPathContentContains('//ul//li[1]', 'AFI Annecy');
	}


	/** @test */
	public function aListItemShouldContainsLognes() {
		$this->assertXPathContentContains('//ul//li[2]', 'AFI Lognes');
	}


	/** @test */
	public function annecyShouldHaveLinkToEdit() {
		$this->assertXPath('//ul//li[1]//a[contains(@href, "lieu/edit/id/3")]');
	}


	/** @test */
	public function annecyShouldHaveLinkToDelete() {
		$this->assertXPath('//ul//li[1]//a[contains(@href, "lieu/delete/id/3")]');
	}


	/** @test */
	public function titreShouldBeLieux() {
		$this->assertXPathContentContains('//h1', 'Lieux');
	}


	/** @test */
	public function pageShouldContainsButtonToCreateLieu() {
		$this->assertXPathContentContains('//div[contains(@onclick, "lieu/add")]//td', 'Déclarer un nouveau lieu');
	}


	/** @test */
	function menuGaucheAdminShouldContainsLinkToLieu() {
		$this->assertXPathContentContains('//div[@class="menuGaucheAdmin"]//a[contains(@href,"admin/lieu")]',
																			"Lieux");
	}
}

?>