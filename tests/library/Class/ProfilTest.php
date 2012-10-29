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

class ProfilVideTest extends ModelTestCase {
	public function setUp() {
		$this->profil_vide = new Class_Profil();
	}

	/** @test */
	public function cfgAccueilShouldReturnArrayWithModules() {
		$this->assertEquals(array("modules" => array()),
												$this->profil_vide->getCfgAccueilAsArray());
	}

	/** @test */
	public function shouldBeValid() {
		$this->assertTrue($this->profil_vide->isValid());
	}

	/** @test */
	public function getBibLibelleShouldReturnEmptyString() {
		$this->assertEquals('', $this->profil_vide->getBibLibelle());
	}

	/** @test */
	public function getModuleAccueilPreferencesShouldReturnEmptyArray() {
		$this->assertEquals(array(), $this->profil_vide->getModuleAccueilPreferences(1));
	}
}



class ProfilJeunesseAstrolabeTest extends ModelTestCase {
	public function setUp() {
		$cfg_accueil = array('modules' => array('1' => array('division' => '1',
																												 'type_module' => 'RECH_SIMPLE',
																												 'preferences' => array())),
												 'options' => array());

		$this->profil_astro = Class_Profil::getLoader()
			->newInstanceWithId(7)
			->setIdSite(12)
			->setLibelle("Jeunesse")
			->setSkin('astrolabe')
			->setCfgMenus(array())
			->setHeaderCss('afi-opac3/userfiles/jeunesse.css')
			->setCfgAccueil($cfg_accueil);

		$this->bib_melun = Class_Bib::getLoader()
			->newInstanceWithId(12)
			->setLibelle('Melun');


		Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('findAllBy')
			->answers(array());

		Class_Profil::setFileWriter(Storm_Test_ObjectWrapper::mock()->whenCalled('fileExists')->answers(true));
	}


	/** @test */
	public function getCfgMenuAsArrayShouldReturnDefaultMenus() {
		$this->assertEquals(array(
															'H' => array(
																					 "libelle" => "Menu horizontal",
																					 "picto" => "vide.gif",
																					 'menus' => array()),
															'V' => array(
																					 "libelle" => "Menu vertical",
																					 "picto" => "vide.gif",
																					 'menus' => array())),
												$this->profil_astro->getCfgMenusAsArray());
	}


	/** @test */
	public function shouldAddMenuHorizontalIfNotExists() {
		$this->profil_astro->setCfgMenus( array(
																						'V' => array(
																												 "libelle" => "Les news",
																												 "picto" => "home.gif",
																												 "menus" => array()),

																						'4' => array(
																												 "libelle" => "Mon menu",
																												 "picto" => "home.png",
																												 "menus" => array())));

		$this->assertEquals(array(
															'H' => array(
																					 "libelle" => "Menu horizontal",
																					 "picto" => "vide.gif",
																					 "menus" => array()),
															'V' => array(
																					 "libelle" => "Les news",
																					 "picto" => "home.gif",
																					 "menus" => array()),

															'4' => array(
																					 "libelle" => "Mon menu",
																					 "picto" => "home.png",
																					 "menus" => array())),
												$this->profil_astro->getCfgMenusAsArray());
	}


	/** @test */
	public function getOrCreateConfigAccueilRechSimpleShouldCreatePreferences() {
		$data = $this->profil_astro->getOrCreateConfigAccueil(1, 'RECH_SIMPLE');
		$this->assertEquals('Rechercher', $data['titre']);
	}


	/** @test */
	public function astroHeaderCssShouldBeJeunesse() {
		$this->assertEquals('afi-opac3/userfiles/jeunesse.css', $this->profil_astro->getHeaderCss());
	}


	/** @test */
	public function astroHeaderCssIE7ShouldBeIE7_Jeunesse() {
		$this->assertEquals('ie7_jeunesse.css', $this->profil_astro->getHeaderCssIE(7));
	}


	/** @test */
	public function astroHeaderCssIE7ShouldBeIE8_Jeunesse() {
		$this->assertEquals('ie8_jeunesse.css', $this->profil_astro->getHeaderCssIE(8));
	}


	/** @test */
	public function withoutCssHeaderCSSIE7ShouldBeEmpty() {
		$this->profil_astro->setHeaderCSS('');
		$this->assertEmpty($this->profil_astro->getHeaderCssIE(7));
	}


