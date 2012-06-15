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
require_once 'AdminAbstractControllerTestCase.php';

abstract class CmsControllerTestCase extends Admin_AbstractControllerTestCase {
	/** @var Class_Article */
	protected $concert;


	protected function _loginHook($account) {
		$account->ROLE = "admin_bib";
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
	}


	public function setUp() {
		parent::setUp();

		$cat_a_la_une = Class_ArticleCategorie::getLoader()
                			->newInstanceWithId(23)
                 			->setLibelle('A la Une');

		$this->cat_evenements = Class_ArticleCategorie::getLoader()
												->newInstanceWithId(34)
												->setLibelle('Evènements')
												->setParentCategorie($cat_a_la_une)
												->setSousCategories(array());

		$cat_a_la_une->setSousCategories(array($this->cat_evenements));

		$this->concert = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setCategorie($this->cat_evenements)
			->setTitre('Erik Truffaz en concert')
			->setDescription('Venez nombreux ici: <img src="/afi-opac3/images/bonlieu.jpg" />')
			->setContenu('à Bonlieu. <img src="/afi-opac3/images/truffaz.jpg" />')
			->setDebut('2011-03-20')
			->setFin('2011-03-28')
			->setEventsDebut('2011-03-27')
			->setEventsFin('2011-03-28')
			->setCacherTitre(1)
			->setLangue('fr')
			->setTags('concert;jazz')
			->setAvis(true)
			->setIndexation(false)
			->setDateCreation('2010-12-25');


		$this->article_wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Article')
			->whenCalled('save')
			->answers(true)
			->getWrapper();


		$this->categorie_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
			->whenCalled('delete')
			->answers(true)
			->getWrapper()
			->whenCalled('save')
			->answers(true)
			->getWrapper();


		$this->annecy = Class_Bib::getLoader()
			->newInstanceWithId(1)
			->setIdZone(4)
			->setLibelle('Annecy')
			->setArticleCategories(array($cat_a_la_une));

		$this->cat_evenements->setBib($this->annecy);


		$this->cran_gevrier = Class_Bib::getLoader()
			->newInstanceWithId(3)
			->setIdZone(4)
			->setLibelle('Cran-Gevrier')
			->setArticleCategories(array());


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAllWithPortail')
			->answers(array(Class_Bib::getLoader()->getPortail(), 
											$this->annecy,
											$this->cran_gevrier));
		

		Class_Users::getLoader()->getIdentity()->setIdSite(1);


		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('en;ro');
	}


	function assertInputValueEquals($input_name, $value) {
		$this->assertXPath(sprintf('//form[@id="news_form"]//input[@name="%s"][@value="%s"]',
															 $input_name, $value));
	}


	function assertCheckboxIsChecked($input_name) {
		$this->assertXPath(sprintf('//form[@id="news_form"]//input[@type="checkbox"][@name="%s"][@checked="checked"]',
															 $input_name));
	}


	function assertCheckboxIsNotChecked($input_name) {
		$this->assertXPath(sprintf('//form[@id="news_form"]//input[@type="checkbox"][@name="%s"]', $input_name));
	}


	function assertTextAreaContains($name, $content) {
		$this->assertXPathContentContains(sprintf('//form[@id="news_form"]//textarea[@name="%s"]', $name),
																			$content);
	}


	function assertNotTextAreaContains($name, $content) {
		$this->assertNotXPathContentContains(sprintf('//form[@id="news_form"]//textarea[@name="%s"]', $name),
																				 $content);
	}
}



class CmsControllerArticleEditWithoutLanguesTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('');

		$this->dispatch('/admin/cms/newsedit/id/4');
	}


	/** @test */
	function traductionsSelectorShouldNotBeVisible() {
		$this->assertNotXPath('//div[@class="traduction_navigator"]');
	}
}


