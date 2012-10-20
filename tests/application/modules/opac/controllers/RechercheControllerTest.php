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

abstract class RechercheControllerNoticeTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->notice = Class_Notice::getLoader()->findFirstBy([]);
	}
}



class RechercheControllerReseauTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		Class_WebService_ReseauxSociaux::setDefaultWebClient(Storm_Test_ObjectWrapper::mock()
																												 ->whenCalled('open_url')
																												 ->answers(false));
		parent::setUp();
		$this->dispatch(sprintf('recherche/reseau/id_notice/%d/type_doc/1', 
														$this->notice->getId()), 
										true);
	}
	

	public function tearDown() {
		Class_WebService_ReseauxSociaux::setDefaultWebClient(null);
	}

	/** @test */
	public function getResauShouldContainsTwitterGif() {
		$this->assertXPath('//img[contains(@src, "twitter.gif")]');
	}


	/** @test */
	public function getResauShouldContainsTwitterLink() {
		$this->assertContains('onclick="$.getScript(\'/social-network/share/on/facebook?url=',
													$this->_response->getBody());
	}
}




class RechercheControllerViewNoticeBabelthequeTest extends RechercheControllerNoticeTestCase {
	/** @test */
	public function withoutBabelthequeJSShouldNotBeLoaded() {
		Class_AdminVar::newInstanceWithId('BABELTHEQUE_JS')->setValeur('');
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertNotXpath('//script[contains(@src, "babeltheque.js")]');
	}


	/** @test */
	public function withBabelthequeJSShouldBeLoadedWithRightId() {
		Class_AdminVar::newInstanceWithId('BABELTHEQUE_JS')->setValeur('http://www.babeltheque.com/bw_666.js');
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertXpath('//script[contains(@src, "babeltheque.js?bwid=666")]');
	}
}




class RechercheControllerViewNoticeWithPreferencesTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		parent::setUp();
		$preferences = [
			'barre_nav' => 'Notice',
			'entete' =>"ABCDEFGIKLMNOPRSTtYZ8v",
			'onglets' =>	[
				'detail' =>					['titre' =>	'Details', 			'aff' =>	'1', 'ordre' =>	1, 'largeur' =>	10],
				'avis' =>						['titre' =>	'avis', 				'aff' =>	'1', 'ordre' =>	2, 'largeur' =>	10],
				'exemplaires' =>		['titre' =>	'exemplaires',	'aff' =>	'2', 'ordre' =>	3, 'largeur' =>	10],
				'resume' =>					['titre' =>	'resume', 			'aff' =>	'2', 'ordre' =>	4, 'largeur' =>	10],
				'tags' =>						['titre' =>	'tags', 				'aff' =>	'2', 'ordre' =>	5, 'largeur' =>	10],
				'biographie' =>		 	['titre' =>	'biographie', 	'aff' =>	'2', 'ordre' =>	6, 'largeur' =>	10],
				'similaires' =>			['titre' =>	'similaires', 	'aff' =>	'2', 'ordre' =>	7, 'largeur' =>	10],
				'bibliographie' =>	['titre' =>	'bibliographie','aff' =>	'3', 'ordre' =>	7, 'largeur' =>	10],
				'morceaux' =>				['titre' =>	'morceaux', 		'aff' =>	'3', 'ordre' =>	8, 'largeur' =>	10],
				'bandeAnnonce' =>		['titre' =>	'bande annonce','aff' =>	'3', 'ordre' =>	9, 'largeur' =>	10],
				'photos' =>					['titre' =>	'photos', 			'aff' =>	'3', 'ordre' =>	14, 'largeur' =>	10],
				'videos' =>					['titre' =>	'videos', 			'aff' =>	'3', 'ordre' =>	11, 'largeur' =>	10],
				'resnumeriques' =>	['titre' =>	'ressources n',	'aff' =>	'3', 'ordre' =>	12, 'largeur' =>	10],
				'babeltheque' =>		['titre' =>	'babeltheque', 	'aff' =>	'3', 'ordre' =>	13, 'largeur' =>	10],
				'frbr' =>						['titre' =>	'frbr', 				'aff' =>	'3', 'ordre' =>	10, 'largeur' =>	10]],
			'boite' =>	null];

		Class_Profil::getCurrentProfil()->setCfgModules(['recherche' =>	['viewnotice1' => $preferences]]);
		$this->notice
			->setId(345)
			->setAnnee('2002')
			->setEditeur('Gallimard')
			->setIsbn('1-234-56789-0')
			->getLoader()->cacheInstance($this->notice);
		$this->dispatch('recherche/viewnotice/id/345/type_doc/1', true);
	}


	/** @test */
	public function enteteShouldDisplayAnnee2002() {
		$this->assertXPathContentContains('//table[@id="entete_notice"]//td', '2002');		
	}


	/** @test */
	public function enteteShouldDisplayEditeurGallimard() {
		$this->assertXPathContentContains('//table[@id="entete_notice"]//td', 'Gallimard');		
	}


	/** @test */
	public function detailsBlocShouldHaveIdBloc_345_0() {
		$this->assertXPathContentContains('//div[@id="bloc_345_0"][@class="notice_bloc_titre"]', 'Details');
	}


	/** @test */
	public function javascriptShouldOpenFirstBlocDetails() {
		$this->assertXPathContentContains('//script', "infos_bloc(\"bloc_345_0\",'1-234-56789-0','detail',0,'',0)", $this->_response->getBody());
	}


	/** @test */
	public function javascriptShouldOpenSecondBlocAvis() {
		$this->assertXPathContentContains('//script', "infos_bloc(\"bloc_345_1\",'1-234-56789-0','avis',0,'',1)");
	}


	/** @test */
	public function noJavascriptShouldOpenThirdBlocExemplaires() {
		$this->assertNotXPathContentContains('//script', "infos_bloc(\"bloc_345_2\"");
	}


	/** @test */
	public function bibliographieOngletShouldHaveIdSet345_onglet_0() {
		$this->assertXPathContentContains('//div[@id="set345_onglet_0"][@class="titre_onglet"]', 'bibliographie');
	}


	/** @test */
	public function javascriptShouldOpenFirstOngletBibliographie() {
		$this->assertXPathContentContains('//script', "infos_onglet('set345_onglet_0','1-234-56789-0','bibliographie',0,'',0)");
	}


	/** @test */
	public function frbrOngletShouldHaveIdSet345_onglet_3() {
		$this->assertXPathContentContains('//div[@id="set345_onglet_3"][@class="titre_onglet"]', 'frbr', $this->_response->getBody());
	}

	/** @test */
	public function noJavascriptShouldOpenSecondOnglet() {
		$this->assertNotXPathContentContains('//script', "infos_onglet('set345_onglet_1')");
	}

}




abstract class RechercheControllerViewNoticeTestCase extends RechercheControllerNoticeTestCase {
	/** @test */
	public function titleShouldBeDisplayed() {
		$this->assertXPathContentContains('//h1',
																			array_first(explode('<br />', $this->notice->getTitrePrincipal())),
																			$this->_response->getBody());
	}


	/** @test */
	public function tagReseauSociauxShouldBePresent() {
		$this->assertXPath('//div[@id="reseaux-sociaux"]');
	}


	/** @test */
	public function headShouldContainsRechercheJS() {
		$this->assertXPath('//head//script[contains(@src,"public/opac/js/recherche.js")]');
	}
}




abstract class RechercheControllerViewNoticeTest extends RechercheControllerViewNoticeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()));
	}
}




class RechercheControllerViewNoticeClefAlphaTest extends RechercheControllerViewNoticeTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->with(['clef_alpha' => 'TESTINGALPHAKEY---101'])
			->answers([$this->notice]);

		$this->dispatch('recherche/viewnotice/clef/TESTINGALPHAKEY---101', true);
	}
}