	/** @test */
	public function loadingAChildrenShouldNotRewriteParentAttributes() {
		$row = array('ID_PROFIL' => 98, 'PARENT_ID' => 7, 'SKIN' => 'zork');
		$child = Class_Profil::getLoader()->newFromRow($row);
		$this->assertEquals('astrolabe', $child->getSkin());
		$this->assertEquals('astrolabe', $this->profil_astro->getSkin());
	}


	/** @test */
	public function getTitreSiteShouldReturnLibelleIfNoteSet() {
		$this->assertEquals("Jeunesse",
												$this->profil_astro->getTitreSite());
	}


	/** @test */
	public function shouldBePublic() {
		$this->assertTrue($this->profil_astro->isPublic());
	}


	/** @test */
	public function getHauteurBanniereShouldReturn100() {
		$this->assertEquals(100, $this->profil_astro->getHauteurBanniere());
	}


	/** @test */
	public function getSkinShouldReturnAstrolabe() {
		$this->assertEquals('astrolabe', $this->profil_astro->getSkin());
	}


  /** @test */
	public function toArrayShouldContainsLibelleJeunesse() {
		$attributes = $this->profil_astro->toArray();
		$this->assertEquals('Jeunesse', $attributes['libelle']);
	}


  /** @test */
	public function toArrayShouldContainsTitreSiteMediathequeAstrolabe() {
		$attributes = $this->profil_astro->toArray();
		$this->assertEquals("", $attributes['titre_site']);
	}


	/** @test */
	public function getBoiteLoginInBanniereShouldReturnFalse() {
		$this->assertFalse($this->profil_astro->getBoiteLoginInBanniere());
	}