class CmsControllerArticleConcertAsAdminPortailEditActionTest extends CmsControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "admin_portail";
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
	}

	public function setUp() {
		parent::setUp();
		Class_Users::getLoader()
			->getIdentity()
			->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL);

		$this->dispatch('/admin/cms/newsedit/id/4');
	}


	/** @test */
	function categorieSelectShouldContainsOptGroupAnnecy() {
		$this->assertXPath('//select[@name="id_cat"]//optgroup[@label="Annecy"]');
	}


	/** @test */
	function categorieSelectShouldContainsOptGroupPortail() {
		$this->assertXPath('//select[@name="id_cat"]//optgroup[@label="Portail"]');
	}


	/** @test */
	function categorieSelectShouldContainsOptGroupCranGevrier() {
		$this->assertXPath('//select[@name="id_cat"]//optgroup[@label="Cran-Gevrier"]');
	}
}


class CmsControllerArticleWithoutCategoryAddActionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/cms/newsadd/id_cat/99999');
	}

	/** @test */
	function answerShouldRedirectToAdminCms() {
		$this->assertRedirectTo('/admin/cms', $this->getResponseLocation());
	}
}



class CmsControllerArticleConcertEditActionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/cms/newsedit/id/4');
	}


	/** @test */
	function linkTraductionEnShouldBeFlagEn() {
		$this->assertXPath('//a[contains(@href, "/admin/cms/newsedit/id/4/lang/en")]//img[contains(@src, "en.png")]');
	}


	/** @test */
	function linkTraductionFrShouldBeSelectedFlagFr() {
		$this->assertXPath('//a[contains(@href, "/admin/cms/newsedit/id/4/lang/fr")]//img[contains(@class,"selected")][contains(@src, "fr.png")]');
	}


	/** @test */
	function pageTitleShouldBeModifierUnArticle() {
		$this->assertXPathContentContains('//h1', 'Modifier un article');
	}


	/** @test */
	function titreShouldBeErikTruffazEnConcert() {
		$this->assertInputValueEquals('titre', "Erik Truffaz en concert");
	}


	/** @test */
	function titreShouldAccept200CharsAsDefinedInClassArticle() {
		$this->assertXPath('//input[@name="titre"][@maxlength="200"]');
	}


	/** @test */
	function eventDebutShouldBe27_03_2011() {
		$this->assertInputValueEquals('events_debut', '27/03/2011');
	}


	/** @test */
	function eventFinShouldBe28_03_2011() {
		$this->assertInputValueEquals('events_fin', '28/03/2011');
	}


	/** @test */
	function debutShouldBe20_03_2011() {
		$this->assertInputValueEquals('debut', '20/03/2011');
	}


	/** @test */
	function finShouldBe28_03_2011() {
		$this->assertInputValueEquals('fin', '28/03/2011');
	}


	/** @test */
	function formActionShouldContainsEditUrl() {
		$this->assertXPath("//form[@id='news_form'][@method='post'][contains(@action, 'admin/cms/newsedit/id/4')]");
	}


	/** @test */
	function cacherTitreShouldBeChecked() {
		$this->assertCheckboxIsChecked('cacher_titre');
	}


	/** @test */
	function contenuShouldContainsABonlieu() {
		$this->assertTextAreaContains('contenu', 'à Bonlieu');
	}


	/** @test */
	function contenuShouldContainsAndFixedImageURL() {
		$this->assertTextAreaContains('contenu',
																	'http://localhost/afi-opac3/images/truffaz.jpg');
	}



	/** @test */
	function descriptionShouldContainsVenezNombreux() {
		$this->assertTextAreaContains('description',
																	'Venez nombreux ici');
	}

	/** @test */
	function descriptionShouldContainsAndFixedImageURL() {
		$this->assertTextAreaContains('description',
																	'http://localhost/afi-opac3/images/bonlieu.jpg');
	}


	/** @test */
	function tagsShouldContainsConcertJazz() {
		$this->assertTextAreaContains('tags', 'concert;jazz');
	}

	/** @test */
	function avisShouldBeChecked() {
		$this->assertCheckboxIsChecked('avis');
	}


	/** @test */
	function indexationShouldNotBeChecked() {
		$this->assertCheckboxIsNotChecked('indexation');
	}


	/** @test */
	function categorieSelectShouldHaveEvenementsSelected() {
		$this->assertXPathContentContains('//select[@name="id_cat"]//option[@selected="selected"]',
																			'Evènements',
																			$this->_response->getBody());
	}


	/** @test */
	function categorieSelectShouldContainsOptGroupAnnecy() {
		$this->assertXPath('//select[@name="id_cat"]//optgroup[@label="Annecy"]');
	}


	/** @test */
	function categorieSelectShouldNotContainsOptGroupPortail() {
		$this->assertNotXPath('//select[@name="id_cat"]//optgroup[@label="Portail"]');
	}


	/** @test */
	function categorieSelectShouldNotContainsOptGroupCranGevrier() {
		$this->assertNotXPath('//select[@name="id_cat"]//optgroup[@label="Cran-Gevrier"]');
	}


	/** @test */
	function permalinkShouldContainsArticleUrl() {
		$this->assertXPath('//div[@id="permalink"]//input[contains(@value, "cms/articleview/id/4")]');
	}


	/** @test */
	function previewShouldContainsArticleUrl() {
		$this->assertXPath('//a[@rel="prettyPhoto"][contains(@href, "/cms/articleview/id/4")]',
											 $this->_response->getBody());
	}

	/** @test */
	function previewTitleShouldBeVisualisationDeLArticle() {
		$this->assertXPath('//a[@rel="prettyPhoto"][contains(@title, "Visualisation de l\'article: Erik Truffaz en concert")]');
	}
}

