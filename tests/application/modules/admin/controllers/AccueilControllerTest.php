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


class AccueilControllerBoite2ColTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$boite2cols = array('type_module' => 'CONTENEUR_DEUX_COLONNES',
												'division' => 2,
												'id_module' => 3,
												'preferences' => array('col_gauche_type' => 'NEWS',
																							 'col_droite_type' => 'CRITIQUES',
																							 'boite' => 'boite_de_la_division_du_milieu',
																							 'titre' => 'A la Une'));

		$this->profil_biologie = Class_Profil::getLoader()
			->newInstanceWithId(34)
			->setLibelle('Biologie')
			->updateModuleConfigAccueil(3, $boite2cols);
		$this->assertTrue($this->profil_biologie->isValid());

		$this->request_url = '/admin/accueil/conteneur2colonnes?id_profil=34&id_module=3&type_module=CONTENEUR_DEUX_COLONNES&config=accueil';
		$this->dispatch($this->request_url, true);
	}


	/** @test */
	public function shouldRenderConteneur2ColonnesView() {
		$this->assertController('accueil');
		$this->assertAction('conteneur2colonnes');
	}


	/** @test */
	public function inputTitleShouldDisplayALaUne() {
		$this->assertXPath("//input[@name='titre'][@value='A la Une']");
	}


	/** @test */
	public function selectColGaucheTypeShouldHaveNEWSSelected() {
		$this->assertXPath("//select[@name='col_gauche_type']/option[@value='NEWS'][@selected='selected']");
	}


	/** @test */
	public function selectColDroiteTypeShouldHaveCRITIQUESSelected() {
		$this->assertXPath("//select[@name='col_droite_type']/option[@value='CRITIQUES'][@selected='selected']");
	}


	/** @test */
	public function postDataShouldSaveTheProfil() {
		$this->profil_wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true)
			->getWrapper();


		$data = array('col_gauche_type' => 'KIOSQUE',
									'col_droite_type' => 'TAGS',
									'titre' => 'Ce mois ci');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch($this->request_url);

		$this->assertTrue($this->profil_wrapper->methodHasBeenCalled('save'));

		return $this->profil_biologie;
	}


	/**
	 * @depends postDataShouldSaveTheProfil
	 * @test
	 */
	public function moduleKiosqueShouldHaveBeenCreated($profil_biologie) {
		$module = $profil_biologie->getModuleAccueilConfig(1000);
		$this->assertEquals('KIOSQUE',$module['type_module']);


		$boite2cols = $profil_biologie->getModuleAccueilConfig(3);
		$this->assertEquals(1000, $boite2cols['preferences']['col_gauche_module_id']);
	}


	/**
	 * @depends postDataShouldSaveTheProfil
	 * @test
	 */
	public function moduleTagsShouldHaveBeenCreated($profil_biologie) {
		$module = $profil_biologie->getModuleAccueilConfig(1001);
		$this->assertEquals('TAGS',$module['type_module']);


		$boite2cols = $profil_biologie->getModuleAccueilConfig(3);
		$this->assertEquals(1001, $boite2cols['preferences']['col_droite_module_id']);
	}
}



class AccueilControllerNewBoite2ColTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->boite2cols = array('type_module' => 'CONTENEUR_DEUX_COLONNES',
															'division' => 2,
															'id_module' => 3);

		$this->profil_biologie = Class_Profil::getLoader()
			->newInstanceWithId(34)
			->setMenuHautOn(false)
			->setLibelle('Biologie')
			->updateModuleConfigAccueil(3, $this->boite2cols);

		Class_Profil::setCurrentProfil($this->profil_biologie);
		$this->assertTrue($this->profil_biologie->isValid());
	}


	/** @test */
	public function shouldRenderConteneur2ColonnesView() {
		$this->request_url = '/admin/accueil/conteneur2colonnes?id_profil=34&id_module=3&type_module=CONTENEUR_DEUX_COLONNES&config=accueil';
		$this->dispatch($this->request_url);

		$this->assertController('accueil');
		$this->assertAction('conteneur2colonnes');
	}


	/** @test */
	public function fonctionAdminOnClickShouldHaveActionConteneur2Colonnes() {
		$this->dispatch('/opac?id_profil=34');
		$this->assertXPath("//img[contains(@onclick, 'conteneur2colonnes')]", $this->_response->getBody());
	}


	/** @test */
	public function withModuleIdModulesShouldBeCreated() {
		$this->boite2cols['preferences'] = array('col_gauche_module_id' => 234,
																						 'col_gauche_type' => 'COMPTEURS',
																						 'col_droite_module_id' => 235,
																						 'col_droite_type' => 'RECH_GUIDEE');

		$this->profil_biologie->updateModuleConfigAccueil(3, $this->boite2cols);
		$this->dispatch('/opac?id_profil=34');

		$this->assertXPathContentContains('//div', 'Le catalogue contient');
		$this->assertXPathContentContains('//div', 'Recherche guidée');
	}
}




class AccueilControllerLangueConfigurationTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$boite_langue = array('type_module' => 'LANGUE',
													'division' => 1,
													'id_module' => 2,
													'preferences' => array('titre' => 'Sélectionnez la langue',
																								 'boite' => 'boite_vide'));

		Class_Profil::getCurrentProfil()->updateModuleConfigAccueil(2, $boite_langue);

		$this->request_url = '/admin/accueil/langue?id_module=2&type_module=LANGUE&config=accueil';
		$this->dispatch($this->request_url);
	}

	/** @test */
	public function titleFieldShouldContinsSelectionnezLaLangue() {
		$this->assertXPath('//input[@value="Sélectionnez la langue"]');
	}
}




class AccueilControllerBibliothequeNumeriqueTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(9, 
																	array('type_module' => 'BIB_NUMERIQUE',
																				'division' => 1,
																				'id_module' => 9,
																				'preferences' => array('titre' => 'Bibliothèque numérique',
																															 'boite' => '',
																															 'id_categories' => '',
																															 'id_albums' => '',
																															 'type_aff' => 'displayTree',
																															 'nb_aff' => '',
																															 'order' => '',
																															 'style_liste' => 'diaporama')
																				));

		$bible = Class_AlbumCategorie::getLoader()
			->newInstanceWithId(1)
			->setLibelle('Bible de Souvigny');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('getCollections')->answers(array($bible))->getWrapper()
			->whenCalled('findAlbumsRecursively')->answers(array(Class_Album::getLoader()->newInstanceWithId(1)
																													 ->setTitre('Harlock')
																													 ->setCategorie($bible)));

		$this->dispatch('/admin/accueil/bibliotheque-numerique?config=accueil&type_module=BIB_NUMERIQUE&id_module=9&proprietes=');
	}


	/** @test */
	public function frameTitleShouldBeProprieteDeLaBibliothequeNumerique() {
		$this->assertXPathContentContains('//h1', 'Propriétés de la bibliothèque numérique', $this->_response->getBody());
	}


	/** @test */
	public function titleShouldBeBibliothequeNumerique() {
		$this->assertXPath('//input[@value="Bibliothèque numérique"]');
	}


	/** @test */
	public function selectionOfCategorieShouldBePresent() {
		$this->assertXPath('//select[@name="id_categories"]');
	}


	/** @test */
	public function selectionOfCategorieBibleDeSouvignyShouldBePresent() {
		$this->assertXPathContentContains('//select[@name="id_categories"]/option', 'Bible de Souvigny');
	}


	/** @test */
	public function selectionOfDisplayModeShouldBePresent() {
		$this->assertXPath('//input[@type="radio"][@name="type_aff"]');
	}


	/** @test */
	public function selectionOfOrderModeShouldBePresent() {
		$this->assertXPath('//input[@type="radio"][@name="order"]');
	}


	/** @test */
	public function selectionOfAlbumShouldBePresent() {
		$this->assertXPath('//select[@name="id_albums"]');
	}


	/** @test */
	public function selectionOfImageCountShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@name="nb_aff"]');
	}


	/** @test */
	public function selectionOfDisplayStyleShouldBePresent() {
		$this->assertXPath('//select[@name="style_liste"]');
	}
}



class AccueilControllerConfigMenuVerticalTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(12,
																	array('type_module' => 'MENU_VERTICAL',
																				'division' => 1,
																				'id_module' => 12,
																				'preferences' => array('titre' => 'Mon Menu',
																															 'menu_deplie' => 1)));

		$this->dispatch('/admin/accueil/menuvertical?config=accueil&type_module=MENU_VERTICAL&id_module=12');
	}


	/** @test */
	function afficheTitreShouldBeChecked() {
		$this->assertXPath('//input[@type="checkbox"][@name="afficher_titre"][@checked="checked"]');
	}


	/** @test */
	function menuDeplieShouldBeChecked() {
		$this->assertXPath('//input[@type="checkbox"][@name="menu_deplie"][@checked="checked"]');
	}
}



class AccueilControllerConfigCalendrierTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(11,
																	array('type_module' => 'CALENDAR',
																				'division' => 1,
																				'id_module' => 11,
																				'preferences' => array('titre' => 'Agenda')));

		$this->dispatch('/admin/accueil/calendrier?config=accueil&type_module=CALENDAR&id_module=11');
	}


	/** @test */
	public function inputTitleShouldDisplayAgenda() {
		$this->assertXPath("//input[@name='titre'][@value='Agenda']");
	}


	/** @test */
	function checkBoxDisplayNextEventShouldBeChecked() {
		$this->assertXPath('//input[@type="checkbox"][@name="display_next_event"][@checked="checked"]');
	}


	/** @test */
	public function inputNbEventsShouldDisplayThree() {
		$this->assertXPath("//input[@name='nb_events'][@value='3']");
	}
}



