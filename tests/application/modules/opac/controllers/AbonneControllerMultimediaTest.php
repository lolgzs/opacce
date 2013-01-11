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
require_once 'application/modules/opac/controllers/AbonneController.php';

trait TAbonneControllerMultimediaFixtureHoldSuccessOnSept12 {
	protected function _launch() {
		$this->onLoaderOfModel('Class_Multimedia_Location')
			->whenCalled('findByIdOrigine')
			->answers(Class_Multimedia_Location::newInstanceWithId(1));

				
		$this->onLoaderOfModel('Class_Multimedia_Device')
			->whenCalled('findByIdOrigineAndLocation')
			->answers(Class_Multimedia_Device::newInstanceWithId(1));
				
		$this->onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->answers(Class_Multimedia_DeviceHold::newInstanceWithId(333)
				->setIdUser($this->_user->getId())
				->setEnd(strtotime('2012-09-12 16:40:00')));
				
		parent::_launch();
	}
}




trait TAbonneControllerMultimediaFixtureWithUserLaurentInDevsAgiles {
	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getLaurent();
		$this->_group= 'Devs agiles';
	}
}





abstract class AbonneControllerMultimediaAuthenticateTestCase extends AbstractControllerTestCase {
	protected $_json, $_auth;

	public function setUp() {
		parent::setUp();

		$this->_auth = Storm_Test_ObjectWrapper::mock()
			->whenCalled('authenticateLoginPassword')->answers(false)
			->whenCalled('hasIdentity')->answers(false)
			->whenCalled('getIdentity')->answers(null)
			->whenCalled('newAuthSIGB')->answers('auth_sigb')
			->whenCalled('newAuthDb')->answers('auth_db');
		
		ZendAfi_Auth::setInstance($this->_auth);
	}


	public function tearDown() {
		ZendAfi_Auth::setInstance(null);
		parent::tearDown();
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
		$this->_auth
			->whenCalled('authenticateLoginPassword')
			->with($user->getLogin(), $user->getPassword(), ['auth_sigb', 'auth_db'])
			->willDo(
				function() use ($user) {
					$this->_auth
						->whenCalled('getIdentity')
						->answers($user);
					return true;
				});

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
				->with(['role' => 'user', 'model' => $user])
				->answers(array(Class_UserGroupMembership::getLoader()
						->newInstance()
						->setUserGroup(Class_UserGroup::getLoader()
							->newInstanceWithId(1)
							->setLibelle($group_label))));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroup')
			->whenCalled('findAllBy')
			->with(['role_level' => $user->getRoleLevel(), 
							'group_type' => Class_UserGroup::TYPE_DYNAMIC])
			->answers([]);
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
	public function controllerActionShouldBeAbonneAuthenticate() {
		$this->getJson('/abonne/authenticate/login/any/password/any');
		$this->assertController('abonne');
		$this->assertAction('authenticate');
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
	use 
		TAbonneControllerMultimediaFixtureHoldSuccessOnSept12,
		TAbonneControllerMultimediaFixtureWithUserLaurentInDevsAgiles;
	
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
		$this->assertEquals(array('adulte','abonne', 'abonne_sigb', 'Devs agiles'),
			                  $this->_json->groupes);
	}


	/** @test */
	public function shouldHaveHold() {
		$this->assertEquals(1, $this->_json->auth);
	}


	/** @test */
	public function holdShouldLastUntil16h40() {
		$this->assertEquals('2012-09-12T16:40:00+02:00', $this->_json->until);
	}
}




class AbonneControllerMultimediaAuthenticateLaurentDeviceNotHeldByUserTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	use TAbonneControllerMultimediaFixtureWithUserLaurentInDevsAgiles;

	protected function _launch() {
		$this->onLoaderOfModel('Class_Multimedia_Location')
			->whenCalled('findByIdOrigine')
			->answers($location = Class_Multimedia_Location::newInstanceWithId(1)								
								->setAuthDelay(1)
								->setAutohold(1));

		$this->onLoaderOfModel('Class_Multimedia_Device')
			->whenCalled('findByIdOrigineAndLocation')
			->answers(Class_Multimedia_Device::newInstanceWithId(1)
								->setGroup(Class_Multimedia_DeviceGroup::newInstanceWithId(34)->setLocation($location)));

		$this->onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getHoldOnDeviceAtTime')
			->answers(Class_Multimedia_DeviceHold::newInstanceWithId(333)
								->setIdUser(9878)
								->setStart(strtotime('2012-09-12 08:30:00'))
								->setEnd(strtotime('2012-09-12 16:40:00')));
				
		parent::_launch();
	}