class CmsControllerArticleConcertEditArticleWithQuotesActionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->concert->setTitre('"Erik Truffaz" en concert');
	}

	/** @test */
	function titleShouldBeEscaped() {
		$this->dispatch('/admin/cms/newsedit/id/4');
		$this->assertContains('value="&quot;Erik Truffaz&quot; en concert"', $this->_response->getBody());
	}

	/** @test */
	function traductionTitleShouldBeEscaped() {
		$this->dispatch('/admin/cms/newsedit/id/4/lang/ro');
		$this->assertContains('value="&quot;Erik Truffaz&quot; en concert"', $this->_response->getBody());
	}
}



class CmsControllerArticleConcertEditActionPostTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('titre' => 'Erik Truffaz - Ladyland quartet en concert',
									'id_cat' => 34,
									'debut' => '01/03/2011',
									'fin' => '26/03/2011',
									'events_debut' => '02/03/2011',
									'events_fin' => '05/03/2011',
									'contenu' => 'Ici: <img src="../../images/bonlieu.jpg" />',
									'description' => 'Affiche: <img src="http://localhost' . BASE_URL . '/images/concert.jpg" />');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/cms/newsedit/id/4');
	}


	/** @test */
	function titreShouldBeUpdatedToErikTruffazLadyland() {
		$this->assertEquals('Erik Truffaz - Ladyland quartet en concert',
												$this->concert->getTitre());
	}


	/** @test */
	function concertShouldHaveBeenSaved() {
		$this->assertTrue($this->article_wrapper->methodHasBeenCalledWithParams('save', array($this->concert)));
	}


	/** @test */
	function dateDebutShouldBe2011_03_01() {
		$this->assertContains('2011-03-01', $this->concert->getDebut());
	}


	/** @test */
	function dateDebutShouldBe2011_03_26() {
		$this->assertContains('2011-03-26', $this->concert->getFin());
	}


	/** @test */
	function eventDebutShouldBe2011_03_02() {
		$this->assertContains('2011-03-02', $this->concert->getEventsDebut());
	}


	/** @test */
	function eventFinShouldBe2011_03_05() {
		$this->assertContains('2011-03-05', $this->concert->getEventsFin());
	}


	/** @test */
	function dateMAJShouldBeToday() {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),
													$this->concert->getDateMaj());
	}


	/** @test */
	function dateCreationShouldBe2010_12_25() {
		$today = new Zend_Date();
		$this->assertContains('2010-12-25',
													$this->concert->getDateCreation());
	}


	/** @test */
	function contenuShouldHaveFixedImageURL() {
		$this->assertEquals('Ici: <img src="' . BASE_URL . '/images/bonlieu.jpg" />',
												$this->concert->getContenu());
	}


	/** @test */
	function descriptionShouldHaveFixedImageURL() {
		$this->assertEquals('Affiche: <img src="' . BASE_URL . '/images/concert.jpg" />',
												$this->concert->getDescription());
	}


	/** @test */
	function derniereModificationShouldBeNow() {
		$date = new Zend_Date();
		$this->assertXPathContentContains('//div',
																			'Dernière modification : '.$date->toString('d MMMM yyyy'),
																			$this->_response->getBody());
	}


	/** @test */
	function categorieShouldBeEvenements() {
		$this->assertEquals($this->cat_evenements, $this->concert->getCategorie());
	}
}



