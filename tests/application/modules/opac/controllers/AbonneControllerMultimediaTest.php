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

class AbonneControllerMultimediaAuthenticateTest extends AbstractControllerTestCase{
	public function setUp() {
		parent::setUp();
		Zend_Auth::getInstance()->clearIdentity();
		$laurent= Class_Users::getLoader()->newInstanceWithId(8)
									->setLogin("laurent")
									->setPassword("afi")
									->setNom('laffont')
									->setPrenom('laurent')
									->setRoleLevel(4)
									->setIdabon('bca2')
									->setNaissance('1978-02-17');
		
		$baptiste= Class_Users::getLoader()->newInstanceWithId(9)
									->setLogin("baptiste")
									->setPassword("afi")
									->setRoleLevel(2)
									->setNaissance('2005-02-17')
									->setDateFin('3000-01-01');
		
		$mireille= Class_Users::getLoader()->newInstanceWithId(10)
									->setLogin("mireille")
									->setPassword("afi")
									->setDateFin('1999-01-01');
		
		$arnaud= Class_Users::getLoader()->newInstanceWithId(11)
									->setLogin("arnaud")
									->setPassword("lelache");
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
						->whenCalled('findFirstBy')
						->with(array('login'=> 'laurent'))
						->answers($laurent)
						
						->whenCalled('findFirstBy')
						->with(array('login'=> 'baptiste'))
						->answers($baptiste)
						
						->whenCalled('findFirstBy')
						->with(array('login'=> 'mireille'))
						->answers($mireille)
						
						->whenCalled('findFirstBy')
						->with(array('login'=> 'arnaud'))
						->answers($arnaud)
						
						->whenCalled('findFirstBy')
						->answers(null);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroupMembership')
				->whenCalled('findAllBy')
				->with(array('role' => 'user', 'model' => $laurent))
				->answers(array(Class_UserGroupMembership::getLoader()
						->newInstance()
						->setUserGroup(Class_UserGroup::getLoader()
							->newInstanceWithId(1)
							->setLibelle('Devs agiles'))))

				->whenCalled('findAllBy')
				->with(array('role' => 'user', 'model' => $baptiste))
				->answers(array(Class_UserGroupMembership::getLoader()
						->newInstance()
						->setUserGroup(Class_UserGroup::getLoader()
							->newInstanceWithId(2)
							->setLibelle('Devs Oldschool'))))

				->whenCalled('findAllBy')
				->with(array('role' => 'user', 'model' => $arnaud))
				->answers(array(Class_UserGroupMembership::getLoader()
						->newInstance()
						->setUserGroup(Class_UserGroup::getLoader()
							->newInstanceWithId(3)
							->setLibelle('Patrons'))));
	}


	/** @test */
	public function responseShouldNotBeARedirect() {
		$json = $this->getJson('/abonne/authenticate/login/laurent/password/afi');
		$this->assertNotRedirect();
	}


	/** @test */
	public function withoutPosteShouldReturnErrorMissingParameter() {
		$json = $this->getJson('/abonne/authenticate/login/laurent/password');
		$this->assertEquals('MissingParameter', $json->error);
	}


	/** @test */
	public function getAbonneZorkShouldReturnErrorUserNotFound() {
		$json= $this->getJson('/abonne/authenticate/login/zork/password/toto/poste/1');
		$this->assertEquals("UserNotFound", $json->error);
		
	}
	

