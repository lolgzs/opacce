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

class AbonneControllerMultimediaTest extends AbstractControllerTestCase{
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

?>
