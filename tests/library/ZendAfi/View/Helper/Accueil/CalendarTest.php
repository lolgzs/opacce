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

abstract class CalendarViewHelperTestCase extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')->setValeur(null);

		$this->nanook2 = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('Nanook 2 en prod !')
			->setEventsDebut('2011-02-1')
			->setEventsFin('2011-02-22')
			->setCategorie(Class_ArticleCategorie::getLoader()
										 ->newInstanceWithId(3)
										 ->setLibelle('Actu des ours')
										 ->setBib(Class_Bib::getLoader()->newInstanceWithId(1)
															->setVille('Annecy')));


		$this->bib_cran = Class_Bib::getLoader()
                    			->newInstanceWithId(5)
                    			->setLibelle('Cran Gevrier')
                    			->setVille('Cran Gevrier');

		$cat_evenements = Class_ArticleCategorie::getLoader()
										 ->newInstanceWithId(25)
			               ->setLibelle('Evenements')
			               ->setBib($this->bib_cran);

		$this->opac4 = Class_Article::getLoader()
			->newInstanceWithId(8)
			->setTitre('OPAC 4 en prod !')
			->setEventsDebut('2011-02-15')
			->setEventsFin('2011-02-28')
			->setCategorie($cat_evenements)
			->setTraductions(array(Class_Article::getLoader()
														 ->newInstanceWithId(78)
														 ->setLangue('en')
														 ->setParentId(8)
														 ->setTitre('OPAC 4 released !')));

		$this->amber = Class_Article::getLoader()
			->newInstanceWithId(14)
			->setTitre('JTalk deviens Amber')
			->setEventsDebut('2011-09-13')
			->setEventsFin('2011-09-13')
			->setCategorie($cat_evenements)
			->setTraductions(array(Class_Article::getLoader()
														 ->newInstanceWithId(132)
														 ->setLangue('en')
														 ->setParentId(14)
														 ->setTitre('JTalk is now Amber')));
	}

	public function tearDown() {
		Zend_Registry::get('translate')->setLocale('fr');
		parent::tearDown();
	}
}


abstract class CalendarWithEmptyPreferencesTestCase extends CalendarViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		$this->nanook2
			->setEventsDebut(strftime('%Y-%m-01'))
			->setEventsFin(strftime('%Y-%m-%d'));


		$this->opac4
			->setEventsDebut(strftime('%Y-%m-%d'))
			->setEventsFin(strftime('%Y-%m-%d'));

		$params = array('division' => '2',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Agenda',
																					 'rss_avis' => '0',
																					 'id_categorie' => '',
																					 'display_cat_select' => '',
																					 'display_event_info' => 'cat'));
		$this->helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);
	}
}


class CalendarWithEmptyPreferencesTest extends CalendarWithEmptyPreferencesTestCase {
	public function setUp() {
		parent::setUp();

		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_date' => strftime('%Y-%m'),
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();

		$this->html = $this->helper->getBoite();
	}


	/** @test */
	public function cacheKeyShouldContainsBASE_URL() {
		$params = array(BASE_URL, 
										2, 
										Class_Profil::getCurrentProfil()->getId(), 
										Zend_Registry::get('translate')->getLocale(), 
										$this->helper->getPreferences());
		$this->assertEquals(md5(serialize($params)), $this->helper->getCacheKey());
	}


	/** @test */
	public function titreShouldBeAgendaAndLinkToArticleViewByDate() {
		$this->assertXPathContentContains($this->html, 
																			'//a[contains(@href, "/cms/articleviewbydate/id_module/2/id_profil/2")]', 
																			'Agenda');
	}


	/** @test */
	function shouldDisplayCurrentMonth() {
		$this->assertXPathContentContains($this->html,
																			'//td[@class="calendar_title_month"]/a',
																			utf8_encode(strftime('%B %Y')));
	}


	/** @test */
	function calendarEventListShouldContainsNanook2() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_title"][contains(@href, "cms/articleview/id/4")]',
																			'Nanook 2 en prod !');
	}


	/** @test */
	function calendarEventListShouldContainsCategorieActuDesOurs() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_info"][contains(@href, "cms/articleviewbydate?cat=3")]',
																			'Actu des ours');
	}


	/** @test */
	function calendarEventListShouldContainsOPAC4() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_title"][contains(@href, "cms/articleview/id/8")]',
																			'OPAC 4 en prod !',
																			$this->html);
	}


	/** @test */
	function opac4DayShouldBeClickable() {
		$this->assertXPath($this->html,
											 sprintf('//a[contains(@class, "day_clickable")][contains(@href, "cms/articleviewbydate?d=%s")]',
															 $this->opac4->getEventsDebut()),
											 $this->html);
	}


	/** @test */
	function calendarEventListShouldContainsCategorieEvenements() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_info"][contains(@href, "cms/articleviewbydate?cat=25")]',
																			'Evenements');
	}
}




