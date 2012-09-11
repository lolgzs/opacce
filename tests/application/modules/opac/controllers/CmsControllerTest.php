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

class CmsControllerRssNoProfileTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
			->whenCalled('find')
			->answers(null);

		$this->dispatch('cms/rss');
	}

	/** @test */
	public function titleShouldBeFluxIndisponible() {
		$this->assertXPathContentContains('//channel/title', 'Flux indisponible');

	}

}

class CmsControllerCalendarRssWithProfileAndArticleTest
extends AbstractControllerTestCase {
	/**
	 * @param Class_Profil $profil
	 */
	protected function _initProfilHook($profil) {
		$profil->setCfgAccueil(
			array(
				'modules' => array(
					'1' => array(
						'division'				=> '1',
						'type_module'			=> 'NEWS',
						'preferences'			=> array(
							'titre'					=> 'Les dernières nouvelles',
							'rss_avis'			=> true,
							'type_aff'			=> 2,
							'display_order' => 'Random',
							'nb_aff'				=> 2,
							'nb_analyse'		=> 5
						)
					)
				),
				'options' => 	array()
			)
		);
	}

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
						->whenCalled('getArticlesByPreferences')
						->answers(array(
							Class_Article::getLoader()
								->newInstanceWithId(1)
								->setTitre('La fête de la banane')
								->setContenu('Une fête qui glisse !')
								->setDateMaj('2011-11-11 11:11:11'),
							Class_Article::getLoader()
								->newInstanceWithId(2)
								->setTitre('La fête de la frite')
								->setContenu('Une fête qui sent !'),
						));

		$this->dispatch('cms/calendarrss?id_profil=2&id_module=1');

	}

	/** @test */
	public function channelTitleShouldBeLesDernieresNouvelles() {
		$this->assertXpathContentContains('//channel/title', 'Les dernières nouvelles');
	}

	/** @test */
	public function channelDescriptionShouldBeAgendaColonLesDerniereNouvelles() {
		$this->assertXpathContentContains('//channel/description', 'Agenda: Les dernières nouvelles');
	}

	/** @test */
	public function itemsCountShouldBeTwo() {
		$this->assertXpathCount('//channel/item', 2);
	}

	/** @test */
	public function firstItemDateShouldBe11_11_2011() {
		$this->assertXpathContentContains('//channel/item/pubDate', '11 Nov 2011');
	}
}

class CmsControllerRssWithProfileAndArticle extends AbstractControllerTestCase {
	/**
	 * @param Class_Profil $profil
	 */
	protected function _initProfilHook($profil) {
		$profil->setCfgAccueil(
			array(
				'modules' => array(
					'1' => array(
						'division'				=> '1',
						'type_module'			=> 'NEWS',
						'preferences'			=> array(
							'titre'					=> 'Les dernières nouvelles',
							'rss_avis'			=> true,
							'type_aff'			=> 2,
							'display_order' => 'Random',
							'nb_aff'				=> 2,
							'nb_analyse'		=> 5
						)
					)
				),
				'options' => 	array()
			)
		);

	}

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
						->whenCalled('getArticlesByPreferences')
						->answers(array(
							Class_Article::getLoader()
								->newInstanceWithId(1)
								->setTitre('La fête de la banane')
								->setContenu('Une fête qui glisse !'),
							Class_Article::getLoader()
								->newInstanceWithId(2)
								->setTitre('La fête de la frite')
								->setContenu('Une fête qui sent !'),
						));

		$this->dispatch('cms/rss?id_profil=2&id_module=1');

	}

	/** @test */
	public function channelTitleShouldBeLesDernieresNouvelles()	{
		$this->assertXpathContentContains('//channel/title', 'Les dernières nouvelles');
	}

	/** @test */
	public function feteDeLaBananeShouldBePresent() {
		$this->assertXpathContentContains('//channel/item[1]/title', 'La fête de la banane');
	}

	/** @test */
	public function feteLeLaBananeDescriptionShouldBeQuiGlisse() {
		$this->assertXpathContentContains("//channel/item[1]/description", 'Une fête qui glisse !');
	}

	/** @test */
	public function feteDeLaFriteShouldBePresent() {
		$this->assertXpathContentContains('//channel/item[2]/title', 'La fête de la frite');
	}

	/** @test */
	public function feteLeLaFriteDescriptionShouldBeQuiSent() {
		$this->assertXpathContentContains('//channel/item[2]/description', 'Une fête qui sent !');
	}


	/** @test */
	public function firstLinkShouldContainsCmsArticleViewOne() {
		$this->assertXpathContentContains('//channel/item[1]/link', 'http://localhost/cms/articleview/id/1');
	}


	/** @test */
	public function secondLinkShouldContainsCmsArticleViewTwo() {
		$this->assertXpathContentContains('//channel/item[2]/link', 'http://localhost/cms/articleview/id/2');
	}
}


