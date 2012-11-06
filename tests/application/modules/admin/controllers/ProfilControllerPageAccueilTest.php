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

abstract class Admin_ProfilControllerJeunessePageAccueilTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$cfg_accueil = array('modules' => array('1' => array('division' => 4,
																												 'type_module' => 'RECH_SIMPLE', 
																												 'preferences' => array()),
																						'2' => array('division' => 1,
																												 'type_module' => 'NEWS',
																												 'preferences' => array()),
																						'3' => array('division' => 2,
																												 'type_module' => 'KIOSQUE',
																												 'preferences' => array('nb_notices' => 12,
																																								'nb_analyse' => 36,
																																								'only_img' => 1)),
																						'6' => array('division' => 2,
																												 'type_module' => 'CRITIQUES',
																												 'preferences' => array()),

																						'8' => array('division' => 1,
																												 'type_module' => 'RESERVATIONS',
																												 'preferences' => array()),

																						'9' => array('division' => 1,
																												 'type_module' => 'PRETS',
																												 'preferences' => array()),

																						'10' => array('division' => 1,
																												 'type_module' => 'NEWSLETTERS',
																												 'preferences' => array()),

																						'11' => array('division' => 1,
																												 'type_module' => 'FORMATIONS',
																												 'preferences' => array()),

																						'12' => array('division' => 1,
																												 'type_module' => 'MULTIMEDIA',
																												 'preferences' => array()),


																						'666' => array('division' => 1,
																													 'type_module' => 'WRONG',
																													 'preferences' => array())));


		$this->profil_jeunesse = Class_Profil::newInstanceWithId(7)
		                         ->setLibelle('Profil Jeunesse')
														 ->setCfgAccueil($cfg_accueil);

	}
}


class Admin_ProfilControllerJeunessePageAccueilTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {
	public function setUp() {
		parent::setUp();

		$this->profil_wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true)
			->getWrapper()
			->whenCalled('findAllByZoneAndBib')
			->answers(array($this->profil_jeunesse))
			->getWrapper();