class CmsControllerArticleConcertEditActionPostWithErrorTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('titre' => '',
									'debut' => '01/04/2011',
									'fin' => '26/03/2011',
									'events_debut' => '',
									'events_fin' => '01/04/2011',
									'description' => '',
									'contenu' => '');


		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/cms/newsedit/id/4');
	}


	/** @test */
	function actionShouldBeEdit() {
		$this->assertAction('newsedit');
	}


	/** @test */
	function shouldNotRedirect() {
		$this->assertNotRedirect();
	}


	/** @test */
	function loaderSaveShouldNotHaveBeenCalled() {
		$this->assertFalse($this->article_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	function errorShouldContainsDateDebutError() {
		$this->assertXPathContentContains('//span[@class="error"]', "La date de début de publication doit être plus récente que la date de fin");
	}


	/** @test */
	function errorShouldContainsVousDevezCompleterLeChampTitre() {
		$this->assertXPathContentContains('//span[@class="error"]', "Vous devez compléter le champ 'Titre'");
	}

}

class CmsControllerArticleAddActionPostTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		$data = array('titre' => 'Marcus Miller en concert !',
									'debut' => '',
									'fin' => '',
									'events_debut' => '',
									'events_fin' => '',
									'description' => '',
									'id_cat' => 23,
									'contenu' => 'Youpi!!');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/cms/newsadd/id_cat/23');

		$this->new_article = $this->article_wrapper->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	function contenuShouldEqualsYoupii() {
		$this->assertEquals('Youpi!!', $this->new_article->getContenu());
	}


	/** @test */
	function redirectToNewsEditNewArticle() {
		$this->assertRedirectTo('/admin/cms/newsedit/id/0', $this->getResponseLocation());
	}


	/** @test */
	function dateMAJShouldBeToday() {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),
													$this->new_article->getDateMaj());
	}


	/** @test */
	function dateCreationShouldBeToday() {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),
													$this->new_article->getDateCreation());
	}


	/** @test */
	function categorieShouldBeALaUne() {
		$this->assertEquals('A la Une', $this->new_article->getCategorie()->getLibelle());
	}
}



class CmsControllerNewsAddActionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/cms/newsadd/id_cat/23');
	}

	/** @test */
	public function formActionShouldContainsAddUrl() {
		$this->assertXPath("//form[@id='news_form'][@method='post'][contains(@action, 'admin/cms/newsadd/id_cat/23')]");
	}

	/** @test */
	public function pageTitleShouldBeAjouterUnArticle() {
		$this->assertXPathContentContains('//h1', 'Ajouter un article');
	}

	/** @test */
	public function titreCacheOptionShouldBePresent() {
		$this->assertXPathContentContains('//form[@id="news_form"]//tr[2]//td[1]',
																			'Titre caché');
		$this->assertXPath('//form[@id="news_form"]//tr[2]//td[2]//input[@name="cacher_titre"]');

	}

	/** @test */
	public function cacherTitreShouldNotBeChecked() {
		$this->assertCheckboxIsNotChecked('cacher_titre');
	}

	/** @test */
	public function finShouldBeEmpty() {
		$this->assertInputValueEquals('fin', '');
	}

	/** @test */
	public function avisShouldNotBeChecked() {
		$this->assertCheckboxIsNotChecked('avis');
	}

	/** @test */
	public function indexationShouldBeChecked() {
		$this->assertCheckboxIsChecked('indexation');
	}

	/** @test */
	public function categorieSelectShouldHaveALaUneSelected() {
		$this->assertXPathContentContains('//select[@name="id_cat"]//option[@selected="selected"]',
																			'A la Une', 
																			$this->_response->getBody());
	}

}



class CmsControllerNewsAddActionWithoutWorkflowTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('');
		$this->dispatch('admin/cms/newsadd/id_cat/23');
	}

	/** @test */
	public function workflowInputsShouldNotAppear() {
		$this->assertNotXpath('//input[@name="status"]');
	}
}


class CmsControllerArticleVisibilityTest extends CmsControllerTestCase {
	/** @test */
	function makeVisibleShouldRedirectToCategorieEvenements() {
		$this->dispatch('admin/cms/makevisible/id/4');
		$this->assertRedirectTo('/admin/cms/index/id_cat/34');
		$this->assertTrue($this->concert->isVisible());
	}


	/** @test */
	function makeInvisibleShouldRedirectToCategorieEvenements() {
		$this->dispatch('admin/cms/makeinvisible/id/4');
		$this->assertRedirectTo('/admin/cms/index/id_cat/34');
		$this->assertFalse($this->concert->isVisible());
	}
}


class CmsControllerNewsAddActionPostWithoutWorkflowTest extends CmsControllerTestCase {
	/** @var Class_Article */
	protected $_article;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('0');

		$data = array(
			'titre' => 'Katsuhiro Otomo en dédicace !',
			'debut' => '',
			'fin' => '',
			'events_debut' => '',
			'events_fin' => '',
			'description' => '',
			'id_cat' => 23,
			'contenu' => 'Ne manquez pas cet évènement.',
			'status' => Class_Article::STATUS_DRAFT,
		);

		$this->getRequest()->setMethod('POST')
												->setPost($data);

		$this->dispatch('/admin/cms/newsadd/id_cat/23');

		$this->_article = $this->article_wrapper->getFirstAttributeForLastCallOn('save');
	}

	/** @test */
	public function statusShouldNotBeUpdatable() {
		$this->assertEquals(3, $this->_article->getStatus());
	}

}

class CmsControllerNewsAddActionPostWithWorkflowTest extends CmsControllerTestCase {
	/** @var Class_Article */
	protected $_article;

	/** @var array */
	protected $_basePostDatas = array(
		'titre' => 'Katsuhiro Otomo en dédicace !',
		'debut' => '',
		'fin' => '',
		'events_debut' => '',
		'events_fin' => '',
		'description' => '',
		'id_cat' => 23,
		'contenu' => 'Ne manquez pas cet évènement.',
	);

	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
	}

	/** @test */
	public function statusShouldBeUpdated() {
		$data = $this->_basePostDatas;
		$data['status'] = Class_Article::STATUS_VALIDATED;

		$this->getRequest()->setMethod('POST')->setPost($data);
		$this->dispatch('/admin/cms/newsadd/id_cat/23');

		$this->_article = $this->article_wrapper->getFirstAttributeForLastCallOn('save');

		$this->assertEquals(Class_Article::STATUS_VALIDATED, $this->_article->getStatus());
	}

	/** @test */
	public function withUnknownStatusShouldNotBeUpdated() {
		$data = $this->_basePostDatas;
		$data['status'] = 999999999;

		$this->getRequest()->setMethod('POST')->setPost($data);
		$this->dispatch('/admin/cms/newsadd/id_cat/23');

		$this->_article = $this->article_wrapper->getFirstAttributeForLastCallOn('save');

		$this->assertEquals(Class_Article::STATUS_DRAFT, $this->_article->getStatus());
	}
}

class CmsControllerNewsAddActionWithWorkflowTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		Class_Users::getLoader()->getIdentity()->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::MODO_BIB);
		$this->dispatch('admin/cms/newsadd/id_cat/23');
	}

	/** @test */
	public function workflowInputsShouldAppear() {
		$this->assertXpathCount('//input[@name="status"]', 4);
	}

	/** @test */
	public function withUserRedacteurBibStatusValideShouldBeDisabled() {
		$this->assertXpath('//input[@name="status"][@value="3"][@disabled="disabled"]');
	}

	/** @test */
	public function withUserAdminStatusValideShouldNotBeDisabled() {
		Class_Users::getLoader()->getIdentity()->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB);
		$this->bootstrap();
		$this->dispatch('admin/cms/newsadd/id_cat/23');
		$this->assertNotXpath('//input[@name="status"][@value="3"][@disabled="disabled"]');
	}
}

class CmsControllerArticleConcertEditActionWithoutWorkflowTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('0');
		$this->dispatch('/admin/cms/newsedit/id/4');
	}

	/** @test */
	public function workflowInputsShouldNotAppear() {
		$this->assertNotXpath('//input[@name="status"]');
	}
}

class CmsControllerArticleConcertEditActionWithWorkflowTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Users::getLoader()->getIdentity()->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::MODO_PORTAIL);
		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')->setValeur('1');
		$this->concert->setStatus(Class_Article::STATUS_VALIDATED);
		$this->dispatch('/admin/cms/newsedit/id/4');
	}

	/** @test */
	public function workflowInputsShouldAppear() {
		$this->assertXpathCount('//input[@name="status"]', 4);
	}

	/** @test */
	public function checkedRadioShouldBeValidatedStatus() {
		$this->assertXpath(sprintf('//input[@name="status"][@value="%d"][@checked="checked"]', Class_Article::STATUS_VALIDATED));
	}

	/** @test */
	public function withUserRedacteurPortailStatusValideShouldBeDisabled() {
		$this->assertXpath('//input[@name="status"][@value="3"][@disabled="disabled"]');
	}
}

class CmsControllerArticleAddActionInvalidDatePostTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('titre' => '',
									'debut' => '',
									'fin' => '',
									'events_debut' => '',
									'events_fin' => '',
									'description' => '',
									'contenu' => '');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/cms/newsadd/id_cat/23');
	}


	/** @test */
	function actionShouldBeAdd() {
		$this->assertAction('newsadd');
	}


	/** @test */
	function shouldNotRedirect() {
		$this->assertNotRedirect();
	}


	/** @test */
	function loaderSaveShouldNotHaveBeenCalled() {
		$this->assertFalse($this->article_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	function errorShouldContainsVousDevezCompleterLeChampContenu() {
		$this->assertXPathContentContains('//span[@class="error"]', "Vous devez compléter le champ 'Contenu'");
	}
}


class CmsControllerArticleIndexActionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$_SESSION["MENU_DEPLOY"] = array("CMS" => array(34, 23));
		$this->dispatch('admin/cms');
	}

	/** @test */
	function actionShouldBeIndex() {
		$this->assertController('cms');
		$this->assertAction('index');
	}
}


class CmsControllerArticleTraductionFREditTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/cms/newsedit/id/4/lang/fr');
	}


	/** @test */
	function pageTitleShouldBeModifierUnArticle() {
		$this->assertXPathContentContains('//h1', 'Modifier un article');
	}
}



