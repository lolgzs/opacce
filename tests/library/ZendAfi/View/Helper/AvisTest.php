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
require_once 'ViewHelperTestCase.php';
require_once 'Class/AvisNotice.php';
require_once 'ZendAfi/View/Helper/Avis.php';


class ViewHelperAvisTestWithAvisNotice extends ViewHelperTestCase {
	public function setUp() {
		$lolo = new Class_Users();
		$lolo
			->setId(91)
			->setPseudo('Lolo');

		$millenium = new Class_Notice();
		$millenium
			->setId(1128)
			->setTitrePrincipal('Millenium (Stieg Larsson)')
			->setUrlVignette('');

		$millenium_with_vignette = new Class_Notice();
		$millenium_with_vignette
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
			->setNotices(array($millenium,
												 $millenium_with_vignette));

		$this->readspeaker = new Class_AdminVar();
		$this->readspeaker
			->setId('ID_READ_SPEAKER')
			->setValeur('54QCJRHZ31IPBV7GW3DKBPUYYP579A14');
		Class_AdminVar::getLoader()->cacheInstance($this->readspeaker);

		$helper = new ZendAfi_View_Helper_Avis();
		$this->html = $helper->avis($avis_millenium);
	}


	public function testEnteteIsJadore() {
		$this->assertXPathContentContains($this->html, 
																			"//div[@class='contenu_critique']//a[contains(@href,'/blog/viewavis/id/23')]", 
																			"J'adore");
	}


	public function testNoteIsFive() {
		$this->assertXPath($this->html,
											 "//div[@class='contenu_critique']//img[@class='note_critique'][@alt='note: 5']");
	}

	public function testNoteImgIsStarsFive() {
		$this->assertXPath($this->html,
											 "//img[@class='note_critique'][contains(@src, 'stars-5.gif')]");
	}

	public function testAvisIsSuspenseIntense() {
		$this->assertQueryContentContains($this->html, 'p', "Suspense intense");
	}

	public function testDateIs18June() {
		$this->assertQueryContentContains($this->html, 'span.auteur_critique', '- 18 mars 2010');
	}

	public function testAuteurIsLolo() {
		$this->assertXPathContentContains($this->html, 
																			"//span[@class='auteur_critique']//a[contains(@href, '/blog/viewauteur/id/91')]", 
																			'Lolo');
	}

	public function testTitreNoticeIsMilleniumStiegLarsson() {
		$this->assertQueryContentContains($this->html, 'div.critique h2', 'Millenium (Stieg Larsson)');
	}

	public function testVignetteNotice() {
		$this->assertXPath($this->html,
											 "//div[@class='vignette_notice']//img[@src='http://amazon.com/vignette.png']");
	}

	public function testVignetteLinkToNotice() {
		$this->assertXPath($this->html,
											 "//div[@class='vignette_notice']/a[contains(@href,'/recherche/viewnotice/id/9867')]");
		
	}

	public function testVignetteLinkVoirLaNotice() {
		$this->assertXPathContentContains($this->html,
																			"//div[@class='vignette_notice']/a[contains(@href,'/recherche/viewnotice/id/9867')]",
																			'Voir la notice');
	}

	public function testReadSpeakerLink() {
		$this->assertXPath($this->html,
											 "//a[contains(@href, 'webreader.php')][contains(@onclick, 'blog/readavis?id=23')]".
											 "/img[contains(@src, 'read_speaker_listen.gif')]");
	}

	public function testNoModerationTag() {
		$this->assertNotXPath($this->html, "//div[@class='moderation']");
	}
}


class ViewHelperAvisTestWithoutAvisNoticeAndModeration extends ViewHelperTestCase {
	public function setUp() {
		$tintin = new Class_Users();
		$tintin
			->setId(26)
			->setPseudo('Tintin');

		$orphan_avis = new Class_AvisNotice();
		$orphan_avis
			->setId(67)
			->setEntete("Bof")
			->setAvis("Pas\nterrible")
			->setNote(2)
			->setDateAvis('2010-01-02 10:00:00')
			->setUser($tintin)
			->setStatut(0)
			->setAbonOuBib(0)
			->setNotices(array());


		$this->modo_avis = new Class_AdminVar();
		$this->modo_avis
			->setId('MODO_AVIS')
			->setValeur(1);
		Class_AdminVar::getLoader()->cacheInstance($this->modo_avis);

		$this->readspeaker = new Class_AdminVar();
		$this->readspeaker
			->setId('ID_READ_SPEAKER')
			->setValeur('54QCJRHZ31IPBV7GW3DKBPUYYP579A14');
		Class_AdminVar::getLoader()->cacheInstance($this->readspeaker);


		$helper = new ZendAfi_View_Helper_Avis();
		$this->html = $helper->avis($orphan_avis);
	}


