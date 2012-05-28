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

class AdminIndexControllerTranslationTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('fr; en; ro');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('getIdentity')
			->answers(Class_Users::getLoader()->newInstanceWithId(777)
								->setLogin('sysadmin')
								->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::SUPER_ADMIN)
								->setPseudo('admin'));
	}


	/** @test */
	public function titleDefaultShouldBeAccueil() {
		unset(Zend_Registry::get('session')->language);
		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('h1', 'Accueil');
	}


	/** @test */
	public function titleShouldBeHomeWithEnglishInSession() {
		Zend_Registry::get('session')->language = 'en';
		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('h1', 'Home');
	}

	/** @test */
	public function titleShouldBeAccueilWithFrenchInSession() {
		Zend_Registry::get('session')->language = 'fr';
		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('h1', 'Accueil');
	}


	/** @test */
	public function titleShouldBePaginaIntialaWithRoumainInSession() {
		Zend_Registry::get('session')->language = 'ro';
		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('h1', 'Pagină iniţială');
	}


	/** @test */
	public function titleShouldBeHomeWithEnglishParmInUrl() {
		$this->dispatch('/admin/index?language=en');
		$this->assertQueryContentContains('h1', 'Home');
	}
}

?>