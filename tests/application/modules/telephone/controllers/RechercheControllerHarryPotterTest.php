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
	public function pageShouldContainsLinkToDescriptionDuDocument() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/detail/id/4")]', 'Description du document');
	}


	/** @test */
	public function pageShouldContainsLinkToCritiques() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/avis/id/4")]', 'Critiques');
	}


	/** @test */
	public function pageShouldContainsLinkToExemplaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/exemplaires/id/4")]', 'Où le trouver');
	}


	/** @test */
	public function pageShouldContainsLinkToResumes() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/resume/id/4")]', 'Résumé');
	}


	/** @test */
	public function pageShouldContainsLinkToTags() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/tags/id/4")]', 'Rebondir dans le catalogue');
	}


	/** @test */
	public function pageShouldContainsLinkToVideos() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/videos/id/4")]', 'Vidéos associées');
	}


	/** @test */
	public function pageShouldContainsLinkToBiographies() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/biographie/id/4")]', 'Biographie');
	}


	/** @test */
	public function pageShouldContainsLinkToNoticesSimilaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/similaires/id/4")]', 'Documents similaires');
	}


	/** @test */
	public function pageShouldNotContainsLinkToRessourcesNumeriques() {
		$this->assertNotXPath('//a[contains(@href, "recherche/ressourcesnumeriques/id/4")]');
	}
}




class Telephone_RechercheControllerHarryPotterViewResumeTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/resume/id/4', true);
	}


	/**
	 * @group integration
	 * @test 
	 */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/**
	 * @group integration
	 * @test 
	 */
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
		Class_Notice::find(4)
			->setExemplaires([Class_Exemplaire::getLoader()
														 ->newInstanceWithId(33)
														 ->setCote('JRROW')
														 ->setBib(Class_Bib::getLoader()
																			->newInstanceWithId(1)
																			->setLibelle('Bibliotheque du florilege')
																			->setInterdireResa(0))
												]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_IntBib')
			->whenCalled('find')
			->with(1)
			->answers(Storm_Test_ObjectWrapper::mock()
								->whenCalled('getSigbExemplaire')
								->answers(Class_WebService_SIGB_Exemplaire::newInstance()
													->setDisponibiliteIndisponible()
													->setCodeAnnexe('MOUL')
													->beReservable()));

		Class_AdminVar::getLoader()
			->newInstanceWithId('PACK_MOBILE')
			->setValeur(1);

		$this->dispatch('/telephone/recherche/exemplaires/id/4', true);
	}


	/** @test */
	public function pageShouldContainsOneExemplaire() {
		$this->assertXPathContentContains('//td', 'n° 1', $this->_response->getBody());
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
		$this->assertXPath('//a[contains(@href, "/recherche/reservation/b/1/e/33/a/MOUL")]');
	}
}



class Telephone_RechercheControllerHarryPotterExemplaireReservablePackMobileInactifTest extends Telephone_RechercheControllerHarryPotterTestCase {
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

		Class_AdminVar::getLoader()
			->newInstanceWithId('PACK_MOBILE')
			->setValeur(0);

		$this->dispatch('/telephone/recherche/exemplaires/id/4', true);
	}


	/** @test */
	public function pageShouldNotContainsHoldFunction() {
		$this->assertNotXPath('//div[@class="fonction"]//a[contains(@href, "/recherche/reservation")]');
	}

}



class Telephone_RechercheControllerHarryPotterReservationNotLogged extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('getIdentity')
			->answers(false);

		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		$this->dispatch('/recherche/reservation/b/1/e/33/a/MOUL');
	}


	/** @test */
	public function shouldRedirectToAuth() {
		$this->assertRedirectTo('/auth/login-reservation/id/4');
	}


	/** @test */
	public function shouldHaveSetReservationBibParam() {
		$this->assertEquals(1, Zend_Registry::get('session')->lastReservationParams['b']);
	}


	/** @test */
	public function shouldHaveSetReservationExemplaireParam() {
		$this->assertEquals(33, Zend_Registry::get('session')->lastReservationParams['e']);
	}


	/** @test */
	public function shouldHaveSetReservationAnnexeParam() {
		$this->assertEquals('MOUL', Zend_Registry::get('session')->lastReservationParams['a']);
	}
}



class Telephone_RechercheControllerHarryPotterReservationWithEnabledPickup extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()
			->newInstanceWithId('site_retrait_resa')
			->setValeur(1);

		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		$this->dispatch('/recherche/reservation/b/1/e/33/a/MOUL');
	}


	/** @test */
	public function shouldRedirectToPickupChoice() {
			$this->assertRedirectTo('/recherche/pickup-location/b/1/e/33/a/MOUL');
	}

}