	/** @test */
	public function getBoiteRechercheInBanniereShouldReturnFalse() {
		$this->assertFalse($this->profil_astro->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function setBoiteLoginInBanniereTrueShouldAddItToCfgAccueil() {
		$this->profil_astro->setBoiteLoginInBanniere(true);
		$this->assertTrue($this->profil_astro->getBoiteLoginInBanniere());
	}


	/** @test */
	public function setBoiteLoginInBanniereTrueShouldGetIdTwo() {
		$this->profil_astro->setBoiteLoginInBanniere(true);
		$cfg_accueil = $this->profil_astro->getCfgAccueilAsArray();
		$this->assertEquals('LOGIN', $cfg_accueil['modules'][2]['type_module']);
		$this->assertEquals(2, $cfg_accueil['modules'][2]['preferences']['id_module']);
	}


	/** @test */
	public function setBoiteRechercheSimpleInBanniereTrueShouldAddItToCfgAccueil() {
		$this->profil_astro->setBoiteRechercheSimpleInBanniere(true);
		$this->assertTrue($this->profil_astro->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function updateAttributesWithBoiteRechercheSimpleTrueShouldAddIt() {
		$this->profil_astro->updateAttributes(array('boite_recherche_simple_in_banniere' => true));
		$this->assertTrue($this->profil_astro->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function updateAttributesWithBoiteLoginTrueShouldAddIt() {
		$this->profil_astro->updateAttributes(array('boite_login_in_banniere' => true));
		$this->assertTrue($this->profil_astro->getBoiteLoginInBanniere());
	}


	/** @test */
	public function hasSubProfilsShouldReturnFalse() {
		$this->assertFalse($this->profil_astro->hasSubProfils());
	}


	/** @test */
	public function getSubProfilsShouldReturnEmptyArray() {
		$this->assertEquals(array(),
												$this->profil_astro->getSubProfils());
		return	Class_Profil::getLoader();

	}


	/**
	 * @depends getSubProfilsShouldReturnEmptyArray
	 * @test
	 */
	public function profilLoaderShouldHaveFindAllByCalledWithParentIdOfAstrolabe($loader) {
		$param = $loader->getFirstAttributeForLastCallOn('findAllBy');
		$this->assertEquals('parent_profil', $param['role']);

		$this->assertEquals($this->profil_astro->toArray(),
												$param['model']->toArray());

	}


	/** @test */
	public function hasParentProfilShouldReturnFalse() {
		$this->assertFalse($this->profil_astro->hasParentProfil());
	}
}




abstract class ProfilAdulteChatenayTestCase extends ModelTestCase {
	public function setUp() {
		parent::setUp();
				$cfg_accueil = array('modules' => array('1' => array('division' => '4',
																												 'type_module' => 'RECH_SIMPLE',
																												 'preferences' => array()),

																								'2' => array('division' => '4',
																														 'type_module' => 'LOGIN',
																														 'preferences' => array()),
																								'4' => array('division' => '1',
																														 'type_module' => 'NEWS')),
												 'options' => 	array());

		$this->profil = Class_Profil::getLoader()
			->newInstanceWithId(5)
			->setTitreSite(null)
			->setLibelle("Adulte")
			->setHauteurBanniere(200)
			->setNbDivisions(2)
			->setLargeurSite(800)
			->setLargeurDivision1(200)
			->setLargeurDivision2(600)
			->setLargeurDivision3(0)
			->setCfgAccueil($cfg_accueil)
			->setAccessLevel('-1')
			->setMailSuggestionAchat('chatenay@chatenay.fr');
	}
}




class ProfilAdulteChatenayTest extends ProfilAdulteChatenayTestCase  {
	public function setUp() {
		parent::setUp();

		$cfg_accueil_histoire = array('modules' => array(
																										 '4' => array('division' => '1',
																																	'type_module' => 'CRITIQUES'),
																										 '7' => array('division' => '1',
																																	'type_module' => 'KIOSQUE')),
																	'options' => 	array());


		$this->page_histoire = Class_Profil::getLoader()
			->newInstanceWithId(51)
			->setParentId($this->profil->getId())
			->setLibelle('Histoire')
			->setCfgAccueil($cfg_accueil_histoire);

		$this->page_politique = Class_Profil::getLoader()
			->newInstanceWithId(51)
			->setParentId($this->profil->getId())
			->setLibelle('Politique');

		Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('findAllBy')
			->answers(array($this->page_politique,
											$this->page_histoire));
	}


	/** @test */
	public function hasSubPofilsShouldReturnsTrue() {
		$this->assertTrue($this->profil->hasSubProfils());
	}


	/** @test */
	public function getSubProfilsShouldContainsProfilHistoire() {
		$this->assertEquals('Histoire',
												array_first($this->profil->getSubProfils())->getLibelle());
	}


	/** @test */
	public function getSubProfilsShouldContainsProfilPolitique() {
		$this->assertEquals('Politique',
												array_last($this->profil->getSubProfils())->getLibelle());
	}


	/** @test */
	public function pagePolitiqueParentProfilShouldBeProfilAdulte() {
		$this->assertEquals($this->profil,
												$this->page_politique->getParentProfil());
	}


	/** @test */
	public function pageHistoireParentShouldBeProfilAdulte() {
		$this->assertEquals($this->profil,
												$this->page_histoire->getParentProfil());
	}


	/** @test */
	public function pageHistoireHasParentShouldReturnTrue() {
		$this->assertTrue($this->page_histoire->hasParentProfil());
	}


	/** @test */
	public function pageHistoireNbDivisionsShouldGetParentAttributes() {
		$this->assertEquals(2, $this->page_histoire->getNbDivisions());
	}


	/** @test */
	public function pageHistoireSetNbDivisionsShouldSetParentAttribute() {
		$this->page_histoire->setNbDivisions(1);
		$this->assertEquals(1, $this->page_histoire->getNbDivisions());
		$this->assertEquals(1, $this->profil->getNbDivisions());
	}


	/** @test */
	public function getLibelleShouldReturnLibelleWhenSiteNotSet() {
		$this->assertEquals("Adulte", $this->profil->getLibelle());
	}


	/** @test */
	public function shouldBePublic() {
		$this->assertTrue($this->profil->isPublic());
	}


	/** @test */
	public function getHauteurBanniereShouldReturn200() {
		$this->assertEquals(200, $this->profil->getHauteurBanniere());
	}


	/** @test */
	public function getLiensSortantOffShouldReturnFalse() {
		$this->assertFalse($this->profil->getLiensSortantsOff());
	}


	/** @test */
	public function getSkinShouldReturnOriginal() {
		$this->assertEquals('original', $this->profil->getSkin());
	}


	/** @test */
	public function shouldBeValid() {
		$this->assertTrue($this->profil->isValid());
	}


	/** @test */
	public function shouldHaveErrorLargeurSiteIfLessThan800() {
		$this->profil->setLargeurSite(500);
		$this->assertFalse($this->profil->isValid());
		$this->assertContains("La largeur du site doit être comprise entre 800 et 2000 pixels.",
													$this->profil->getErrors());
	}


	protected function _checkColorFormatValidFor($field) {
		$valid_entries = array('#FFF', '#123456', '', null);

		foreach($valid_entries as $entry) {
			$this->profil->_set($field, $entry);
			$this->assertTrue($this->profil->isValid(),
												'Should be valid for value: "'.(string)$entry.'"');
		}


		$invalid_entries = array('zork', '#1', '#1234', '#1234567', '#R2D', '#3F33');

		foreach($invalid_entries as $entry) {
			$this->profil->_set($field, $entry);
			$this->assertFalse($this->profil->isValid(),
												 'Should not be valid for value: "'.$entry.'"');
		}
	}


	/** @test */
	public function shouldBeValidIfCouleurTexteBandeauRightFormatted() {
		$this->_checkColorFormatValidFor('couleur_texte_bandeau');
	}


	/** @test */
	public function shouldBeValidIfCouleurLienBandeauRightFormatted() {
		$this->_checkColorFormatValidFor('couleur_lien_bandeau');
	}


	/** @test */
	public function getBoiteLoginInBanniereShouldReturnTrue() {
		$this->assertTrue($this->profil->getBoiteLoginInBanniere());
	}


	/** @test */
	public function getBoiteRechercheInBanniereShouldReturnTrue() {
		$this->assertTrue($this->profil->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function setBoiteRechercheSimpleInBanniereFalseShouldRemoveIt() {
		$this->profil->setBoiteRechercheSimpleInBanniere(false);
		$this->assertFalse($this->profil->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function setBoiteLoginInBanniereFalseShouldRemoveIt() {
		$this->profil->setBoiteLoginInBanniere(false);
		$this->assertFalse($this->profil->getBoiteLoginInBanniere());
	}


	/** @test */
	public function pageHistoireGetBoiteRechercheInBanniereShouldReturnTrue() {
		$this->assertTrue($this->page_histoire->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function pagePolitiqueGetBoiteLoginInBanniereShouldReturnTrue() {
		$this->assertTrue($this->page_politique->getBoiteLoginInBanniere());
	}


	/** @test */
	public function deleteShouldCascadeOnPages() {
		$loader = Class_Profil::getLoader()
			->whenCalled('delete')
			->answers(null)
			->getWrapper();

		$this->profil->delete();

		$this->assertTrue($loader
											->methodHasBeenCalledWithParams('delete',
																											array($this->page_politique)));

		$this->assertTrue($loader
											->methodHasBeenCalledWithParams('delete',
																											array($this->page_histoire)));
	}


	/** @test */
	public function pagePolitiqueMailSuggestionAchatShouldBeChatenayAtChatenayDotFr() {
		$this->assertEquals('chatenay@chatenay.fr', $this->page_politique->getMailSuggestionAchatOrPortail());
	}


	/** @test */
	public function withoutMailSuggestionAchatPagePolitiqueShouldGetOneFromPortail() {
		Class_Profil::getPortail()->setMailSuggestionAchat('suggest@chatenay.fr');
		$this->profil->setMailSuggestionAchat('');
		$this->assertEquals('suggest@chatenay.fr', $this->page_politique->getMailSuggestionAchatOrPortail());
	}


	/** @test */
	public function withoutMailSuggestionAchatShouldGetMailSite() {
		Class_Profil::getPortail()->setMailSuggestionAchat('');
		$this->profil->setMailSuggestionAchat('')->setMailSite('contact@chatenay.fr');
		$this->assertEquals('contact@chatenay.fr', $this->page_politique->getMailSuggestionAchatOrPortail());
	}


	/** @test */
	public function withoutMailSuggestionAchatProfilPortailShouldGetMailSite() {
		Class_Profil::getPortail()->setMailSuggestionAchat('')->setMailSite('admin@chatenay.fr');
		$this->assertEquals('admin@chatenay.fr', Class_Profil::getPortail()->getMailSuggestionAchatOrPortail());
	}
}




class ProfilAdulteChatenayMoveModuleMoveNEWSFromDiv1PosZeroToDivFourPositionOneTest extends ProfilAdulteChatenayTestCase {
	public function setUp() {
		parent::setUp();
		$this->profil->moveModuleOldDivPosNewDivPos(1, 0, 4, 1);
		$this->modules = array_at('modules', $this->profil->getCfgAccueilAsArray());
	}


	/** @test */
	public function moduleIdFourShouldBeInDivFour() {
		$this->assertEquals(4, $this->modules['4']['division']);
	}


	/** @test */
	public function moduleIdFourShouldBeBetweenOneAndTwo() {
		$this->assertEquals(array(1, 4, 2), array_keys($this->modules));
	}


	/** @test */
	public function modulesCountShouldBeThree() {
		$this->assertEquals(3, count($this->modules));
	}
}




class ProfilAdulteChatenayMoveModuleLOGINToFirstPosInDivFourTest extends ProfilAdulteChatenayTestCase {
	public function setUp() {
		parent::setUp();
		$this->profil->moveModuleOldDivPosNewDivPos(4, 1, 4, 0);
		$this->modules = array_at('modules', $this->profil->getCfgAccueilAsArray());
	}


	/** @test */
	public function moduleIdTwoShouldBeInDivFour() {
		$this->assertEquals(4, $this->modules['2']['division']);
	}


	/** @test */
	public function moduleIdTwoShouldBeOnTop() {
		$this->assertEquals(array(2, 1, 4), array_keys($this->modules));
	}


	/** @test */
	public function modulesCountShouldBeThree() {
		$this->assertEquals(3, count($this->modules));
	}
}




class ProfilAdulteChatenayMoveModuleRECH_SIMPLEToSecondPosInDivFourTest extends ProfilAdulteChatenayTestCase {
	public function setUp() {
		parent::setUp();
		$this->profil->moveModuleOldDivPosNewDivPos(4, 0, 4, 1);
		$this->modules = array_at('modules', $this->profil->getCfgAccueilAsArray());
	}


	/** @test */
	public function moduleIdOneShouldBeInDivFour() {
		$this->assertEquals(4, $this->modules['1']['division']);
	}


	/** @test */
	public function moduleIdTwoShouldBeOnTop() {
		$this->assertEquals(array(2, 4, 1), array_keys($this->modules));
	}


	/** @test */
	public function modulesCountShouldBeThree() {
		$this->assertEquals(3, count($this->modules));
	}
}




class ProfilAdulteChatenayMoveModuleLOGINToLastPosInDivOneTest extends ProfilAdulteChatenayTestCase {
	public function setUp() {
		parent::setUp();
		$this->profil->moveModuleOldDivPosNewDivPos(4, 1, 1, 1);
		$this->modules = array_at('modules', $this->profil->getCfgAccueilAsArray());
	}


	/** @test */
	public function moduleIdTwoShouldBeInDivOne() {
		$this->assertEquals(1, $this->modules['2']['division']);
	}


	/** @test */
	public function moduleIdTwoShouldBeLast() {
		$this->assertEquals(array(1, 4, 2), array_keys($this->modules));
	}


	/** @test */
	public function modulesCountShouldBeThree() {
		$this->assertEquals(3, count($this->modules));
	}
}




class ProfilEvenementsTest extends ModelTestCase {
	public function setUp() {
		$this->profil = new Class_Profil();

		$this->profil
			->setTitreSite(null)
			->setLibelle("Evenements")
			->setBoiteRechercheSimpleInBanniere(true)
			->setBoiteLoginInBanniere(true)
			->setAccessLevel(1);

		$boites_banniere = $this->profil->getBoitesDivision(4);
		$this->boite_recherche = $boites_banniere[1];
		$this->boite_login = $boites_banniere[2];
	}


	/** @test */
	public function getBoitesDivisionFourShouldReturnATwoElementsArray() {
		$this->assertEquals(2, count($this->profil->getBoitesDivision(4)));
	}


	/** @test */
	public function shouldNotBePublic() {
		$this->assertFalse($this->profil->isPublic());
	}


	/** @test */
	public function afterSetBoiteLoginTrueShouldStillHaveTwoBoites() {
		$this->profil->setBoiteLoginInBanniere(true);
		$this->assertEquals(2, count($this->profil->getBoitesDivision(4)));
	}


	/** @test */
	public function afterSetRechercheSimpleTrueShouldStillHaveTwoBoites() {
		$this->profil->setRechercheSimpleInBanniere(true);
		$this->assertEquals(2, count($this->profil->getBoitesDivision(4)));
	}


	/** @test */
	public function boiteRechercheSimpleTitreShouldBeRechercher() {
		$this->assertEquals('Rechercher', $this->boite_recherche['preferences']['titre']);
	}


	/** @test */
	public function boiteRechercheSimpleTemplateShouldBeBoiteBanniereGauche() {
		$this->assertEquals('boite_banniere_gauche', $this->boite_recherche['preferences']['boite']);
	}


	/** @test */
	public function boiteLoginTemplateShouldBeBoiteBanniereDroite() {
		$this->assertEquals('boite_banniere_droite', $this->boite_login['preferences']['boite']);
	}


	/** @test */
	public function boiteConnexionTitreShouldBeSeConnecter() {
		$this->assertEquals('Se connecter', $this->boite_login['preferences']['titre']);
	}
}


class ProfilPesseyValandryTranslatedTest extends ModelTestCase {
	/**
	 * @var Class_Profil
	 */
	private $_profil;

	public function setUp() {
		$this->_profil = new Class_Profil();

		$this->_profil
			->setTitreSite('Médiathèque de pessey Valandry')
			->setLibelle('Tout sur notre médiatheque')
			->setAccessLevel(1)
			->setCfgMenus(ProfilPesseyValandryFixtures::createMenusConfiguration())
			->setCfgSite(ProfilPesseyValandryFixtures::createSiteConfiguration())
			->setCfgAccueil(ProfilPesseyValandryFixtures::createAccueilConfiguration())
			->setCfgNotice(ProfilPesseyValandryFixtures::createNoticeConfiguration())
			->setCfgModules(ProfilPesseyValandryFixtures::createModulesConfiguration())
			;

	}

	/** @test */
	public function getCfgMenusAsArrayShouldCallTranslator() {
		$mockTranslator = $this->_getMockTranslator();
		$mockTranslator->expects($this->once())
									->method('translate')
									->with(ProfilPesseyValandryFixtures::createMenusConfiguration(), 'Menus')
									;

		$this->_profil->setTranslator($mockTranslator);

		$menus = $this->_profil->getCfgMenusAsArray();

	}

	/** @test */
	public function getCfgSiteAsArrayShouldCallTranslator() {
		$mockTranslator = $this->_getMockTranslator();
		$mockTranslator->expects($this->once())
												->method('translate')
												->with(ProfilPesseyValandryFixtures::createSiteConfiguration(), 'Site')
												;

		$this->_profil->setTranslator($mockTranslator);

		$menus = $this->_profil->getCfgSiteAsArray();

	}

	/** @test */
	public function getCfgAccueilAsArrayShouldCallTranslator() {
		$mockTranslator = $this->_getMockTranslator();
		$mockTranslator->expects($this->once())
												->method('translate')
												->with(ProfilPesseyValandryFixtures::createAccueilConfiguration(), 'Accueil')
												;

		$this->_profil->setTranslator($mockTranslator);

		$menus = $this->_profil->getCfgAccueilAsArray();

	}

	/** @test */
	public function getCfgNoticeAsArrayShouldCallTranslator() {
		$mockTranslator = $this->_getMockTranslator();
		$mockTranslator->expects($this->once())
												->method('translate')
												->with(ProfilPesseyValandryFixtures::createNoticeConfiguration(), 'Notice')
												;

		$this->_profil->setTranslator($mockTranslator);

		$menus = $this->_profil->getCfgNoticeAsArray();
	}

	/** @test */
	public function getCfgModulesAsArrayShouldCallTranslator() {
		$mockTranslator = $this->_getMockTranslator();
		$mockTranslator->expects($this->once())
												->method('translate')
												->with(ProfilPesseyValandryFixtures::createModulesConfiguration(), 'Modules')
												;

		$this->_profil->setTranslator($mockTranslator);

		$menus = $this->_profil->getCfgModulesAsArray();

	}

	/**
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	private function _getMockTranslator() {
		return $this->getMockBuilder('Class_Profil_I18nTranslator')
												->disableOriginalConstructor()
												->getMock()
												;
	}

}

class ProfilPesseyValandryFixtures {

	public static function createModulesConfiguration() {
		return array('dummy_modules' => array('all' => 'the configurations'));
	}

	public static function createNoticeConfiguration() {
		return array('dummy_notice' => array('all' => 'the configurations'));
	}

	public static function createMenusConfiguration() {
		return array(
			'H' => array(
				'libelle' => 'Menu horizontal',
				'picto' => 'vide.gif',
				'menus' => array(
				),
			),
		);

	}

	public static function createSiteConfiguration() {
		return array('dummy_key' => 'dummy_value');
	}

	public static function createAccueilConfiguration() {
		return array('dummy_accueil' => array('all' => 'the configurations'));
	}
}




class ProfilPortailTest extends ModelTestCase {
	/** @var Class_Profil */
	protected $_profil;

	/** @var Storm_Test_ObjectWrapper */
	protected $_wrapper;


	public function setUp() {
		parent::setUp();

		$this->_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
												->whenCalled('delete')->answers(true)->getWrapper();

		$this->_profil = Class_Profil::getLoader()->newInstanceWithId(1);
	}


	/** @test */
	public function deletingPortalShouldNotBePossible() {
		$this->_profil->delete();
		$this->assertFalse($this->_wrapper->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function copyShouldBeNew() {
		$this->assertFalse($this->_profil->isNew());
		$this->assertTrue($this->_profil->copy()->isNew());
	}


	/** @test */
	public function copyShouldNotHaveIdProfil() {
		$this->assertEmpty($this->_profil->copy()->id_profil);
	}
}




class ProfilWithPagesCopyTest extends Storm_Test_ModelTestCase {
	protected $_clone;

	public function setUp() {
		parent::setUp();

		$id = 100;
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
			->whenCalled('findAllBy')
			->answers([])
			->whenCalled('save')
			->willDo(function ($model) use (&$id) {
					$model->setId($id++);
				} );


		$profil = Class_Profil::newInstanceWithId(3)
			->setSubProfils([ Class_Profil::newInstanceWithId(31),
												Class_Profil::newInstanceWithId(32),
												Class_Profil::newInstanceWithId(33)->setLibelle('CD'),
												Class_Profil::newInstanceWithId(34)
												]);

		$this->_clone = $profil->deepCopy();
		$this->_clone->save();
	}


	/** @test */
	public function cloneShouldHaveFourPages() {
		$this->assertEquals(4, $this->_clone->numberOfSubProfils());
	}


	/** @test */
	public function cloneFirstPageLibelleShouldBeIndexedAtNouveauProfil() {
		$this->assertEquals('** nouveau profil **', $this->_clone->getSubProfils()['** nouveau profil **']->getLibelle());
	}


	/** @test */
	public function cloneSecondPageLibelleShouldBeIndexedAtNouveauProfil1() {
		$this->assertEquals('** nouveau profil **', $this->_clone->getSubProfils()['** nouveau profil ** (1)']->getLibelle());
	}


	/** @test */
	public function cloneThirdPageLibelleShouldBeIndexedAtCD() {
		$this->assertEquals('CD', $this->_clone->getSubProfils()['CD']->getLibelle());
	}


	/** @test */
	public function cloneFourthPageLibelleShouldBeIndexedAtNouveauProfil2() {
		$this->assertEquals('** nouveau profil **', $this->_clone->getSubProfils()['** nouveau profil ** (2)']->getLibelle());
	}


	/** @test */
	public function cloneIdShouldBe100() {
		$this->assertEquals(100, $this->_clone->getId());
	}


	/** @test */
	public function cloneFirstPageIdShouldBe101() {
		$this->assertEquals(101, array_values($this->_clone->getSubProfils())[0]->getId());
	}


	/** @test */
	public function cloneFirstPageParentIdShouldBe100() {
		$this->assertEquals(100, array_values($this->_clone->getSubProfils())[0]->getParentId());
	}


	/** @test */
	public function cloneLastPageIdShouldBe104() {
		$this->assertEquals(104, array_values($this->_clone->getSubProfils())[3]->getId());
	}

	/** @test */
	public function cloneLastPageParentIdShouldBe100() {
		$this->assertEquals(100, array_values($this->_clone->getSubProfils())[3]->getParentId());
	}

}


?>