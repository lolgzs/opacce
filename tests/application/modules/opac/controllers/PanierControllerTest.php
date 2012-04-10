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

abstract class PanierControllerTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = 2;
		$account->ID_USER = $this->manon->getId();
		$account->PSEUDO = "ManonL";
	}
	

	public function setUp() {
		$this->manon = Class_Users::getLoader()
			->newInstanceWithId(23)
			->setPseudo('ManonL');


		$this->panier_bd = Class_PanierNotice::getLoader()
			->newInstanceWithId(2)
			->setIdPanier(1)
			->setLibelle('Mes BD')
			->setDateMaj('10/02/2011')
			->setNotices('COMBAT ORDINAIRE;BLACKSAD');

		$this->panier_romans = Class_PanierNotice::getLoader()
			->newInstanceWithId(15)
			->setIdPanier(2)
			->setLibelle('Mes Romans')
			->setDateMaj('25/05/2010')
			->setNotices('MONTESPAN');


		$this->panier_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_PanierNotice')
			->whenCalled('findAll')
			->answers(array($this->panier_bd, $this->panier_romans))
			->getWrapper()
			->whenCalled('findAllBy')
			->answers(array($this->panier_bd, $this->panier_romans))
			->getWrapper();

		parent::setUp();


		$_SERVER["HTTP_REFERER"] = 'http://localhost/afi-opac3/abonne/fiche';
	}
}



class PanierControllerIndexActionTest extends PanierControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/panier');
	}

	
	/** @test */
	public function actionShouldBeIndex() {
		$this->assertController('panier');
		$this->assertAction('index');
	}


	/** @test */
	public function panierMesBDShouldBeVisible() {
		$this->assertQueryContentContains('td', 'Mes BD');
	}


	/** @test */
	public function panierMesRomansShouldBeVisible() {
		$this->assertQueryContentContains('td', 'Mes Romans');
	}
}



class PanierControllerIndexWithPanierIdFifteenTest extends PanierControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/panier?id_panier=15');
	}

	
	/** @test */
	public function actionShouldBeIndex() {
		$this->assertController('panier');
		$this->assertAction('index');
	}

	/** @test */
	public function panierMesBDShouldBeVisible() {
		$this->assertQueryContentContains('td', 'Mes BD');
	}


	/** @test */
	public function panierMesRomansShouldBeVisible() {
		$this->assertQueryContentContains('td', 'Mes Romans');
	}
}



?>