class AccueilControllerConfigSitothequeDefaultsTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(25,
																	array('type_module' => 'SITO',
																				'division' => 1,
																				'id_module' => 25,
																				'preferences' => array()));

		$this->dispatch('/admin/accueil/sitotheque?config=accueil&type_module=SITO&id_module=25');
	}


	/** @test */
	public function inputTitleShouldDisplaySitotheque() {
		$this->assertXPath("//input[@name='titre'][@value='Sitothèque']");
	}


	/** @test */
	function nbAffShouldEqualsTwo() {
		$this->assertXPath("//input[@name='nb_aff'][@value='2']");
	}

	/** @test */
	public function checkboxGroupByCategorieShouldNotBeChecked() {
		$this->assertXPath('//input[@type="checkbox"][@name="group_by_categorie"][not(@checked)]');	
	}
}




class AccueilControllerConfigSitothequeWithPreferencesTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(25,
																	array('type_module' => 'SITO',
																				'division' => 1,
																				'id_module' => 25,
																				'preferences' => array('titre' => 'Ma Sito',
																															 'group_by_categorie' => true)));

		$this->dispatch('/admin/accueil/sitotheque?config=accueil&type_module=SITO&id_module=25');
	}


	/** @test */
	public function inputTitleShouldDisplayMaSito() {
		$this->assertXPath("//input[@name='titre'][@value='Ma Sito']");
	}


	/** @test */
	public function checkboxGroupByCategorieShouldNotBeChecked() {
		$this->assertXPath('//input[@type="checkbox"][@name="group_by_categorie"][@checked="checked"]');	
	}
}




class AccueilControllerConfigRSSDefaultsTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(25,
																	array('type_module' => 'RSS',
																				'division' => 1,
																				'id_module' => 25,
																				'preferences' => array()));

		$this->dispatch('/admin/accueil/rss?config=accueil&type_module=RSS&id_module=25');
	}


	/** @test */
	public function inputTitleShouldDisplayFilsRss() {
		$this->assertXPath("//input[@name='titre'][@value='Fils Rss']");
	}


	/** @test */
	function nbAffShouldEqualsTwo() {
		$this->assertXPath("//input[@name='nb_aff'][@value='2']");
	}
}




class AccueilControllerConfigBoiteLoginTest extends Admin_AbstractControllerTestCase  {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(25,
																	array('type_module' => 'LOGIN',
																				'division' => 4,
																				'id_module' => 32,
																				'preferences' => array()));		
		$this->dispatch('/admin/accueil/login?config=accueil&type_module=LOGIN&id_module=32');
	}


	/** @test */
	public function inputTitreShouldBeSeConnecter() {
		$this->assertXPath('//input[@name="titre"][@value="Se connecter"]');
	}


	/** @test */
	public function inputIdentifiantExempleShouldBeEmpty() {
		$this->assertXPath('//input[@name="identifiant_exemple"][@value=""]');
	}


	/** @test */
	public function inputMotDePasseExempleShouldBeEmpty() {
		$this->assertXPath('//input[@name="mot_de_passe_exemple"][@value=""]');
	}


	/** @test */
	public function inputLibelleLienConnexion() {
		$this->assertXPath('//input[@name="lien_connexion"][@value="» Se connecter"]');
	}


	/** @test */
	public function inputLibelleLienMotDePasseOublie() {
		$this->assertXPath('//input[@name="lien_mot_de_passe_oublie"][@value="» Mot de passe oublié ?"]');
	}
}




abstract class AccueilControllerConfigBoiteKiosqueProfilLognesTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Bib::newInstanceWithId(3)
			->setLibelle('Lognes');

		Class_Profil::getCurrentProfil()
			->updateModuleConfigAccueil(25,
																	array('type_module' => 'KIOSQUE',
																				'division' => 4,
																				'id_module' => 32,
																				'preferences' => array()))
			->setIdSite(3);
		$this->dispatch('/admin/accueil/kiosque?config=accueil&type_module=KIOSQUE&id_module=32', true);
	}
}




class AccueilControllerConfigBoiteKiosqueProfilLognesAsAdminPortailTest extends AccueilControllerConfigBoiteKiosqueProfilLognesTestCase {
	/** @test */
	public function selectStyleListeShouldContainsOptGroupObjetsJS() {
		$this->assertXPath('//select[@name="style_liste"]//optgroup[@label="Objets javascript"]');
	}


	/** @test */
	public function selectStyleListeShouldContainsOptGroupObjetsFlash() {
		$this->assertXPath('//select[@name="style_liste"]//optgroup[@label="Objets flash"]');
	}
	
}



class AccueilControllerConfigBoiteKiosqueAsAdminBibLognesTest extends AccueilControllerConfigBoiteKiosqueProfilLognesTestCase  {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ID_SITE = 3;
	}


	/** @test */
	public function pageShouldBeVisible() {
		$this->assertXPath('//select[@name="style_liste"]');
	}
}



class AccueilControllerConfigBoiteKiosqueAsAdminBibOtherSiteTest extends AccueilControllerConfigBoiteKiosqueProfilLognesTestCase  {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ID_SITE = 5;
	}


	/** @test */
	public function responseShouldRedirectToPageAccueil() {
		$this->assertRedirect();
	}
}

?>