class Telephone_RechercheControllerHarryPotterReservationBackFromLoginWithEnabledPickup extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()
			->newInstanceWithId('site_retrait_resa')
			->setValeur(1);

		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		Zend_Registry::get('session')->lastReservationParams = array('b' => 1,
																																 'e' => 33,
																																 'a' => 'MOUL');
		$this->dispatch('/recherche/reservation');
	}


	public function tearDown() {
		unset(Zend_Registry::get('session')->lastReservationParams);
	}


	/** @test */
	public function shouldRedirectToPickupChoice() {
			$this->assertRedirectTo('/recherche/pickup-location/b/1/e/33/a/MOUL');
	}

}



class Telephone_RechercheControllerHarryPotterExemplairePickupChoiceTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {  
		parent::setUp();
		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CodifAnnexe')
			->whenCalled('findAllBy')
			->with(array('no_pickup' => '0',
									 'order' => 'libelle'))
			->answers(array(Class_CodifAnnexe::getLoader()->newInstanceWithId(2)
											->setLibelle('Annecy')
											->setCode('ANN'),
											Class_CodifAnnexe::getLoader()->newInstanceWithId(3)
											->setLibelle('Cran')
											->setCode('CRN')));
		$this->dispatch('/recherche/pickup-location/b/1/e/33/a/MOUL', true);
	}


	/** @test */
	public function pageShouldContainsAnnecyChoice() {
		$this->assertXPathContentContains('//a[contains(@href, "/recherche/reservation/b/1/e/33/a/MOUL/pickup/ANN")]', 
																			'Annecy');
	}
}



class Telephone_RechercheControllerHarryPotterReservationSuccessTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()
			->newInstanceWithId('site_retrait_resa')
			->setValeur(0);

		Class_CommSigb::setInstance(Storm_Test_ObjectWrapper::mock()
																->whenCalled('reserverExemplaire')
																->answers(array()));
		$this->dispatch('/recherche/reservation/b/1/e/33/a/MOUL', true);
	}

	
	/** @test */
	public function shouldRedirectToFicheAbonne() {
		$this->assertRedirectTo('/abonne/fiche');
	}
}



class Telephone_RechercheControllerHarryPotterReservationErrorTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()
			->newInstanceWithId('site_retrait_resa')
			->setValeur(0);

		Class_CommSigb::setInstance(Storm_Test_ObjectWrapper::mock()
																->whenCalled('reserverExemplaire')
																->answers(array('erreur' => 'A marche pas')));
		
		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		$this->dispatch('/recherche/reservation/b/1/e/33/a/MOUL', true);
	}

	
	/** @test */
	public function pageShouldContainsErrorMessage() {
		$this->assertXPathContentContains('//div', 'A marche pas');
	}
}



class Telephone_RechercheControllerHarryPotterReservationWithPopupTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()
			->newInstanceWithId('site_retrait_resa')
			->setValeur(0);

		Class_CommSigb::setInstance(Storm_Test_ObjectWrapper::mock()
																->whenCalled('reserverExemplaire')
																->answers(array('popup' => 'http://url.de/la-popup')));

		Class_Exemplaire::getLoader()
			->newInstanceWithId(33)
			->setCote('JRROW')
			->setNotice(Class_Notice::getLoader()->find(4));

		$this->dispatch('/recherche/reservation/b/1/e/33/a/MOUL', true);
	}

	
	/** @test */
	public function pageShouldContainsErrorMessage() {
		$this->assertXPathContentContains('//div', 'non supportée');
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




class Telephone_RechercheControllerHarryPotterVideosTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();

		$answer = array('statut_recherche' => 2,
										'erreur' => '',
										'source' => 'youtube',
										'video' => '<object width="500" height="400"><param name="movie" value="http://www.youtube.com/v/np9U1pmRsGs&fs=1&source=uds&autoplay=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/np9U1pmRsGs&fs=1&source=uds&autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="500" height="400"></embed></object>',
										'statut' => 'OK');

		$http_client = Storm_Test_ObjectWrapper::mock();
		$http_client
			->whenCalled('open_url')
			->answers(json_encode($answer));

		Class_WebService_AllServices::setHttpClient($http_client);

		$this->dispatch('/telephone/recherche/videos/id/4', true);
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsIFrameWithEmbedVideoUrl() {
		$this->assertXPath('//iframe[@src="http://www.youtube.com/embed/np9U1pmRsGs"]');
	}

}




class Telephone_RechercheControllerHarryPotterVideoNotFoundTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();

		$answer = array('statut_recherche' => 1,
										'erreur' => '',
										'source' => '',
										'video' => '',
										'statut' => 'OK');

		$http_client = Storm_Test_ObjectWrapper::mock();
		$http_client
			->whenCalled('open_url')
			->answers(json_encode($answer));

		Class_WebService_AllServices::setHttpClient($http_client);

		$this->dispatch('/telephone/recherche/videos/id/4', true);
	}


	/** @test */
	public function pageShouldDisplayAucuneVideoTrouvee() {
		$this->assertXPathContentContains('//p', 'Aucune vidéo');
	}
}


?>