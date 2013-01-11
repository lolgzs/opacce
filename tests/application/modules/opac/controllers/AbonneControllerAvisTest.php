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

abstract class AbonneFlorenceIsLoggedControllerTestCase extends AbstractControllerTestCase {
	protected function _initProfilHook($profil) {
		$profil
			->setBrowser('opac')
			->setCfgAccueil('a:2:{s:7:"modules";a:0:{}s:7:"options";a:0:{}}');
	}

	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = $this->florence->getRoleLevel();
		$account->ID_USER = $this->florence->getId();
		$account->PSEUDO = "Florence";
		$this->account = $account;
	}

	public function setUp() {
		$this->florence = Class_Users::getLoader()->newInstanceWithId(123456)
			->setPseudo('FloFlo')
			->setRoleLevel(3)
			->setRole('abonne_sigb')
			->setLogin('florence')
			->setPassword('caramel')
			->setIdSite(1)
			->setIdabon('00123')
			;

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('save')
			->answers(true);

		parent::setUp();
	}
}



abstract class AbonneControllerAvisTestCase extends AbonneFlorenceIsLoggedControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->avis_loader = $this->_generateLoaderFor('Class_AvisNotice',
																									 array('findAllBy', 'save'));

		$this->notice_loader = $this->_generateLoaderFor('Class_Notice',
																										 array('find'));
		$this->potter = new Class_Notice();
		$this->potter
			->setClefOeuvre('POTTER')
			->setIdNotice(53);
		$this->notice_loader
			->expects($this->any())
			->method('find')
			->with(53)
			->will($this->returnValue($this->potter));

		parent::setUp();
	}
}



class AbonneControllerNoticeWithoutAvisTest extends AbonneControllerAvisTestCase {
	public function setUp() {
		parent::setUp();

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'POTTER',
									 'id_user' => 123456))
			->will($this->returnValue(array()));


		$this->dispatch('/opac/abonne/avis/id_notice/53');
	}

	public function testAvisFormRendered() {
		$this->assertController('abonne');
		$this->assertAction('avis');
	}

	public function testSignatureIsFloFlo() {
		$this->assertXPath("//input[@name='avisSignature'][@value='FloFlo']");
	}

	public function testFormActionIsAvis() {
		$this->assertXPath("//form[@action='/abonne/avis/id_notice/53']");
	}

}



class AbonneControllerInvalidNoticeAvisSaveTest extends  AbonneControllerAvisTestCase {
	public function setUp() {
		parent::setUp();

		$this->avis_min_saisie = new Class_AdminVar();
		$this->avis_min_saisie
			->setId('AVIS_MIN_SAISIE')
			->setValeur(10);

		$this->avis_max_saisie = new Class_AdminVar();
		$this->avis_max_saisie
			->setId('AVIS_MAX_SAISIE')
			->setValeur(1200);

		Class_AdminVar::getLoader()
			->cacheInstance($this->avis_min_saisie)
			->cacheInstance($this->avis_max_saisie);

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'POTTER',
									 'id_user' => 123456))
			->will($this->returnValue(array()));
	}


	public function testAvisTooShort() {
		$data = array('avisEntete' => 'Sorcellerie',
									'avisTexte' => 'On adore',
									'avisNote' => 5,
									'avisSignature' => 'FloCouv');

		$this->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/opac/abonne/avis/id_notice/53');

		$this->assertController('abonne');
		$this->assertAction('avis');
		$this->assertQueryContentContains('p.error',
																			"L'avis doit avoir une longueur comprise entre 10 et 1200 caractères");
	}

	public function testEmptyEntete() {
		$data = array('avisEntete' => '',
									'avisTexte' => 'On adore la magie',
									'avisNote' => 5,
									'avisSignature' => '');

		$this->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/opac/abonne/avis/id_notice/53');

		$this->assertController('abonne');
		$this->assertAction('avis');
		$this->assertQueryContentContains('p.error',
																			"Vous devez saisir un titre");
	}
}