	/** @test */
	public function authenticateAbonneLaurentPasswordXXXShouldReturnWrongPassword() {
		$json=$this->getJson('/abonne/authenticate/login/laurent/password/xxx/poste/1');
		$this->assertEquals("PasswordIsWrong", $json->error);	
	}

	
	/** @test */
	public function rightAuthenticationShouldNotReturnError() {
		$json = $this->getJson('/abonne/authenticate/login/laurent/password/afi/poste/1');
		$this->assertFalse(property_exists($json, 'error'));
		return $json;
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentIdShoudBe8($json) {
		$this->assertEquals('8', $json->id);
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentLoginShoudBelaurent($json) {
		$this->assertEquals('laurent', $json->login);
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentPasswordShoudBeAfi($json) {
		$this->assertEquals('afi',$json->password);
	}
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentNomShoudBelaffont($json) {
		$this->assertEquals('laffont', $json->nom);
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentPrenomShoudBelaurent($json) {
		$this->assertEquals('laurent', $json->prenom);
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentDateNaissanceShoudBe1978_02_17($json) {
		$this->assertEquals('1978/02/17', $json->date_naissance);
	}
	
	
	/**
	 * @test 
	 * @depends rightAuthenticationShouldNotReturnError
	 */
	public function laurentGroupeShoudBeAdulteAbonneAdminAndAgile($json) {
		$this->assertEquals(array('adulte','abonne','admin_bib', 'Devs agiles'), $json->groupes);
	}
	
	
	/** @test */
	public function baptisteGroupesShouldBeMineurAbonneAndOldSchool(){
		$json = $this->getJson('/abonne/authenticate/login/baptiste/password/afi/poste/1');
		$this->assertEquals(array('mineur','abonne_sigb', 'Devs Oldschool'), $json->groupes);	
	}
	
	
	/** @test */
		public function mireilleAuthenticateShouldReturnSubscriptionExpired(){
		$json=$this->getJson('/abonne/authenticate/login/mireille/password/afi/poste/1');
		$this->assertEquals('SubscriptionExpired',$json->error);	
	}
	

	/** @test */
	public function arnaudGroupesShouldBeInviteAndPatrons() {
		$json=$this->getJson('/abonne/authenticate/login/arnaud/password/lelache/poste/1');
		$this->assertEquals(array('invite', 'Patrons'), $json->groupes);	
	}


	protected function getJson($url) {
		$this->dispatch($url);
		return json_decode($this->_response->getBody());
	}
}


abstract class AbonneControllerMultimediaHoldTestCase extends AbstractControllerTestCase {
	protected $_session;
		
	public function setUp() {
		parent::setUp();
		$this->_session = new Zend_Session_Namespace('abonneController');
	}

	/** @test */
	public function timelineWithFiveStepsShouldBePresent() {
		$this->assertXPathCount('//div[@class="timeline"]//li', 5);
	}

	protected function _prepareLocationInSession() {
		$this->_session->location = 123;
		Class_Multimedia_Location::getLoader()
				->newInstanceWithId(123)
				->setSlotSize(30)
				->setMaxSlots(4);
	}

	protected function _assertCurrentTimelineStep($step) {
		$this->_assertTimeLineStepWithClass($step, 'selected');
	}


	protected function _assertPassedTimelineStep($step) {
		$this->_assertTimeLineStepWithClass($step, 'passed');
	}


	protected function _assertTimeLineStepWithClass($step, $class) {
		$this->assertXPathContentContains('//div[@class="timeline"]//li[@class="' . $class . '"]',
			                                $step);
	}
}


class AbonneControllerMultimediaHoldLocationTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
				->whenCalled('findAllBy')
				->answers(array(
						Class_Multimedia_Location::getLoader()->newInstanceWithId(1)
						->setLibelle('Salle 1'),
						Class_Multimedia_Location::getLoader()->newInstanceWithId(2)
						->setLibelle('Salle 2')));
		$this->dispatch('/abonne/multimedia-hold-location', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeLieu() {
		$this->_assertCurrentTimelineStep('Lieu');
	}
		

	/** @test */
	public function locationSalle1ShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-day/location/1")]', 'Salle 1');
	}


	/** @test */
	public function locationSalle2ShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-day/location/2")]', 'Salle 2');
	}
}


class AbonneControllerMultimediaHoldDayTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/abonne/multimedia-hold-day/location/1', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeJour() {
		$this->_assertCurrentTimelineStep('Jour');
	}


	/** @test */
	public function timelineStepShouldBePassed() {
		$this->_assertPassedTimelineStep('Lieu');
	}


	/** @test */
	public function calendarShouldBePresent() {
		$this->assertXPath('//div[@class="calendar"]');
	}
}


class AbonneControllerMultimediaHoldHoursTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->dispatch('/abonne/multimedia-hold-hours/day/2012-09-09', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeHoraires() {
		$this->_assertCurrentTimelineStep('Horaires');
	}
		

	/** @test */
	public function listOfStartTimesShouldBePresent() {
		$this->assertXPathCount('//select[@id="hold-time"]/option', 48);
	}


	/** @test */
	public function startingAt10ShouldBePossible() {
		$this->assertXPathContentContains('//option[@value="10:00"]', '10h00');
	}


	/** @test */
	public function oneHourDurationLinkShouldBePresent() {
		$this->assertXPathContentContains('//a', '1h');
	}


	/** @test */
	public function oneHourAndAHalfDurationLinkShouldBePresent() {
		$this->assertXPathContentContains('//a', '1h30mn');
	}
}


class AbonneControllerMultimediaHoldDeviceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceGroup')
				->whenCalled('findAllBy')
				->answers(array(Class_Multimedia_DeviceGroup::getLoader()
						->newInstanceWithId(3)));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Device')
				->whenCalled('findAllBy')
				->answers(array(Class_Multimedia_Device::getLoader()
						->newInstanceWithId(1)
						->setLibelle('Poste 1')
						->setOs('Ubuntu Lucid')
						->setDisabled(0)));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('countBy')
				->answers(0);
				
		$this->dispatch('/abonne/multimedia-hold-device/time/' . urlencode('11:15')
			                                                     . '/duration/45', true);
	}

	/** @test */
	public function currentTimelineStepShouldBePoste() {
		$this->_assertCurrentTimelineStep('Poste');
	}


	/** @test */
	public function posteUnShouldBeHoldable() {
		$this->assertXPathContentContains(
			'//a[contains(@href, "multimedia-hold-confirm/device/1")]',
			'Poste 1');
	}
}
