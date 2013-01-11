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

abstract class BibControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_annecy = Class_Bib::newInstanceWithId(2)
			->setLibelle('Annecy')
			->setResponsable('Ludivine')
			->setAffZone('')
			->setVille('Annecy')
			->setUrlWeb('http://www.annecy.fr')
			->setMail('jp@annecy.com')
			->setTelephone('04 50 51 32 12')
			->setArticleCategories(array());


		$this->bib_cran = Class_Bib::newInstanceWithId(3)
			->setLibelle('Cran-Gévrier');

		$all_bibs = array($this->bib_annecy, $this->bib_cran);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAll')
			->answers($all_bibs)

			->whenCalled('findAllByIdZone')
			->answers($all_bibs);
	}
}




class BibControllerIndexWidthAdminPortailTest extends BibControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
	}

	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('MULTIMEDIA_KEY')->setValeur('coin coin');
		$this->dispatch('admin/bib/index', true);
	}


	/** @test */
	public function pageShouldDisplayBibAnnecy() {
		$this->assertXPathContentContains('//tr[3]//td', 'Annecy');
	}


	/** @test */
	public function bibAnnecyShouldHaveActionToEdit() {
		$this->assertXPath('//tr[3]//a[contains(@href, "bib/edit/id/2")]');
	}


	/** @test */
	public function bibAnnecyShouldHaveActionToOuverturesIndex() {
		$this->assertXPath('//tr[3]//a[contains(@href, "ouvertures/index/id_site/2")]');
	}


	/** @test */
	public function pageShouldDisplayBibCran() {
		$this->assertXPathContentContains('//tr[4]//td', 'Cran-Gévrier');
	}


	/** @test */
	public function bibCranShouldHaveActionToEdit() {
		$this->assertXPath('//tr[4]//a[contains(@href, "bib/edit/id/3")]');
	}


	/** @test */
	public function whenMultimediaDisabledOuverturesShouldNotBeAvailable() {
		Class_AdminVar::find('MULTIMEDIA_KEY')->setValeur('');
		$this->bootstrap();
		parent::setUp();
		$this->dispatch('admin/bib/index', true);
		$this->assertNotXPath('//a[contains(@href, "ouvertures")]');
	}
}




class BibControllerWithModoPortailTest extends BibControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::MODO_PORTAIL;
	}


	/** @test */
	function responseToIndexShouldNotBeARedirectToAccueil() {
		$this->dispatch('admin/bib/index');
		$this->assertNotRedirect();
	}

	/** @test */
	function responseToEditSiteOneShouldBePageEdit() {
		$this->dispatch('admin/bib/edit/id/1');
		$this->assertNotRedirect('admin/index');
	}
}



class BibControllerWithModerateurBibTest extends BibControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::MODO_BIB;
	}


	/** @test */
	function responseToIndexShouldBeARedirectToAccueil() {
		$this->dispatch('admin/bib/index');
		$this->assertRedirect('admin/index');
	}


	/** @test */
	function responseToEditSiteOneShouldBeARedirectToAccueil() {
		$this->dispatch('admin/bib/edit/id/1');
		$this->assertRedirect('admin/index');
	}
}




abstract class BibControllerWithAdminBibTestCase extends BibControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ID_SITE = 2;
	}
}




class BibControllerWithAdminBibTest extends BibControllerWithAdminBibTestCase {
	/** @test */
	function responseToIndexShouldBeARedirectToEditSite2() {
		$this->dispatch('admin/bib/index');
		$this->assertRedirect('admin/bib/edit/id/2');
	}


	/** @test */
	function responseToEditSiteTwoShouldBePageEdit() {
		$this->dispatch('admin/bib/edit/id/2');
		$this->assertNotRedirect('admin/index');
	}


	/** @test */
	function responseToEditSiteThreeShouldBePageEdit() {
		$this->dispatch('admin/bib/edit/id/3');
		$this->assertRedirect('admin/index');
	}
}



