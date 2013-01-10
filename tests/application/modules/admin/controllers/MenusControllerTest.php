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

class Admin_MenusControllerProfilJazzTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();	

		$this->profil_jazz = Class_Profil::getLoader()
			->newInstanceWithId(5)
			->setBrowser('opac')
			->setLibelle('Jazz pour tous');

		
		$this->dispatch('admin/menus/index?'.
										http_build_query(array('id_profil' => '5',
																					 'id_bib' => 'null',
																					 'type_menu' => 'MENU',
																					 'id_module' => 1,
																					 'libelle' => 'Pratique',
																					 'picto' => 'bookmark.png',
																					 'preferences' => '')));
	}


	/** @test */
	public function libellePratiqueShouldBeDisplayed() {
		$this->assertXPath("//input[@name='libelle'][@value='Pratique']");
	}


	/** @test */
	public function bookmarkIconShouldBeDisplayed() {
		$this->assertXPath("//img[@id='select_picto'][contains(@src, 'bookmark.png')]");
	}


	/** @test */
	function profilTranslaterShouldBeNullTranslator() {
		//pour éviter que le contenu des menus / données sérializées soient traduites dans l'interface d'édition
		$this->assertInstanceOf('Class_Profil_NullTranslator', Class_Profil::getCurrentProfil()->getTranslator());
	}
}




class Admin_MenusControllerEditMenuBibNumTest extends Admin_AbstractControllerTestCase {
	public function createBibNum() {
		$classique_cat = Class_AlbumCategorie::getLoader()
			->newInstanceWithId(10)
			->setLibelle('Classique')
			->setSousCategories(array())
			->setAlbums(array());

		$jazz_cat = Class_AlbumCategorie::getLoader()
			->newInstanceWithId(4)
			->setParentCategorie(null)
			->setLibelle('Jazz')
			->setAlbums(array())
			->setSousCategories(array(

																$mag_jazz_cat = Class_AlbumCategorie::getLoader()
																->newInstanceWithId(41)
																->setParentId(4)
																->setSousCategories(array())
																->setLibelle('Magazines')
																->setAlbums(array()),

																$bd_jazz_cat = Class_AlbumCategorie::getLoader()
																->newInstanceWithId(42)
																->setParentId(4)
																->setSousCategories(array())
																->setLibelle('BD')
																->setAlbums(array( 
																									$fitzgerald = Class_Album::getLoader()
																									->newInstanceWithId(421)
																									->setCatId(42)
																									->setTitre('Fitzgerald'),

																									 $armstrong = Class_Album::getLoader()
																									 ->newInstanceWithId(422)
																									 ->setCatId(42)
																									 ->setTitre('Armstrong')
																									 ))
																)									
													);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('findAll')
			->answers(array($jazz_cat, $classique_cat));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('findAll')
			->answers(array($fitzgerald, $armstrong));
	}

	
	public function setUp() {
		parent::setUp();
		$this->createBibNum();
		$this->dispatch('admin/menus/album?'.
										http_build_query(array('id_profil' => '5',
																					 'id_bib' => 'null',
																					 'type_menu' => 'BIBNUM',
																					 'id_module' => 1,
																					 'libelle' => 'Lien vers un album',
																					 'picto' => 'book.png',
																					 'preferences' => 'album_id=422')));
	}


	/** @test */
	public function libelleLienVersUnAlbumShouldBeDisplayed() {
		$this->assertXPath("//input[@name='libelle'][@value='Lien vers un album']");
	}


	/** @test */
	function albumSelectShouldContainsFitzgerald() {
		$this->assertXPathContentContains("//select[@id='album_id']//option[@value='421']", "Jazz&gt;BD&gt;Fitzgerald", $this->_response->getBody());
	}


	/** @test */
	function albumSelectShouldContainsArmstrong() {
		$this->assertXPathContentContains("//select//option[@value='422'][@selected='selected']", 
																			"Jazz&gt;BD&gt;Armstrong", 
																			$this->_response->getBody());
	}


	/** @test */
	function albumArmstrongShouldBeFirst() {
		$this->assertXPathContentContains("//select//option[1]", "Armstrong");
	}
}

?>