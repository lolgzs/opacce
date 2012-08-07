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
require_once 'ModelTestCase.php';


class TimesSourceForTest extends Class_TimeSource {
	protected $_time;

	public function setTime($time) {
		$this->_time = $time;
		return $this;
	}

	public function time() {
		return $this->_time;
	}
}




abstract class Multimedia_DeviceCurrentHoldTestCase extends ModelTestCase {
	/** @var Class_Multimedia_Device */
	protected $_device;
	/** @var Class_Multimedia_DeviceGroup */
	protected $_group;
	/** @var Class_Multimedia_Location */
	protected $_location;
	/** @var Class_Multimedia_DeviceHold */
	protected $_hold;
	/** @var int */
	protected $_time;
	/** @var Class_Bib */
	protected $_bib_antibes;
	/** @var Storm_Test_ObjectWrapper */
	protected $_time_source;

	public function setUp() {
		parent::setUp();

		$this->_bib_antibes = Class_Bib::newInstanceWithId(5)->setLibelle('Antibes');

		$this->_location = Class_Multimedia_Location::newInstanceWithId(2)
			->setBib($this->_bib_antibes)
			->setOuvertures([]);

		$this->_group = Class_Multimedia_DeviceGroup::newInstanceWithId(2)
				->setLocation($this->_location);

		$this->_device = Class_Multimedia_Device::newInstanceWithId(2)
				->setGroup($this->_group);

		$time = strtotime('today');
		$this->_time_source = (new TimesSourceForTest())->setTime($time);

		Class_Multimedia_Device::setTimeSource($this->_time_source);
		Class_Multimedia_Location::setTimeSource($this->_time_source);
	}


	public function tearDown() {
		Class_Multimedia_Device::setTimeSource(null);
		Class_Multimedia_Location::setTimeSource(null);

		parent::tearDown();
	}
}




class Multimedia_DeviceCurrentHoldForUserHavingHoldTest extends Multimedia_DeviceCurrentHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_location->setAutohold(0);
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('getHoldOnDeviceAtTime')
			  ->with($this->_device, $this->_time_source->time())
				->answers(Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(123)
					->setIdUser(7));

		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldHaveHold() {
		$this->assertNotNull($this->_hold);
	}
}




class Multimedia_DeviceCurrentHoldForUserWithoutHoldAndNoAutoholdTest extends Multimedia_DeviceCurrentHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_location->setAutohold(0);
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->with($this->_device, $this->_time_source->time())
			->answers(null);

		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldNotHaveHold() {
		$this->assertNull($this->_hold);
	}
}




class Multimedia_DeviceCurrentHoldForUserWithoutHoldAndAnotherValidHoldTest extends Multimedia_DeviceCurrentHoldTestCase {
	public function setUp() {
		parent::setUp();

		$this->_location
				->setAuthDelay(10)
				->setAutohold(1)
				->setSlotSize(15);
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->with($this->_device, $this->_time_source->time())
			->answers(Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(123)
								->setIdUser(5)
								->setStart($this->_time_source->time() - 60));

		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldNotHaveHold() {
		$this->assertNull($this->_hold);
	}
}




class Multimedia_DeviceCurrentHoldForUserWithoutHoldAndNoStartTimeTest extends Multimedia_DeviceCurrentHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_location
				->setAuthDelay(10)
				->setAutohold(1)
				->setSlotSize(15);
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->with($this->_device, $this->_time_source->time())
			->answers(null);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
				->whenCalled('getPossibleTimes')
				->answers(array());

		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldNotHaveHold() {
		$this->assertNull($this->_hold);
	}
}




class Multimedia_DeviceCurrentHoldForUserWithoutHoldAndMaxSlotsAfterCloseHoursTest extends Multimedia_DeviceCurrentHoldTestCase {
	public function setUp() {
		parent::setUp();

		$this->_time_source->setTime(strtotime('2012-09-09 09:00:00'));

		$this->_location
			->setAuthDelay(10)
			->setAutohold(1)
			->setSlotSize(15)
			->setAutoholdSlotsMax(600)
			->addOuverture(Class_Ouverture::newInstanceWithId(5)
										 ->setJourSemaine(date('w', $this->_time_source->time()))
										 ->setBib($this->_bib_antibes)
										 ->setHoraires(['08:00', '10:00', '10:00', '10:00']));
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('getHoldOnDeviceAtTime')
				->with($this->_device, $this->_time_source->time())
				->answers(null)

				->whenCalled('getFirstHoldOnDeviceBetweenTimes')
				->answers(null)

				->whenCalled('save')
				->answers(true);
		xdebug_break();
		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldHaveHold() {
		$this->assertNotNull($this->_hold);
	}


	/** @test */
	public function holdEndShouldBeCloseHour() {
		$this->assertEquals($this->_location->getMaxTimeForToday(), $this->_hold->getEnd());
	}
}




class Multimedia_DeviceCurrentHoldForUserWithoutHoldAndMaxSlotsAfterNextHoldStartTest extends Multimedia_DeviceCurrentHoldTestCase {
	/** @var int */
	protected $_nextStartTime;
	
	public function setUp() {
		parent::setUp();
		$this->_location
			->setAuthDelay(10)
			->setAutohold(1)
			->setSlotSize(15)
			->setAutoholdSlotsMax(600)
			->addOuverture(Class_Ouverture::newInstanceWithId(5)
										 ->setBib($this->_bib_antibes)
										 ->setJourSemaine(date('w'))
										 ->setHoraires(['08:00', '12:00', '14:00', '23:00']));

		$this->_nextStartTime = $this->_time_source->time() + (60 * 60);
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('getHoldOnDeviceAtTime')
				->answers(null)

				->whenCalled('getFirstHoldOnDeviceBetweenTimes')
				->answers(Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(333)
					->setStart($this->_nextStartTime))

				->whenCalled('save')
				->answers(true);

		$this->_hold = $this->_device->getCurrentHoldForUser(Class_Users::getLoader()->newInstanceWithId(7));
	}


	/** @test */
	public function shouldHaveHold() {
		$this->assertNotNull($this->_hold);
	}


	/** @test */
	public function holdEndShouldBeNextStartTime() {
		$this->assertEquals($this->_nextStartTime, $this->_hold->getEnd());
	}
}