class CmsControllerArticleViewByDateTest extends AbstractControllerTestCase {
	protected $_article_loader;

	public function setUp() {
		parent::setUp();

		$this->_article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array('event_date' => '2011-09-03',
									 'id_bib' => null,
									 'display_order' => 'EventDebut',
									 'events_only' => true,
									 'published' => false))
			->answers(
				array(
					Class_Article::getLoader()
								->newInstanceWithId(1)
								->setTitre('La fête de la banane')
								->setContenu('Une fête qui glisse !')
								->setEventsDebut('2011-09-03')
								->setEventsFin('2011-10-03')
								->setCategorie(
										Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
												->setLibelle('Alimentaire')
												->setBib(Class_Bib::getLoader()
																	->newInstanceWithId(1)
																	->setLibelle('Bonlieu'))
								),
					Class_Article::getLoader()
								->newInstanceWithId(1)
								->setTitre('La fête de la frite')
								->setContenu('')
								->setEventsDebut('2011-09-03')
								->setEventsFin('2011-09-03')
								->setCategorie(
										Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
												->setLibelle('Alimentaire')
								),
							));


		$this->dispatch('/cms/articleviewbydate?d=2011-09-03&id_module=8&id_profil=2&select_id_categorie=all');
	}

	/** @test */
	public function feteDeLaBananeShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//a', 'La fête de la banane');
	}

	/** @test */
	public function feteDeLaBananeAnchorShouldLinkToActionViewArticleOne() {
		$this->assertXpathContentContains('//ul//li//a[contains(@href, "cms/articleview/id/1")]', 
																			'La fête de la banane');
	}

	/** @test */
	public function dateForFeteDeLaBananeShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//span', 'Du 03 septembre au 03 octobre');
	}

	/** @test */
	public function feteDeLaFriteShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//a', 'La fête de la frite');
	}

	/** @test */
	public function dateForFeteDeLaFriteShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//span', 'Le 03 septembre');
	}

	/** @test */
	public function bibliothequeLibelleShouldBePresent() {
		$this->assertXpathContentContains('//h2', 'Bonlieu');
	}

	/** @test */
	public function emptyBibliothequeLibelleShouldBeDisplayedAsPortail() {
		$this->assertXpathContentContains('//h2', 'Portail');
	}

	/** @test */
	function byPeferencesParamShouldNotContainsIdCategory() {
		$this->assertFalse(array_key_exists("id_categorie", 
																				$this->_article_loader->getFirstAttributeForLastCallOn('getArticlesByPreferences'))); 
	}
}


class CmsControllerArticleViewByDateCategorie23AndNoProfilParamTest extends AbstractControllerTestCase {
	protected $_article_loader;

	public function setUp() {
		parent::setUp();

		$this->_article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->answers(array())
			->getWrapper();

		$this->dispatch('/cms/articleviewbydate?d=2011-09-03&id_module=8&select_id_categorie=23');
	}


	/** @test */
	function byPeferencesParamShouldContainsIdCategorie23() {
		$prefs = $this->_article_loader->getFirstAttributeForLastCallOn('getArticlesByPreferences');
		$this->assertEquals(23,	$prefs['id_categorie']); 
	}

	/** @test */
	function contenuShouldContainsAucunContenu() {
		$this->assertXPathContentContains('//div', 'Aucun contenu');
	}
}




class CmsControllerArticleViewByDateWitoutEventDateTest extends AbstractControllerTestCase {
	protected $_article_loader;

