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

abstract class AbonneControllerMultimediaAuthenticateTestCase extends AbstractControllerTestCase {
	protected $_json;

	public function setUp() {
		parent::setUp();
		Zend_Auth::getInstance()->clearIdentity();
	}


	/**
	 * @param $url string
	 * @return stdClass
	 */
	protected function getJson($url) {
		$this->dispatch($url, true);
		return json_decode($this->_response->getBody());
	}


	/**
	 * @param $user Class_Users
	 */
	protected function _expectUserToLoad($user) {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('findFirstBy')
			->with(array('login'=> $user->getLogin()))
			->answers($user)
				
			->whenCalled('findFirstBy')
			->answers(null);
	}


	/**
	 * @param $user Class_Users
	 * @param $group_label string
	 */
	protected function _expectGroupForUser($user, $group_label) {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroupMembership')
				->whenCalled('findAllBy')
				->with(array('role' => 'user', 'model' => $user))
				->answers(array(Class_UserGroupMembership::getLoader()
						->newInstance()
						->setUserGroup(Class_UserGroup::getLoader()
							->newInstanceWithId(1)
							->setLibelle($group_label))));
	}
}


class AbonneControllerMultimediaAuthenticateValidationTest extends AbonneControllerMultimediaAuthenticateTestCase {
	public function setUp() {
		parent::setUp();
		$this->_expectUserToLoad(AbonneControllerMultimediaUsersFixtures::getLaurent());
	}

		
	/** @test */
	public function responseShouldNotBeARedirect() {
		$json = $this->getJson('/abonne/authenticate/login/any/password/any');
		$this->assertNotRedirect();
	}


	/** @test */
	public function withoutPosteShouldReturnErrorMissingParameter() {
		$json = $this->getJson('/abonne/authenticate/login/any');
		$this->assertEquals('MissingParameter', $json->error);
	}


	/** @test */
	public function withoutSiteShouldReturnErrorMissingParameter() {
		$json = $this->getJson('/abonne/authenticate/login/any/password/any/poste/1');
		$this->assertEquals('MissingParameter', $json->error);
	}


	/** @test */
	public function getAbonneZorkShouldReturnErrorUserNotFound() {
		$json = $this->getJson('/abonne/authenticate/login/any/password/toto/poste/1/site/1');
		$this->assertEquals("UserNotFound", $json->error);
	}


	/** @test */
	public function authenticateAbonneLaurentPasswordXXXShouldReturnWrongPassword() {
		$json = $this->getJson('/abonne/authenticate/login/laurent/password/xxx/poste/1/site/1');
		$this->assertEquals("PasswordIsWrong", $json->error);	
	}
}


class AbonneControllerMultimediaAuthenticateMireilleTest extends AbonneControllerMultimediaAuthenticateTestCase {
	public function setUp() {
		parent::setUp();
		$user = AbonneControllerMultimediaUsersFixtures::getMireille();
		$this->_expectUserToLoad($user);

		$this->_json = $this->getJson('/abonne/authenticate/login/mireille/password/afi/poste/1/site/1');
	}


	/** @test */
	public function shouldReturnSubscriptionExpired() {
		$this->assertEquals('SubscriptionExpired', $this->_json->error);	
	}
}


abstract class AbonneControllerMultimediaAuthenticateValidTestCase extends AbonneControllerMultimediaAuthenticateTestCase {
	protected $_user;
	protected $_group;

	public function setUp() {
		parent::setUp();

		$this->_initUser();
		$this->_expectUserToLoad($this->_user);
		$this->_expectGroupForUser($this->_user, $this->_group);
		$this->_launch();
	}


	protected function _launch() {
		$this->_json = $this->getJson(sprintf('/abonne/authenticate/login/%s/password/%s/poste/1/site/1',
				                                  $this->_user->getLogin(),
				                                  $this->_user->getPassword()));
	}


	protected function _initUser() {}
}


