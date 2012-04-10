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
require_once 'ProfilControllerTest.php';

abstract class ProfilControllerProfilJeunesseAndAdultesWithMenusTestCase extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		$picsou = Class_Article::getLoader()
			->newInstanceWithId(4)
			->setTitre('Picsou fait faillite');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('getArticlesByPreferences')
			->with(array('nb_aff' => '2'))
			->answers(array($picsou))
			->getWrapper()

			->whenCalled('filterByLocaleAndWorkflow')
			->with(array($picsou))
			->answers(array($picsou));

		$this->mon_menu_jeunesse = array(
														"libelle" => "Mon menu",
														"picto" => "home.png",
														"menus" => array(array('type_menu' => 'MENU',
																									 'libelle' => 'Pratique',
																									 'picto' => 'bookmark.png',
																									 'preferences' => array()),

																						 array('type_menu' => 'URL',
																									 'libelle' => 'Google',
																									 'picto' => 'vide.gif',
																									 'preferences' => array('url' => 'http://www.google.com',
																																					'target' => 0)),
																						 array('type_menu' => 'NEWS',
																									 'libelle' => 'Actu',
																									 'picto' => 'vide.gif',
																									 'preferences' => array('nb_aff' => '2',
																																					'display_mode' => 'Submenu'))));

		$cfg_menus_jeunesse = array('H' => array(
																						 "libelle" => "Menu horizontal",
																						 "picto" => "vide.gif",
																						 "menus" => array()),
																'V' => array(
																						 "libelle" => "Menu vertical",
																						 "picto" => "vide.gif",
																						 "menus" => array()),

																'4' => $this->mon_menu_jeunesse);

		$this->profil_jeunesse->setCfgMenus($cfg_menus_jeunesse);

		$this
			->page_musique
			->setCfgAccueil(array('modules' => array(
																							 '6' => array('division' => 2,
																														'type_module' => 'CRITIQUES',
																														'preferences' => array()),
																							 '8' => array('division' => 1,
																														'type_module' => 'MENU_VERTICAL',
																														'preferences' => array('afficher_titre' => '1',
																																									 'menu' => 4)))));



		$cfg_menus_adulte = array('H' => array(
																					 "libelle" => "Menu horizontal",
																					 "picto" => "vide.gif",
																						 "menus" => array()),

															'4' => array(
																					 "libelle" => "Menu adulte",
																					 "picto" => "vide.gif",
																					 "menus" => array()));


		$this->profil_adulte = Class_Profil::getLoader()
			->newInstanceWithId(6)
			->setBrowser('opac')
			->setSkin('modele')
			->setTitreSite('Médiathèque de Melun')
			->setLibelle('Profil Adulte')
			->setCfgMenus($cfg_menus_adulte);
	}
}


class ProfilControllerProfilJeunesseAndAdultesWithMenusTestAccessors extends ProfilControllerProfilJeunesseAndAdultesWithMenusTestCase {

	/** @test */
	public function movingPageMusiqueToProfilJeunesseShouldDoNothing() {
		//régression sur le déplacement des pages dans un même profil
		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$menus_jeunesse = $this->profil_jeunesse->getCfgMenusAsArray();

		$this->dispatch('/admin/profil/move/id_profil/'.$this->page_musique->getId().'/to/'.$this->profil_jeunesse->getId());
		$this->assertEquals($menus_jeunesse,
												$this->profil_jeunesse->getCfgMenusAsArray());
	}



	/** @test */
	public function menusInBoiteMenuVerticalForProfilJeunesseShouldBeEmpty() {
		$this->assertEquals(array(), $this->profil_jeunesse->getMenusInBoitesMenuVertical());
	}


	/** @test */
	public function getBoitesMenuVerticalForProfilJeunesseShouldBeEmpty() {
		$this->assertEquals(array(), $this->profil_jeunesse->getBoitesMenuVertical());
	}


	/** @test */
	public function getBoitesMenuVerticalForPageMusiqueShouldReturnBoiteWithId8() {
		$this->assertEquals(array('8' => array('division' => 1,
																					 'type_module' => 'MENU_VERTICAL',
																					 'preferences' => array('afficher_titre' => '1',
																																	'menu' => 4))),
												$this->page_musique->getBoitesMenuVertical());
	}


	/** @test */
	public function menusInBoiteMenuVerticalForPageMusiqueShouldReturnMonMenu() {
		$this->assertEquals(array('4' => $this->mon_menu_jeunesse),
												$this->page_musique->getMenusInBoitesMenuVertical());
	}