		ZendAfi_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 7;
		$this->dispatch('/admin/profil/accueil/id_profil/7', true);
	}


	/** @test */
	public function formActionUrlShouldBeOnIdProfilSeven() {
		$this->assertXPath('//form[contains(@action, "profil/accueil/id_profil/7")]');
	}


	/** @test */
	public function boitePretsShouldBeAvailable() {
		$this->assertXPathContentContains('//ul[@id="allItems"]/li[@id="PRETS"]','Prêts');
	}

	/** @test */
	public function boitePretsShouldBeInDivisionOne() {
		$this->assertXPath('//ul[@id="box1"]/li[@id="PRETS"][@id_module="9"]//img[contains(@onclick,"accueil/prets")]');
	}


	/** @test */
	public function boiteReservationsShouldBeAvailable() {
		$this->assertXPathContentContains('//ul[@id="allItems"]/li[@id="RESERVATIONS"]','Réservations',$this->_response->getBody());
	}

	/** @test */
	public function boiteReservationsShouldBeInDivisionOne() {
		$this->assertXPath('//ul[@id="box1"]/li[@id="RESERVATIONS"][@id_module="8"]//img[contains(@onclick,"accueil/reservations")]');
	}


	/** @test */
	public function boiteNewsShouldBeInDivisionOne() {
		$this->assertXPath('//ul[@id="box1"]/li[@id="NEWS"][@id_module="2"]');
	}

	
	/** @test */
	public function boiteKiosqueShouldBeInDivisionTwo() {
		$this->assertXPath('//ul[@id="box2"]/li[@id="KIOSQUE"][@id_module="3"]');
	}


	/** @test */
	public function boiteWrongShouldNotBeVisible() {
		$this->assertNotXPath('//li[@id_module="666"]');
	}


	/** @test */
	public function boiteCritiquesShouldBeInDivisionTwo() {
		$this->assertXPath('//ul[@id="box2"]/li[@id="CRITIQUES"][@id_module="6"]');
	}


	/** @test */
	public function preferencesBoiteKiosqueShouldBeEncodedInAttributeProprietes() {
		$this->assertXPath('//li[@id_module="3"][contains(@proprietes,"nb_notices=12/only_img=1/aleatoire=1")]');
	}


	/** @test */
	public function postingDataWithNoModifications() {
		$cfg_module = 'box1|2|NEWS|;box2|3|KIOSQUE|nb_notices=12/nb_analyse=36/only_img=1/;box2|6|CRITIQUES|';

		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('saveContent' => $cfg_module));
		$this->dispatch('/admin/profil/accueil/id_profil/7');

		$this->assertTrue($this->profil_wrapper->methodHasBeenCalled('save'));
		$this->assertRedirect('/admin/profil/accueil/id_profil/7');

		return $this->profil_jeunesse;
	}


	/** 
	 * @depends postingDataWithNoModifications
	 * @test 
	 */
	public function shouldPutDefaultNewsPrefereces($profil_jeunesse) {
		$news = $profil_jeunesse->getModuleAccueilConfig(2);
		$this->assertEquals(1, $news['division']);
		$this->assertEquals('NEWS', $news['type_module']);
		$this->assertEquals('Articles', $news['preferences']['titre']);
	}


	/** 
	 * @depends postingDataWithNoModifications
	 * @test 
	 */
	public function shouldKeepKiosquePrefereces($profil_jeunesse) {
		$kiosque = $profil_jeunesse->getModuleAccueilConfig(3);
		$this->assertEquals(12, $kiosque['preferences']['nb_notices']);
		$this->assertEquals(36, $kiosque['preferences']['nb_analyse']);

		$this->assertEquals(2, $kiosque['division']);
		$this->assertEquals('KIOSQUE', $kiosque['type_module']);
	}


	/** 
	 * @depends postingDataWithNoModifications
	 * @test 
	 */
	public function shouldKeepRechSimpleInBanniere($profil_jeunesse) {
		$rech = $profil_jeunesse->getModuleAccueilConfig(1);
		$this->assertEquals(4, $rech['division']);
	}


	/** @test */
	public function postingDataWithBoiteDeuxColonnes() {
		$cfg_module = 'box2|3|KIOSQUE|nb_notices=12/nb_analyse=36/only_img=1/;box2|6|CRITIQUES|;box2|new|CONTENEUR_DEUX_COLONNES|';
	
		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('saveContent' => $cfg_module));
		$this->dispatch('/admin/profil/accueil/id_profil/7');

		$this->assertTrue($this->profil_wrapper->methodHasBeenCalled('save'));
		$this->assertRedirect('/admin/profil/accueil/id_profil/7');

		return $this->profil_jeunesse;
	}


	/** 
	 * @depends postingDataWithBoiteDeuxColonnes
	 * @test 
	 */
	public function boiteDeuxColonnesShouldGetIdFourAndDefaultValues($profil_jeunesse) {
		$b2cols = $profil_jeunesse->getModuleAccueilConfig(4);
		$this->assertEquals(2, $b2cols['division']);
		$this->assertEquals('CONTENEUR_DEUX_COLONNES', $b2cols['type_module']);
		$this->assertEquals('NEWS', $b2cols['preferences']['col_gauche_type']);
		$this->assertEquals('CRITIQUES', $b2cols['preferences']['col_droite_type']);
	}
}



class ProfilControllerPageAccueilWithTelephonePackMobileTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()->newInstanceWithId('PACK_MOBILE')
			->setValeur(1);

		Class_AdminVar::getLoader()->newInstanceWithId('BIB_NUMERIQUE')
			->setValeur(1);

		$profil_telephone = Class_Profil::getLoader()
			->newInstanceWithId(3)
			->setLibelle('iPhone')
			->beTelephone();
		$this->dispatch('/admin/profil/accueil/id_profil/3');
	}


	/** @test */
	public function moduleNewsShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="NEWS"]');
	}


	/** @test */
	public function moduleBibNumeriqueShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="BIB_NUMERIQUE"]');
	}


	/** @test */
	public function moduleCritiquesShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="CRITIQUES"]');
	}


	/** @test */
	public function moduleKiosqueShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="KIOSQUE"]');
	}


	/** @test */
	public function moduleLoginShouldNotBeAvailable() {
		$this->assertNotXPath('//ul/li[@id="LOGIN"]');
	}
}




class ProfilControllerPageAccueilWithTelephoneNoPackMobileNoBibNumTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()->newInstanceWithId('PACK_MOBILE')
			->setValeur(0);

		Class_AdminVar::getLoader()->newInstanceWithId('BIBNUM')
			->setValeur(0);

		$this->profil_jeunesse->beTelephone();

		$this->dispatch('/admin/profil/accueil/id_profil/'.$this->profil_jeunesse->getId());
	}


	/** @test */
	public function moduleNewsShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="NEWS"]');
	}


	/** @test */
	public function moduleBibNumeriqueShouldNotBeAvailable() {
		$this->assertNotXPath('//ul/li[@id="BIB_NUMERIQUE"]');
	}


	/** @test */
	public function moduleCritiquesShouldNotBeAvailable() {
		$this->assertNotXPath('//ul/li[@id="CRITIQUES"]');
	}


	/** @test */
	public function moduleKiosqueShouldNotBeAvailable() {
		$this->assertNotXPath('//ul/li[@id="KIOSQUE"]');
	}
}