class AbonneControllerMultimediaAuthenticateLaurentTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getLaurent();
		$this->_group= 'Devs agiles';
	}

		
	protected function _launch() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
			->whenCalled('findByIdOrigine')
			->answers(Class_Multimedia_Location::getLoader()->newInstanceWithId(1));
				
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Device')
			->whenCalled('findByIdOrigineAndLocation')
			->answers(Class_Multimedia_Device::getLoader()->newInstanceWithId(1));
				
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->answers(Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(333)
				->setIdUser($this->_user->getId())
				->setEnd(strtotime('2012-09-09 16:40:00')));
				
		parent::_launch();
	}

	
	/** @test */
	public function shouldNotReturnError() {
		$this->assertFalse(property_exists($this->_json, 'error'));
	}
	
	
	/** @test */
	public function idShoudBe8() {
		$this->assertEquals('8', $this->_json->id);
	}
	
	
	/** @test */
	public function loginShoudBelaurent() {
		$this->assertEquals('laurent', $this->_json->login);
	}
	
	
	/** @test */
	public function passwordShoudBeAfi() {
		$this->assertEquals('afi', $this->_json->password);
	}


	/** @test */
	public function nomShoudBelaffont() {
		$this->assertEquals('laffont', $this->_json->nom);
	}
	
	
	/** @test */
	public function prenomShoudBelaurent() {
		$this->assertEquals('laurent', $this->_json->prenom);
	}
	
	
	/** @test */
	public function dateNaissanceShoudBe1978_02_17() {
		$this->assertEquals('1978/02/17', $this->_json->date_naissance);
	}


	/** @test */
	public function groupShoudBeAdulteAbonneAdminAndAgile() {
		$this->assertEquals(array('adulte','abonne','admin_bib', 'Devs agiles'),
			                  $this->_json->groupes);
	}


	/** @test */
	public function shouldHaveHold() {
		$this->assertEquals(1, $this->_json->auth);
	}


	/** @test */
	public function holdShouldLastUntil16h40() {
		$this->assertEquals('2012-09-09T16:40:00+02:00', $this->_json->until);
	}
}


class AbonneControllerMultimediaAuthenticateArnaudTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getArnaud();
		$this->_group= 'Patrons';
	}

	/** @test */
	public function groupsShouldBeInviteAndPatrons() {
		$this->assertEquals(array('invite', 'Patrons'), $this->_json->groupes);	
	}
}


class AbonneControllerMultimediaAuthenticateBaptisteTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getBaptiste();
		$this->_group= 'Devs Oldschool';
	}

		
	/** @test */
	public function groupsShouldBeMineurAbonneAndOldSchool() {
		$this->assertEquals(array('mineur','abonne_sigb', 'Devs Oldschool'), $this->_json->groupes);	
	}
}


abstract class AbonneControllerMultimediaHoldTestCase extends AbstractControllerTestCase {
	protected $_session;
	protected $_bean;
	
	public function setUp() {
		parent::setUp();
		$this->_session = new Zend_Session_Namespace('abonneController');
		$bean = new stdClass();
		$bean->location = 0;
		$bean->day = '';
		$bean->time = '';
		$bean->duration = 0;
		$bean->device = 0;
		$this->_session->holdBean = $this->_bean = $bean;

		Class_Users::getLoader()->getIdentity()->setUserGroups(array(
				Class_UserGroup::getLoader()
				->newInstanceWithId(12)
				->setMaxDay(120)
				->setMaxWeek(240)
				->setMaxMonth(360)));
				
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getDurationForUserBetweenTimes')
			->answers(0);
	}


	protected function _prepareLocationInSession() {
		$this->_bean->location = 123;
		Class_Multimedia_Location::getLoader()
				->newInstanceWithId(123)
				->setLibelle('Antibes')
				->setSlotSize(30)
				->setMaxSlots(4)
				->setHoldDelayMin(0)
				->setHoldDelayMax(60)
				->setDays('3,4')
				->setOpenHour('08:30')
				->setCloseHour('17:45');
	}


	protected function _prepareDayInSession() {
		$this->_bean->day = '2012-09-09';
	}


	protected function _prepareTimeAndDurationInSession() {
		$this->_bean->time = '9:45';
		$this->_bean->duration = 45;
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
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-location/location/1")]', 'Salle 1');
	}


	/** @test */
	public function locationSalle2ShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-location/location/2")]', 'Salle 2');
	}
}


class AbonneControllerMultimediaHoldLocationChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/abonne/multimedia-hold-location/location/1', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-day');
	}


	/** @test */
	public function beanShouldHaveLocationSet() {
		$this->assertEquals(1, $this->_session->holdBean->location);
	}
}


class AbonneControllerMultimediaHoldDayTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->dispatch('/abonne/multimedia-hold-day', true);
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


class AbonneControllerMultimediaHoldDayChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->dispatch('/abonne/multimedia-hold-day/day/2012-09-09', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-hours');
	}


	/** @test */
	public function beanShouldHaveDaySet() {
		$this->assertEquals('2012-09-09', $this->_session->holdBean->day);
	}
}


class AbonneControllerMultimediaHoldDayChoiceWithOverQuotaTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getDurationForUserBetweenTimes')
			->answers(8000);
				
		$this->dispatch('/abonne/multimedia-hold-day/day/2012-09-09', true);
	}


	/** @test */
	public function shouldNotRedirect() {
		$this->assertNotRedirect();
	}


	/** @test */
	public function quotaErrorShouldBePresent() {
		$this->assertXPathContentContains('//div', 'Quota déjà atteint ce jour');
	}
}


class AbonneControllerMultimediaHoldHoursTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();

		Class_Multimedia_Location::setTimeSource(Storm_Test_ObjectWrapper::mock()
			->whenCalled('time')
			->willDo(function() {return strtotime('2012-09-09 09:00:00');}));

		$this->dispatch('/abonne/multimedia-hold-hours', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeHoraires() {
		$this->_assertCurrentTimelineStep('Horaires');
	}
		

	/** @test */
	public function listOfStartTimesShouldBePresent() {
		$this->assertXPathCount('//select[@id="time"]/option', 18);
	}


	/** @test */
	public function startingAt10ShouldBePossible() {
		$this->assertXPathContentContains('//option[@value="10:00"]', '10h00');
	}


	/** @test */
	public function startingAt8AndHalfShouldNotBePossible() {
		$this->assertNotXpath('//option[@value="08:30"]');
	}


	/** @test */
	public function oneHourDurationOptionShouldBePresent() {
		$this->assertXPathContentContains('//option[@value="60"]', '1h');
	}


	/** @test */
	public function oneHourAndAHalfDurationLinkShouldBePresent() {
		$this->assertXPathContentContains('//option[@value="90"]', '1h30mn');
	}
}


class AbonneControllerMultimediaHoldHoursChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->dispatch('/abonne/multimedia-hold-hours/time/' . urlencode('9:45') . '/duration/45', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-device');
	}


	/** @test */
	public function beanShouldHaveTimeAndDurationSet() {
		$this->assertEquals('9:45', $this->_session->holdBean->time);
		$this->assertEquals('45', $this->_session->holdBean->duration);
	}
}


class AbonneControllerMultimediaHoldHoursChooseAlreadyHeldTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('countBetweenTimesForUser')
				->answers(1);
		$this->dispatch('/abonne/multimedia-hold-hours/time/' . urlencode('9:45') . '/duration/45', true);
	}


	/** @test */
	public function errorMessageShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="error"]', 'Vous avez déjà une réservation dans ce créneau horaire');
	}
}


class AbonneControllerMultimediaHoldDeviceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
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
				
		$this->dispatch('/abonne/multimedia-hold-device', true);
	}

	/** @test */
	public function currentTimelineStepShouldBePoste() {
		$this->_assertCurrentTimelineStep('Poste');
	}


	/** @test */
	public function posteUnShouldBeHoldable() {
		$this->assertXPathContentContains(
			'//a[contains(@href, "multimedia-hold-device/device/1")]',
			'Poste 1', $this->_response->getBody());
	}
}


class AbonneControllerMultimediaHoldDeviceChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->dispatch('/abonne/multimedia-hold-device/device/1', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-confirm');
	}


	/** @test */
	public function beanShouldHaveDeviceSet() {
		$this->assertEquals(1, $this->_session->holdBean->device);
	}
}


class AbonneControllerMultimediaHoldConfirmTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_bean->device = 23;
		Class_Multimedia_Device::getLoader()->newInstanceWithId(23)
				->setLibelle('Poste 1')
				->setOs('Ubuntu Lucid');
		$this->dispatch('/abonne/multimedia-hold-confirm', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeConfirmation() {
		$this->_assertCurrentTimelineStep('Confirmation');
	}

		
	/** @test */
	public function locationShouldBeAntibes() {
		$this->assertXPathContentContains('//li', 'Lieu : Antibes');
	}


	/** @test */
	public function dayShouldBeSeptemberNine2012() {
		$this->assertXPathContentContains('//li', 'Jour : 09 septembre 2012');
	}


	/** @test */
	public function startTimeShouldBe9h45() {
		$this->assertXPathContentContains('//li', 'À partir de : 9h45');
	}


	/** @test */
	public function durationShouldBeFortyFiveMinutes() {
		$this->assertXPathContentContains('//li', 'Durée : 45mn');
	}


	/** @test */
	public function deviceShouldBePoste1() {
		$this->assertXPathContentContains('//li', 'Poste : Poste 1 - Ubuntu Lucid');
	}


	/** @test */
	public function confirmationLinkShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "multimedia-hold-confirm/validate/1")]', 'Confirmer');
	}
}


class AbonneControllerMultimediaHoldConfirmValidatedTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_bean->device = 23;
		Class_Multimedia_Device::getLoader()->newInstanceWithId(23)
				->setLibelle('Poste 1')
				->setOs('Ubuntu Lucid');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('save')
				->willDo(function ($model) {$model->setId(455);return true;});
		$this->dispatch('/abonne/multimedia-hold-confirm/validate/1', true);
	}


	/** @test */
	public function shouldRedirectToHoldView() {
		$this->assertRedirectTo('/abonne/multimedia-hold-view/id/455');
	}
}


class AbonneControllerMultimediaHoldViewTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(455)
				->setUser(Class_Users::getLoader()->getIdentity())
				->setDevice(Class_Multimedia_Device::getLoader()->newInstanceWithId(34)
					->setLibelle('Poste 34')
					->setOs('Archlinux')
					->setGroup(Class_Multimedia_DeviceGroup::getLoader()->newInstanceWithId(1)
						->setLibelle('Groupe 1')
						->setLocation(Class_Multimedia_Location::getLoader()->newInstanceWithId(1)
							->setLibelle('Antibes'))))
				->setStart(strtotime('2012-12-28 14:30:00'))
				->setEnd(strtotime('2012-12-28 16:00:00'));
		$this->dispatch('/abonne/multimedia-hold-view/id/455', true);
	}


	/** @test */
	public function locationShouldBeAntibes() {
		$this->assertXPathContentContains('//li', 'Lieu : Antibes');
	}


	/** @test */
	public function dayShouldBeSeptemberNine2012() {
		$this->assertXPathContentContains('//li', 'Jour : 28 décembre 2012');
	}


	/** @test */
	public function startTimeShouldBe14h30() {
		$this->assertXPathContentContains('//li', 'À partir de : 14h30');
	}


	/** @test */
	public function durationShouldBeNinetyMinutes() {
		$this->assertXPathContentContains('//li', 'Durée : 90mn');
	}


	/** @test */
	public function deviceShouldBePoste34() {
		$this->assertXPathContentContains('//li', 'Poste : Poste 34 - Archlinux');
	}


	/** @test */
	public function cancelationLinkShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "multimedia-hold-view/id/455/delete/1")]');
	}
}


class AbonneControllerMultimediaHoldViewDeleteTest extends AbonneControllerMultimediaHoldTestCase {
	protected $_wrapper;
	
	public function setUp() {
		parent::setUp();
		Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(455)
				->setUser(Class_Users::getLoader()->getIdentity());
		$this->_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('delete')
				->answers(null);
		$this->dispatch('/abonne/multimedia-hold-view/id/455/delete/1', true);
	}


	/** @test */
	public function deleteShouldHaveBeenCalled() {
		$this->assertTrue($this->_wrapper->methodHasBeenCalled('delete'));
	}

		
	/** @test */
	public function shouldRedirectToFicheAbonne() {
		$this->assertRedirectTo('/abonne/fiche');
	}
}


class AbonneControllerMultimediaHoldViewOfAnotherUserTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(455)
				->setUser(Class_Users::getLoader()->newInstanceWithId(999));
		$this->dispatch('/abonne/multimedia-hold-view/id/455', true);
	}


	/** @test */
	public function shouldRedirectToFicheAbonne() {
		$this->assertRedirectTo('/abonne/fiche');
	}
}


class AbonneControllerMultimediaHoldFicheAbonneTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::getLoader()->newInstanceWithId('MULTIMEDIA_KEY')
				->setValeur('aaaaaaaaaaaaaaabbaabba');
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
				->whenCalled('getFutureHoldsOfUser')
				->with(Class_Users::getLoader()->getIdentity())
				->answers(array(Class_Multimedia_DeviceHold::getLoader()->newInstanceWithId(12)
					->setStart(strtotime('2012-28-12 14:00:00'))
					->setEnd(strtotime('2012-28-12 15:00:00'))
					->setDevice(Class_Multimedia_Device::getLoader()->newInstanceWithId(34)
						->setLibelle('Poste 1')
						->setOs('Archlinux')
						->setGroup(Class_Multimedia_DeviceGroup::getLoader()->newInstanceWithId(3)
							->setLocation(Class_Multimedia_Location::getLoader()->newInstanceWithId(2)
								->setLibelle('Antibes'))))));
		$this->dispatch('/abonne/fiche', true);
	}


	/** @test */
	public function addHoldLinkShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "multimedia-hold-location")]');
	}


	/** @test */
	public function viewHoldLinkShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "multimedia-hold-view/id/12")]');
	}
}


class AbonneControllerMultimediaUsersFixtures {
	public static function getLaurent() {
		return Class_Users::getLoader()->newInstanceWithId(8)
				->setLogin("laurent")
				->setPassword("afi")
				->setNom('laffont')
				->setPrenom('laurent')
				->setRoleLevel(4)
				->setIdabon('bca2')
				->setNaissance('1978-02-17');
	}


	public static function getBaptiste() {
		return Class_Users::getLoader()->newInstanceWithId(9)
				->setLogin("baptiste")
				->setPassword("afi")
				->setRoleLevel(2)
				->setNaissance('2005-02-17')
				->setDateFin('3000-01-01');
	}


	public static function getMireille() {
		return Class_Users::getLoader()->newInstanceWithId(10)
				->setLogin("mireille")
				->setPassword("afi")
				->setDateFin('1999-01-01');
	}


	public static function getArnaud() {
		return Class_Users::getLoader()->newInstanceWithId(11)
				->setLogin("arnaud")
				->setPassword("lelache");
	}
}