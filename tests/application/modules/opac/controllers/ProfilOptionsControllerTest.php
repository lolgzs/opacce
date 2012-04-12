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

abstract class ProfilOptionsControllerWithProfilAdulteTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 0;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}

	public function setUp() {
		parent::setUp();

		$cfg_menus = array(
											 'H' => array( 
																		"libelle" => "Menu horizontal",
																		"picto" => "vide.gif",
																		"menus" => array(array('type_menu' => 'MENU',
																													 'libelle' => 'Pratique',
																													 'picto' => 'bookmark.png',
																													 'preferences' => array()),
																										 array('type_menu' => 'URL',
																													 'libelle' => 'Google',
																													 'picto' => 'vide.gif',
																													 'preferences' => array('url' => 'http://www.google.com',
																																									'target' => 0)))),
											 'V' => array(
																		"libelle" => "Menu vertical",
																		"picto" => "vide.gif"));



		$this->profil_adulte = Class_Profil::getCurrentProfil()
			->setBrowser('opac')
			->setLibelle('Profil Adulte')
			->setHauteurBanniere(150)
			->setCouleurTexteBandeau('#F2C')
			->setCouleurLienBandeau('#234')
			->setMenuHautOn(true)
			->setCfgMenus($cfg_menus)
			->setCommentaire('Super bib')
			->setRefTags("bib,Adulte");
	}
}


class ProfilOptionsControllerTwitterLinkWithProfilAdulteTest extends ProfilOptionsControllerWithProfilAdulteTestCase {
	protected $_mock_web_client;

	public function setUp() {
		parent::setUp();
		$this->_mock_web_client = Storm_Test_ObjectWrapper::on(new Class_WebService_SimpleWebClient());
		Class_WebService_ReseauxSociaux::setDefaultWebClient($this->_mock_web_client);

		$this->_mock_web_client
			->whenCalled('open_url')
			->with(sprintf('http://is.gd/api.php?longurl=%s', 
										 urlencode('http://localhost' . BASE_URL . '/index/index/id_profil/2')))
			->answers('http://is.gd/PkdNgD')
			->beStrict();
	}


	public function tearDown() {
		Class_WebService_ReseauxSociaux::resetDefaultWebClient();
		parent::tearDown();
	}


	/** @test */
	public function twitterLinkShouldReturnJavascriptForTweet() {
		$this->dispatch('/opac/index/share/on/twitter/titre/Profil+Adulte');
		$this->assertContains(sprintf("window.open('http://twitter.com/home?status=%s','_blank','location=yes, width=800, height=410')",
																	urlencode('Profil Adulte http://is.gd/PkdNgD')),
													$this->_response->getBody());
	}


	/** @test */
	public function facebookLinkShouldReturnJavascriptForTweet() {
		$this->dispatch('/opac/index/share/on/facebook/titre/Profil+Adulte');
		$this->assertContains(sprintf("window.open('http://www.facebook.com/share.php?u=%s','_blank','location=yes, width=800, height=410')",
																	urlencode('Profil Adulte http://is.gd/PkdNgD')),
													$this->_response->getBody());
	}
}


class ProfilOptionsControllerProfilAdulteTest extends ProfilOptionsControllerWithProfilAdulteTestCase {
	/** @test */
	public function titleShouldBeProfilAdulteSeConnecterInAuth() {
		$this->dispatch('/opac/auth/login');
		$this->assertQueryContentContains('head title', 'Profil Adulte - Se connecter');
	}


	/** @test */
	public function getHauteurBanniereShouldReturn150() {
		$this->assertEquals(150, $this->profil_adulte->getHauteurBanniere());
	}


	/** @test */
	public function cfgSiteShouldIncludeHauteurBanniere() {
		$cfg_site = $this->profil_adulte->getCfgSiteAsArray();
		$this->assertEquals(150, $cfg_site['hauteur_banniere']);
	}


	/** @test */
	public function PATH_JAVAShouldExists() {
		$this->assertTrue(file_exists(PATH_JAVA));
	}


	/** @test */
	public function PATH_FLASHShouldExists() {
		$this->assertTrue(file_exists(PATH_FLASH));
	}


	/** @test */
	function withSiteDownShouldDisplaySiteBloque() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('SITE_OK')
			->setValeur('0');

		$this->dispatch('/opac/');
		$this->assertXPathContentContains('//div', 'accès au site est momentanément bloqué');
	}
}