	public function testEnteteIsBof() {
		$this->assertXPathContentContains($this->html, 
																			"//div[@class='contenu_critique']//a[contains(@href,'/blog/viewavis/id/67')]", 
																			"Bof");
	}


	public function testNoteIsTwo() {
		$this->assertXPath($this->html,
											 "//div[@class='contenu_critique']//img[@class='note_critique'][@alt='note: 2']");
	}

	public function testNoteImgIsStarsTwo() {
		$this->assertXPath($this->html,
											 "//img[@class='note_critique'][contains(@src,'stars-2.gif')]");
											 
	}

	public function testAvisIsPasTerrible() {
		$this->assertTrue(strpos($this->html, "Pas<br />\nterrible") !== false);
	}

	public function testDateIsSecondJanuary() {
		$this->assertQueryContentContains($this->html, 'span.auteur_critique', '- 2 janvier 2010');
	}

	public function testAuteurIsTintin() {
		$this->assertXPathContentContains($this->html,
																			"//span[@class='auteur_critique']//a[contains(@href, '/blog/viewauteur/id/26')]", 
																			'Tintin');
	}

	public function testTitreNoticeEmpty() {
		$this->assertQueryContentContains($this->html, 'div.critique h2', 
																			utf8_encode('Oeuvre non trouvée'));
	}

	public function testVignetteNoticeIsEmpty() {
		$this->assertXPath($this->html,
											 "//div[@class='vignette_notice']//img[@src='".URL_ADMIN_IMG."supports/vignette_vide.gif']");
	}

	public function testReadSpeakerLink() {
		$this->assertXPath($this->html,
											 "//a[contains(@href, 'webreader.php')][contains(@onclick, 'blog/readavis?id=67')]".
											 "/img[contains(@src, 'read_speaker_listen.gif')]");
	}

	public function testModerationTag() {
		$this->assertXPath($this->html, "//div[@class='moderation']");
	}
}


class ViewHelperAvisTestHtmlForCritiquesModule extends ViewHelperTestCase {
	public function setUp() {
		$tintin = new Class_Users();
		$tintin
			->setId(26)
			->setPseudo('Tintin');

		$millenium = new Class_Notice();
		$millenium
			->setId(9867)
			->setTitrePrincipal('Millenium (Stieg Larsson)')
			->setUrlVignette('http://amazon.com/vignette.png');

		$avis_millenium = new Class_AvisNotice();
		$avis_millenium
			->setId(23)
			->setEntete("J'adore")
			->setAvis("Suspense intense très 
                 intéressant longue critique")
			->setNote(5)
			->setDateAvis('2010-03-18 13:00:00')
			->setUser($tintin)
			->setStatut(0)
			->setAbonOuBib(0)
			->setNotices(array($millenium));

		$helper = new ZendAfi_View_Helper_Avis();
		$this->html = $helper
			->setVignetteLinkToAvis()
			->setLimitNbWord(2)
			->avis($avis_millenium);
	}

	public function testVignetteLinkToAvis() {
		$this->assertXPath($this->html,
											 "//div[@class='vignette_notice']/a[contains(@href,'/blog/viewavis/id/23')]");
	}

	public function testAvisCut() {
		$this->assertQueryContentContains($this->html, 'p', "Suspense intense [...]");
	}

	public function testLinkLireLaSuite() {
		$this->assertXPathContentContains($this->html,
																			"//div[@class='lire_la_suite']/a[contains(@href,'/blog/viewavis/id/23')]",
																			"Lire la suite");
	}
}


class ViewHelperAvisAmazonTestContenuAvisHtml extends ViewHelperTestCase {
	public function setUp() {
		$avis_millenium = new Class_AvisNotice();
		$avis_millenium
			->setEntete("J'adore")
			->setAvis("Suspense intense très 
                 intéressant longue critique")
			->setNote(5)
			->setDateAvis('2010-03-18 13:00:00')
			->setAbonOuBib(0)
			->setStatut(0)
			->setUser(null);

		$helper = new ZendAfi_View_Helper_Avis();
		$this->html = $helper->contenu_avis($avis_millenium);
	}

	public function testNoReadspeaker() {
		$this->assertNotXpath($this->html,
													"//a[contains(@href, 'webreader.php')]");
	}

	public function testNoAuteur() {
		$this->assertNotQueryContentContains($this->html,"span.auteur_critique", 'par');
	}

	public function testAvisLinkIsSharp() {
		$this->assertQueryContentContains($this->html, 
																			"div.contenu_critique a[href='#']", 
																			"J'adore");
	}
}



?>