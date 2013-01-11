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

class ModulesMenuTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::newInstanceWithId('VODECLIC_KEY', ['valeur' => 1234]);
		Class_AdminVar::newInstanceWithId('MULTIMEDIA_KEY', ['valeur' => 'zork']);


		$this->module_menu = new Class_Systeme_ModulesMenu();
	}


	/** @test */
	public function menuShouldContainsVodeclic() {
		Class_AdminVar::getLoader()->find('VODECLIC_KEY')->setValeur(null);

		$this->module_menu = new Class_Systeme_ModulesMenu();
		$menu = $this->module_menu->getFonction('VODECLIC');
		$this->assertEquals('index', $menu->getAction());
	}


	/** @test */
	public function menuShouldNotContainsVodeclicWhenDisabled() {
		$menu = $this->module_menu->getFonction('VODECLIC');
		$this->assertEquals('Lien vers Vodeclic', $menu->getLibelle());
	}


	/** @test */
	public function vodeclicUrlWithoutUserShouldBeLoginPage() {
		ZendAfi_Auth::getInstance()->clearIdentity();
		$this->assertEquals(array('url' => BASE_URL.'/auth/login', 'target' => ''), 
												$this->module_menu->getUrl('VODECLIC', array()));
	}


	protected function _logUserGaston() {
		$account = new stdClass();
		$account->username     = 'gaston';
		$account->password     = 'password';
		$account->ID_USER      = 34;
		
		ZendAfi_Auth::getInstance()->getStorage()->write($account);

		return Class_Users::getLoader()
			->newInstanceWithId(34)
			->setIdabon(34)
			->setNom('Lagaffe')
			->setPrenom('Gaston');
	}


	/** @test */
	public function vodeclicUrlWithUserLoggedShouldBeVodeclicSSO() {
		$this->_logUserGaston()
			->beAbonneSIGB()
			->setDateDebut('1999-02-10')
			->setDateFin('2025-09-12');
		
		$menu_url = $this->module_menu->getUrl('VODECLIC', array());
		$this->assertContains('vodeclic', $menu_url['url']);
		$this->assertEquals('_blank', $menu_url['target']);
	}


	/** @test */
	public function vodeclicUrlWithAbonnementInvalidShouldBeJSAlertAbonnementInvalid() {
		$this->_logUserGaston();

		$menu_url = $this->module_menu->getUrl('VODECLIC', array());
		$this->assertContains('javascript:alert(\\\'Votre abonnement est terminé\\\')', $menu_url['url']);
		$this->assertEquals('_blank', $menu_url['target']);
	}


	/** @test */
	public function reserverPosteUrlShouldBeAbonneReservations() {
		$this->assertEquals(['url' => BASE_URL.'/abonne/multimedia-hold-location', 'target' => ''],
												$this->module_menu->getUrl('RESERVER_POSTE', []));
	}
}


?>