class CalendarWithPreferencesNbEventsOneTest extends CalendarWithEmptyPreferencesTestCase {
	public function setUp() {
		parent::setUp();

		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_date' => strftime('%Y-%m'),
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();


		$params = array('division' => '1',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Calendrier',
																					 'rss_avis' => '0',
																					 'id_categorie' => '',
																					 'nb_events' => 1));
		$this->helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);

		$this->html = $this->helper->getBoite();
	}


	/** @test */
	function calendarEventListShouldContainsOneArticle() {
		$this->assertXPathCount($this->html,
														'//a[@class="calendar_event_title"][contains(@href, "cms/articleview")]',
														1);
	}
}




class CalendarWithEmptyParamsLocaleEnAndOnlyTwoArticlesReturned Extends CalendarWithEmptyPreferencesTestCase {
	public function setUp() {
		parent::setUp();
		Zend_Registry::get('translate')->setLocale('en');

		Class_AdminVar::getLoader()->find('LANGUES')->setValeur('fr;en');

		$next_month =  time() + (7 * 24 * 60 * 60 * 30);
		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_date' => strftime('%Y-%m'),
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4))
			->getWrapper()

			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_start_after' => strftime('%Y-%m', $next_month),
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->amber))
			->getWrapper();

		$this->html = $this->helper->getBoite();
	}


	/** @test */
	function calendarEventListShouldContainsOPAC4Released() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_title"][contains(@href, "cms/articleview/id/78")]',
																			'OPAC 4 released !',
																			$this->html);
	}


	/** @test */
	function calendarEventListShouldNotContainNanook2() {
		$this->assertNotXPathContentContains($this->html, '//a', 'Nanook 2 en prod !');
	}


	/** @test */
	function calendarEventListContainsJTalkIsNowAmber() {
		$this->assertNotXPathContentContains($this->html, '//a', 'JTalk is now Amber');
	}
}




class CalendarWithCategoryLimitAndBibPreferencesTest extends CalendarViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()->setBib($this->bib_cran);

		$params = array('division' => '2',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Concerts !',
																					 'rss_avis' => '0',
																					 'id_categorie' => '12-3',
																					 'display_cat_select' => '',
																					 'display_event_info' => 'bib',
																					 'display_date' => '2011-03-17'));
		$helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);


		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '12-3',
									 'event_date' => '2011-03',
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();
		$this->html = $helper->getBoite();
	}


	/** @test */
	function shouldDisplayCurrentMonth() {
		$this->assertXPathContentContains($this->html,
																			'//td[@class="calendar_title_month"]/a',
																			'mars 2011');
	}


	/** @test */
	function titleMonthFirstLinkShouldGoToFebruary() {
		$this->assertXPath($this->html,
					'//td[@class="calendar_title_month"]//a[1][contains(@href, "cms/calendar?date=2011-02&id_module=2&id_profil=2&select_id_categorie=all")]',
					$this->html);
	}


	/** @test */
	function titleMonthSecondLinkShouldLinkToArticleViewByDate() {
		$this->assertXPath($this->html,
					'//td[@class="calendar_title_month"]//a[2][contains(@href, "cms/articleviewbydate?d=2011-03&id_module=2&id_profil=2&select_id_categorie=all")]',
					$this->html);
	}


	/** @test */
	function titleMonthLastLinkShouldGoToApril() {
		$this->assertXPath($this->html,
					'//td[@class="calendar_title_month"]//a[3][contains(@href, "cms/calendar?date=2011-04&id_module=2&id_profil=2&select_id_categorie=all")]',
					$this->html);
	}


	/** @test */
	function calendarEventListShouldNotContainsNanook2() {
		$this->assertNotXPathContentContains($this->html,
																			'//a[@class="calendar_event_title"]',
																			'Nanook 2 en prod !');
	}


	/** @test */
	function calendarEventListShouldContainsOPAC4() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_title"]',
																			'OPAC 4 en prod !',
																			$this->html);
	}


	/** @test */
	function calendarEventListShouldContainsBibCran() {
		$this->assertXPathContentContains($this->html,
																			'//a[@class="calendar_event_info"][contains(@href, "cms/articleviewbydate?b=5")]',
																			'Cran Gevrier',
																			$this->html);
	}


	/** @test */
	function calendarEventListShouldNotContainsPortail() {
		$this->assertNotXPathContentContains($this->html,
																			'//a[@class="calendar_event_info"][contains(@href, "cms/articleviewbydate?b=0")]',
																			'Portail',
																			$this->html);
	}
}



