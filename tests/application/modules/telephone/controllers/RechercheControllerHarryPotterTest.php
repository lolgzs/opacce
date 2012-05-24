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

require_once 'TelephoneAbstractControllerTestCase.php';

abstract class Telephone_RechercheControllerHarryPotterTestCase extends TelephoneAbstractControllerTestCase {
	protected $_summary;

	public function setUp() {
		parent::setUp();

		$this->_summary = 'Apres la mort < tragique de Lily et James Potter, Harry est recueilli par sa tante Petunia, la soeur de Lily et son oncle Vernon. Son oncle et sa tante, possedant une haine feroce envers les parents d\'Harry, le maltraitent et laissent leur fils Dudley l\'humilier. Harry ne sait rien sur ses parents. On lui a toujours dit qu\'ils etaient morts dans un accident de voiture.';

		$potter = Class_Notice::getLoader()
			->newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setClefOeuvre('HARRYPOT')
			->setTitrePrincipal('Harry Potter à l\'ecole des sorciers')
			->setAuteurPrincipal('J.K. Rowling')
			->setDateMaj('2012-04-23')
			->setAnnee('2012')
			->setResume($this->_summary)
			->setMatieres(array('Potions', 'Etude des runes'))
			->setEditeur('Gallimard')
			->setCollation('')
			->setLangueCodes(array('fre', 'eng'))
			->setIsbn('978-2-07-054127-0')
			->setEan('')
			->setUrlVignette('http://amazon.fr/potter.jpg')
			->setUrlImage('http://amazon.fr/potter_grand.jpg')
			->beLivre();
	}
}




class Telephone_RechercheControllerHarryPotterViewNoticeTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/viewnotice/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToRechercheInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/simple")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function auteurShouldBeJKRowling() {
		$this->assertXPathContentContains('//h1', 'J.K. Rowling');
	}


	/** @test */
	public function pageShouldContainsEditeurGallimard() {
		$this->assertXPathContentContains('//div', 'Editeur(s) : Gallimard');
	}


	/** @test */
	public function pageShouldContainsAnnee2012() {
		$this->assertXPathContentContains('//div', 'Année : 2012');
	}


	/** @test */
	public function pageShouldContainsVignetteForHarryPotter() {
		$this->assertXPath('//a[contains(@href, "recherche/grandeimage")]//img[contains(@src, "potter.jpg")]');
	}


	/** @test */
	public function pageShouldContainsIconeSupport() {
		$this->assertXPath('//img[contains(@src, "famille_livre_small.png")]');
	}


	/** @test */
	public function pageShouldContainsLinkToNoticeDetaillee() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/detail/id/4")]', 'Notice détaillée');
	}


	/** @test */
	public function pageShouldContainsLinkToAvis() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/avis/id/4")]', 'Avis');
	}


	/** @test */
	public function pageShouldContainsLinkToExemplaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/exemplaires/id/4")]', 'Exemplaires');
	}


	/** @test */
	public function pageShouldContainsLinkToResumes() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/resume/id/4")]', 'Résumés, analyses');
	}


	/** @test */
	public function pageShouldContainsLinkToTags() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/tags/id/4")]', 'Tags');
	}


	/** @test */
	public function pageShouldContainsLinkToBiographies() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/biographie/id/4")]', 'Biographies');
	}


	/** @test */
	public function pageShouldContainsLinkToNoticesSimilaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/similaires/id/4")]', 'Notices similaires');
	}


	/** @test */
	public function pageShouldContainsLinkToRessourcesNumeriques() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/ressourcesnumeriques/id/4")]', 'Ressources numériques');
	}
}




class Telephone_RechercheControllerHarryPotterViewResumeTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/resume/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsResumeBib() {
		$this->assertXPathContentContains('//div', 'Apres la mort');
	}	
}




class Telephone_RechercheControllerHarryPotterTagsTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/tags/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsNuageTagsCss() {
		$this->assertXPath('//link[contains(@href, "nuage_tags")]',
											 $this->_response->getBody());
	}
}



class Telephone_RechercheControllerHarryPotterBiographieTest extends Telephone_RechercheControllerHarryPotterTestCase {
	/**
	 * @group longtest
	 * @group integration
	 * @test 
	 */
	public function dispatchBiographie() {
		$this->dispatch('/telephone/recherche/biographie/id/4', true);
		return $this->_response->getBody();
	}

