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
	public function assertQueryContentContains($html, $path, $match, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
		}
	}


	public function assertNotQueryContentContains($html, $path, $match, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__, $match)) {
            $constraint->fail($path, $message);
		}
	}


	public function assertXpath($html, $path, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__)) {
            $constraint->fail($path, $message);
		}
	}


	public function assertNotXpath($html, $path, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__)) {
            $constraint->fail($path, $message);
		}
	}


	public function assertXpathContentContains($html, $path, $match, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}


	public function assertNotXpathContentContains($html, $path, $match, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__, $match)) {
			$constraint->fail($path, $message);
		}
	}


	public function assertQueryCount($html, $path, $count, $message = '')
	{
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($html, __FUNCTION__, $count)) {
			$constraint->fail($path, $message);
		}
	}


	/** 
	 * @param string $html
	 * @param array $paths
	 * @param string $message
	 */
	public function assertAnyXpath($html, array $paths, $message = '') {
		foreach ($paths as $path) {
			$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
			if ($constraint->evaluate($html, 'assertXpath')) {
				return;
			}
		}

		$this->fail("Failed asserting any path from (\n\t" . implode(", \n\t", $paths)  
								. ') EXISTS' . "\n" . $message);
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
		Zend_Auth::getInstance()->clearIdentity();
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

		Zend_Auth::getInstance()->getStorage()->write($account);
	}
}
?>