class ProfilOptionsControllerViewProfilAdulteTest extends ProfilOptionsControllerWithProfilAdulteTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/');		
	}


	/** @test */
	public function titleShouldBeProfilAdulteInHomePage() {
		$this->assertQueryContentContains('head title', 'Profil Adulte');
	}

	/** @test */
	public function metaDescriptionShouldBeSuperBibInHomePage() {
		$this->assertXPath("//meta[@name='description'][contains(@content,'Super bib')]");
	}


	/** @test */
	public function metaKeywordsShouldBeBibAndAdulteInHomePage() {
		$this->assertXPath("//meta[@name='keywords'][@content='bib,Adulte']");
	}


	/** @test */
	public function profilCssShouldBeIncludedInHeader() {
		$this->assertXPath("//style[@id='profil_stylesheet']");
	}


	/** @test */
	public function hauteurBanniere150ShouldBeInProfilCss() {
		$this->assertXPathContentContains("//style[@id='profil_stylesheet']",
																			"div#banniere, div#header{height:150px}");
	}


	/** @test */
	public function headerTextColorShouldBeSharpF2C() {
		$this->assertXPathContentContains("//style[@id='profil_stylesheet']",
																			"div#header * {color:#F2C}");
	}


	/** @test */
	public function headerLinkColorShouldBeSharp234() {
		$this->assertXPathContentContains("//style[@id='profil_stylesheet']",
																			"div#header a, div#header a:visited {color:#234}");
	}

	/** @test */
	public function menuHorizontalShouldIncludeExternalLinkToGoogle() {
		$this->assertXPathContentContains("//div[@id='menu_horizontal']//li//a[@href='http://www.google.com'][@target='_blank']", 'Google', $this->_response->getBody());
	}


	/** @test */
	function faviconShouldNotBeSet() {
		$this->assertNotXPath('//link[@rel="shortcut icon"]');
	}


	/** @test */
	public function logoGaucheShouldNotBeSet() {
		$this->assertNotXPath("//div[@class='logo_gauche']");
	}


	/** @test */
	public function logoDroiteShouldNotBeSet() {
		$this->assertNotXPath("//div[@class='logo_droite']");
	}


	/** @test */
	function cycleBanniereScriptsShouldNotBeIncluded() {
		$this->assertNotXPathContentContains('//script', '$("#banniere a").cycle');
	}
}



abstract class ProfilOptionsControllerProfilJeunesseWithPagesJeuxMusiqueTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 0;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}

	public function setUp() {
		parent::setUp();


		$cfg_accueil_jeunesse = 
			array('modules' => array('1' => array('division' => '4',
																						'type_module' => 'RECH_SIMPLE',
																						'preferences' => array('recherche_avancee' => "on")),

															 '2' => array('division' => '4',
																						'type_module' => 'LOGIN'),

															 '4' => array('division' => '1',
																						'type_module' => 'NEWS')), 
						'options' => 	array());


		$this->profil_jeunesse = Class_Profil::getCurrentProfil()
			->setBrowser('opac')
			->setTitreSite(null)
			->setLibelle('Profil Jeunesse')
			->setCfgAccueil($cfg_accueil_jeunesse)
			->setFavicon('afi-opac3/userfiles/favicon.ico')
			->setHeaderCss('afi-opac3/userfiles/jeunesse.css')
			->setLogoGaucheImg('/userfiles/mabib.png')
			->setLogoGaucheLink('http://mabib.fr')
			->setLogoDroiteImg('/userfiles/macommune.png')
			->setLogoDroiteLink('http://macommune.fr')
			->setHeaderImgCycle(true);

		$cfg_accueil_jeux = 
			array('modules' => array(
															 '4' => array('division' => '1',
																						'type_module' => 'CRITIQUES'),
															 '7' => array('division' => '1',
																						'type_module' => 'KIOSQUE'),
															 '8' => array('division' => '1',
																						'type_module' => 'RSS'),
															 '10' => array('division' => '2',
																						 'type_module' => 'SITO')),
						'options' => 	array());

		$this->page_jeux = Class_Profil::getLoader()
			->newInstanceWithId(12)
			->setParentId($this->profil_jeunesse->getId())
			->setLibelle('Jeux')
			->setCfgAccueil($cfg_accueil_jeux);


		$this->page_musique = Class_Profil::getLoader()
			->newInstanceWithId(23)
			->setParentId($this->profil_jeunesse->getId())
			->setLibelle('Musique');

		$_SERVER["REQUEST_URI"] = '/';
	}
}


class ProfilOptionsControllerProfilJeunesseAndJeuxTest extends ProfilOptionsControllerProfilJeunesseWithPagesJeuxMusiqueTestCase {
	/** @test */
	function boiteActionForModuleIdOneShouldDisplayBoiteRecherche() {
		$this->dispatch('/opac/index/embed_module/id_profil/2/id_module/1');
		$this->assertXPath('//div[@class="recherche_avancee"]//a[contains(@href, "avancee")]');
	}

	/** @test */
	public function titleShouldBeProfilJeunesseSeConnecterInAuth() {
		$this->dispatch('/opac/auth/login');
		$this->assertQueryContentContains('head title', 'Profil Jeunesse - Se connecter');
	}
}


