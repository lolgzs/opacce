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


abstract class AbstractIndexControllerTelephoneWithModulesTest extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 0;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}


	public function setUp() {
		parent::setUp();


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->answers(array(Class_Article::getLoader()
											->newInstanceWithId(3)
											->setTitre('E.Truffaz')
											->setDescription('A Bonlieu <img src="truffaz.jpg" />.')));

		$cfg_accueil =
			array('modules' => array('1' => array('division' => '1',
																						'type_module' => 'RECH_SIMPLE',
																						'preferences' => array()),

															 '2' => array('division' => '1',
																						'type_module' => 'NEWS',
																						'preferences' => array('titre' => 'Concerts',
																																	 'rss_avis' => 0)),

															 '3' => array('division' => '1',
																						'type_module' => 'LOGIN',
																						'preferences' => array('titre' => 'Se connecter',
																																	 'identifiant' => 'identifiant',
																																	 'identifiant_exemple' => 'numero carte',
																																	 'mot_de_passe' => 'mot de passe',
																																	 'mot_de_passe_exemple' => 'zork',
																																	 'lien_connexion' => 'go')))); 

		$this->profil_adulte = Class_Profil::getCurrentProfil()
			->beTelephone()
			->setTitreSite('Smartphone')
			->setLibelle(null)
			->setCfgAccueil($cfg_accueil)
			->setHauteurBanniere(150);
	}
}




class IndexControllerTelephoneWithModulesTest extends AbstractIndexControllerTelephoneWithModulesTest {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/');
	}


	/** @test	 */
  public function moduleShouldBeTelephone() {
		$this->assertModule('telephone');
	}


	/** @test	 */
	public function shouldBeDisplayedInIPhoneSimulation() {
		$this->assertQuery('div#iphone_container');
	}


	/** @test */
	function formRechercheShouldBeVisible() {
		$this->assertXPath('//form[contains(@action, "recherche/lancer")]');
	}


	/** @test */
	function articlesShouldBeVisible() {
		$this->assertXPath('//ul[@class="articles"]');
	}


	/** @test */
	function titreBoiteNewsShouldBeConcert() {
		$this->assertXPathContentContains('//div[@class="titre"]', 'Concerts');
	}


	/** @test */
	function articleTruffazShouldBeDisplayed() {
		$this->assertXPathContentContains('//span', 'E.Truffaz', $this->_response->getBody());
	}


	/** @test */
	function vignetteTruffazShouldBeFirstImage() {
		$this->assertXPath('//img[@src="truffaz.jpg"]');
	}


	/** @test */
	public function formLoginShouldBeVisible() {
		$this->assertXPath('//form[contains(@action, "auth/boitelogin")][@method="post"]');
	}


	/** @test */
	public function boiteLoginShouldBeVisible() {
		$this->assertXPathContentContains('//h2', 'Se connecter');
	}


	/** @test */
	public function boiteLoginShouldHaveInputForUsername() {
		$this->assertXPath('//input[@name="username"][@placeholder="numero carte"]');
	}


	/** @test */
	public function boiteLoginShouldLabelForUsername() {
		$this->assertXPathContentContains('//label[@for="username"]', 'identifiant');
	}


	/** @test */
	public function boiteLoginShouldHaveInputForPassword() {
		$this->assertXPath('//input[@name="password"][@type="password"][@placeholder="zork"]');
	}


	/** @test */
	public function boiteLoginShouldLabelForPassword() {
		$this->assertXPathContentContains('//label[@for="password"]', 'mot de passe');
	}


	/** @test */
	public function boiteLoginShouldHaveButtonForConnect() {
		$this->assertXPath('//input[@type="submit"][@value="go"]');
	}


	/** @test */
	public function formShouldNotHaveDDAndDTTag() {
		$this->assertNotXPath('//dd');
		$this->assertNotXPath('//dt');
	}
}




