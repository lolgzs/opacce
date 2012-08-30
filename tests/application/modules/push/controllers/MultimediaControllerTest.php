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

abstract class Push_MultimediaControllerInvalidConfigTestCase extends AbstractControllerTestCase {
	/** @var Storm_Test_ObjectWrapper */
	protected $_log;

	public function setUp() {
		parent::setUp();
				
		$this->_log = Storm_Test_ObjectWrapper::mock()
				->whenCalled('err')
				->answers(null)
				
				->whenCalled('info')
				->answers(null);

		Class_Multimedia::setLog($this->_log);

		$this->postDispatch('/push/multimedia/config', $this->_getInvalidParams());
	}


	/** @test */
	public function startShouldBeLogged() {
		$this->assertTrue($this->_log->methodHasBeenCalled('info'));
	}


	/** @return array */
	protected function _getInvalidParams() {
		return array();
	}


	/** @param $message string */
	protected function _assertErrorLogWithMessage($message) {
		$this->assertEquals($message, $this->_log->getFirstAttributeForLastCallOn('err'));
	}
}


class Push_MultimediaControllerMissingJsonConfigTest extends Push_MultimediaControllerInvalidConfigTestCase {	
	/** @test */
	public function missingJsonErrorShouldBeLogged() {
		$this->_assertErrorLogWithMessage('Missing json parameter');
	}
}


class Push_MultimediaControllerMissingSignConfigTest extends Push_MultimediaControllerInvalidConfigTestCase {
	/** @return array */
	protected function _getInvalidParams() {
		return array('json' => '{}');
	}
	
	/** @test */
	public function missingSignErrorShouldBeLogged() {
		$this->_assertErrorLogWithMessage('Missing sign parameter');
	}
}


class Push_MultimediaControllerInvalidJsonConfigTest extends Push_MultimediaControllerInvalidConfigTestCase {
	/** @return array */
	protected function _getInvalidParams() {
		return array('json' => 'it is invalid', 'sign' => 'iu/-@+uieiucrc');
	}
	
	/** @test */
	public function invalidJsonErrorShouldBeLogged() {
		$this->_assertErrorLogWithMessage('Invalid json');
	}
}


class Push_MultimediaControllerInvalidSignConfigTest extends Push_MultimediaControllerInvalidConfigTestCase {
	/** @return array */
	protected function _getInvalidParams() {
		return array('json' => '[{"libelle":"Groupe 1", "id":1, "site":{"id":1,"libelle":"Site 1"}, "postes":[{"id":1, "libelle":"Poste 1", "os":"Windows XP", "maintenance":"1"}, {"id":2, "libelle":"Poste 2", "os":"Ubuntu Lucid Lynx", "maintenance":"0"}]}]',
			                     'sign' => 'iu/-@+uieiucrc');
	}
	
	/** @test */
	public function signCheckFailureErrorShouldBeLogged() {
		$this->_assertErrorLogWithMessage('Sign check failure');
	}
}


class Push_MultimediaControllerValidConfigTest extends AbstractControllerTestCase {
	protected $_group;
	protected $_device_wrapper;
	protected $_device_to_delete;
	protected $_devices = array();

	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
				->whenCalled('findFirstBy')
				->answers(null)
				
				->whenCalled('save')
				->willDo(function($model) {$model->setId(1);return true;});

		$device_group_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceGroup')
				->whenCalled('findFirstBy')
				->answers(null)
				
				->whenCalled('save')
				->answers(true);

		$this->_device_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Device')
				->whenCalled('findFirstBy')
				->answers(null)

				->whenCalled('delete')
				->answers(null)
				
				->whenCalled('save')
				->willDo(function ($model) {
						$this->_devices[] = $model;
						return true;
					})

			->whenCalled('findAllBy')
  		->with(['where' => 'id_origine not in(\'1-1\',\'1-2\') and id_origine like \'1-%\''])		 
			->answers([$this->_device_to_delete = Class_Multimedia_Device::newInstanceWithId(34)->setIdOrigine('1-34')]);

		Class_Multimedia::setInstance(Storm_Test_ObjectWrapper::mock()
			->whenCalled('isValidHashForContent')
			->answers(true));

		$this->postDispatch(
				'/push/multimedia/config',
				array(
						'json' => '[{"libelle":"Groupe 1", "id":1, "site":{"id":1,"libelle":"Site 1","admin_url":"192.168.2.92"}, "postes":[{"id":1, "libelle":"Poste 1", "os":"Windows XP", "maintenance":"1"}, {"id":2, "libelle":"Poste 2", "os":"Ubuntu Lucid Lynx", "maintenance":"0"}]}]',
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
	public function deviceGroupSiteUrlAdminShouldBeAdminSite() {
		$this->assertEquals("http://192.168.2.92", $this->_group->getLocation()->getAdminUrl());
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



	/** @test */
	public function deviceNotInPushShouldHaveBeenDeleted() {
		$this->assertEquals($this->_device_to_delete, Class_Multimedia_Device::getFirstAttributeForLastCallOn('delete'));
	}

}