class ProfilOptionsControllerProfilJeunesseViewPageJeuxTest extends ProfilOptionsControllerProfilJeunesseWithPagesJeuxMusiqueTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac?id_profil=12');		
	}


	/** @test */
	public function titleShouldBeJeuxForPageJeux() {
		$this->assertQueryContentContains('head title', 'Jeux');
	}


	/** @test */
	public function boiteLoginShouldBeVisibleInPageJeux() {
		$this->assertXPath("//div[@id='boite_login']");
	}


	/** @test */
	public function boiteNewsShouldNotBeVisibleInPageJeux() {
		$this->assertNotXPathContentContains("//a[contains(@href, 'articleviewselection')]",'Articles');
	}


	/** @test */
	public function boiteKiosqueShouldBeVisibleInPageJeux() {
		$this->assertXPath("//iframe[contains(@src, 'kiosque')]");
	}
}


class ProfilOptionsControllerViewProfilJeunesseAccueilTest extends ProfilOptionsControllerProfilJeunesseWithPagesJeuxMusiqueTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/');
	}

	/** @test */
	public function hauteurBanniereShouldBe100() {
		$this->assertEquals(100, $this->profil_jeunesse->getHauteurBanniere());
	}


	/** @test */
	function faviconShouldBeSet() {
		$this->assertXPath('//link[@rel="shortcut icon"][@href="afi-opac3/userfiles/favicon.ico"]');
	}


	/** @test */
	function headerCssJeunesseShouldBeIncluded() {
		$this->assertXPath('//link[@rel="stylesheet"][@type="text/css"][@href="afi-opac3/userfiles/jeunesse.css"]');
	}


	/** @test */
	function rechercheAvanceLinkShouldBeVisible() {
		$this->assertXPath('//div[@class="recherche_avancee"]//a[contains(@href, "avancee")]');
	}


	/** @test */
	public function titleShouldBeProfilJeunesseInHomePage() {
		$this->dispatch('/opac/');
		$this->assertQueryContentContains('head title', 'Profil Jeunesse');
	}

	
	/** @test */
	public function boiteLoginShouldBeVisibleInProfilJeunesse() {
		$this->assertXPath("//div[@id='boite_login']");
	}


	/** @test */
	public function boiteNewsShouldBeVisibleInProfilJeunesse() {
		$this->assertXPathContentContains("//a[contains(@href, 'articleviewselection')]",'Articles');
	}


	/** @test */
	public function logoGaucheShouldBeInBanniere() {
		$this->assertXPath("//div[@id='banniere']//div[@class='logo_gauche']//a[@href='http://mabib.fr']//img[@src='/userfiles/mabib.png']",
											 $this->_response->getBody());
	}


	/** @test */
	public function logoDroiteShouldBeInBanniere() {
		$this->assertXPath("//div[@id='banniere']//div[@class='logo_droite']//a[@href='http://macommune.fr']//img[@src='/userfiles/macommune.png']",
											 $this->_response->getBody());
	}


	/** @test */
	public function hauteurBanniereShouldBe100InProfilCss() {
		$this->assertXPath("//style[@id='profil_stylesheet']");
		$this->assertXPathContentContains("//style[@id='profil_stylesheet']",
																			"div#banniere, div#header{height:100px}");
	}


	/** @test */
	public function headerLinkColorShouldNotBePresent() {
		$this->assertNotXPathContentContains("//style[@id='profil_stylesheet']",
																				 "div#header a");
	}


	/** @test */
	public function headerColorShouldNotBePresent() {
		$this->assertNotXPathContentContains("//style[@id='profil_stylesheet']",
																				 "div#header *");
	}


	/** @test */
	function cycleBanniereScriptsShouldBeIncluded() {
		$this->assertXPathContentContains('//script', '$("#banniere a.home").cycle');
	}

	/** @test */
	public function banniereClassLinkShouldBeHome() {
		$this->assertXPath('//div[@id="banniere"]//a[@class="home"]');
	}
}


class UserRoleLevelThreeViewPrivateProfilTest extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 3;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}


	public function setUp() {
		parent::setUp();
		$this->private_profil = Class_Profil::getCurrentProfil()
			->setBrowser('opac')
			->setTitreSite(null)
			->setLibelle('Profil privé');
	}


	/** @test */
	public function shouldRenderLoginPageWhenProfilAccessLevelIsFour() {
			$this->private_profil->setAccessLevel(4);
			$this->dispatch('/opac/');
			$this->assertModule('admin');
			$this->assertController('auth');
			$this->assertAction('login');
	}

	/** @test */
	public function shouldRenderIndexPageWhenProfilAccessLevelIsThree() {
			$this->private_profil->setAccessLevel(3);
			$this->dispatch('/opac/');
			$this->assertController('index');
			$this->assertAction('index');
	}

}

	

?>