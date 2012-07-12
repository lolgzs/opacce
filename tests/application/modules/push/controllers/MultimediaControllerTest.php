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

class Push_MultimediaControllerValidConfigTest extends AbstractControllerTestCase {
	protected $_group;
	protected $_device_wrapper;
	protected $_devices = array();

	public function setUp() {
		parent::setUp();
		$device_group_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceGroup')
				->whenCalled('save')
				->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
				->whenCalled('save')
				->answers(true);

		$this->_device_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Device')
				->whenCalled('save')
				->willDo(function ($model) {
						$this->_devices[] = $model;
						return true;
					});

		Class_Multimedia::setInstance(Storm_Test_ObjectWrapper::mock()
			->whenCalled('isValidHashForContent')
			->answers(true));

				$this->postDispatch(
						'/push/multimedia/config',
						array(
							'json' => '[{"libelle":"Groupe 1", "id":1, "site":{"id":1,"libelle":"Site 1"}, "postes":[{"id":1, "libelle":"Poste 1", "os":"Windows XP", "maintenance":"1"}, {"id":2, "libelle":"Poste 2", "os":"Ubuntu Lucid Lynx", "maintenance":"0"}]}]',
							'sign' => 'auieau09676IUE96'));
		$this->_group = $device_group_wrapper->getFirstAttributeForLastCallOn('save');
	}

		
	/** @test */
	public function deviceGroupLibelleShouldBeGroupeOne() {
		$this->assertEquals('Groupe 1', $this->_group->getLibelle());
	}


	/** @test */
	public function deviceGroupIdOrigineShouldBeOne() {
		$this->assertEquals('1-1', $this->_group->getIdOrigine());
	}


  /** @test */
	public function deviceGroupSiteShouldBeSiteOne() {
		$this->assertEquals('Site 1', $this->_group->getLocation()->getLibelle());
		$this->assertEquals(1, $this->_group->getLocation()->getIdOrigine());
	}


	/** @test */
	public function deviceGroupShouldHaveCreatedTwoDevices() {
		$this->assertEquals(2, count($this->_devices));
	}


	/** @test */
	public function firstDeviceLibelleShouldBePoste1() {
		$this->assertEquals('Poste 1', $this->_devices[0]->getLibelle());
	}


	/** @test */
	public function firstDeviceOSShouldBeWindowsXP() {
		$this->assertEquals('Windows XP', $this->_devices[0]->getOs());
	}


	/** @test */
	public function firstDeviceIdOrigineShouldBeOne() {
		$this->assertEquals('1-1', $this->_devices[0]->getIdOrigine());
	}


	/** @test */
	public function firstDeviceGroupShouldBeGroupe1() {
		$this->assertEquals($this->_group, $this->_devices[0]->getGroup());
	}


	/** @test */
	public function firstDeviceShouldBeDisabled() {
		$this->assertTrue($this->_devices[0]->isDisabled());
	}


	/** @test */
	public function secondDeviceLibelleShouldBePoste2() {
		$this->assertEquals('Poste 2', $this->_devices[1]->getLibelle());
	}


	/** @test */
	public function secondDeviceOSShouldBeUbuntuLucid() {
		$this->assertEquals('Ubuntu Lucid Lynx', $this->_devices[1]->getOs());
	}


	/** @test */
	public function secondDeviceIdOrigineShouldBeTwo() {
		$this->assertEquals('1-2', $this->_devices[1]->getIdOrigine());
	}

	
	/** @test */
	public function secondDeviceShouldNotBeDisabled() {
		$this->assertFalse($this->_devices[1]->isDisabled());
	}

}