	/** @test */
	public function jsonShouldContainsErrorDeviceNotHeldByUser() {
		$this->assertEquals('DeviceNotHeldByUser', $this->_json->error);
	}


	/** @test */
	public function loginShoudBelaurent() {
		$this->assertEquals('laurent', $this->_json->login);
	}


	/** @test */
	public function authShouldBeZero() {
		$this->assertEquals('0', $this->_json->auth);
	}

}



class AbonneControllerMultimediaAuthenticateLaurentDeviceNotFoundTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	use TAbonneControllerMultimediaFixtureWithUserLaurentInDevsAgiles;
	protected function _launch() {
		$this->onLoaderOfModel('Class_Multimedia_Location')
			->whenCalled('findByIdOrigine')
			->answers(Class_Multimedia_Location::newInstanceWithId(1));

		$this->onLoaderOfModel('Class_Multimedia_Device')
			->whenCalled('findByIdOrigineAndLocation')
			->answers(null);
				
		parent::_launch();
	}


	/** @test */
	public function jsonShouldContainsErrorDeviceNotFound() {
		$this->assertEquals('DeviceNotFound', $this->_json->error);
	}


	/** @test */
	public function loginShoudBelaurent() {
		$this->assertEquals('laurent', $this->_json->login);
	}
}



class AbonneControllerMultimediaAuthenticateArnaudTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	use TAbonneControllerMultimediaFixtureHoldSuccessOnSept12;

	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getArnaud();
		$this->_group= 'Patrons';
	}


	/** @test */
	public function groupsShouldBeAbonneAndPatrons() {
		$this->assertEquals(array('abonne_sigb', 'Patrons'), $this->_json->groupes);	
	}


	/** @test */
	public function shouldNotReturnError() {
		$this->assertFalse(property_exists($this->_json, 'error'));
	}
}




class AbonneControllerMultimediaAuthenticateBaptisteTest extends AbonneControllerMultimediaAuthenticateValidTestCase {
	use TAbonneControllerMultimediaFixtureHoldSuccessOnSept12;

	protected function _initUser() {
		$this->_user = AbonneControllerMultimediaUsersFixtures::getBaptiste();
		$this->_group= 'Devs Oldschool';
	}

		
	/** @test */
	public function groupsShouldBeMineurAbonneAndOldSchool() {
		$this->assertEquals(array('mineur','abonne_sigb', 'Devs Oldschool'), $this->_json->groupes);	
	}
}




/* Début test du workflow de réservation */
abstract class AbonneControllerMultimediaHoldTestCase extends AbstractControllerTestCase {
	protected $_session;
	protected $_bean;
	
	public function setUp() {
		parent::setUp();
		$this->_session = new Zend_Session_Namespace('abonneController');
		$this->_session->holdBean = $this->_bean = new Class_Multimedia_ReservationBean();

		Class_Users::getIdentity()
			->setUserGroups([Class_UserGroup::newInstanceWithId(12)
											 ->setMaxDay(120)
											 ->setMaxWeek(240)
											 ->setMaxMonth(360)]);
				
		$this
			->onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getDurationForUserBetweenTimes')
			->answers(0);
	}


	protected function _prepareLocationInSession() {
		$this->_bean->location = 123;

		Class_Bib::newInstanceWithId(3)
			->setLibelle('Médiathèque d\'Antibes');

		
		Class_Multimedia_Location::newInstanceWithId(123)
			->setIdSite(3)
			->setLibelle('Antibes')
			->setSlotSize(30)
			->setMaxSlots(4)
			->setHoldDelayMin(0)
			->setHoldDelayMax(60)
			->setOuvertures([Class_Ouverture::chaqueLundi('08:30', '12:00', '12:00', '17:45')->setId(1)->cache(),
											 Class_Ouverture::chaqueMercredi('08:30', '12:00', '12:00', '17:45')->setId(3)->cache(),
											 Class_Ouverture::chaqueJeudi('08:30', '12:00', '12:00', '17:45')->setId(4)->cache()])
			->setGroups([Class_Multimedia_DeviceGroup::newInstanceWithId(3)
									 ->setLibelle('Musique')
									 ->setDevices([Class_Multimedia_Device::getLoader()
																 ->newInstanceWithId(1)
																 ->setLibelle('Poste 1')
																 ->setOs('Ubuntu Lucid')
																 ->setDisabled(0),

																 Class_Multimedia_Device::getLoader()
																 ->newInstanceWithId(3)
																 ->setLibelle('Poste 3')
																 ->setOs('OSX')
																 ->setDisabled(0)]),

									 Class_Multimedia_DeviceGroup::newInstanceWithId(5)
									 ->setLibelle('Jeunesse')
									 ->setDevices([
																 Class_Multimedia_Device::getLoader()
																 ->newInstanceWithId(2)
																 ->setLibelle('Poste 2')
																 ->setOs('Windows XP')
																 ->setDisabled(0),

																 Class_Multimedia_Device::getLoader()
																 ->newInstanceWithId(4)
																 ->setLibelle('Poste 4')
																 ->setOs('Amiga OS')
																 ->setDisabled(0)])
									 ]);
	}