class Admin_ProfilControllerJeunessePageAccueilConfigPretsTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {
	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/prets?config=admin&id_profil=7&type_module=PRETS&id_module=9&proprietes=boite=/titre=Mes prets/',true);
	}

	/** @test */
	public function actionShouldBePrets() {
		$this->assertAction('prets');
	}

	/** @test */
	public function titleShouldBeProprieteDuModulePret() {
		$this->assertXPathContentContains('//h1','Propriétés du module Prêts');
	}


	/** @test */
	public function comboBoiteShouldBePresent() {
		$this->assertXPath('//select[@name="boite"]/option[@value="boite_de_la_division_droite"]');
	}


	/** @test */
	public function titreInputShouldHaveValueMesPrets() {
		$this->assertXPath('//input[@name="titre"][@value="Mes prets"]');
	}

}



class Admin_ProfilControllerJeunessePageAccueilConfigEmptyPretTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {

	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/prets?config=admin&id_profil=7&type_module=PRETS&id_module=9',true);

	}

	/** @test */
	public function titreInputShouldHaveValueMesPrets() {
		$this->assertXPath('//input[@name="titre"][@value="Mes prêts"]');
	}
}


class Admin_ProfilControllerJeunessePageAccueilConfigReservationsTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {
	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/reservations?config=admin&id_profil=7&type_module=RESERVATIONS&id_module=8&proprietes=boite=/titre=Mes reservations/',true);
	}

	/** @test */
	public function actionShouldBeReservations() {
		$this->assertAction('reservations');
	}

	/** @test */
	public function titleShouldBeProprieteDuModuleReservations() {
		$this->assertXPathContentContains('//h1','Propriétés du module Réservations');
	}


	/** @test */
	public function comboBoiteShouldBePresent() {
		$this->assertXPath('//select[@name="boite"]/option[@value="boite_de_la_division_droite"]');
	}


	/** @test */
	public function titreInputShouldHaveValueMesReservations() {
		$this->assertXPath('//input[@name="titre"][@value="Mes reservations"]');
	}

}



class Admin_ProfilControllerJeunessePageAccueilConfigEmptyReservationTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {

	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/reservations?config=admin&id_profil=7&type_module=RESERVATIONS&id_module=8',true);

	}

	/** @test */
	public function titreInputShouldHaveValueMesReservations() {
		$this->assertXPath('//input[@name="titre"][@value="Mes réservations"]');
	}
}



class Admin_ProfilControllerJeunessePageAccueilConfigNewslettersTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {
	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/newsletters?config=admin&id_profil=7&type_module=NEWSLETTERS&id_module=10&proprietes=boite=/titre=Mes newsletters/',true);
	}

	/** @test */
	public function actionShouldBeNewsletters() {
		$this->assertAction('newsletters');
	}

	/** @test */
	public function titleShouldBeProprieteDuModuleNewsletters() {
		$this->assertXPathContentContains('//h1','Propriétés du module Lettres d\'informations',$this->_response->getBody());
	}


	/** @test */
	public function comboBoiteShouldBePresent() {
		$this->assertXPath('//select[@name="boite"]/option[@value="boite_de_la_division_droite"]');
	}


	/** @test */
	public function titreInputShouldHaveValueMesNewsletters() {
		$this->assertXPath('//input[@name="titre"][@value="Mes newsletters"]',$this->_response->getBody());
	}

}



class Admin_ProfilControllerJeunessePageAccueilConfigEmptyNewsletterTest extends Admin_ProfilControllerJeunessePageAccueilTestCase {

	public function setup() {
		parent::setup();
		$this->dispatch('admin/accueil/newsletters?config=admin&id_profil=7&type_module=NEWSLETTERS&id_module=11',true);

	}

	/** @test */
	public function titreInputShouldHaveValueMesNewsletters() {
		$this->assertXPath('//input[@name="titre"][@value="Lettres d\'informations"]',$this->_response->getBody());
	}
}




?>