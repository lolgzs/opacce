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

abstract class PortailWithOneLoginModuleTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$cfg_accueil = array('modules' => array(4 => array('division' => '4',
																									'id_module' => 4,
																									'type_module' => 'LOGIN',
																									'preferences' => array(
																											'titre'	=> 'Se connecter',
																											'identifiant' => 'Numéro de carte',
																											'mot_de_passe'=> 'Année de naissance'))),
												 'options' => 	array());

		Class_Profil::getCurrentProfil()
			->setBrowser('opac')
			->setCfgAccueil(ZendAfi_Filters_Serialize::serialize($cfg_accueil));
	}
}



class AuthControllerInviteLoggedTest extends PortailWithOneLoginModuleTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "invite";
		$account->ROLE_LEVEL = 1;
	}

	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/');
	}

	/** @test */
	public function noLinkPretsShouldBePresent() {
		$this->assertNotXPath('//div[@id="boite_login"]//a[contains(@href, "prets")]');
	}

	/** @test */
	public function linkSeDeconnecterShouldBePresent() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "auth/logout")]');
	}
}




class AuthControllerAbonneSIGBLoggedTest extends PortailWithOneLoginModuleTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB;
		$account->ID_USER      = 5;
		$account->ID_SITE      = 1;
	}

	public function setUp() {
		$this->emprunteur_patrick = Class_WebService_SIGB_Emprunteur::newInstance(5, 'patrick')
			->empruntsAddAll(array(Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
														 ->setDateRetour('23/12/2056'),

														 Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
														 ->setDateRetour('3/2/2056'),

														 Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
														 ->setDateRetour('23/1/1999')))
			
			->reservationsAddAll(array(Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire()));

		Class_Users::getLoader()->newInstanceWithId(5)
			->setLogin('patrick')
			->setIdabon(456)
			->setFicheSigb(array('fiche' => $this->emprunteur_patrick));

		parent::setUp();
		$this->dispatch('/opac/');
	}


	/** @test */
	public function linkPretsShouldBePresent() {
		$this->assertXPathContentContains('//div[@id="boite_login"]//a[contains(@href, "prets")]', '3');
	}


	/** @test */
	public function linkReservationsShouldBePresent() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "reservations")]');
	}


	/** @test */
	public function linkSeDeconnecterShouldBePresent() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "auth/logout")]');
	}
}




abstract class AuthControllerNobodyLoggedTestCase extends PortailWithOneLoginModuleTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 0;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}
}


class AuthControllerNobodyLoggedAndRegistrationAllowedTest extends AuthControllerNobodyLoggedTestCase {
	public function setUp() {
		$interdire_enregistrement = new Class_AdminVar();
		$interdire_enregistrement
			->setId('INTERDIRE_ENREG_UTIL')
			->setValeur(0);
		Class_AdminVar::getLoader()->cacheInstance($interdire_enregistrement);

		parent::setUp();
		$this->dispatch('/opac/');
	}


	public function testLinkSeConnecter() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@onclick, "submit")]');
		$this->assertXPathContentContains('//div[@id="boite_login"]//a[contains(@onclick, "submit")]',
																			'Se connecter');
	}

	public function testLinkLostPassword() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "auth/lostpass")]');
		$this->assertXPathContentContains('//div[@id="boite_login"]//a[contains(@href, "auth/lostpass")]',
																			'Mot de passe oublié ?');
	}

	public function testLinkSenregistrer() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "auth/register")]');
		$this->assertXPathContentContains('//div[@id="boite_login"]//a[contains(@href, "auth/register")]',
																			"S'enregistrer");
	}


	public function testCanAccessRegisterPage() {
		$this->dispatch('auth/register');
		$this->assertAction('register');
		$this->assertController('auth');
		$this->assertNotRedirect('/');
	}


	/** @test */
	function headShouldContainsAbonnesJS() {		
		$this->assertXPath('//head//script[contains(@src,"public/opac/js/abonne.js")]', $this->_response->getBody());
	}


	/** @test */
	function headShouldContainsAdminCommonJS() {
		$this->assertXPath('//head//script[contains(@src,"public/admin/js/common.js")]');
	}


	/** @test */
	function headShouldContainsJQuery() {
		$this->assertXPath('//head//script[contains(@src, "jquery")]');
	}
}




