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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';

abstract class SitoViewHelperTestCase extends ViewHelperTestCase {
	protected $html;
	protected $_preferences = array();

	public function setUp() {
		parent::setUp();

		$site_fosdem = Class_Sitotheque::getLoader()
			->newInstanceWithId(12)
			->setTitre('FOSDEM')
			->setDescription('plein de bières belges')
			->setUrl('http://www.fosdem.org')
			->setCategorie($belgique = Class_SitothequeCategorie::getLoader()
										 ->newInstanceWithId(5)
										 ->setLibelle('Belgique'));


		$site_rmll = Class_Sitotheque::getLoader()
			->newInstanceWithId(12)
			->setTitre('RMLL')
			->setDescription('du vin du vin !')
			->setUrl('http://www.rmll.info')
			->setCategorie($france = Class_SitothequeCategorie::getLoader()
										 ->newInstanceWithId(9)
										 ->setLibelle('France'));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Sitotheque')
			->whenCalled('getSitesFromIdsAndCategories')
			->answers(array($site_fosdem))

			->whenCalled('findAllBy')
			->with(array('limit' => 50))
			->answers(array($site_fosdem, $site_rmll));

		$helper = new ZendAfi_View_Helper_Accueil_Sito(2, array('division' => '1',
																														'type_module' => 'SITO',
																														'preferences' => $this->_preferences));
		$this->html = $helper->getBoite();
	}
}




class SitoViewHelperSelectItemsAndCatsTest extends SitoViewHelperTestCase {
	protected $_preferences = array('titre' => 'Ma sito',
																	'type_aff' => 1,
																	'id_items' => 12,
																	'id_categorie' => 3,
																	'nb_aff' => 2);

	/** @test */
	public function titleShouldBeMaSito() {
		$this->assertXPathContentContains($this->html, 
																			'//div[@class="titre"]//h1', 
																			'Ma sito');
	}

	
	/** @test */
	public function h2ShouldContainsFosdemDotOrg() {
		$this->assertXPathContentContains($this->html, 
																			'//h2//a[contains(@href, "fosdem.org")]', 
																			'FOSDEM');
	}


	/** @test */
	public function divSitothequeShouldContainsPleinDeBieres() {
		$this->assertXPathContentContains($this->html, 
																			'//div[@class="sitotheque"]', 
																			utf8_encode('plein de bières belges'));
	}
}




class SitoViewHelperLastTest extends SitoViewHelperTestCase {
	protected $_preferences = array('titre' => 'Derniers sites',
																	'type_aff' => 2,
																	'nb_aff' => 2);

	/** @test */
	public function titleShouldBeDerniersSites() {
		$this->assertXPathContentContains($this->html, 
																			'//div[@class="titre"]//h1', 
																			'Derniers sites');
	}

	
	/** @test */
	public function h2ShouldContainsRmllDotInfo() {
		$this->assertXPathContentContains($this->html, 
																			'//h2//a[contains(@href, "rmll.info")]', 
																			'RMLL');
	}
}




class SitoViewHelperGroupByCategorieTest extends SitoViewHelperTestCase {
	protected $_preferences = array('titre' => 'Ma sito',
																	'type_aff' => 2,
																	'nb_aff' => 2,
																	'group_by_categorie' => true);


	/** @test */
	public function liEnBelgiqueShouldContainsFosdem() {
		$this->assertXPathContentContains($this->html, 
																			'//ul/li/h2/a[text()="Belgique"]/../../ul/li',
																			'FOSDEM');
	}


	/** @test */
	public function liEnFranceShouldContainsRMLL() {
		$this->assertXPathContentContains($this->html, 
																			'//ul/li/h2/a[text()="France"]/../../ul/li',
																			'RMLL');
	}


	/** @test */
	public function scriptLoaderShouldContainsCodeToAnimateSitotheque() {
		$this->assertContains('ul.sitotheque>li>h2>a', 
													Class_ScriptLoader::getInstance()->html());
	}
}

?>