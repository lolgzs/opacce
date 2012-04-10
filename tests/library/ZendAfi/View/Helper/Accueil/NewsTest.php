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


abstract class NewsHelperTestCase extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		$this->article_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article');
		Class_Profil::setCurrentProfil(Class_Profil::getLoader()->newInstanceWithId(5));


		Zend_Registry::get('translate')->setLocale('fr');

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('');

		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur(0);
	}
}


class EmptyNewsHelperTest extends NewsHelperTestCase {
	public function setUp() {
		parent::setUp();

		$this->article_wrapper
			->whenCalled('getArticlesByPreferences')
			->answers(array());


		$params = array('type_module' => 'NEWS',
										'division' => 2,
										'preferences' => array('titre' => 'Rien à dire',
																					 'rss_avis' => true));		

		$helper = new ZendAfi_View_Helper_Accueil_News(12, $params);
		$this->html = $helper->getBoite();
	}


	/** @test */
	function getArticlesByPreferencesParamsShouldNotContainLangue() {
		$prefs = $this->article_wrapper->getFirstAttributeForLastCallOn('getArticlesByPreferences');
		$this->assertFalse(array_key_exists('langue', $prefs));
	}


	/** @test */
	public function titreShouldBeRienADire() {
		$this->assertXPathContentContains($this->html,
																			'//div[@class="titre"]//h1', 
																			utf8_encode('Rien à dire'));
	}


	/** @test */
	public function titreShouldLinkToModuleView() {
		$this->assertXPath($this->html,
											 '//div[@class="titre"]//a[contains(@href, "/cms/articleviewselection/id_module/12")]');
	}


	/** @test */
	public function contenuShouldBeEmpty() {
		$this->assertNotXPath($this->html,
													'//div[@class="contenu"]//div//div');
	}


	/** @test */
	function contentClassShouldBeNews12AndNews() {
		$this->assertXPath($this->html,
											 '//div[@class="contenu"]//div[@class="news-12 news"]');
	}
	
	
	/** @test */
	public function rssLinkShouldBePresent() {
		$this->assertXPath($this->html,
											 '//div[@class="rss"]//a[contains(@href, "/cms/rss?id_module=12&id_profil=5&language=fr")]');
	}
}



abstract class NewsHelperWithThreeArticlesTestCase extends NewsHelperTestCase {
	public function setUp() {
		parent::setUp();

		$this->fete_pomme = $this->article_wrapper
			->newInstanceWithId(34)
			->setTitre('Fête de la pomme')
			->setContenu('à Boussy');


		$this->fete_poire = $this->article_wrapper
			->newInstanceWithId(56)
			->setTitre('Fête de la poire')
			->setContenu('venez nombreux !')
			->setDescription('à Chavanod')
			->setTraductions(array($this->article_wrapper
														 ->newInstanceWithId(23)
														 ->setParentId(56)
														 ->setLangue('ro')
														 ->setTitre('Pear sărbătoare')
														 ->setContenu('Vino !')
														 ->setDescription('la Chavanod')));

		$this->fete_nashi = $this->article_wrapper
			->newInstanceWithId(78)
			->setTitre('Fête du nashi')
			->setCacherTitre(true)
			->setContenu('à Beijing');

		$this->article_wrapper
			->whenCalled('getArticlesByPreferences')
			->answers(array($this->fete_pomme, 
											$this->fete_poire, 
											$this->fete_nashi));
	}
}



class NewsHelperWithThreeArticlesAndRomaniaRequestedTest extends NewsHelperWithThreeArticlesTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('ro');

		$params = array('type_module' => 'NEWS',
										'division' => 2,
										'preferences' => array('titre' => 'En Automne',
																					 'rss_avis' => false,
																					 'display_titles_only' => false));
		
		
		
		Zend_Registry::get('translate')->setLocale('ro');

		$helper = new ZendAfi_View_Helper_Accueil_News(12, $params);
		$this->html = $helper->getBoite();
	}



	public function tearDown() {
		Zend_Registry::get('translate')->setLocale('fr');
		parent::tearDown();
	}


	/** @test */
	function getArticlesByPreferencesParamsShouldContainLangueRO() {
		$prefs = $this->article_wrapper->getFirstAttributeForLastCallOn('getArticlesByPreferences');
		$this->assertEquals('ro', $prefs['langue']);
	}


	/** @test */
	function editArticleLinksShouldNotBePresent() {
		$this->assertNotXPath($this->html, 'img[@class="article_edit"]');
	}


	/** @test */
	public function articleFeteDeLaPoireTranslatedShouldBePresent() {
		$this->assertXPathContentContains($this->html,
											'//div[@class="auto_resize article_full"]//a[contains(@href, "/cms/articleview/id/23")]',
											utf8_encode('Pear sărbătoare'));

		$this->assertXPathContentContains($this->html,
																			'//div[@class="auto_resize article_full"]',
																			utf8_encode('la Chavanod'));
	}


	/** @test */
	public function articleFeteDeLaPommeShouldNotBePresent() {
		$this->assertNotXPathContentContains($this->html, '//div',	utf8_encode('Fête de la pomme'));
	}
}




class NewsHelperWithThreeArticlesWorkflowActivatedUserAdminTest extends NewsHelperWithThreeArticlesTestCase {
	public function setUp() {
		parent::setUp();

		$this->login(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL);

		Class_AdminVar::getLoader()
			->newInstanceWithId('WORKFLOW')
			->setValeur(1);

		$this->fete_pomme->beValidated();
		$this->fete_poire->beValidated();
		$this->fete_nashi->beValidationPending();

		$params = array('type_module' => 'NEWS',
										'division' => 2,
										'preferences' => array('titre' => 'En Hiver',
																					 'rss_avis' => false,
																					 'display_titles_only' => false,
																					 'style_liste' => 'diaporama',
																					 'op_largeur_img' => 200,
																					 'op_transition' => 'zork'));
		
		$helper = new ZendAfi_View_Helper_Accueil_News(12, $params);
		$this->html = $helper->getBoite();
	}

