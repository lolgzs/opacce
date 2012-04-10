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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';
require_once 'Class/AvisNotice.php';

class CritiquesAvisEmptyTestCase extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$params = array('type_module' => 'CRITIQUES',
										'division' => 2,
										'preferences' => array());

		$this->avis_loader = $this->getMock('MockLoader', array('getAvisFromPreferences'));
		Storm_Model_Abstract::setLoaderFor('Class_AvisNotice', $this->avis_loader);
		$this->avis_loader
			->expects($this->once())
			->method('getAvisFromPreferences')
			->will($this->returnValue(array()));

		$helper = new ZendAfi_View_Helper_Accueil_Critiques(2, $params);
		$this->html = $helper->getBoite();
	}

	public function testAucuneCritiquesPresent() {
		$this->assertQueryContentContains($this->html, 'p', utf8_encode('Aucune critique récente'));
	}

	public function testBoiteDivisionMilieu() {
		$this->assertXPath($this->html, "//div[@class='boiteMilieu']");
	}
}



abstract class CritiquesAvisTestCase extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		$lolo = new Class_Users();
		$lolo
			->setId(91)
			->setPseudo('Lolo');

		$millenium = new Class_Notice();
		$millenium
			->setId(9867)
			->setTitrePrincipal('Millenium (Stieg Larsson)')
			->setUrlVignette('http://amazon.com/vignette.png');

		$avis_millenium = new Class_AvisNotice();
		$avis_millenium
			->setId(23)
			->setEntete("J'adore")
			->setAvis("Suspense intense")
			->setNote(5)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($lolo)
			->setAbonOuBib(0)
			->setStatut(1)
			->setNotices(array($millenium));

		$avis_orphan = new Class_AvisNotice();
		$avis_orphan
			->setId(34)
			->setEntete("J'ai oublié")
			->setAvis("Ce que c'était")
			->setNote(2)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($lolo)
			->setAbonOuBib(0)
			->setStatut(1)
			->setNotices(array());


		$potter = new Class_Notice();
		$potter
			->setId(687)
			->setTitrePrincipal('Potter')
			->setUrlVignette('NO');

		$avis_potter = new Class_AvisNotice();
		$avis_potter
			->setId(87)
			->setEntete("Le sorcier")
			->setAvis("A du charme")
			->setNote(4)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($lolo)
			->setAbonOuBib(0)
			->setStatut(1)
			->setNotices(array($potter));


		$this->avis_loader = $this->getMock('MockLoader', array('getAvisFromPreferences', 'delete'));
		Storm_Model_Abstract::setLoaderFor('Class_AvisNotice', $this->avis_loader);

		$this->avis_loader
			->expects($this->once())
			->method('getAvisFromPreferences')
			->will($this->returnValue(array($avis_millenium, $avis_orphan, $avis_potter)));
	}
}


class CritiquesWithVignettesTest extends CritiquesAvisTestCase {	
	public function setUp() {
		parent::setUp();

		$params = array('type_module' => 'CRITIQUES',
										'division' => 1,
										'preferences' =>  array('rss_avis' => true,
																						'only_img' => 1,
																						'display_order' => 'Random',
																						'titre' => 'Livres préférés',
																						'nb_aff_avis' => '3',
																						'nb_words' => 20,
																						'boite' => 'boite_de_la_division_droite'));

		
		$profil = new Class_Profil();
		$profil->setId(18);
		Class_Profil::setCurrentProfil($profil);

		$helper = new ZendAfi_View_Helper_Accueil_Critiques(2, $params);
		$this->html = $helper->getBoite();
	}

	public function testMilleniumIsHere() {
		$this->assertQueryContentContains($this->html, 'div.critique h2', 'Millenium');
	}

	public function testPotterIsNotShown() {
		$this->assertNotQueryContentContains($this->html, 'div.critique h2', 'Potter');
	}

	public function testOrphanAvisIsNotShown() {
		$this->assertNotQueryContentContains($this->html, 'a', utf8_encode("J'ai oublié"));
	}

	public function testTitreIsLivresPreferes() {
		$this->assertQueryContentContains($this->html, 'a', utf8_encode('Livres préférés'));
	}

	public function testRSSLinkPresents() {
		$this->assertXPath($this->html, "//a[contains(@href, 'rss/critiques?id_module=2&id_profil=18')]");
	}

	public function testBoiteDivisionDroite() {
		$this->assertXPath($this->html, "//div[@class='boiteDroite']");
	}
}



class CritiquesWithEmptyVignettesAllowedTest extends CritiquesAvisTestCase {	
	public function setUp() {
		parent::setUp();

		$params = array('type_module' => 'CRITIQUES',
										'division' => 1,
										'preferences' =>  array('rss_avis' => false,
																						'only_img' => 0,
																						'display_order' => 'Random',
																						'titre' => 'Livres préférés',
																						'nb_aff_avis' => '3',
																						'nb_words' => 20,
																						'boite' => null));


		$helper = new ZendAfi_View_Helper_Accueil_Critiques(2, $params);
		$this->html = $helper->getBoite();
	}

	public function testMilleniumIsHere() {
		$this->assertQueryContentContains($this->html, 'div.critique h2', 'Millenium');
	}

	public function testPotterIsHere() {
		$this->assertQueryContentContains($this->html, 'div.critique h2', 'Potter');
	}

	public function testOrphanAvisIsNotShown() {
		$this->assertNotQueryContentContains($this->html, 'a', utf8_encode("J'ai oublié"));
	}

	public function testRSSLinkNotPresents() {
		$this->assertNotXPath($this->html, "//div[@class='rss']/a[contains(@href, 'rss/critiques?id_module=2&id_profil=2')]");
	}

	public function testBoiteDivisionGauche() {
		$this->assertXPath($this->html, "//div[@class='boiteGauche']");
	}
}

?>