class AbonneControllerNoticeAvisSaveTest extends  AbonneControllerAvisTestCase {
	public function testSaveNewAvis() {
		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'POTTER',
									 'id_user' => 123456))
			->will($this->returnValue(array()));

		$expected_avis = new Class_AvisNotice();
		$expected_avis
			->setEntete('Sorcellerie')
			->setAvis('On adore la magie')
			->setNote(5)
			->setClefOeuvre('POTTER')
			->setUser($this->florence)
			->setAbonOuBib(1)
			->setStatut(0);

		$this->postAndAssertAvisIsSaved($expected_avis);
	}


	public function postAndAssertAvisIsSaved($expected_avis) {
		$this->avis_loader
			->expects($this->once())
			->method('save')
			->with($this->equalTo($expected_avis));

		$data = array('avisEntete' => 'Sorcellerie',
									'avisTexte' => 'On adore la magie',
									'avisNote' => 5,
									'avisSignature' => 'FloCouv');

		$this->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/opac/abonne/avis/id_notice/53');

		$this->assertEquals(
						$this->florence,
						Class_Users::getLoader()->getFirstAttributeForLastCallOn('save'));


		$this->assertEquals('FloCouv', $this->florence->getPseudo());
	}


	public function testSaveExistingAvis() {
		$avis = new Class_AvisNotice();
		$avis
			->setId(12)
			->setAvis('Super génial')
			->setEntete('Le sorcier super mimi')
			->setClefOeuvre('POTTER')
			->setNote(4)
			->setUser($this->florence)
			->setStatut(1);

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'POTTER',
									 'id_user' => 123456))
			->will($this->returnValue(array($avis)));

		$expected_avis = new Class_AvisNotice();
		$expected_avis
			->setId(12)
			->setEntete('Sorcellerie')
			->setAvis('On adore la magie')
			->setNote(5)
			->setClefOeuvre('POTTER')
			->setUser($this->florence)
			->setAbonOuBib(1)
			->setStatut(0);

		$this->postAndAssertAvisIsSaved($expected_avis);
	}
}


class AbonneControllerNoticeWithAvisTest extends AbonneControllerAvisTestCase {
	public function setUp() {
		parent::setUp();

		$avis = new Class_AvisNotice();
		$avis
			->setId(12)
			->setAvis('Super génial')
			->setEntete('Le sorcier super mimi')
			->setNote(4);

		$this->avis_loader
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_oeuvre' => 'POTTER',
									 'id_user' => 123456))
			->will($this->returnValue(array($avis)));

		$this->dispatch('/opac/abonne/avis/id_notice/53');
	}

	public function testAvisFormRendered() {
		$this->assertController('abonne');
		$this->assertAction('avis');
	}


	public function testAvisIsFilled() {
		$this->assertQueryContentContains('form textarea',
																			'Super génial');
	}

	public function testSignatureIsFloFlo() {
		$this->assertXPath("//input[@name='avisSignature'][@value='FloFlo']");
	}

	public function testEntete() {
		$this->assertXPath("//input[@name='avisEntete'][@value='Le sorcier super mimi']");
	}

	public function testNote() {
		$this->assertXPath("//select[@name='avisNote']/option[@value='4'][@selected='1']");
	}

	public function testFormActionIsAvis() {
		$this->assertXPath("//form[@action='/abonne/avis/id_notice/53/id/12']");
	}
}


abstract class AvisControllersFixturesTestCase extends AbonneFlorenceIsLoggedControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->modo_avis = new Class_AdminVar();
		$this->modo_avis
			->setId('MODO_AVIS')
			->setValeur(0);

		$this->modo_avis_biblio = new Class_AdminVar();
		$this->modo_avis_biblio
			->setId('MODO_AVIS_BIBLIO')
			->setValeur(0);

		$this->readspeaker = new Class_AdminVar();
		$this->readspeaker
			->setId('ID_READ_SPEAKER')
			->setValeur('54QCJRHZ31IPBV7GW3DKBPUYYP579A14');


		Class_AdminVar::getLoader()
			->cacheInstance($this->modo_avis)
			->cacheInstance($this->modo_avis_biblio)
			->cacheInstance($this->readspeaker);


		$this->millenium = new Class_Notice();
		$this->millenium
			->setTitrePrincipal('Millenium')
			->setAuteurPrincipal('Stieg Larsson')
			->setUrlVignette('');

		$this->millenium_with_vignette = new Class_Notice();
		$this->millenium_with_vignette
			->setTitrePrincipal('Millenium')
			->setAuteurPrincipal('Stieg Larsson')
			->setUrlVignette('http://amazon.com/vignette_millenium.png');

		$this->avis_millenium = new Class_AvisNotice();
		$this->avis_millenium
			->setId(13)
			->setEntete("J'adore")
			->setAvis("Suspense intense")
			->setNote(5)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($this->florence)
			->setStatut(0)
			->setAbonOuBib(1)
			->setNotices(array($this->millenium,
												 $this->millenium_with_vignette));


		$this->potter = new Class_Notice();
		$this->potter
			->setTitrePrincipal('Potter et la chambre des secrets')
			->setAuteurPrincipal('')
			->setUrlVignette('http://amazon.com/vignette_potter.png');

		$this->avis_potter = new Class_AvisNotice();
		$this->avis_potter
			->setId(25)
			->setEntete("Prenant")
			->setAvis("Mais un peu trop naïf")
			->setNote(4)
			->setDateAvis('2010-10-12 10:00:00')
			->setUser($this->florence)
			->setStatut(1)
			->setAbonOuBib(1)
			->setNotices(array($this->potter));


		$this->florence->setAvis(array($this->avis_millenium, $this->avis_potter));
	}
}