class BibControllerWithAdminBibPlanAccesTestTest extends BibControllerWithAdminBibTestCase {
	/** @test */
	function responseToEditSite2ShouldNotBeARedirect() {
		$this->dispatch('admin/bib/planacces/id_bib/3');
		$this->assertNotRedirect();
	}
}




class BibControllerWithAdminBibEditAnnecyTest extends BibControllerWithAdminBibTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/bib/edit/id/2');
	}

	/** @test */
	function inputLibelleShouldBeAnnecy() {
		$this->assertXPath('//input[@name="libelle"][@value="Annecy"]');
	}

	/** @test */
	function nomResponsableShouldBeLudivine() {
			$this->assertXPath('//input[@name="responsable"][@value="Ludivine"]');
	}
}




class BibControllerGetArticlesJSONTest extends BibControllerTestCase {
	public function setUp() {
		parent::setUp();

		$vive_les_vacances = Class_Article::newInstanceWithId(42)
			->setIdCat(3)
			->setTitre('Vive les vacances !');

		$cat_cran_news = Class_ArticleCategorie::newInstanceWithId(3)
			->setLibelle('News')
			->setIdSite(3)
			->setArticles(array($vive_les_vacances))
			->setSousCategories(array());

		$this->bib_cran->setArticleCategories(array($cat_cran_news));


		$reseau_en_route = Class_Article::newInstanceWithId(123)
			->setIdCat(9)
			->setTitre('Reseau en route');

		$cat_portail_infos = Class_ArticleCategorie::newInstanceWithId(9)
			->setLibelle('Infos')
			->setArticles(array($reseau_en_route))
			->setSousCategories(array());

		$portail = Class_Bib::newInstanceWithId(0)
			->setLibelle('Portail')
			->setArticleCategories(array($cat_portail_infos));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAllByWithPortail')
			->with(array('order' => 'libelle'))
			->answers(array($portail, $this->bib_annecy, $this->bib_cran))
			->getWrapper()
			->whenCalled('newInstanceWithId')
			->with(0)
			->answers($portail);
	}


	/** @test */
	function withBibThreeShouldRenderCranJSON() {
		$this->dispatch('admin/bib/articles/id_bib/3');
		$expectedJSON = <<<JSON
			[{"id":3,
				"label": "Cran-Gévrier",
				"categories": [{"id":3,
												"label": "News",
												"categories": [],
												"items": [
														{ "id":42,
															"label":"Vive les vacances !"	}
												]}],
				"items": []}]
JSON;
			$this->assertEquals(json_decode($expectedJSON),
													json_decode($this->_response->getBody()));
	}

	/** @test */
	function withNoBibShouldRenderAllArticles() {
		$this->dispatch('admin/bib/articles/categories_only/0');
		$expectedJSON = <<<JSON
			[{"id":0,
				"label": "Portail",
				"categories": [{"id":9,
												"label": "Infos",
												"categories": [],
												"items": [
														{ "id":123,
															"label":"Reseau en route"	}
												]}],
				"items": []},

			 {"id":2,
				"label": "Annecy",
				"categories": [],
				"items": []},

			 {"id":3,
				"label": "Cran-Gévrier",
				"categories": [{"id":3,
												"label": "News",
												"categories": [],
												"items": [
														{ "id":42,
															"label":"Vive les vacances !"	}
												]}],
				"items": []}]
JSON;
			$this->assertEquals(json_decode($expectedJSON),
													json_decode($this->_response->getBody()));
	}


	/** @test */
	function withNoBibAndCategoriesOnlyShouldNotRenderArticles() {
		$this->dispatch('admin/bib/articles/categories_only/1');
		$expectedJSON = <<<JSON
			[{"id":0,
				"label": "Portail",
				"categories": [{"id":9,
												"label": "Infos",
												"categories": [],
												"items": []}],
				"items": []},

			 {"id":2,
				"label": "Annecy",
				"categories": [],
				"items": []},

			 {"id":3,
				"label": "Cran-Gévrier",
				"categories": [{"id":3,
												"label": "News",
												"categories": [],
												"items": []}],
				"items": []}]
JSON;
			$this->assertEquals(json_decode($expectedJSON),
													json_decode($this->_response->getBody()));
	}
}

?>