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

		Class_AdminVar::getLoader()
			->newInstanceWithId('VODECLIC_KEY')
			->setValeur(1234);

		$this->module_menu = new Class_Systeme_ModulesMenu();
	}


	/** @test */
	public function menuShouldContainsVodeclic() {
		Class_AdminVar::getLoader()->find('VODECLIC_KEY')->setValeur(null);

		$this->module_menu = new Class_Systeme_ModulesMenu();
		$menu = $this->module_menu->getFonction('VODECLIC');
		$this->assertEquals('index', $menu['action']);
	}


	/** @test */
	public function menuShouldNotContainsVodeclicWhenDisabled() {
		$menu = $this->module_menu->getFonction('VODECLIC');
		$this->assertEquals('Lien vers Vodeclic', $menu['libelle']);
	}


	/** @test */
	public function vodeclicUrlWithoutUserShouldBeLoginPage() {
		Zend_Auth::getInstance()->clearIdentity();
		$this->assertEquals(array('url' => BASE_URL.'/auth/login', 'target' => '0'), 
												$this->module_menu->getUrl('VODECLIC', array()));
	}


	/** @test */
	public function vodeclicUrlWithUserLoggedShouldBeVodeclicSSO() {
		$account = new stdClass();
		$account->username     = 'jean';
		$account->password     = 'password';
		$account->ID_USER      = 34;
		
		Class_Users::getLoader()->newInstanceWithId(34)->setIdabon(34);
		Zend_Auth::getInstance()->getStorage()->write($account);
		$menu_url = $this->module_menu->getUrl('VODECLIC', array());
		$this->assertContains('vodeclic', $menu_url['url']);
		$this->assertEquals('1', $menu_url['target']);
	}
}


?>