	protected function _prepareDayInSession() {
		$this->_bean->day = '2012-09-12';
	}


	protected function _prepareTimeAndDurationInSession() {
		$this->_bean->time = '9:45';
		$this->_bean->duration = 45;
	}


	protected function _prepareGroupInSession() {
		$this->_bean->group = 5;
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




/* Premier écran de choix du lieu */
class AbonneControllerMultimediaHoldLocationTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_Location')
				->whenCalled('findAllBy')
				->answers([
									 Class_Multimedia_Location::newInstanceWithId(1)
									 ->setLibelle('Salle 1')
									 ->setBib(Class_Bib::newInstanceWithId(1)->setLibelle('Médiathèque Antibes'))
									 ->setOuvertures([Class_Ouverture::chaqueLundi('8:00', '12:00', '13:00', '18:00')->cache()]),

									 Class_Multimedia_Location::newInstanceWithId(2)
									 ->setLibelle('Salle 2')
									 ->setBib(Class_Bib::newInstanceWithId(2)->setLibelle('Médiathèque Roquefort'))
									 ->setOuvertures([Class_Ouverture::chaqueMercredi('8:00', '12:00', '13:00', '18:00')->cache()]),

									 Class_Multimedia_Location::newInstanceWithId(3)
									 ->setLibelle('Salle 3')
									 ->setBib(Class_Bib::newInstanceWithId(3)->setLibelle('Médiathèque Valbonne'))
									 ->setOuvertures([])
									 ]);
		$this->dispatch('/abonne/multimedia-hold-location', true);
	}


	/** @test */
	public function bodyShouldContainsClass_abonne_multimedia_hold_location() {
		$this->assertXPath('//body[contains(@class, "abonne_multimedia-hold-location")]');
	}


	/** @test */
	public function currentTimelineStepShouldBeLieu() {
		$this->_assertCurrentTimelineStep('Lieu');
	}
		

	/** @test */
	public function locationSalle1ShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-location/location/1")]', 'Médiathèque Antibes');
	}


	/** @test */
	public function locationSalle2ShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-location/location/2")]', 'Médiathèque Roquefort');
	}


	/** @test */
	public function locationSalle3WithoutAnyOuvertureShouldNotBePresent() {
		$this->assertNotXPath('//a[contains(@href, "/multimedia-hold-location/location/3")]');
	}
}




/* Validation du lieu, on est redirigé sur l'écran choix du jour */
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




/* Second écran choix du jour */
class AbonneControllerMultimediaHoldDayTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->dispatch('/abonne/multimedia-hold-day', true);
	}


	/** @test */
	public function bodyShouldContainsClass_abonne_multimedia_hold_location() {
		$this->assertXPath('//body[contains(@class, "abonne_multimedia-hold-day")]');
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




/* Validation du second écran choix du jour, redirection vers le choix de l'heure */
class AbonneControllerMultimediaHoldDayChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->dispatch('/abonne/multimedia-hold-day/day/2012-09-12', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-hours');
	}


	/** @test */
	public function beanShouldHaveDaySet() {
		$this->assertEquals('2012-09-12', $this->_session->holdBean->day);
	}
}




/* Validation du second écran mais l'utilisateur a dépassé son quota de réservation */
class AbonneControllerMultimediaHoldDayChoiceWithOverQuotaTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getDurationForUserBetweenTimes')
			->answers(8000);
				
		$this->dispatch('/abonne/multimedia-hold-day/day/2012-09-12', true);
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