class CmsControllerArticleWithUnknownLangueTraductionEditTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Article::getLoader()
			->newInstanceWithId(8)
			->setIdCat(23)
			->setLangue(null)
			->setTitre('Langue non spécifiée');

		$this->dispatch('/admin/cms/newsedit/id/8/lang/fr');
	}


	/** @test */
	function pageTitleShouldBeModifierUnArticle() {
		$this->assertXPathContentContains('//h1', 'Modifier un article');
	}


	/** @test */
	function titreShouldBeLangueNonSpecifiee() {
		$this->assertInputValueEquals('titre', "Langue non spécifiée");
	}

}



class CmsControllerArticleNewTraductionEditTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/cms/newsedit/id/4/lang/ro');
	}


	/** @test */
	function actionShouldBeIndex() {
		$this->assertController('cms');
		$this->assertAction('newsedit');
	}


	/** @test */
	function pageTitleShouldBeTraduireUnArticle() {
		$this->assertXPathContentContains('//h1', 'Traduire un article');
	}



	/** @test */
	function formActionShouldContainsEditUrl() {
		$this->assertXPath("//form[@id='news_form'][@method='post'][contains(@action, 'admin/cms/newsedit/id/4/lang/ro')]");
	}


	/** @test */
	function contenuShouldContainsOriginal() {
		$this->assertTextAreaContains('contenu', 'à Bonlieu');
	}


	/** @test */
	function romaniaFlagShouldBeVisibleAndSelected() {
		$this->assertXPath('//img[contains(@class, "selected")][contains(@src, "ro.png")]');
	}


	/** @test */
	function titreOriginalShouldBeVisible() {
		$this->assertXPathContentContains('//div[@class="art_original"]', 'Erik Truffaz en concert');
	}


	/** @test */
	function contenuOriginalShouldContainsABonlieu() {
		$this->assertXPathContentContains('//div[@class="art_original"]', 'à Bonlieu');
	}


	/** @test */
	function descriptionOriginalShouldContainsVenezNombreux() {
		$this->assertXPath('//div[@class="art_original"][contains(text(), "Venez nombreux ici: ")]//img[@src="/afi-opac3/images/bonlieu.jpg"]');
	}
}



class CmsControllerArticleTraductionEditWithoutDescriptionTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->concert->setDescription('');
		$this->dispatch('/admin/cms/newsedit/id/4/lang/ro');
	}


	/** @test */
	function descriptionOriginalShouldNotBeVisible() {
		$this->assertNotXPathContentContains('//div[@class="art_original"]', 'Venez nombreux ici: <img src="/afi-opac3/images/bonlieu.jpg" />');
	}
}



class CmsControllerArticleExistingTraductionEditTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->concert->setTraductions(array(Class_Article::getLoader()
																				 ->newInstanceWithId(41)
																				 ->setParentId(4)
																				 ->setTitre('Erik Truffaz live')
																				 ->setDescription('Waiting for you !')
																				 ->setContenu('at Bonlieu')
																				 ->setLangue('en')));

		$this->dispatch('/admin/cms/newsedit/id/4/lang/en');
	}


	/** @test */
	function contenuShouldContainsAtBonlieu() {
		$this->assertTextAreaContains('contenu', 'at Bonlieu');
	}


	/** @test */
	function contenuOriginalShouldContainsABonlieu() {
		$this->assertXPathContentContains('//div[@class="art_original"]', 'à Bonlieu');
	}


	/** @test */
	function permalinkShouldContainsArticleUrl() {
		$this->assertXPath('//div[@id="permalink"]//input[contains(@value, "cms/articleview/id/41")]');
	}


	/** @test */
	function previewShouldContainsArticleUrl() {
		$this->assertXPath('//a[@rel="prettyPhoto"][contains(@href, "cms/articleview/id/41")]');
	}
}




class CmsControllerArticleNewTraductionPostTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$data = array('titre' => 'Erik în concert',
									'description' => 'Mulţi vin',
									'contenu' => 'la Bonlieu');
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/cms/newsedit/id/4/lang/ro');

		$this->article_roumain = $this->article_wrapper->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	function concertContenuShouldNotHaveBeenChanged() {
		$this->assertEquals('Erik Truffaz en concert', $this->concert->getTitre());
	}


	/** @test */
	function titreShouldBeTranslatedOne() {
		$this->assertEquals('Erik în concert', $this->article_roumain->getTitre());
	}


	/** @test */
	function contenuShouldBeTranslatedOne() {
		$this->assertEquals('la Bonlieu', $this->article_roumain->getContenu());
	}


	/** @test */
	function descriptionShouldBeTranslatedOne() {
		$this->assertEquals('Mulţi vin', $this->article_roumain->getDescription());
	}
}



class CmsControllerDeleteArticleTest extends CmsControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->article_wrapper
			->whenCalled('delete')
			->answers(true);


		$this->dispatch('/admin/cms/delete/id/4');
	}


	/** @test */
	public function deleteShouldHaveBeenCalledOnConcert() {
		$this->deleted_art = $this->article_wrapper->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals($this->concert, $this->deleted_art);
	}


	/** @test */
	public function redirectToAdminCmsOnCategorieEvenements() {
		$this->assertRedirectTo('/admin/cms/index/id_cat/34', $this->getResponseLocation());
	}
}



class CmsControllerCategorieEvenementTest extends CmsControllerTestCase {

	/** @test */
	function deleteShouldRedirectToAdminCmsParentCategorie() {
		$this->dispatch('/admin/cms/catdel/id/34');

		$this->assertRedirectTo('/admin/cms/index/id_cat/23');

		$this->assertEquals($this->cat_evenements,
												$this->categorie_wrapper->getFirstAttributeForLastCallOn('delete'));
	}


	/** @test */
	function addCategorieShouldDisplayCatEvenementsAsTitle() {
		$this->dispatch('/admin/cms/catadd/id/34');
		$this->assertXPathContentContains('//p', 'Localisation : Annecy');
	}


	/** @test */
	function editCategorieShouldDisplayAllCat() {
		$this->dispatch('/admin/cms/catedit/id/34');
		$this->assertXPath('//input[@value="Evènements"]');
		$this->assertXPathContentContains('//select[@name="id_cat_mere"]//option[@value="0"]', "Aucune");
		$this->assertXPathContentContains('//select[@name="id_cat_mere"]//option[@value="23"]', "A la Une");
	}


	/** @test */
	function postAddCategorieShouldRedirectWithIdCat() {
		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('libelle' => 'concerts',
											'id_cat_mere' => 34));
		$this->dispatch('/admin/cms/catadd/id/34');

		$this->assertEquals('/admin/cms/index/id_cat/34', $this->getResponseLocation());

		$new_cat = $this->categorie_wrapper->getFirstAttributeForLastCallOn('save');
		$this->assertEquals('concerts', $new_cat->getLibelle());
		$this->assertEquals(34, $new_cat->getIdCatMere());
	}


	/** @test */
	function postEditCategoriePostShouldRedirectWithIdCat() {
		Class_ArticleCategorie::getLoader()->newInstanceWithId(254);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('libelle' => 'Actualite',
											'id_cat_mere' => 254));
		$this->dispatch('/admin/cms/catedit/id/34');
		$this->assertEquals('/admin/cms/index/id_cat/34', $this->getResponseLocation());
		$this->assertEquals('Actualite', $this->cat_evenements->getLibelle());
		$this->assertEquals(254, $this->cat_evenements->getIdCatMere());
		$this->assertTrue($this->categorie_wrapper->methodHasBeenCalled('save'));
	}
}



class CmsControllerCategorieNotFoundTest extends CmsControllerTestCase {
	/** @test */
	function deleteShouldRedirectToAdminCms() {
		$this->dispatch('/admin/cms/delete/id/999');
		$this->assertRedirect('admin/cms');
	}


	/** @test */
	function addCategorieShouldDisplayAnnecy() {
		$this->dispatch('/admin/cms/catadd');
		$this->assertXPathContentContains('//p', 'Localisation : Annecy');
	}
}


?>