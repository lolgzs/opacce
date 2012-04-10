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

class CalendarTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->cfg_accueil =
			array('modules' => array('1' => array('division' => '1',
																						'type_module' => 'CALENDAR',
																						'preferences' => array('titre' => 'Rendez-vous',
																																	 'rss_avis' => false,
																																	 'id_categorie' => '12-2',
																																	 'display_cat_select' => true,
																																	 'display_event_info' => 'none'))),
						'options' => 	array());


		$this->profil_rdv = Class_Profil::getLoader()->newInstanceWithId(3)
			->setBrowser('opac')
			->setLibelle('Rendez-vous')
			->setCfgAccueil($this->cfg_accueil);

		$_SESSION["CALENDAR"] = array('DATE' => '',
																	'HTML' => array(''));

		Class_AdminVar::getLoader()->newInstanceWithId('CACHE_ACTIF')->setValeur('1');

		$nanook2 = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('Nanook 2 en prod !')
			->setEventsDebut('2011-02-17')
			->setEventsFin('2011-02-22');


		$opac4 = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('OPAC 4 en prod !')
			->setEventsDebut('2011-02-17')
			->setEventsFin('2011-02-22');

		$article_loader = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')->answers(array($nanook2))->getWrapper();
	}


	function tearDown() {
		Class_AdminVar::getLoader()->find('CACHE_ACTIF')->setValeur('0');
		parent::tearDown();
	}


	/**
	 * Test non-regression vu sur Bucarest (prenait le layout normal au lieu du layout iframe)
	 * @test
	 */
	function calendarShouldNotBeInAnIframeEventWithCacheActive() {
		Class_AdminVar::getLoader()->find('CACHE_ACTIF')->setValeur(true);
		Zend_Registry::get('cache')->clean(Zend_Cache::CLEANING_MODE_ALL);

		$module_cal = new ZendAfi_View_Helper_Accueil_Calendar(1, $this->cfg_accueil['modules']['1']);
		$cache_key = $module_cal->getCacheKey();

		$this->dispatch('/cms/calendar/?id_profil=3&id_module=1&cachekey='.$cache_key);
		// ça plantait lors de la réutilisation du cache, donc 2 dispatchs
		$this->bootstrap();
		$this->dispatch('/cms/calendar/?id_profil=3&id_module=1&cachekey='.$cache_key);
		$this->assertNotXPath('//div[@id="site_web_wrapper"]');
	}


	/** @test */
	function withSelectIdCatShouldSelectRightCategorie() {
		$this->dispatch('/cms/calendar/?id_profil=3&id_module=1&select_id_categorie=2');
	}


	/** @test */
	function withLocaleEnMonthShouldBeFebruary() {
		$this->dispatch('/cms/calendar/?id_profil=3&id_module=1&select_id_categorie=2&date=2011-02&language=en');
		$this->assertXPathContentContains('//td[@class="calendar_title_month"]/a', "february", $this->_response->getBody()); 
	}

}

?>
