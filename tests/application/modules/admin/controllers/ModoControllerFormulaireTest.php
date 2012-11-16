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

class ModoControllerFormulaireForArticleListTest extends Admin_AbstractControllerTestCase {
  public function setUp() {
    parent::setUp();
		$article = Class_Article::newInstanceWithId(12, ['titre' => 'Inscrivez vous au Hackaton']);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
		->whenCalled('findAllBy')
		->with([ 'role' => 'article',
						 'model' => $article,
						 'order' => 'date_creation desc'])
		->answers([
			Class_Formulaire::newInstanceWithId(3, ['data' => serialize(['nom' => 'Tinguette',
																																	 'prenom' => 'Quentine'])]),

			Class_Formulaire::newInstanceWithId(5, ['data' => serialize(['nom' => 'Bougie',
																																	 'Prenom' => 'Mireille'])]),

			Class_Formulaire::newInstanceWithId(6, ['data' => serialize(['name' => 'Lefort',
																																	 'prenom' => 'Nono',
																																	 'age' => 12])]) 
		])
		->beStrict();


    $this->dispatch('admin/modo/formulaires/id_article/12', true);
  }


  /** 
   * @test
   */
  public function h1ShouldContainsFormulaires() {
    $this->assertXPathContentContains('//h1', 'Modération des formulaires');
	}


	/** @test */
	public function aTDShouldContainsTinguette() {
		$this->assertXPathContentContains('//td', 'Tinguette');
	}


	/** @test */
	public function aTDShouldContainsBougie() {
		$this->assertXPathContentContains('//td', 'Bougie');
	}


	/** @test */
	public function aLastTDShouldContainsAge12() {
		$this->assertXPathContentContains('//td', '12',$this->_response->getBody());
	}


	/** @test */
	public function aTDShouldContainsPrenomMireille() {
		$this->assertXPathContentContains('//td', 'Mireille',$this->_response->getBody());
	}

}

class ModoControllerFormulaireListTest extends Admin_AbstractControllerTestCase {
  public function setUp() {
    parent::setUp();
		$hackaton =		Class_Article::newInstanceWithId(4, ['titre' => 'Inscrivez vous au Hackaton']);
		$preinscription =Class_Article::newInstanceWithId(2, ['titre' => 'Formulaire de préinscription']);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
		->whenCalled('findAll')
		->with('select id_article,titre from cms_article where id_article in (select distinct id_article from formulaires)')
		->answers([
			$hackaton,
			$preinscription
		]);



    $this->dispatch('admin/modo/formulaires/', true);
  }


	/** @test */
	public function liShouldContainsLinkToFormulaireForHackaton() {
		$this->assertXPathContentContains('//li/a[contains(@href,"formulaires/id_article/4")]', 'Inscrivez vous au Hackaton',$this->_response->getBody());
	}


}


class ModoControllerFormulaireIndexWithOptionActivatedTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('CMS_FORMULAIRES')->setValeur(1);
		$this->dispatch('admin/modo/', true);
	}

	/** @test */
	public function linkToModerateFormulairesShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/admin/modo/formulaires")]');
	}

}


class ModoControllerFormulaireIndexWithOptionDesactivatedTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('CMS_FORMULAIRES')->setValeur(0);
		$this->dispatch('admin/modo/', true);
	}

	/** @test */
	public function linkToModerateFormulairesShouldNotBePresent() {
		$this->assertNotXPath('//a[contains(@href, "/admin/modo/formulaires")]');
	}

}

?>

