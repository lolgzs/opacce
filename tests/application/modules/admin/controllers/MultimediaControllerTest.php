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

abstract class Admin_MultimetiaControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAllBy')
			->answers([Class_Bib::newInstanceWithId(1)->setLibelle('Cran-Gévrier'),
								 Class_Bib::newInstanceWithId(3)->setLibelle('Annecy')]);
	}
}




class Admin_MultimediaControllerIndexTest extends Admin_MultimetiaControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
			->whenCalled('findAllBy')
			->answers([Class_Multimedia_Location::newInstanceWithId(33)
								 ->setLibelle('Antibe')
								 ->setIdSite(3)]);

		$this->dispatch('/admin/multimedia', true);
	}


	/** @test */
	public function controllerShouldBeMultimedia() {
		$this->assertController('multimedia');
	}


	/** @test */
	public function actionShouldBeIndex() {
		$this->assertAction('index');
	}


	/** @test */
	public function titleShouldBeSitesMultimedia() {
		$this->assertXPathContentContains('//h1', 'Sites multimédia');
	}


	/** @test */
	public function antibeShouldBePresent() {
		$this->assertXPathContentContains('//table[@id="multimedia_location"]//td', 'Antibe');
	}


	/** @test */
	public function antibeShouldBeBoundToBibAnnecy() {
		$this->assertXPathContentContains('//table[@id="multimedia_location"]//td', 'Annecy');
	}


	/** @test */
	public function antibeEditLinkShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/multimedia/edit/id/33")]');
	}


	/** @test */
	public function antibeBrowseLinkShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "/multimedia/browse/id/33")]');
	}
}




class Admin_MultimediaControllerEditTest extends Admin_MultimetiaControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Multimedia_Location::newInstanceWithId(33)
			->setIdSite(3)
			->setLibelle('Antibe')
			->setSlotSize(15)
			->setMaxSlots(4)
			->setAutoholdMinTime(5);
				
		$this->dispatch('/admin/multimedia/edit/id/33', true);
	}


	/** @test */
	public function titleShouldBeModificationAntibe() {
		$this->assertXPathContentContains('//h1', 'Modification du site multimédia "Antibe"');
	}


	/** @test */
	public function slotSizeInputShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@value="15"][@name="slot_size"]');
	}


	/** @test */
	public function maxSlotsInputShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@value="4"][@name="max_slots"]');
	}


	/** @test */
	public function minHoldDelayInputShouldBePresent() {
		$this->assertXPath('//input[@name="hold_delay_min"]');
	}


	/** @test */
	public function autoHoldMinTimeShouldBePresent() {
		$this->assertXPath('//input[@name="autohold_min_time"][@value="5"]');
	}


	/** @test */
	public function maxHoldDelayInputShouldBePresent() {
		$this->assertXPath('//input[@name="hold_delay_max"]');
	}


	/** @test */
	public function authDelayInputShouldBePresent() {
		$this->assertXPath('//input[@name="auth_delay"]');
	}


	/** @test */
	public function autoHoldInputShouldBePresent() {
		$this->assertXPath('//input[@name="autohold"]');
	}


	/** @test */
	public function autoHoldMaxSlotsInputShouldBePresent() {
		$this->assertXPath('//input[@name="autohold_slots_max"]');
	}


	/** @test */
	public function selectForIdSiteShouldBePresent() {
		$this->assertXPath('//select[@name="id_site"]//option[@value="3"][@label="Annecy"][@selected="selected"]');
		$this->assertXPath('//select[@name="id_site"]//option[@value="1"][@label="Cran-Gévrier"]');
	}
}




class Admin_MultimediaControllerBrowseTest extends Admin_MultimetiaControllerTestCase {
	public function setUp() {
		parent::setUp();

		$device = Class_Multimedia_Device::getLoader()->newInstanceWithId(1)
				->setLibelle('Poste 1')
				->setOs('Archlinux');

		$group = Class_Multimedia_DeviceGroup::getLoader()->newInstanceWithId(2)
				->setLibelle('Documentation')
				->setDevices(array($device));

		$device->setGroup($group);
		Class_Multimedia_Location::getLoader()->newInstanceWithId(33)
				->setLibelle('Antibe')
				->setGroups(array($group));
				
		$this->dispatch('/admin/multimedia/browse/id/33', true);
	}


	/** @test */
	public function subtitleShouldBePostesDuSiteAntibe() {
		$this->assertXPathContentContains('//h2', 'Postes du site multimédia "Antibe"');
	}

	
	/** @test */
	public function posteUnShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Poste 1');
	}


	/** @test */
	public function archlinuxShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Archlinux');
	}

	/** @test */
	public function groupDocumentationShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Documentation');
	}
}


class Admin_MultimediaControllerAddTest extends Admin_MultimetiaControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/multimedia/add', true);
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/multimedia');
	}
}


class Admin_MultimediaControllerDeleteTest extends Admin_MultimetiaControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/multimedia/delete/id/255', true);
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/multimedia');
	}
}