class CalendarWithCategorySelectorAndRssPreferencesTest extends CalendarViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		$params = array('division' => '2',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Concerts !',
																					 'rss_avis' => '1',
																					 'id_categorie' => '1-12-23',
																					 'display_cat_select' => '1',
																					 'display_event_info' => 'none',
																					 'select_id_categorie' => '12',
																					 'display_date' => '2011-12-25'));
		$helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);


		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '12',
									 'event_date' => '2011-12',
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();

		$this->html = $helper->getBoite();
	}


	/** @test */
	function shouldDisplayCurrentMonth() {
		$this->assertXPathContentContains($this->html,
																			'//td[@class="calendar_title_month"]/a',
																			utf8_encode('décembre 2011'));
	}


	/** @test */
	function titleMonthLastLinkShouldGoToJanuary2012Categorie12() {
		$this->assertXPath($this->html,
					'//td[@class="calendar_title_month"]//a[3][contains(@href, "cms/calendar?date=2012-01&id_module=2&id_profil=2&select_id_categorie=12")]',
					$this->html);
	}


	/** @test */
	function calendarEventListShouldNotContainsPortail() {
		$this->assertNotXPathContentContains($this->html, '//a', 'Portail');
	}


	/** @test */
	public function categorySelectorShouldBeVisible() {
		$this->assertXPath($this->html, '//form[@id="calendar_select_categorie"]//select');
	}


	/** @test */
	public function categorySelectorActionShouldBeOpacCmsCalendar() {
		$this->assertXPath($this->html, '//form[@id="calendar_select_categorie"][contains(@action, "opac/cms/calendar")]');
	}
}




class CalendarWithCategorySelectorButNoSelectedCategoriesTest
extends CalendarViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		$params = array('division' => '2',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Concerts !',
																					 'rss_avis' => '0',
																					 'id_categorie' => '',
																					 'display_cat_select' => '1',
																					 'display_event_info' => 'none',
																					 'select_id_categorie' => '',
																					 'display_date' => '2011-12-25',
																					 'display_next_event' => '0'));
		$helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array('display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_date' => '2011-12',
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
			->whenCalled('findAllBy')
			->with(array('ID_CAT_MERE'=> 0,
										'order'			=> 'LIBELLE'))
			->answers(array(
				Class_ArticleCategorie::getLoader()->newInstanceWithId(1)
					->setLibelle('La musique'),
				Class_ArticleCategorie::getLoader()->newInstanceWithId(2)
					->setLibelle('Les dédicaces')
			))
			->getWrapper()->beStrict();
		$this->html = $helper->getBoite();
	}


	/** @test */
	public function shouldRenderCategoriesSelectorWithThreeOptions() {
		$this->assertQueryCount($this->html,
															'//select[@name="select_id_categorie"]/option', 3);
	}


	/** @test */
	public function shouldRenderCategoriesSelectorForIdOne() {
		$this->assertXpath($this->html,
										'//select[@name="select_id_categorie"]/option[@value="1"]');
	}


	/** @test */
	public function shouldRenderCategoriesSelectorForIdTwo() {
		$this->assertXpath($this->html,
										'//select[@name="select_id_categorie"]/option[@value="2"]');
	}


	/** @test */
	public function shouldeRenderCategoriesSelectorForAll() {
		$this->assertXpath($this->html,
										'//select[@name="select_id_categorie"]/option[@value="all"]');
	}

	/** @test */
	function prochainsRendezVousShouldNotBeVisible() {
		$this->assertNotXPathContentContains($this->html, '//p', 'Prochains rendez-vous');
	}
}




class CalendarOnJanuaryTest extends CalendarViewHelperTestCase {
	public function setUp() {
		parent::setUp();

		$this->nanook2
			->setEventsDebut('2011-12-25')
			->setEventsFin('2012-01-01');


		$this->opac4
			->setEventsDebut('2012-01-05')
			->setEventsFin('2012-03-01');


		$params = array('division' => '2',
										'type_module' => 'CALENDAR',
										'preferences' => array('titre' => 'Concerts !',
																					 'rss_avis' => '1',
																					 'id_categorie' => 'kikou',
																					 'display_cat_select' => '1',
																					 'display_event_info' => 'zork',
																					 'display_date' => '2012-01',
																					 'display_next_event' => '1'));
		$helper = new ZendAfi_View_Helper_Accueil_Calendar(2, $params);


		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array(
									 'display_order' => 'EventDebut',
									 'id_categorie' => '',
									 'event_date' => '2012-01',
									 'events_only' => true,
									 'published' => false))
			->answers(array($this->nanook2, $this->opac4, $this->amber))
			->getWrapper()
			->beStrict();

		$this->html = $helper->getBoite();
	}


	/** @test */
	function shouldDisplayCurrentMonth() {
		$this->assertXPathContentContains($this->html,
																			'//td[@class="calendar_title_month"]/a',
																			utf8_encode('janvier 2012'));
	}


	/** @test */
	function titleMonthPreviousLinkShouldGoToDecember2011() {
		$this->assertXPath($this->html,
					'//td[@class="calendar_title_month"]//a[1][contains(@href, "cms/calendar?date=2011-12&id_module=2&id_profil=2&select_id_categorie=all")]',
					$this->html);
	}



	/** @test */
	function calendarEventDateShouldContains5JanDot2012() {
		$this->assertXPathContentContains($this->html,
																			'//span[@class="calendar_event_date"]',
																			'Du 05 janvier au 01 mars 2012',
																			$this->html);
	}

}



?>