	/** 
	 * @depends dispatchBiographie
	 * @test 
	 */
	public function pageShouldContainsUrlToViewNoticeInToolbar($response) {
		Storm_Test_XPath::newInstance()
			->assertXPath($response,
										'//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** 
	 * @depends dispatchBiographie
	 * @test 
	 */
	public function titleShouldBeHarryPotter($response) {
		Storm_Test_XPath::newInstance()
			->assertXPathContentContains($response,
																	 '//h1', 
																	 'Harry Potter à l\'ecole des sorciers');
	}


	/** 
	 * @depends dispatchBiographie
	 * @test 
	 */
	public function pageShouldContainsInspirationEtControverse($response) {
		Storm_Test_XPath::newInstance()
			->assertXPathContentContains($response,
																	 '//td', 
																	 'Inspiration et controverse');
	}	
}




class Telephone_RechercheControllerHarryPotterNoticeDetailleeTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/detail/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function pageShouldContainsTitres() {
		$this->assertXPathContentContains('//td[preceding::td[contains(text(), "Titre(s)")]]',
																			'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsEditeurs() {
		$this->assertXPathContentContains('//td[preceding::td[contains(text(), "Editeur(s)")]]',
																			'Gallimard');
	}


	/** @test */
	public function pageShouldContainsAuteurs() {
		$this->assertXPathContentContains('//td[preceding::td[contains(text(), "Auteur(s)")]]',
																			'J.K. Rowling');
	}


	/** @test */
	public function pageShouldNotContainsCollationAsEmpty() {
		$this->assertNotXPath('//td[contains(text(), "Collation")]');
	}


	public function pageShouldContainsLanguesFrancaisAnglais() {
		$this->assertXPathContentContains('//td[preceding::td[contains(text(), "Langue(s)")]]//li',
																			'français');

		$this->assertXPathContentContains('//td[preceding::td[contains(text(), "Langue(s)")]]//li',
																			'anglais',
																			$this->_response->getBody());
	}
}




class Telephone_RechercheControllerHarryPotterGrandeImageTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/grandeimage/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsPotterJpg() {
		$this->assertXPath('//a[contains(@href, "recherche/viewnotice/id/4")]//img[contains(@src, "potter_grand.jpg")]');		
	}
}




class Telephone_RechercheControllerHarryPotterExemplaireReservableTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_Notice::getLoader()->find(4)
			->setExemplaires(array(Class_Exemplaire::getLoader()
														 ->newInstanceWithId(33)
														 ->setCote('JRROW')
														 ->setBib(Class_Bib::getLoader()
																			->newInstanceWithId(1)
																			->setLibelle('Bibliotheque du florilege')
																			->setInterdireResa(0))
														 ->setSigbExemplaire(Class_WebService_SIGB_Exemplaire::newInstance()
																								 ->setDisponibiliteIndisponible()
																								 ->setCodeAnnexe('MOUL')
																								 ->beReservable())));
		$this->dispatch('/telephone/recherche/exemplaires/id/4', true);
	}


	/** @test */
	public function pageShouldContainsOneExemplaire() {
		$this->assertXPathContentContains('//div[@class="pave"]//td', 'n° 1');
	}


	/** @test */
	public function pageShouldContainsBibFlorilege() {
		$this->assertXPathContentContains('//td', 'Bibliotheque du florilege');
	}


	/** $test */
	public function pageShouldContainsCoteJRROW() {
		$this->assertXPathContentContains('//td', 'JRROW');
	}


	/** @test */
	public function pageShouldContainsDispoIndisponible() {
		$this->assertXPathContentContains('//td', 'Indisponible');
	}


	/** @test */
	public function pageShouldContainsHoldFunction() {
		$this->assertXPath('//div[@class="fonction"]//a[contains(@href, "/recherche/reservation/b/1/e/33/a/MOUL")]');
	}
}




class Telephone_RechercheControllerHarryPotterAvisTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();

		$avis = array(Class_AvisNotice::getLoader()
									->newInstanceWithId(34)
									->setDateAvis('2012-01-01')
									->setClefOeuvre('HARRYPOT')
									->beWrittenByBibliothecaire()
									->setNote(3)
									->setEntete('bien')
									->setAvis('bla bla')
									->setUser(Class_Users::getLoader()->newInstanceWithId(2)),

									Class_AvisNotice::getLoader()
									->newInstanceWithId(35)
									->setDateAvis('2012-01-01')
									->setClefOeuvre('HARRYPOT')
									->beWrittenByBibliothecaire()
									->setNote(5)
									->setEntete('super')
									->setAvis('blou blou')
									->setUser(Class_Users::getLoader()->newInstanceWithId(3)),

									Class_AvisNotice::getLoader()
									->newInstanceWithId(46)
									->setDateAvis('2012-01-01')
									->setClefOeuvre('HARRYPOT')
									->beWrittenByAbonne()
									->setNote(1)
									->setEntete('bof')
									->setAvis('bli bli')
									->setStatut(0)
									->setUser(Class_Users::getLoader()->newInstanceWithId(4)));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AvisNotice')
			->whenCalled('findAllBy')
			->answers($avis);


		$this->dispatch('/telephone/recherche/avis/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function toolbarTitreShouldBeAvis() {
		$this->assertXPathContentContains('//div[@class="toolbar"]', 'Avis');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	public function pageShouldContainsBibliothecaire2Evaluation() {
		$this->assertXPathContentContains('//div', 'Bibliothécaires (2 évaluations)');
	}


	/** @test */
	public function pageShouldContainsBibliothecaireStars4ForBibliothecaire() {
		$this->assertXPath('//div[contains(text(), "Bibliothécaires")]//img[contains(@src, "stars-4.gif")]',
											 $this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsLecteursDuPortail1Evaluation() {
		$this->assertXPathContentContains('//div', 'Lecteurs du portail (1 évaluation)');
	}

	
	/** @test */
	public function pageShouldContainsAvisBien() {
		$this->assertXPathContentContains('//div', 'bien');
	}


	/** @test */
	public function pageShouldContainsAvisBof() {
		$this->assertXPathContentContains('//div', 'bof');
	}
}


?>