/* Troisième écran choix de l'heure de début de réservation */
class AbonneControllerMultimediaHoldHoursTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();

		Class_Multimedia_Location::setTimeSource((new TimeSourceForTest())->setTime(strtotime('2012-09-12 09:00:00')));

		$this->dispatch('/abonne/multimedia-hold-hours', true);
	}


	/** @test */
	public function currentTimelineStepShouldBeHoraires() {
		$this->_assertCurrentTimelineStep('Horaires');
	}
		

	/** @test */
	public function listOfStartTimesShouldBePresent() {
		$this->assertXPathCount('//select[@id="time"]/option', 17, $this->_response->getBody());
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




/* Troisième écran choix de l'heure, redirection sur le choix du poste */
class AbonneControllerMultimediaHoldHoursChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->dispatch('/abonne/multimedia-hold-hours/time/' . urlencode('9:45') . '/duration/45', true);
	}


	/** @test */
	public function shouldRedirectToNextStep() {
		$this->assertRedirectTo('/abonne/multimedia-hold-group');
	}


	/** @test */
	public function beanShouldHaveTimeAndDurationSet() {
		$this->assertEquals('9:45', $this->_session->holdBean->time);
		$this->assertEquals('45', $this->_session->holdBean->duration);
	}
}




/* Troisième écran choix d'une heure déjà allouée */
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




/* Quatrième écran choix du groupe de postes */
class AbonneControllerMultimediaHoldGroupTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();

		$this->dispatch('/abonne/multimedia-hold-group', true);
	}


	/** @test */
	public function pageShouldContainsLinkToGroupMusique() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-group/group/3")]', 'Musique');
	}


	/** @test */
	public function pageShouldContainsLinkToGroupJeunesse() {
		$this->assertXPathContentContains('//a[contains(@href, "/multimedia-hold-group/group/5")]', 'Jeunesse');
	}


	/** @test */
	public function currentTimelineShouldBeSecteur() {
		$this->_assertCurrentTimelineStep('Secteur');
	}


	/** @test */
	public function timelinePreviousActionsShouldHaveLink() {
		$this->assertXPathContentContains('//div[@class="timeline"]//li//a[contains(@href, abonne/multimedia-hold-location)]', 'Lieu');
		$this->assertXPathContentContains('//div[@class="timeline"]//li//a[contains(@href, abonne/multimedia-hold-day)]', 'Jour');
		$this->assertXPathContentContains('//div[@class="timeline"]//li//a[contains(@href, abonne/multimedia-hold-hours)]', 'Horaires');
		$this->assertNotXPathContentContains('//div[@class="timeline"]//li//a', 'Secteur');
		$this->assertNotXPathContentContains('//div[@class="timeline"]//li//a', 'Poste');
		$this->assertNotXPathContentContains('//div[@class="timeline"]//li//a', 'Confirmation');
	}
}




/* Quatrième écran validation du choix du groupe de postes, redirection vers le choix du poste */
class AbonneControllerMultimediaHoldGroupChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->dispatch('/abonne/multimedia-hold-group/group/5', true);
	}


	/** @test */
	public function shouldRedirectToStepHoldDevice() {
		$this->assertRedirectTo('/abonne/multimedia-hold-device');
	}


	/** @test */
	public function beanShouldHaveGroupSetToFive() {
		$this->assertEquals(5, $this->_session->holdBean->group);
	}
}




/* Quatrième écran validation du choix du groupe de postes, redirection vers le choix du poste */
class AbonneControllerMultimediaHoldGroupChoiceErrorTest extends AbonneControllerMultimediaHoldTestCase {
	/** @test */
	public function withoutHoursShouldRedirectToHoldHours() {
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->dispatch('/abonne/multimedia-hold-group/group/5', true);
		$this->assertRedirectTo('/abonne/multimedia-hold-hours');
	}


	/** @test */
	public function withoutDayShouldRedirectToHoldDay() {
		$this->_prepareLocationInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->dispatch('/abonne/multimedia-hold-group/group/5', true);
		$this->assertRedirectTo('/abonne/multimedia-hold-day');
	}


	/** @test */
	public function withoutLocationShouldRedirectToHoldLocation() {
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->dispatch('/abonne/multimedia-hold-group/group/5', true);
		$this->assertRedirectTo('/abonne/multimedia-hold-location');
	}
}




/* Cinquième écran choix du poste */
class AbonneControllerMultimediaHoldDeviceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_prepareGroupInSession();

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
	public function posteUnShouldNotBeHoldable() {
		$this->assertNotXPathContentContains('//a','Poste 1');
	}


	/** @test */
	public function posteDeuxShouldBeHoldable() {
		$this->assertXPathContentContains('//a[contains(@href, "multimedia-hold-device/device/2")]','Poste 2');
	}
}




/* Cinquième écran validation du choix du poste, redirection vers la confirmation */
class AbonneControllerMultimediaHoldDeviceChoiceTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_prepareGroupInSession();
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




class AbonneControllerMultimediaHoldDeviceChoiceErrorTest extends AbonneControllerMultimediaHoldTestCase {
	/** @test */
	public function withoutGroupShouldRedirectToHoldGroup() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->dispatch('/abonne/multimedia-hold-device/device/1', true);
		$this->assertRedirectTo('/abonne/multimedia-hold-group');
	}
}