class BlogControllerViewUnknownAuteurTest extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/blog/viewauteur/id/999999999');
	}


	public function testPageIsRendered() {
		$this->assertController('blog');
		$this->assertAction('viewauteur');
	}


	public function testDisplayAuteurIntrouvable() {
		$this->assertXPathContentContains('//h1', 'Auteur introuvable');
	}
}



class BlogControllerViewActionsTest extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/blog/viewauteur/id/123456');
	}


	public function testPageIsRendered() {
		$this->assertController('blog');
		$this->assertAction('viewauteur');
	}

	public function testMilleniumIsHere() {
		$this->assertXPathContentContains("//div[@class='critique'][1]//h2", 'Millenium (Stieg Larsson)');
	}

	public function testPotterIsHere() {
		$this->assertXPathContentContains("//div[@class='critique'][2]//h2", 'Potter et la chambre des secrets');
	}

	public function testDeleteMilleniumButtonPresent() {
		$this->assertXPath("//a[@href='/blog/delavisnotice/id/13']");
	}

	public function testDeletePotterButtonPresent() {
		$this->assertXPath("//a[@href='/blog/delavisnotice/id/25']");
	}

	/** @test */
	function rssLinkShouldBePresent() {
		$this->assertXPath("//div[@class='rss']/a[@href='/rss/user/id/123456']");
	}
}



class RssControllerViewAvisUserTest extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AvisNotice')
			->whenCalled('findAllBy')
			->with(array(
									 'id_user' => 123456,
									 'order' => 'date_avis desc',
									 'limit' =>  10))
			->answers($this->florence->getAvis())
			->getWrapper()
			->beStrict();

		$this->dispatch('/opac/rss/user/id/123456');
	}


	/** @test */
	function channelTitleShouldBeAvisDeFloFlo() {
		$this->assertXpathContentContains('//channel/title', 'Avis de FloFlo');
	}


	/** @test */
	function linkShouldBeLocalhostAfiOpac3BlogRssUser123456() {
		$this->assertXPathContentContains('//channel/link', 
																			'http://localhost' . BASE_URL . '/blog/viewauteur/id/123456', 
																			$this->_response->getBody());
	}


	/** @test */
	function channelItemCountShouldBeTwo() {
		$this->assertXpathCount('//channel/item', 2);
	}


	/** @test */
	function firstItemTitleShouldBeJAdore() {
		$this->assertXPathContentContains('//channel/item/title', "J'adore");
	}


	/** @test */
	function firstItemLinkShouldBeBlogViewAvisId13() {
		$this->assertXPathContentContains('//channel/item/link', 
																			"http://localhost" . BASE_URL . "/opac/blog/viewavis/id/13");
	}


	/** @test */
	function firstItemPubUpdateShouldBeTue12Oct() {
		$this->assertXPathContentContains('//channel/item/pubDate', "Tue, 12 Oct 2010");
	}


	/** @test */
	function firstItemNoteCritiqueShouldBeImgStars4Gif() {
		$this->assertTrue(false !== strpos($this->_response->getBody(),
																			 'src="http://localhost' . BASE_URL . '/public/admin/images/stars/stars-4.gif"'));
	}
}



class BlogControllerDeleteAvisTest extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();

		$avis_loader = $this->_generateLoaderFor('Class_AvisNotice', array('find', 'delete'));
		$avis_loader
			->expects($this->once())
			->method('find')
			->with(25)
			->will($this->returnValue($this->avis_potter));

		$avis_loader
			->expects($this->once())
			->method('delete')
			->with($this->avis_potter);

		$this->dispatch('/blog/delavisnotice/id/25');
	}

	public function testRedirectToViewAuteurPage() {
		$this->assertRedirectTo('/blog/viewauteur');
	}
}