class IndexControllerTelephoneWithModulesAndUserLoggedTest extends AbstractIndexControllerTelephoneWithModulesTest {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = 2;
		$account->ID_USER = 54321;
		$account->PSEUDO = "mario";
	}


	public function setUp() {
		Class_Users::getLoader()
			->newInstanceWithId(54321)
			->setNom('Bros')
			->setPrenom('Mario');
			
		parent::setUp();
		$this->dispatch('/');
	}


	/** @test */
	public function formLoginShouldNotBeVisible() {
		$this->assertNotXPath('//form[contains(@action, "boitelogin")]');
	}


	/** @test */
	public function boiteLoginShouldDisplayBienvenuMario() {
		$this->assertXPathContentContains('//div', 'Bienvenue Mario');
	}


	/** @test */
	public function boiteLoginShouldDisplayLinkToLogout() {
		$this->assertXPath('//a[contains(@href, "auth/logout")]');
	}
}




class IndexControllerTelephoneTelephoneSwitchProfilTest extends Zend_Test_PHPUnit_ControllerTestCase {
	public $bootstrap = 'bootstrap_frontcontroller.php';

 	public function setUp() {
 		$_SERVER['HTTP_USER_AGENT'] = 'iphone';
 		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel("Class_Profil")
							->whenCalled('findFirstBy')
							->with(array('BROWSER' => 'telephone'))
							->answers(Class_Profil::getLoader()->newInstanceWithId(4)
												->setBrowser('telephone')
												->setTitreSite('Smartphone'));

		$this->dispatch('/');
	}

	public function tearDown()
	{
		unset ($_SERVER['HTTP_USER_AGENT']);
		parent::tearDown();

	}

	/**
	 * @test
	 */
  public function moduleShouldBeTelephone() {
		$this->assertModule('telephone');
	}

	/** @test */
	public function currentProfilIdShouldBeFour() {
		$this->assertEquals(4, Class_Profil::getCurrentProfil()->getId());
	}
}



class IndexControllerTelephoneEmbedModuleTest extends AbstractIndexControllerTelephoneWithModulesTest {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/embed');
	}


	/**
	 * @test
	 */
	public function moduleShouldBeTelephone() {
		$this->assertModule('telephone');
	}


	/**
	 * @test
	 */
	public function shouldNotBeDisplayedInIPhoneSimulation() {
		$this->assertNotQuery('div#iphone_container');
	}


	/** @test */
	function articlesShouldBeVisible() {
		$this->assertXPath('//ul[@class="articles"]');
	}


	/** @test */
	function articleErikTruffazUrlShouldKeepModuleEmbed() {
		$this->assertXPath('//ul[@class="articles"]//a[contains(@href, "/embed/cms/articleview/id/3")]', $this->_response->getBody());
	}


	/** @test */
	function formRechercheShouldContainsUrlEmbed() {
		$this->assertXPath('//form[contains(@action, "embed/recherche/lancer")]');
	}
}




class IndexControllerTelephoneWithNoProfilTelephoneTest extends Zend_Test_PHPUnit_ControllerTestCase {
	public $bootstrap = 'bootstrap_frontcontroller.php';

 	public function setUp() {
 		$_SERVER['HTTP_USER_AGENT'] = 'iphone';
 		parent::setUp();


		$cfg_accueil =
			array('modules' => array('1' => array('division' => '1',
																						'type_module' => 'LOGIN',
																						'preferences' => array()),

															 '2' => array('division' => '1',
																						'type_module' => 'NEWS',
																						'preferences' => array('titre' => 'Concerts',
																																	 'rss_avis' => 0)))); 

		Class_Profil::setCurrentProfil(
																	 Class_Profil::getLoader()
																	 ->newInstanceWithId(1)
																	 ->setBrowser('opac')
																	 ->setTitreSite('portail')
																	 ->setCfgAccueil($cfg_accueil));
		$_SESSION['id_profil'] = 1;

		Storm_Test_ObjectWrapper::onLoaderOfModel("Class_Profil")
							->whenCalled('findFirstBy')
							->with(array('BROWSER' => 'telephone'))
							->answers(null);
		$this->dispatch('/');
	}


	/**
	 * @test
	 */
	public function moduleShouldBeOpac() {
		$this->assertModule('opac');
	}
}
?>