	/** @test */
	public function menusInBoiteMenuVerticalForACopyOfProfilJeunesseShouldReturnMonMenu() {
		$this->profil_jeunesse->setCfgAccueil($this->page_musique->getCfgAccueil());

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$_SERVER['HTTP_REFERER'] = BASE_URL.'admin/profil';
		$this->dispatch('/admin/profil/copy/id_profil/'.$this->profil_jeunesse->getId());
		$this->copy_jeunesse = Class_Profil::getLoader()->getFirstAttributeForLastCallOn('save');

		$this->assertEquals(array('4' => $this->mon_menu_jeunesse),
												$this->copy_jeunesse->getMenusInBoitesMenuVertical());
	}
}


class ProfilControllerProfilJeunesseAndAdultesWithMenusTestRender extends ProfilControllerProfilJeunesseAndAdultesWithMenusTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac?id_profil='.$this->page_musique->getId());
	}


	/** @test */
	public function boiteMenuVerticalTitreShouldBePresent() {
		$this->assertXPathContentContains("//div[@class='boiteGauche']//div[@class='titre']", 'Mon menu');
	}


	/** @test */
	public function boiteMenuVerticalMenuPratiqueShouldBePresent() {
		$this->assertXPathContentContains("//ul[@class='menuGauche']//li", 'Pratique');
	}


	/** @test */
	public function boiteMenuVerticalMenuGoogleShouldBePresent() {
		$this->assertXPathContentContains("//ul[@class='menuGauche']//li", 'Google');
	}


	/** @test */
	public function titreSiteShouldBeMediathequeMelunPageMusique() {
		$this->assertXPathContentContains("//head//title", 'Profil Jeunesse - Musique');
	}


	/** @test */
	public function boiteMenuVerticalMenuActuShouldBePresent() {
		$this->assertXPathContentContains("//ul[@class='menuGauche']//li", 'Actu');
	}


	/** @test */
	public function linkForArticlePicsouShouldBeCmsArticleViewFourAndNamedActu() {
		$this->assertXPathContentContains('//a[contains(@href, "cms/articleview/id/4")]',
																			'Actu');
	}


	/** @test */
	public function articlesShouldHaveBeenFilterByLocaleAndWorkflow() {
		$this->assertTrue(Class_Article::getLoader()->methodHasBeenCalled('filterByLocaleAndWorkflow'));
	}
}


class ProfilControllerProfilJeunesseAndAdultesWithMenusTestSwitchPageMusiqueToProfilAdulte extends ProfilControllerProfilJeunesseAndAdultesWithMenusTestCase {
	public function setUp() {
		parent::setUp();
		$this->page_musique->setParentProfil($this->profil_adulte);
	}


	/** @test */
	public function pageMusiqueParentShouldBeProfilAdulte() {
		$this->assertEquals($this->profil_adulte,
												$this->page_musique->getParentProfil());
	}


	/** @test */
	public function profilAdulteShouldContainsMonMenu() {
		$mon_menu = array_last($this->profil_adulte->getCfgMenusAsArray());
		$this->assertEquals('Profil Jeunesse:: Mon menu', $mon_menu['libelle']);
	}


	/** @test */
	public function profilAdulteShouldContainsMenuAdulte() {
		$menu_adulte = array_at('4', $this->profil_adulte->getCfgMenusAsArray());
		$this->assertEquals('Menu adulte', $menu_adulte['libelle']);
	}


	/** @test */
	public function monMenuShouldBeDisplayedOnPageMusique() {
		$this->dispatch('/opac?id_profil='.$this->page_musique->getId());
		$this->assertXPathContentContains("//div[@class='boiteGauche']//div[@class='titre']", 
																			'Mon menu',
																			$this->_response->getBody());
		$this->assertXPathContentContains("//ul[@class='menuGauche']//li", 'Pratique');
		$this->assertXPathContentContains("//ul[@class='menuGauche']//li", 'Google');
	}
}


class ProfilControllerProfilJeunesseAndAdultesWithMenusTestMovePageMusiqueToProfilAdulte extends ProfilControllerProfilJeunesseAndAdultesWithMenusTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/profil/move/id_profil/'.$this->page_musique->getId().'/to/'.$this->profil_adulte->getId());
	}


	/** @test */
	public function pageMusiqueParentShouldBeProfilAdulte() {
		$this->assertEquals($this->profil_adulte->getId(),
												$this->page_musique->getParentProfil()->getId());
	}


	/** @test */
	public function profilAdulteShouldContainsMonMenu() {
		$mon_menu = array_last($this->profil_adulte->getCfgMenusAsArray());
		$this->assertEquals('Profil Jeunesse:: Mon menu', $mon_menu['libelle']);
	}
}


?>