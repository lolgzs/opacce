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

class Admin_ProfilControllerJeunessePageAccueilTest extends Admin_AbstractControllerTestCase {
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
																												 'preferences' => array())));


		$this->profil_jeunesse = new Class_Profil();
		$this->profil_jeunesse
			->setId(7)
			->setLibelle('Profil Jeunesse')
			->setCfgAccueil($cfg_accueil);

		Class_Profil::getLoader()->cacheInstance($this->profil_jeunesse);

		$this->profil_wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true)
			->getWrapper()
			->whenCalled('findAllByZoneAndBib')
			->answers(array($this->profil_jeunesse))
			->getWrapper();


		Zend_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 7;
		$this->dispatch('/admin/profil/accueil/id_profil/7');
	}


	/** @test */
	public function formActionUrlShouldBeOnIdProfilSeven() {
		$this->assertXPath("//form[contains(@action, 'profil/accueil/id_profil/7')]");
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
	public function boiteCritiquesShouldBeInDivisionTwo() {
		$this->assertXPath('//ul[@id="box2"]/li[@id="CRITIQUES"][@id_module="6"]');
	}


	/** @test */
	public function preferencesBoiteKiosqueShouldBeEncodedInAttributeProprietes() {
		$this->assertXPath('//li[@id_module="3"][@proprietes="nb_notices=12/nb_analyse=36/only_img=1/"]');
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



class ProfilControllerWithTelephoneAccueilTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
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
	public function moduleRechSimpleShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="RECH_SIMPLE"]');
	}


	/** @test */
	public function moduleLoginShouldBeAvailable() {
		$this->assertXPath('//ul/li[@id="LOGIN"]');
	}
}

?>