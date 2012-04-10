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
require_once 'AbstractControllerTestCase.php';

abstract class ZoneControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->zone_annecy = Class_Zone::getLoader()
			->newInstanceWithId(2)
			->setLibelle('Annecy')
			->setCouleur('#123')
			->setMapCoords('93,14,87,20')
			->setImage('bassin annecy.jpg');

		$this->zone_pringy = Class_Zone::getLoader()
			->newInstanceWithId(4)
			->setLibelle('Pringy')
			->setCouleur('#456')
			->setImage('pringy.jpg');


		$this->loader_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Zone')
			->whenCalled('findAll')
			->answers(array($this->zone_annecy, $this->zone_pringy))
			->getWrapper();
	}	
}


class ZoneControllerIndexActionTest extends ZoneControllerTestCase {
	public function setUp(){
		parent::setUp();
		$this->dispatch('admin/zone/index');
	}


	/** @test */
	function annecyLibelleShouldBeDisplayed() {
		$this->assertXPathContentContains('//td', 'Annecy');
	}


	/** @test */
	function pringyLibelleShouldBeDisplayed() {
		$this->assertXPathContentContains('//td', 'Pringy');
	}
}



class ZoneControllerPlacerBibsActionTest extends ZoneControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/zone/placerbibs/id_zone/2');
	}


	/** @test */
	public function titreShouldBePlacementDesBibSurLaCarte() {
		$this->assertXPathContentContains('//h1', 'Placement des bibliothèques sur la carte');
	}
}



class ZoneControllerEditAnnecyTest extends ZoneControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/zone/edit/id/2');
	}


	/** @test */
	function inputLibelleShouldContainsAnnecy() {
		$this->assertXPath('//input[@name="libelle"][@value="Annecy"]');
	}

	/** @test */
	function inputCouleurShouldContainsSharp123() {
		$this->assertXPath('//input[@name="couleur"][@value="#123"]');
	}


	/** @test */
	function titleShouldBeModifierLeTerritoireAnnecy() {
		$this->assertXPathContentContains('//h1', 'Modifier le territoire: Annecy');
	}


	/** @test */
	function inputMapCoordsShouldContains_93_14_87_20() {
		$this->assertXPath('//input[@name="map_coords"][@value="93,14,87,20"]');
	}


	/** @test */
	function formActionShouldBeEditId2() {
		$this->assertXPath('//form[contains(@action, "admin/zone/edit/id/2")]');
	}


	/** @test */
	public function iframeTagUploadShouldHaveFilenameBassinAnnecy() {
		$this->assertXPath('//iframe[contains(@src, "filename=bassin%2Bannecy.jpg&")]');
	}
}


class ZoneControllerEditUnknownZoneTest extends ZoneControllerTestCase {
	/** @test */
	function shouldRedirectToIndexPage() {
		$this->dispatch('admin/zone/edit/id/99999');
		$this->assertRedirect('admin/zone/index');
	}
}


class ZoneControllerPostValidDataForAnnecyTest extends ZoneControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('libelle' => 'Bassin annécien',
									'couleur' => '123456',
									'map_coords' => '34,45',
									'image' => 'paquier.jpg');

		$this->loader_wrapper
			->whenCalled('save')
			->answers(true);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('admin/zone/edit/id/2');
	}


	/** @test */
	function libelleShouldBeBassinAnnecien() {
		$this->assertEquals('Bassin annécien', $this->zone_annecy->getLibelle());
	}


	/** @test */
	function shouldRedirectToIndexPage() {
		$this->assertRedirect('admin/zone/index');	
	}


	/** @test */
	function couleurShouldBeSharp123456() {
		$this->assertEquals('#123456', $this->zone_annecy->getCouleur());
	}
}


class ZoneControllerPostEmptyLabelForAnnecyTest extends ZoneControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('libelle' => '');

		$this->loader_wrapper
			->whenCalled('save')
			->answers(true);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('admin/zone/edit/id/2');
	}


	/** @test */
	function shouldNotRedirectToIndexPage() {
		$this->assertNotRedirect('admin/zone/index');	
	}

	/** @test */
	function errorNomShouldBeVisible() {
		$this->assertXPathContentContains('//span', "Vous devez compléter le champ 'Nom'");
	}
}



class ZoneControllerAddActionTest extends ZoneControllerTestCase {
	/** @test */
	function titleShouldBeAjouterUnTerritoire() {
		$this->dispatch('admin/zone/add');
		$this->assertXPathContentContains('//h1', 'Ajouter un territoire');
	}


	/** @test */
	function uploadFormActionShouldPointModuleOpac() {
		$this->dispatch('admin/zone/add');
		$this->assertXPath('//iframe[contains(@src, "localhost/upload/form")]');
	}


	/** @test */
	function postShouldSaveNewObject() {
		$data = array('libelle' => 'Cran',
									'couleur' => '#456',
									'map_coords' => '34,45',
									'image' => 'paquier.jpg');

		$this->loader_wrapper
			->whenCalled('save')
			->answers(true);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('admin/zone/add');

		$new_zone = $this->loader_wrapper->getFirstAttributeForLastCallOn('save');
		$this->assertEquals('Cran', $new_zone->getLibelle());
	}

}

?>