class RechercheControllerViewNoticeClefAlphaWithDoublonsTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->with(['clef_alpha' => 'TWILIGHT--SLADED-3-M6VIDEO-2010-4'])
			->answers([Class_Notice::newInstanceWithId($this->notice->getId())
								 ->setTitrePrincipal('Twilight 1')
								 ->setClefAlpha('TWILIGHT--SLADED-3-M6VIDEO-2010-4')
								 ->setFacettes(''),
								 
								 Class_Notice::newInstanceWithId(1)
								 ->setTitrePrincipal('Twilight 2')
								 ->setClefAlpha('TWILIGHT--SLADED-3-M6VIDEO-2010-4')
								 ->setFacettes('')
								 ]);
	}

	
	/** @test */
	public function withOnlyClefResponseShouldRedirectToRechercheTWILIGHT_SLADED() {
		$this->dispatch('recherche/viewnotice/clef/'.urlencode('TWILIGHT--SLADED-3-M6VIDEO-2010-4'), true);
		$this->assertRedirectTo('/opac/recherche?q=TWILIGHT+SLADED');
	}


	/** @test */
	public function withClefAndIdResponseShouldNotRedirectTo() {
		$this->dispatch('recherche/viewnotice/clef/'.urlencode('TWILIGHT--SLADED-3-M6VIDEO-2010-4').'/id/'.$this->notice->getId(), true);
		$this->assertNotRedirect();
	}
}




class RechercheControllerUploadVignetteTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		parent::setUp();
		$this->notice->setTypeDoc(5);
	}


	/** @test */
	public function linkToUploadVignetteShouldNotBePresentForAbonneSIGB() {
		Class_Users::getIdentity()->beAbonneSIGB();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertNotXPathContentContains('//a', 'Modifier la vignette');
	}


	/** @test */
	public function linkToUploadVignetteShouldBePresentForAdmin() {
		Class_Users::getIdentity()->beAdminPortail();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertXPathContentContains('//a', 'Modifier la vignette');
	}


	/** @test */
	public function linkToUploadVignetteShouldBePresentForModoBib() {
		Class_Users::getIdentity()->changeRoleTo(ZendAfi_Acl_AdminControllerRoles::MODO_BIB);
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertXPathContentContains('//a', 'Modifier la vignette');
	}


	/** @test */
	public function linkToUploadVignetteShouldNotBePresentForTypeDocMoreThanFive() {
		Class_Users::getIdentity()->beAdminPortail();
		$this->notice->setTypeDoc(6);
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertNotXPathContentContains('//a', 'Modifier la vignette');
	}
}




class RechercheControllerReservationPickupAjaxActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

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

		$this->dispatch('recherche/reservation-pickup-ajax?id_bib=2&id_origine=12&code_annexe=ANN');
	}


	/** @test */
	public function shouldRenderAnnecyCheckedRadio() {
		$this->assertXPath('//input[@name="code_annexe"][@value="ANN"][@checked="checked"]');
	}


	/** @test */
	public function shouldRenderCranRadio() {
		$this->assertXPath('//input[@name="code_annexe"][@value="CRN"]');
	}


	/** @test */
	public function layoutShouldBeEmpty() {
		$this->assertNotXPath('//div[@id="banniere"]');
	}
}



abstract class RechercheControllerSimpleActionTestCase extends AbstractControllerTestCase {
	public function lanceRecherche($params) {
		$this->postDispatch('/recherche/simple', $params);

		$this->assertRedirect('/recherche/simple');

		$recherche = $_SESSION['recherche'];
		$this->bootstrap();
		$_SESSION['recherche'] = $recherche;
		$this->dispatch('/recherche/simple', true);
	}
}




class RechercheControllerSimpleActionWithDefaultConfigTest extends RechercheControllerSimpleActionTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()->setCfgModules([]);
		$this->lanceRecherche(['expressionRecherche' => 'pomme']);
	}


	/** @test */
	public function pommeShouldBePresentInRedirectedPageAsResultatInSession() {
		$this->assertXPathContentContains('//div', 'pomme');
	}


	/** @test */
	public function pageShouldContainsLinkToSuggestionAchats() {
		$this->assertXPathContentContains('//a[contains(@href, "/abonne/suggestion-achat")]', 
																			'Suggérer un achat');
	}
}