	public function setUp() {
		parent::setUp();

		$this->_article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(['event_date' => null,
							'id_bib' => null,
							'display_order' => 'EventDebut',
							'events_only' => true,
							'published' => true])
			->answers([Class_Article::newInstanceWithId(1)
								->setTitre('Corrige le clic sur le bandeau de la boite calendrier qui affichait les articles non publiés')
								->setCategorie(Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
												->setLibelle('Bugs')
												->setBib(Class_Bib::newInstanceWithId(1)->setLibelle('Annecy')))
			]);


		$this->dispatch('/cms/articleviewbydate/id_module/8/id_profil/2');
	}


	/** @test */
	public function articleCorrigeCalendirerShouldBePresent() {
		$this->assertXpathContentContains('//ul//li//a', 'Corrige le clic');
	}
}




abstract class CmsControllerWithFeteDeLaFriteTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('MODO_AVIS_BIBLIO')
			->setValeur(0);

		Class_AdminVar::getLoader()
			->newInstanceWithId('MODO_AVIS')
			->setValeur(0);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('find')
			->with(224)
			->answers(
					Class_Article::getLoader()
					->newInstanceWithId(224)
					->setTitre('La fête de la frite')
					->setContenu('<div>Une fête appétissante</div>')
					->setEventsDebut('2011-09-03')
					->setEventsFin('2011-10-05')
					->setTraductions(array(Class_Article::getLoader()
																 ->newInstanceWithId(2241)
																 ->setLangue('en')
																 ->setParentId(224)
																 ->setTitre('Feast of fried')
																 ->setContenu('<div>an appetizing feast</div>')))
					->setAvis(1)
					->setAvisUsers(array($avis_mimi = Class_Avis::getLoader()
															 ->newInstanceWithId(34)
															 ->setAuteur(Class_Users::getLoader()
																					 ->newInstanceWithId(98)
																					 ->setPseudo('Mimi'))
															 ->setDateAvis('2012-02-05')
															 ->setNote(4)
															 ->setEntete('Hmmm')
															 ->setAvis('ça a l\'air bon')
															 ->beWrittenByAbonne(),

															 $avis_florence = Class_Avis::getLoader()
															 ->newInstanceWithId(35)
															 ->setAuteur(Class_Users::getLoader()
																					 ->newInstanceWithId(123)
																					 ->setPseudo('Florence'))
															 ->setDateAvis('2012-12-05')
															 ->setNote(2)
															 ->setEntete('Argg')
															 ->setAvis('ça ne me tente pas')
															 ->beWrittenByBibliothecaire())));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CmsRank')
			->whenCalled('findFirstBy')
			->answers(null)
			
			->whenCalled('findFirstBy')
			->with(array('id_cms' => 224))
			->answers(Class_CmsRank::getLoader()->newInstanceWithId(987));
      

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Avis')
			->whenCalled('findAllBy')
			->with(array(
				'id_cms' => 224,
				'order' => 'date_avis desc',
				'abon_ou_bib' => 0))
			->answers(array($avis_mimi))


			->whenCalled('findAllBy')
			->with(array(
				'id_cms' => 224,
				'order' => 'date_avis desc',
				'abon_ou_bib' => 1))
			->answers(array($avis_florence));

	}
}




