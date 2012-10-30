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

class ModulesControllerRechercheTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$_SESSION["recherche"] = array("mode" => '');
		$this->dispatch('/admin/modules/recherche?id_profil=2&action1=viewnotice&type_module=recherche&config=site', true);
	}

	
	/** @test */
	public function helpLinkShouldBePresent() {
    $this->assertXPath("//a[@href='https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=3647']//img");
	}


	/** @test */
	public function ressourcesNumeriquesShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Ressources numériques');
	}


	/** @test */
	public function babelthequeShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Babelthèque');
	}
}




class ModulesControllerVariousConfigTest extends Admin_AbstractControllerTestCase {
	/** @test */
	public function modulesAuthBoiteLoginShouldNotFail() {
		$this->dispatch('/admin/modules/auth?config=site&type_module=auth&id_profil=1&action1=boitelogin&action2=');
		$this->assertAction('auth');
	}
}




class ModulesControllerBibIndexTest extends Admin_AbstractControllerTestCase {
	/** @test */
	function optionCacherArticlesShouldBePresentOnIndex() {
		$this->dispatch('/admin/modules/bib?id_profil=2&action1=index&type_module=bib&config=site');
		$this->assertXPath('//input[@type="checkbox"][@name="hide_news"]');
		$this->assertNotXPath('//input[@type="checkbox"][@name="hide_news"][@checked]');
	}


	/** @test */
	function optionCacherArticlesShouldBePresentOnZoneView() {
		$this->dispatch('/admin/modules/bib?id_profil=2&action1=zoneview&type_module=bib&config=site');
		$this->assertXPath('//input[@type="checkbox"][@name="hide_news"]');
	}


	/** @test */
	function optionCacherArticleShouldBeCheckedWhenSetToOne() {
		Class_Profil::getCurrentProfil()->setCfgModules(array("bib" => array("index" => array("hide_news" => 1))));
		$this->dispatch('/admin/modules/bib?id_profil=2&action1=index&type_module=bib&config=site');
		$this->assertXPath('//input[@type="checkbox"][@name="hide_news"][@checked="checked"]', $this->_response->getBody());
	}


	/** @test */
	function postOptionCacherArticleShouldUpdateCfgModules() {
		$this->postDispatch('/admin/modules/bib?id_profil=2&action1=index&type_module=bib&config=site',
												array("hide_news" => 1));
		$cfg_modules = Class_Profil::getCurrentProfil()->getCfgModulesAsArray();
		$this->assertEquals(1, $cfg_modules["bib"]["index"]["hide_news"]);
	}
}




class ModulesControllerConfigRechercheResultatTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$_SESSION["recherche"] = array("mode" => '');
	}


	/** @test */
	public function checkboxSuggestionAchatShouldBeCheckedByDefault() {
		Class_Profil::getCurrentProfil()->setCfgModules([]);
		$this->dispatch('/admin/modules/recherche?config=site&type_module=recherche&id_profil=2&action1=resultat&action2=simple', true);
		$this->assertXPath('//input[@type="checkbox"][@name="suggestion_achat"][@checked="checked"]');
	}


	/** @test */
	public function checkboxSuggestionAchatShouldBeUncheckedIfPrefencesSuggestionEqualsZero() {
		Class_Profil::getCurrentProfil()->setCfgModules(['recherche' => ['simple' => ['suggestion_achat' => 0]]]);
		$this->dispatch('/admin/modules/recherche?config=site&type_module=recherche&id_profil=2&action1=resultat&action2=simple', true);
		$this->assertXPath('//input[@type="checkbox"][@name="suggestion_achat"][@checked="checked"]');
	}


	/** @test */
	public function withAction2ViewNoticeShouldDisplayConfigurationOfPageNotice() {
		$this->dispatch('/admin/modules/recherche?config=site&type_module=recherche&id_profil=2&action1=viewnotice');
		$this->assertXPathContentContains('//h1', 'Propriétés d\'affichage des notices');
	}
}

?>