class RechercheControllerSimpleActionWithConfigWithoutSuggestionAchatTest extends RechercheControllerSimpleActionTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()->setCfgModules(['recherche' => ['resultatsimple' => ['suggestion_achat' => 0]]]);
		$_SESSION['recherche'] = ['selection' => ['expressionRecherche' => 'potter',
																							'mode' => 'simple'],
															'resultat' => ['req_liste' => "select id_notice,MATCH(alpha_titre)  AGAINST(' (POMME POMMES POM)' ) as rel1, MATCH(alpha_auteur) AGAINST(' (POMME POMMES POM)' ) as rel2 from notices Where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE) order by (rel1 * 1.5)+(rel2) desc",
																						 'req_facettes' => "select id_notice,type_doc,facettes from notices Where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE) limit 15000",
																						 'nombre' => 5,
																						 'facettes' => [],
																						 'tags' => []]];
		$this->dispatch('/recherche/simple', true);
	}


	/** @test */
	public function pageShouldNotContainsLinkToSuggestionAchats() {
		$this->assertNotXPath('//a[contains(@href, "/abonne/suggestion-achat")]');
	}
}




class RechercheControllerSimpleByISBNActionTest extends RechercheControllerSimpleActionTestCase {
	public function setUp() {
		parent::setUp();
		$this->lanceRecherche(['expressionRecherche' => '2-203-00119-4']);
	}

	
	/** @test */
	public function pageResultatRechecheShouldBeDisplayed() {
		$this->assertXPathContentContains('//div', 'Recherche : 2-203-00119-4');
	}
}




class RechercheControllerAvanceeActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/recherche/avancee', true);
	}

	
	/** @test */
	public function pageRechercheAvanceeShouldBeDisplayed() {
		$this->assertXPathContentContains('//h1', 'recherche');
	}
}




class RechercheControllerPostReservationAction extends AbstractControllerTestCase {
	protected $_sent_mails;

	public function setUp() {
		parent::setUp();

		$_SESSION["captcha_code"] = '1234';

		$mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($mock_transport);


		Class_Bib::getLoader()->newInstanceWithId(4)->setLibelle('Astrolabe');

		$this->postDispatch('/recherche/reservation',
												array('id_notice' => 4,
															'id_bib' => 4,
															'mail_bib' => 'zork@gloub.fr',
															'user_name' => 'nanuk',
															'demande' => 'je veux le livre',
															'user_mail' => 'nanuk@gloub.com',
															'code_saisi' => '1234',
															'cote' => 'XYZ'),
												true);
		$this->_sent_mails = $mock_transport->getSentMails();
	}


	/** @test */
	public function twoMailsShouldHaveBeenSent() {
		$this->assertEquals(2, count($this->_sent_mails));
	}


	/** @test */
	public function firstMailFromShouldBeNanuk() {
		$this->assertEquals('nanuk@gloub.com', 
												array_first($this->_sent_mails)->getFrom());
	}


	/** @test */
	public function firstMailToShouldBeZork() {
		$this->assertContains('zork@gloub.fr', 
													array_first($this->_sent_mails)->getRecipients());
	}


	/** @test */
	public function secondMailFromShouldBeNobody() {
		$this->assertEquals('nobody@noreply.fr', 
												array_last($this->_sent_mails)->getFrom());
	}


	/** @test */
	public function secondMailToShouldBeNanuk() {
		$this->assertContains('nanuk@gloub.com', 
													array_last($this->_sent_mails)->getRecipients());
	}

}




class RechercheControllerRebondTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$facettes = Class_Notice::findFirstBy(['where' => 'facettes>""'])->getFacettes();
		$code_rebond = explode(' ', trim($facettes))[0];
		$this->dispatch('/recherche/rebond?facette=reset&code_rebond='.$code_rebond.'&tri=alpha_titre', true);
	}

	
	/** @test */
	public function comboTriShouldHaveAnneePublicationSelected() {
		$this->assertXPathContentContains('//select[@id="tri"]//option[@value="alpha_titre"][@selected="selected"]', 
																			'Titre et auteur');
	}

	
	/** @test */
	public function lienRetourRechercheInitialeShouldBeRechercheSimple() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/simple?statut=saisie")]', 
																			'Retour');
	}


	/** @test */
	public function lienNouvelleRechercheShouldBeRechercheSimple() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/simple?statut=reset")]', 
																			'Nouvelle recherche', 
																			$this->_response->getBody());
	}
}

?>