class CmsControllerArticleViewTest extends CmsControllerWithFeteDeLaFriteTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB;
		$account->PSEUDO = "admin";
	}

	
	public function setUp() {
		parent::setUp();
		$this->dispatch('/cms/articleview/id/224', true);
	}


	/** @test */
	public function titleShouldBeFeteDeLaFrite() {
		$this->assertXpathContentContains('//h1', 'La fête de la frite');
	}


	/** @test */
	public function pageTitleShouldContainsLaFeteDeLaFrite() {
		$this->assertXpathContentContains('//title', 'La fête de la frite');
	}


	/** @test */
	public function calendarDateShouldBeDu3SeptembreAu5Octobre() {
		$this->assertXpathContentContains('//span[@class="calendar_event_date"]', 'Du 03 septembre au 05 octobre');
	}


	/** @test */
	public function socialNetworksContainerShouldBePresent() {
		$this->assertXpath('//div[@id="reseaux-sociaux-224"]');
	}


	/** @test */
	public function contentShouldBePresent() {
		$this->assertXpathContentContains('//div', 'Une fête appétissante');
	}

	
	/** @test */
	function withLanguageEnShouldReturnEnglishTranslation() {
		$this->bootstrap();
		$this->dispatch('/cms/articleview/id/224/language/en');
		$this->assertXpathContentContains('//h1', 'Feast of fried');
	}


	/** @test */
	function withLanguageEnEventDateShouldBeTranslated() {
		$this->bootstrap();
		$this->dispatch('/cms/articleview/id/224/language/en');
		$this->assertXpathContentContains('//span[@class="calendar_event_date"]', 
																			'From 03 September to 05 October 2011',
																			$this->_response->getBody());
	}


	/** @test */
	function withCurrentLocaleEnShouldReturnEnglishTranslation() {
		$this->bootstrap();
		Zend_Registry::get('session')->language = 'en';
		$this->dispatch('/cms/articleview/id/224');
		$this->assertXpathContentContains('//h1', 'Feast of fried');
	}

	/** @test */
	public function avisArgShouldNotHaveLinkForDeletion() {
		$this->assertNotXPath('//a[contains(@href, "admin/modo/delete-cms-avis/id/35")]');
	}
}




class CmsControllerArticleViewAsAdminTest extends CmsControllerWithFeteDeLaFriteTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
		$account->PSEUDO = "admin";
	}
	
	public function setUp() {
		parent::setUp();
		$this->dispatch('/cms/articleview/id/224', true);
	}


	/** @test */
	public function avisShouldContainsEnteteArgg() {
		$this->assertXPathContentContains('//table[@class="avis"]//td', 'Argg');
	}



	/** @test */
	public function avisShouldContainsEnteteHmmm() {
		$this->assertXPathContentContains('//table[@class="avis"]//td', 'Hmmm');
	}


	/** @test */
	public function avisHmmShouldHaveLinkForDeletion() {
		$this->assertXPath('//table[@class="avis"]//td[contains(text(), "Hmmm")]//a[contains(@href, "admin/modo/delete-cms-avis/id/34")]');
	}


	/** @test */
	public function avisArgShouldHaveLinkForDeletion() {
		$this->assertXPath('//table[@class="avis"]//td[contains(text(), "Argg")]//a[contains(@href, "admin/modo/delete-cms-avis/id/35")]');
	}
}




class CmsControllerArticleViewWithModoTest extends CmsControllerWithFeteDeLaFriteTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('MODO_AVIS_BIBLIO')
			->setValeur(1);

		Class_AdminVar::getLoader()
			->newInstanceWithId('MODO_AVIS')
			->setValeur(1);
		
		$this->dispatch('/cms/articleview/id/224', true);
	}


	/** @test */
	public function avisNotShouldContainsEnteteArgg() {
		$this->assertNotXPathContentContains('//table[@class="avis"]//td', 'Argg');
	}



	/** @test */
	public function avisNotShouldContainsEnteteHmmm() {
		$this->assertNotXPathContentContains('//table[@class="avis"]//td', 'Hmmm');
	}

}




class CmsControllerArticleReadTest extends CmsControllerWithFeteDeLaFriteTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/cms/articleread/id/224', true);
	}

	/** @test */
	public function speakStartMarkerShouldBePresent() {
		$this->assertXpathContentContains('//body', '<!-- RSPEAK_START -->');
	}

	/** @test */
	public function speakStopMarkerShouldBePresent() {
		$this->assertXpathContentContains('//body', '<!-- RSPEAK_STOP -->');
	}


	/** @test */
	function withLanguageEnArticleReadShouldReturnEnglishTranslation() {
		$this->bootstrap();
		$this->dispatch('/cms/articleread/id/224/language/en', true);
		$this->assertXpathContentContains('//h1', 'Feast of fried');
	}
}




abstract class CmsControllerListTestCase extends AbstractControllerTestCase {
	/**
	 * @return array
	 */
	protected function _createFeteDeLaFriteAndMatsumotoArticles() {
		return array(
					Class_Article::getLoader()
						->newInstanceWithId(224)
						->setTitre('La fête de la frite')
					   ->setDescription('Ce soir ça frite !')
						->setContenu('<div>Une fête appétissante</div>'),
					Class_Article::getLoader()
						->newInstanceWithId(225)
						->setTitre('Dédicaces de Leiji Matsumoto sama')
						->setContenu('<div>Albaaaaaator, albaaaator</div>')
			);
}

