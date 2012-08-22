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
abstract class ViewHelperTestCase extends PHPUnit_Framework_TestCase {
	public function assertQueryContentContains() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());
	}


	public function assertNotQueryContentContains() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}


	public function assertXpath() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}


	public function assertXpathCount() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}


	public function assertNotXpath() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}


	public function assertXpathContentContains() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());
	}


	public function assertNotXpathContentContains() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());
	}


	public function assertQueryCount()	{
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}

	/** 
	 * @param string $html
	 * @param array $paths
	 * @param string $message
	 */
	public function assertAnyXpath() {
		call_user_func_array(array(new Storm_Test_XPath(), __FUNCTION__), 
												 func_get_args());

	}


	public function setUp() {
		parent::setUp();

		Zend_Registry::set("path_templates", 'public/opac/skins/original/templates/');
		if (!defined("URL_IMG"))
			define("URL_IMG", 'public/opac/skins/original/images');

		Class_AdminVar::getLoader()->newInstanceWithId('CACHE_ACTIF')->setValeur(false);
		Class_Profil::setCurrentProfil(Class_Profil::getLoader()->newInstanceWithId(2));

		$router = new Zend_Controller_Router_Rewrite();
		$router->addDefaultRoutes();

		Zend_Controller_Front::getInstance()
			->setRouter($router)
			->setDefaultModule('opac');

		if (null !== ($request = Zend_Controller_Front::getInstance()->getRequest())) {
			$request->setActionName('index')
				->setControllerName('index');
		}

		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur(null);
	}


	protected function tearDown() {
		Storm_Model_Abstract::unsetLoaders();
		Zend_Registry::get('cache')->clean();
		$this->logout();
	}


	public function logout() {
		ZendAfi_Auth::getInstance()->clearIdentity();
	}

	public function login($role) {
		$account = new stdClass();
		$account->username     = 'AutoTest' . time();
		$account->password     = md5( 'password' );
		$account->ID_USER      = 666;
		$account->ROLE_LEVEL   = $role;
		$account->ID_SITE      = 1;
		$account->confirmed    = true;
		$account->enabled      = true;

		Class_Users::getLoader()
			->newInstanceWithId(666)
			->setRoleLevel($role);

		Class_Bib::getLoader()
			->newInstanceWithId(1)
			->setLibelle('Tombouctou');

		ZendAfi_Auth::getInstance()->getStorage()->write($account);
	}
}
?>