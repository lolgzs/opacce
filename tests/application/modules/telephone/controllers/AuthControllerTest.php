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
require_once 'TelephoneAbstractControllerTestCase.php';


class AuthControllerTelephoneBoiteLoginTest extends TelephoneAbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "";
		$account->ROLE_LEVEL = 0;
		$account->ID_USER = "";
		$account->PSEUDO = "";
	}


	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->setCfgAccueil(array());

		$this->postDispatch('auth/boitelogin',
												array('identifiant' => 'joe', 'password' => 'secret'));
	}


	/** @test */
	public function responseShouldRedirectToIndex() {
		$this->assertRedirectTo('/telephone/index');
	}
}



class AuthControllerTelephoneLoginTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()
			->setCfgAccueil(array());

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('hasIdentity')
			->answers(false);

		$this->dispatch('auth/login', true);
	}


	/** @test */
	public function pageShouldContainsLoginInput() {
		$this->assertXPath('//form//input[@name="username"]');
	}


	/** @test */
	public function pageShouldContainsPassInput() {
		$this->assertXPath('//form//input[@name="password"]');
	}


	/** @test */
	public function titleShouldBeIdentification() {
		$this->assertXPathContentContains('//h1', 'Identification');
	}
}



class AuthControllerTelephoneLoginReservationTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::getCurrentProfil()
			->setCfgAccueil(array());

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('hasIdentity')
			->answers(false);

		$this->dispatch('auth/login-reservation/id/45324', true);
	}


	/** @test */
	public function pageShouldContainsLoginInput() {
		$this->assertXPath('//form//input[@name="username"]');
	}


	/** @test */
	public function pageShouldContainsPassInput() {
		$this->assertXPath('//form//input[@name="password"]');
	}


	/** @test */
	public function titleShouldBeIdentification() {
		$this->assertXPathContentContains('//h1', 'Identification');
	}
}



class AuthControllerTelephoneLoginReservationInvalidPostTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('hasIdentity')
			->answers(false);

		$this->postDispatch('auth/login-reservation/id/45324', array());
	}


	/** @test */
	public function shouldRedirectToCurrentUrl() {
		$this->assertRedirectTo('/auth/login-reservation/id/45324');
	}


	/** @test */
	public function flashMessengerShouldContainsErrorMessage() {
		$this->assertTrue(0 < count($messages = $_SESSION['FlashMessenger']['default']));
		$this->assertEquals('Entrez votre identifiant S.V.P.', $messages[0]);
	}
}



class AuthControllerTelephoneLoginReservationValidPostTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()
			->setCfgAccueil(array());

		$user = Class_Users::getLoader()->findFirstBy(array());

		$this->postDispatch('auth/login-reservation/id/45324',
												array('username' => $user->getLogin(),
															'password' => $user->getPassword()));
	}


	/** @test */
	public function shouldRedirectToReservation() {
		$this->assertRedirectTo('/recherche/reservation');
	}
}

?>