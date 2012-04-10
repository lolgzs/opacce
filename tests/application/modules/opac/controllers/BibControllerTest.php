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

abstract class BibControllerWithZoneTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		unset($_REQUEST['geo_zone']);
		$_SESSION['selection_bib'] = array('nb_notices' => 12345,
																			 'id_bibs' => null);

		$this->bib_annecy = Class_Bib::getLoader()
			->newInstanceWithId(4)
			->setIdZone(1)
			->setLibelle('Annecy')
			->setAffZone('')
			->setVille('Annecy')
			->setUrlWeb('http://www.annecy.fr')
			->setMail('jp@annecy.com')
			->setTelephone('04 50 51 32 12')
			->setAdresse('1 rue Jean Jaures')
			->setVisibilite(Class_Bib::V_DATA);

		$this->haute_savoie = Class_Zone::getLoader()
			->newInstanceWithId(1)
			->setCouleurTexte('#059')
			->setCouleurOmbre('#234')
			->setTailleFonte('14')
			->setImage('carte_moulins.jpg')
			->setBibs(array($this->bib_annecy));

		$ecrivez_des_tests = Class_Article::getLoader()
			->newInstanceWithId(2)
			->setIdSite(0)
			->setTitre('Ecrivez des tests !')
			->setContenu('Ça fera le plus grand bien')
			->setCategorie(Class_ArticleCategorie::getLoader()
										 ->newInstanceWithId(5)
										 ->setLibelle('Bonnes pratiques'));
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array('id_bib' => '4'))
			->answers(array($ecrivez_des_tests))

			->whenCalled('filterByLocaleAndWorkflow')
			->with(array($ecrivez_des_tests))
			->answers(array($ecrivez_des_tests));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Zone')
			->whenCalled('findAll')
			->answers(array($this->haute_savoie));
	}
}



class BibControllerZoneViewOneTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('bib/zoneview/id/1');
	}

	/** @test */
	public function bibListShouldBeVisible() {
		$this->assertXPathContentContains("//table//td[@class='listeTitre']", 
																			"Bibliothèque");
	}


	/** @test */
	public function actualitesShouldBeVisible() {
		$this->assertXPathContentContains("//h2",	"Actualités :");
	}


	/** @test */
	public function bibAnnecyShouldBeVisible() {
		$this->assertXPathContentContains("//table//td//a",
																			"Annecy");
	}


	/** @test */
	public function bibAnnecyShouldBeVisibleOnMap() {
		$this->assertXPathContentContains("//span[contains(@onclick, '/bib/bibview/id/4')]", "Annecy");
	}


	/** @test */
	public function mapShouldBeVisible() {
		$this->assertXPath("//div[@id='image_container']");
	}


	/** @test */
	public function imageZoneShouldBeVisible() {
		$this->assertXPath("//div[@id='image_container'][contains(@style, 'images/blank.gif')]");
	}


	/** @test */
	public function articleEcrivezDesTestsShouldBeVisible() {
		$this->assertXPathContentContains("//li//a", "Ecrivez des tests !");
	}
}


class BibControllerZoneViewOneWithHideNewsTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()->setCfgModules(array("bib" => array("zoneview" => array("hide_news" => 1))));
		$this->dispatch('bib/zoneview/id/1');
	}


	/** @test */
	public function actualitesShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//h2",	"Actualités :");
	}


	/** @test */
	public function articleEcrivezDesTestsShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//li//a", "Ecrivez des tests !");
	}
}


class BibControllerIndexWithHideNewsTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()->setCfgModules(array("bib" => array("index" => array("hide_news" => 1))));
		$this->dispatch('bib/index');
	}


	/** @test */
	public function actualitesShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//h2",	"Actualités :");
	}


	/** @test */
	public function articleEcrivezDesTestsShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//li//a", "Ecrivez des tests !");
	}
}



class BibControllerIndexWithShowNewsTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()->setCfgModules(array("bib" => array("index" => array("hide_news" => 0))));
		$this->dispatch('bib/index');
	}


	/** @test */
	public function actualitesShouldBeVisible() {
		$this->assertXPathContentContains("//h2",	"Actualités :");
	}


	/** @test */
	public function articleEcrivezDesTestsShouldBeVisible() {
		$this->assertXPathContentContains("//li//a", "Ecrivez des tests !");
	}


	/** @test */
	function hrefForArticleEcrivezDesTestsShouldLinkToArticleView() {
		$this->assertXPath('//li//a[contains(@href, "cms/articleview/id/2")]',
											 $this->_response->getBody());
	}
}


class BibControllerMapViewTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();

		$aff_zone = array('profilID' => '3',
											'libelle' => 'Annecy',
											'posX' => 2,
											'posY' => 5,
											'posPoint' => 'droite');
		$this->bib_annecy->setAffZone(ZendAfi_Filters_Serialize::serialize($aff_zone));

		$this->dispatch('bib/mapzoneview/id/1');
	}


	/** @test */
	public function actionShouldBeMapZoneView() {
		$this->assertAction('mapzoneview');
	}


	/** @test */
	public function mapShouldBeVisible() {
		$this->assertXPath("//div[@id='image_container']");
	}


	/** @test */
	public function imageZoneShouldBeVisible() {
		$this->assertXPath("//div[@id='image_container'][contains(@style, 'images/blank.gif')]");
	}


	/** @test */
	public function bibAnnecyShouldBeVisibleOnMap() {
		$this->assertXPathContentContains("//span[contains(@onclick, '?id_profil')]", "Annecy");
	}
}



class BibControllerBibViewAnnecyTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('bib/bibview/id/4');
	}


	/** @test */
	function AdresseShouldBe1RueJeanJaures() {
		$this->assertXPathContentContains('//tr', '1 rue Jean Jaures');
	}


	/** @test */
	function articleEcrivezDesTestsShouldBeVisible() {
		$this->assertXPathContentContains('//h2', 'Ecrivez des tests !');
	}

	/** @test */
	function libelleCatBonnesPratiquesShouldBeVisible() {
		$this->assertXPathContentContains('//h1', 'Bonnes pratiques');
	}

	/** @test */
	public function urlRetourShouldBeZoneViewIdOne() {
		$this->assertXPathContentContains('//a[contains(@href, "/bib/zoneview/id/1")]', 
																			'Retour');		
	}
}


class BibControllerBibViewAnnecyWithParamRetourHistoriqueTest extends BibControllerWithZoneTestCase {
	/** @test */
	public function withUrlRetourCmsArticleViewFiveShouldBeAccepted() {
		$this->dispatch('bib/bibview/id/4?retour=cms+articleview+5');
		$this->assertXPathContentContains('//a[contains(@href, "/cms/articleview/id/5")]', 
																			'Retour');		
	}

	/** @test */
	public function withUrlRetourCmsOnlyShouldNotBeAccepted() {
		$this->dispatch('bib/bibview/id/4?retour=cms');
		$this->assertXPathContentContains('//a[contains(@href, "/bib/zoneview/id/1")]', 
																			'Retour');		
	}
}


class BibControllerBibViewInexistantTest extends BibControllerWithZoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('bib/bibview/');
	}

	/** @test */
	public function responseShouldRedirectToIndex() {
		$this->assertRedirectTo('/opac/bib/index');
	}
}

?>