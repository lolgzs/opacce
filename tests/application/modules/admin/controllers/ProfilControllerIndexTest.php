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

abstract class Admin_ProfilControllerIndexTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		
    Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
      ->whenCalled('findAllByZoneAndBib')
      ->answers(array(
                      Class_Profil::getLoader()
                        ->newInstanceWithId(12)
                        ->setLibelle("Jeunesse")
											  ->setAccessLevel(4)
                        ->setBib(Class_Bib::getLoader()
																   ->newInstanceWithId(5)
																   ->setLibelle("Annecy")),

                      
                      Class_Profil::getLoader()
                        ->newInstanceWithId(8)
                        ->setLibelle("Adulte")
                        ->setCommentaire("Section Adulte")
											  ->setIdSite(0),

                      Class_Profil::getLoader()
											  ->newInstanceWithId(1)
                        ->setLibelle("Portail")
                        ->setCommentaire("Accueil du portail")
											  ->setIdSite(0),

                      Class_Profil::getLoader()
											  ->newInstanceWithId(3)
                        ->setLibelle("Smartphone")
                        ->setBrowser("telephone")
                        ->setIdSite(0)));


		$this->dispatch('/admin/profil/index');
	}
}



class Admin_ProfilControllerWithAdminPortailIndexTest extends Admin_ProfilControllerIndexTestCase  {
	protected function _loginHook($account) {
		parent::_loginHook($account);
		$account->ROLE_LEVEL = 7;
		$account->ID_SITE = 0;
	}


	/** @test */
	public function controllerShouldBeProfil() {
		$this->assertController('profil');
	}


	/** @test */
	public function actionShouldBeIndex() {
		$this->assertAction('index');
	}


	/** @test */
	public function titleShouldBeGestionDesProfils() {
		$this->assertQueryContentContains('title', 'Gestion des profils');
	}


	/** @test */
	public function profilJeunesseShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[1]//div",
																			"Jeunesse");
	}


	/** @test */
	public function profilJeunessePageAccueilShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[1]//a[contains(@href, 'id_profil/12')]",
																			"Accueil");
	}


	/** @test */
	public function profilJeunesseIconeShouldBeEcranRouge() {
		$this->assertXPath("//ul[1]//li[1]//img[contains(@src, 'ecran_rouge.png')]");
	}


	/** @test */
	public function profilAdulteShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[1]//div", "Adulte",
																			$this->_response->getBody());
	}


	/** @test */
	public function pageAccueilProfilAdulteShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[1]//a[contains(@href, 'id_profil/8')]",
																			"Accueil");
	}


	/** @test */
	function previewPageAccueilAnchorTitleShouldBeVisualisationDeLaPageAdulte() {
		$this->assertXPath('//ul[1]//li[1]//a[@rel="prettyPhoto"][contains(@title, "Visualisation de la page \'Adulte\'")]',
											 $this->_response->getBody());
	}


	/** @test */
	public function profilAdulteIconeShouldBeEcran() {
		$this->assertXPath("//ul[1]//li[1]//img[contains(@src, 'ecran.png')]");
	}


	/** @test */
	public function profilPortailShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[2]//div",
																			"Portail");
	}


	/** @test */
	public function profilPortailIconeShouldBeMap() {
		$this->assertXPath("//ul[1]//li[2]//img[contains(@src, 'map.gif')]");
	}


	/** @test */
	public function profilSmartphoneShouldBeVisible() {
		$this->assertXPathContentContains("//ul[1]//li[3]//div",
																			"Smartphone");
	}


	/** @test */
	public function profilSmartphoneIconeShouldBeTelephone() {
		$this->assertXPath("//ul[1]//li[3]//img[contains(@src, 'telephone.gif')]");
	}


	/** @test */
	public function buttonAjouterProfilShouldBeVisible() {
		$this->assertXPathContentContains("//div[contains(@onclick, 'profil/add')]", 
																			"Ajouter un profil");
	}

}


class Admin_ProfilControllerWithAdminBibIndexTest extends Admin_ProfilControllerIndexTestCase  {
	protected function _loginHook($account) {
		parent::_loginHook($account);
		$account->ROLE_LEVEL = 5;
		$account->ID_SITE = 5;
	}


	/** @test */
	public function profilJeunesseShouldBeVisible() {
		$this->assertXPathContentContains("//div",		"Jeunesse");
	}


	/** @test */
	public function profilPortailShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//li//a[contains(@href, 'id_profil=1')]",
																				 "Portail");
	}


	/** @test */
	public function profilSmartphoneShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//li", "Smartphone");
	}


	/** @test */
	public function buttonAjouterProfilShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//div[contains(@onclick, 'profil/add')]", 
																				 "Ajouter un profil");
	}
}

?>