	/** @test */
	function getArticlesByPreferencesParamsShouldContainStatusValidated() {
		$prefs = $this->article_wrapper->getFirstAttributeForLastCallOn('getArticlesByPreferences');
		$this->assertEquals(Class_Article::STATUS_VALIDATED, $prefs['status']);
	}


	/** @test */
	function getArticlesByPreferencesParamsShouldNotContainLangue() {
		$prefs = $this->article_wrapper->getFirstAttributeForLastCallOn('getArticlesByPreferences');
		$this->assertFalse(array_isset('langue',$prefs));
	}


	/** @test */
	public function articleFeteDeLaPoireTranslatedShouldBePresent() {
		$this->assertXPathContentContains($this->html,'//div', utf8_encode('Fête de la poire'));
	}

	/** @test */
	public function articleFeteDeLaPommeShouldBePresent() {
		$this->assertXPathContentContains($this->html, '//div',	utf8_encode('Fête de la pomme'));
	}


	/** @test */
	public function articleFeteDuNashiShouldNotBePresent() {
		$this->assertNotXPathContentContains($this->html, '//div',	utf8_encode('à Beijing'));
	}


	/** @test */
	function editArticleLinksShouldNotBePresert() {
		$this->assertXPath($this->html, 
											 '//a[contains(@href, "admin/cms/newsedit/id/34")]//img[@class="article_edit"]');
	}


	/** @test */
	function separatorShouldNotBeVisible() {
		$this->assertNotXPath($this->html,
													'//div[@class="article_full_separator"]');
	}

	/** @test */
	function diaporamaTransitionShouldFallbackToFade() {
		$this->assertContains('"fx":"fade"', 
													Class_ScriptLoader::getInstance()->html());
	}
}


class NewsHelperWithThreeArticlesTest extends NewsHelperWithThreeArticlesTestCase {
	public function setUp() {
		parent::setUp();
		$params = array('type_module' => 'NEWS',
										'division' => 2,
										'preferences' => array('titre' => 'En Automne',
																					 'rss_avis' => false,
																					 'display_titles_only' => false));
		
		
		

		$helper = new ZendAfi_View_Helper_Accueil_News(12, $params);
		$this->html = $helper->getBoite();
	}


	/** @test */
	public function titreShouldBeEnAutomne() {
		$this->assertXPathContentContains($this->html,
																			'//div[@class="titre"]//h1', 
																			utf8_encode('En Automne'));
	}


	/** @test */
	public function rssLinkShouldNotBePresent() {
		$this->assertNotXPath($this->html, '//div[@class="rss"]//a');
	}


	/** @test */
	public function articleFeteDeLaPommeShouldBePresent() {
		$this->assertXPathContentContains($this->html,
																			'//div[@class="auto_resize article_full"]//a[contains(@href, "/cms/articleview/id/34")]',
																			utf8_encode('Fête de la pomme'));

		$this->assertXPathContentContains($this->html,
																			'//div[@class="auto_resize article_full"]',
																			utf8_encode('à Boussy'));
	} 


	/** @test */
	public function articleFeteDeLaPoireShouldBePresent() {
		$this->assertXPathContentContains($this->html,
											'//div[@class="auto_resize article_full"]//a[contains(@href, "/cms/articleview/id/56")]',
											utf8_encode('Fête de la poire'));

		$this->assertXPathContentContains($this->html,
																			'//div[@class="auto_resize article_full"]',
																			utf8_encode('à Chavanod'));
	} 


	/** @test */
	public function articleFeteDuNashiShouldBePresentWithoutTitle() {
		$this->assertNotXPathContentContains($this->html,
											'//div[@class="auto_resize article_full"]//a',
											utf8_encode('Fête du nashi'));

		$this->assertXPathContentContains($this->html,
																			'//div[@class="auto_resize article_full"]',
																			utf8_encode('à Beijing'));
	}


	/** @test */
	function separatorShouldBeVisible() {
		$this->assertXPath($this->html,
											 '//div[@class="article_full_separator"]',
											 $this->html);
	}
}



class NewsHelperWithThreeArticlesDisplayTitleOnlyTest extends NewsHelperWithThreeArticlesTestCase {
	public function setUp() {
		parent::setUp();

		$params = array('type_module' => 'NEWS',
										'division' => 2,
										'preferences' => array('titre' => 'En Automne',
																					 'rss_avis' => false,
																					 'display_titles_only' => true));
		
		
		

		$helper = new ZendAfi_View_Helper_Accueil_News(12, $params);
		$this->html = $helper->getBoite();
	}


	/** @test */
	public function articleFeteDeLaPoireOnlyTitleShouldBePresent() {
		$this->assertXPathContentContains($this->html,
											'//div[@class="auto_resize article_title_only"]//a[contains(@href, "/cms/articleview/id/56")]',
											utf8_encode('Fête de la poire'));
	}


	/** @test */
	public function articleFeteDuNashiOnlyTitleShouldBePresent() {
		$this->assertXPathContentContains($this->html,
											'//div[@class="auto_resize article_title_only"]//a[contains(@href, "/cms/articleview/id/78")]',
											utf8_encode('Fête du nashi'));
	}

	/** @test */
	function separatorTitleShouldBeVisible() {
		$this->assertXPath($this->html,
											 '//div[@class="article_only_title_separator"]',
											 $this->html);
	}
}





?>