class AuthControllerNobodyLoggedAndNoRegistrationTest extends AuthControllerNobodyLoggedTestCase {
	public function setUp() {
		$interdire_enregistrement = new Class_AdminVar();
		$interdire_enregistrement
			->setId('INTERDIRE_ENREG_UTIL')
			->setValeur(1);
		Class_AdminVar::getLoader()->cacheInstance($interdire_enregistrement);

		parent::setUp();
		$this->dispatch('/opac/');
	}


	public function testLinkSenregistrerNotHere() {
		$this->assertNotXPath('//div[@id="boite_login"]//a[contains(@href, "auth/register")]');
	}


	public function testCannotAccessRegisterPage() {
		$this->dispatch('auth/register');
		$this->assertRedirect('/');
	}
}




class AuthControllerAdminIsLoggedTest extends PortailWithOneLoginModuleTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
		$account->ID_USER = "1";
		$account->PSEUDO = "sysadm";
	}


	public function setUp() {
		$this->sysadm = new Class_Users();
		$this->sysadm
			->setPseudo('sysadm')
			->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL)
			->setRole('super_admin')
			->setLogin('sysadm')
			->setPassword('pafgjl')
			->setIdSite(1)
			->setIdabon('')
			->setId(1);
		Class_Users::getLoader()->cacheInstance($this->sysadm);

		parent::setUp();
		$this->dispatch('/opac/auth/login');
	}


	public function testBoiteLoginDisplaysBienvenueSysadm() {
		$this->assertQueryContentContains('#boite_login .welcome', 'Bienvenue sysadm');
	}


	public function testLinkSeDeconnecter() {
		$this->assertXPath('//div[@id="boite_login"]//a[contains(@href, "auth/logout")]',
																			$this->_response->getBody());
		$this->assertXPathContentContains('//div[@id="boite_login"]//a[contains(@href, "auth/logout")]',
																			'Se déconnecter');
	}


	public function testLinkFonctionAdmin() {
		$this->assertXPath("//div[@class='configuration_module']//img[contains(@onclick,'admin/modules/auth?config=site&type_module=auth&id_profil=2&action1=login')]");
	}
}




class AuthControllerEmptyPostTest extends AuthControllerNobodyLoggedTestCase {
	public function setUp()	{
		parent::setUp();
		$this->postDispatch('/opac/auth/boitelogin?id_module=4',
												array('username' => '',
															'password' => ''));
	}


	/** @test */
	public function emptyUsernameShouldDisplayEntrezVotreNom() {
		$this->assertXpathContentContains('//p[@class="error"]', 'Entrez votre identifiant S.V.P');
	}


	/** @test */
	public function boiteFormActionShouldContainIdModule() {
		$this->assertXpath('//div[@id="boite_login"]//form[contains(@action, "auth/boitelogin/id_module/4")]');
	}


	/** @test */
	public function contentFormActionShouldContainIdModule()	{
		$this->assertXpath('//div[@id="col_wrapper"]//form[contains(@action, "auth/boitelogin/id_module/4")]');
	}
}




class AuthControllerPostTest extends AuthControllerNobodyLoggedTestCase {
	/** @test */
	public function emptyPasswordShouldDisplayEntrezVotreMotDePasse()	{
		$this->postDispatch('/opac/auth/boitelogin?id_module=4',
												array('username' => 'My overkill username',
															'password' => ''));

		$this->assertXpathContentContains('//p[@class="error"]', 'Entrez votre mot de passe S.V.P.');
	}


	/** @test */
	public function validAuthenticationShouldRedirect()	{
		$user = Class_Users::getLoader()->findFirstBy(array());

		$this->postDispatch('/opac/auth/boitelogin?id_module=4',
												array('username' => $user->getLogin(),
															'password' => $user->getPassword()));

		$this->assertRedirect($this->_response->getBody());
	}
}


?>