class BlogControllerLastCritiquesTest extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();

		$this->_generateLoaderFor('Class_AvisNotice', array('findAllBy'))
			->expects($this->atLeastOnce())
			->method('findAllBy')
			->with(array('order' => 'date_avis desc', 'limit' => '10'))
			->will($this->returnValue(array($this->avis_millenium, $this->avis_potter)));

		$this->dispatch('/opac/blog/lastcritique/nb/10');
	}

	public function testPageIsRendered() {
		$this->assertController('blog');
		$this->assertAction('lastcritique');
	}

	public function testMilleniumIsHere() {
		$this->assertXPathContentContains("//div[@class='critique'][1]//h2", 'Millenium (Stieg Larsson)');
	}

	public function testPotterIsBeforeMillenium() {
		$this->assertXPathContentContains("//div[@class='critique'][2]//h2", 'Potter et la chambre des secrets');
	}
}


abstract class ModuleSelectionCritiquesTestCase extends AvisControllersFixturesTestCase {
	public function setUp() {
		parent::setUp();

		$preferences = array('modules' => array(3 => array('division' => 2,
																											 'type_module' => 'CRITIQUES',
																											 'preferences' => array('titre' => 'Coups de coeur'))));
		$profil = Class_Profil::getLoader()
			->find(2)
			->setCfgAccueil($preferences);

		$this->_generateLoaderFor('Class_AvisNotice', array('getAvisFromPreferences'))
			->expects($this->once())
			->method('getAvisFromPreferences')
			->will($this->returnValue(array($this->avis_millenium, $this->avis_potter)));
	}
}




class BlogControllerViewCritiquesTest extends ModuleSelectionCritiquesTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/blog/viewcritiques/id_profil/2/id_module/3');
	}

	public function testMilleniumIsHere() {
		$this->assertQueryContentContains('h2', 'Millenium');
	}

	public function testPotterIsHere() {
		$this->assertQueryContentContains('h2', 'Potter');
	}

	/** @test */
	public function titleShouldBeCoupsDeCoeur() {
		$this->assertXPathContentContains('//h1', 'Coups de coeur');
	}
}




class BlogControllerViewCritiquesWithoutModuleTest extends ModuleSelectionCritiquesTestCase {
	/** @test */
	public function titleShouldBeDernieresCritiques() {
		$this->dispatch('/opac/blog/viewcritiques');
		$this->assertXPathContentContains('//h1', 'Dernières critiques');
	}
}




class RssControllerLastCritiquesTest extends ModuleSelectionCritiquesTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/opac/rss/critiques/id_profil/2/id_module/3');
		$this->body = $this->getResponse()->getBody();

	}

	public function testMilleniumIsHere() {
		$this->assertNotEquals(false, strpos($this->body, 'Millenium'));
	}

	public function testPotterIsHere() {
		$this->assertNotEquals(false, strpos($this->body, 'Potter'));
	}
}



class BlogControllerViewReadAvisTest extends  AbonneFlorenceIsLoggedControllerTestCase {
	public function setUp() {
		parent::setUp();

		$millenium = new Class_Notice();
		$millenium
			->setTitrePrincipal('Millenium (Stieg Larsson)')
			->setUrlVignette('http://amazon.com/vignette.png');

		$avis_millenium = new Class_AvisNotice();
		$avis_millenium
			->setId(18)
			->setEntete("J'adore")
			->setAvis("Suspense intense")
			->setNote(5)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($this->florence)
			->setAbonOuBib(1)
			->setStatut(0)
			->setNotices(array($millenium));

		$avis_loader = $this->_generateLoaderFor('Class_AvisNotice',
																						 array('find'));
		$avis_loader
			->expects($this->once())
			->method('find')
			->with(18)
			->will($this->returnValue($avis_millenium));
	}


	public function testViewAvisIsRendered() {
		$this->dispatch('/opac/blog/viewavis/id/18');

		$this->assertController('blog');
		$this->assertAction('viewavis');

		$this->assertQueryContentContains('div.critique h2', 'Millenium (Stieg Larsson)');
	}


	public function testReadAvisIsRendered() {
		$this->dispatch('/opac/blog/readavis/id/18');

		$this->assertController('blog');
		$this->assertAction('readavis');

		$this->assertQueryContentContains('p', 'Millenium (Stieg Larsson)');
		$this->assertQueryContentContains('p', '18 mars 2010');
	}
}

?>