	/**
	 * Used to choose which action to dispatch
	 */
	protected function _dispatchHook() {}

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->answers($this->_createFeteDeLaFriteAndMatsumotoArticles())
			->getWrapper()
			->whenCalled('filterByLocaleAndWorkflow')
			->with($this->_createFeteDeLaFriteAndMatsumotoArticles())
			->answers($this->_createFeteDeLaFriteAndMatsumotoArticles());

		$this->_dispatchHook();
	}

	/** @test */
	public function feteDeLaFriteShouldBePresent() {
		$this->assertXpathContentContains('//h1', 'La fête de la frite');
	}

	/** @test */
	public function dedicaceMatsumotoShouldBePresent() {
		$this->assertXpathContentContains('//h1', 'Dédicaces de Leiji Matsumoto sama');
	}

	/** @test */
	public function workflowAndTranslationFilterShouldBeCalled() {
		$this->assertTrue(
			Class_Article::getLoader()->methodHasBeenCalled('filterByLocaleAndWorkflow')
		);
	}
}




class CmsControllerArticleViewRecentTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/articleviewrecent/nb/2');
	}
}




class CmsControllerViewSummaryTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/viewsummary');
	}
}



class CmsControllerArticleViewSelectionTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/articleviewselection');
	}


	public function setUp() {
		parent::setUp();
		$this->preferences = Class_Article::getLoader()->getFirstAttributeForLastCallOn('getArticlesByPreferences');
	}


	/** @test */
	public function nbAffShouldBeThirty() {
		$this->assertEquals(30, $this->preferences['nb_aff']);
	}


	/** @test */
	public function orderShouldBeDatePublicationDesc() {
		$this->assertEquals('DateCreationDesc', $this->preferences['display_order']);
	}
}




class CmsControllerArticleViewPreferencesBySelectionTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/articleviewpreferences?id_items=1-3&display_order=Selection');
	}


	public function setUp() {
		parent::setUp();
		$this->preferences = Class_Article::getLoader()->getFirstAttributeForLastCallOn('getArticlesByPreferences');
	}


	/** @test */
	public function itemsShouldBeOneAndThree() {
		$this->assertEquals('1-3', $this->preferences['id_items']);
	}


	/** @test */
	public function orderShouldBeDatePublicationDesc() {
		$this->assertEquals('Selection', $this->preferences['display_order']);
	}


	/** @test */
	public function aDivShouldContainsUneFeteAppetissante() {
		$this->assertXPathContentContains('//div', 'Une fête appétissante');
	}


	/** @test */
	public function noDivShouldContainsCeSoirCaFrite() {
		$this->assertNotXPathContentContains('//div', 'Ce soir ça frite !');
	}
}




class CmsControllerArticleViewPreferencesSummaryTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/articleviewpreferences?id_items=1-3&display_order=Selection&display_mode=Summary&summary_content=Summary');
	}


	/** @test */
	public function noDivShouldContainsUneFeteAppetissante() {
		$this->assertNotXPathContentContains('//div', 'Une fête appétissante');
	}


	/** @test */
	public function aDivShouldContainsCeSoirCaFrite() {
		$this->assertXPathContentContains('//div', 'Ce soir ça frite !');
	}
}




class CmsControllerArticleViewPreferencesSummaryWithoutDisplayModeTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/articleviewpreferences?id_items=1-3&display_order=Selection&summary_content=Summary');
	}


	/** @test */
	public function aDivShouldContainsUneFeteAppetissante() {
		$this->assertXPathContentContains('//div', 'Une fête appétissante');
	}


	/** @test */
	public function noDivShouldContainsCeSoirCaFrite() {
		$this->assertNotXPathContentContains('//div', 'Ce soir ça frite !');
	}
}




class CmsControllerCategorieViewTest extends CmsControllerListTestCase {
	protected function _dispatchHook() {
		$this->dispatch('/cms/categorieview');
	}
}