/* Sixième écran confirmation de la réservation */
class AbonneControllerMultimediaHoldConfirmTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_prepareGroupInSession();
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
		$this->assertXPathContentContains('//li', 'Lieu : Médiathèque d\'Antibes');
	}


	/** @test */
	public function dayShouldBeSeptemberTwelve2012() {
		$this->assertXPathContentContains('//li', 'Jour : 12 septembre 2012');
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




/* Sixième écran, réservation confirmée */
class AbonneControllerMultimediaHoldConfirmValidatedTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		$this->_prepareLocationInSession();
		$this->_prepareDayInSession();
		$this->_prepareTimeAndDurationInSession();
		$this->_prepareGroupInSession();
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




/* Septième écran, visualisation de la réservation */
class AbonneControllerMultimediaHoldViewTest extends AbonneControllerMultimediaHoldTestCase {
	public function setUp() {
		parent::setUp();
		Class_Multimedia_DeviceHold::newInstanceWithId(455)
			->setUser(Class_Users::getIdentity())
			->setDevice(Class_Multimedia_Device::newInstanceWithId(34)
									->setLibelle('Poste 34')
									->setOs('Archlinux')
									->setGroup(Class_Multimedia_DeviceGroup::newInstanceWithId(1)
														 ->setLibelle('Groupe 1')
														 ->setLocation(Class_Multimedia_Location::newInstanceWithId(1)
																					 ->setLibelle('Antibes')
																					 ->setBib(Class_Bib::newInstanceWithId(5)
																										->setLibelle('Médiathèque d\'Antibes')))))
			->setStart(strtotime('2012-12-28 14:30:00'))
			->setEnd(strtotime('2012-12-28 16:00:00'));
		$this->dispatch('/abonne/multimedia-hold-view/id/455', true);
	}


	/** @test */
	public function locationShouldBeAntibes() {
		$this->assertXPathContentContains('//li', 'Lieu : Médiathèque d\'Antibes');
	}


	/** @test */
	public function dayShouldBeSeptemberTwentyHeight2012() {
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
		Class_AdminVar::newInstanceWithId('MULTIMEDIA_KEY')->setValeur('aaaaaaaaaaaaaaabbaabba');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getFutureHoldsOfUser')
			->with(Class_Users::getIdentity())
			->answers([Class_Multimedia_DeviceHold::newInstanceWithId(12)
								 ->setStart(strtotime('2012-12-28 14:00:00'))
								 ->setEnd(strtotime('2012-12-28 15:00:00'))
								 ->setDevice(Class_Multimedia_Device::newInstanceWithId(34)
														 ->setLibelle('Poste 1')
														 ->setOs('Archlinux')
														 ->setGroup(Class_Multimedia_DeviceGroup::newInstanceWithId(3)
																				->setLocation(Class_Multimedia_Location::newInstanceWithId(2)
																											->setLibelle('Antibes')
																											->setBib(Class_Bib::newInstanceWithId(5)
																															 ->setLibelle('Médiathèque d\'Antibes')))))
								 ]);
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


	/** @test */
	public function viewHoldLinkShouldBeDisplayLibelleBibOsAndTime() {
		$this->assertXPathContentContains('//a[contains(@href, "multimedia-hold-view/id/12")]',
																			'Poste 1 - Archlinux, le 28 décembre 2012 à 14h00 pour 60mn, Médiathèque d\'Antibes');
	}
}




class AbonneControllerMultimediaUsersFixtures {
	public static function getLaurent() {
		return Class_Users::getLoader()->newInstanceWithId(8)
				->setLogin("laurent")
				->setPassword("afi")
				->setNom('laffont')
				->setPrenom('laurent')
				->setRoleLevel(2)
				->setIdabon('bca2')
				->setNaissance('1978-02-17');
	}


	public static function getBaptiste() {
		return Class_Users::getLoader()->newInstanceWithId(9)
			->setLogin("baptiste")
			->setPassword("afi")
			->beAbonneSIGB()
			->setNaissance('2005-02-17')
			->setDateFin('3000-01-01');
	}


	public static function getMireille() {
		return Class_Users::getLoader()->newInstanceWithId(10)
			->beAbonneSIGB()
			->setLogin("mireille")
			->setPassword("afi")
			->setDateFin('1999-01-01');
	}


	public static function getArnaud() {
		return Class_Users::getLoader()->newInstanceWithId(11)
			->beAbonneSIGB()
			->setLogin("arnaud")
			->setPassword("lelache");
	}
}