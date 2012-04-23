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
abstract class AbstractControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {
	protected $_registry_sql;

	//permet d'authentifier sur la partie admin avant test
	public $bootstrap = 'bootstrap_frontcontroller.php';

	protected function _initProfilHook($profil) {}


	protected function _initMockProfil() {
		$cfg_site = array("largeur_site" => 1000,
											"nb_divisions" => 3,
											"menu_haut_on" => 0,
											"barre_nav_on" => 1,
											"accessibilite_on" => 0,
											"largeur_division1" => 250,
											"marge_division1" => 10,
											"largeur_division2" => 600,
											"marge_division2" => 10,
											"largeur_division3" => 150,
											"marge_division3" => 10,
											"ref_description" => '',
											"ref_tags" => '');
		$cfg_modules = array();


		$profil = new Class_Profil();
		$profil
			->setId(2)
			->setLibelle('PHP Unit')
			->setTitreSite('PHP Unit')
			->setCfgSite(ZendAfi_Filters_Serialize::serialize($cfg_site))
			->setSkin('original')
   		->setRefTags('')
			->setMailSite('laurent@afi-sa.net')
			->setBrowser('opac')
			->setCfgModules(ZendAfi_Filters_Serialize::serialize($cfg_modules));

		$this->_initProfilHook($profil); //so subclasses can put custom values

		Class_Profil::getLoader()->cacheInstance($profil);
		Class_Profil::setCurrentProfil($profil);
	}


	protected function _loginHook($account) {}

	protected function _login() {
		$account = new stdClass();
		$account->username     = 'AutoTest' . time();
		$account->password     = md5( 'password' );
		$account->ID_USER      = 666;
		$account->ROLE_LEVEL   = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ROLE         = "admin_portail";
		$account->ID_SITE      = 1;
		$account->confirmed    = true;
		$account->enabled      = true;

		$user = Class_Users::getLoader()
			->newInstanceWithId(666);

		Class_Bib::getLoader()
			->newInstanceWithId(1)
			->setLibelle('Tombouctou');

		$this->_loginHook($account);

		$user->setRoleLevel($account->ROLE_LEVEL);
		Zend_Auth::getInstance()->getStorage()->write($account);
	}


	public function setUp() {
		$this->_registry_sql = Zend_Registry::get('sql');
		Class_ScriptLoader::resetInstance();

		$this->_initMockProfil();

		parent::setUp();

		$this->_login();

		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur(null);

		Class_AdminVar::getLoader()
			->newInstanceWithId('CACHE_ACTIF')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->newInstanceWithId('BIBNUM')
			->setValeur(1);
	}

	public function tearDown() {
		Storm_Model_Abstract::unsetLoaders();
		Zend_Registry::set('sql', $this->_registry_sql);

		Class_I18n::reset();
	}


	protected function _generateLoaderFor($model, $methods) {
		$loader = $this->getMock('Mock'.$model, $methods);
		Storm_Model_Abstract::setLoaderFor($model, $loader);
		return $loader;
	}


	public function getSqlMock() {
		$mock_sql = $this->getMockBuilder('Class_Systeme_Sql')
                         			->disableOriginalConstructor()
                        			->getMock();

		Zend_Registry::set('sql', $mock_sql);
		return $mock_sql;
	}


	public function postDispatch($url, $data) {
		$this->getRequest()
			->setMethod('POST')
			->setPost($data);

		return $this->dispatch($url);
	}

	/**
	 * Retourne la valeur du header Location 
	 * @return String
	 */
	function getResponseLocation() {
		$headers = $this->_response->getHeaders();
		foreach ($headers as $header)
			if ($header['name'] = 'Location')
				return $header['value'];
		return null;
	}
}

?>