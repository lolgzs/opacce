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

class AdminAuthControllerNobodyLoggedTest extends Admin_AbstractControllerTestCase {
	protected $_auth;
	protected $_auth_db_adapter;

	public function setUp() {
		parent::setUp();
		$this->_auth_db_adapter = Storm_Test_ObjectWrapper::mock();
		$this->_auth = Storm_Test_ObjectWrapper::mock()
			->whenCalled('authenticateLoginPassword')
			->answers(false)
			->whenCalled('hasIdentity')
			->answers(false)
			->whenCalled('getIdentity')
			->answers(null)
			->whenCalled('newAuthDb')
			->answers($this->_auth_db_adapter);
		
		ZendAfi_Auth::setInstance($this->_auth);
	}


	public function tearDown() {
		ZendAfi_Auth::setInstance(null);
		parent::tearDown();
	}


	/** @test */
	public function withAuthenticationSuccessfullShouldRedirectToAdmin() {
		$this->_auth
			->whenCalled('authenticateLoginPassword')
			->with('foo', 'bar', [$this->_auth_db_adapter])
			->answers(true);

		$this->postDispatch('/admin/auth/login',
												['username' => 'foo', 'password' => 'bar']);

		$this->assertRedirectTo('/admin/');
	}


	/** @test */
	public function withAuthenticationFailureShouldNotRedirectToAdmin() {
		$this->postDispatch('/admin/auth/login',
												['username' => 'foo', 'password' => 'bar']);

		$this->assertNotRedirect('/admin/');
	}


	/** @test */
	public function withNoUsernameShouldDisplayMessageEntrezVotreNom() {
		$this->postDispatch('/admin/auth/login', []);
		$this->assertXPathContentContains('//span', 'Entrez votre nom d\'utilisateur');
	}


	/** @test */
	public function withNoUserNameShouldContainsLinkToGoBackToProfilOne() {
		$this->dispatch('/admin/auth/login');
		$this->assertXPathContentContains('//a[contains(@href, "index/id_profil/1")]', 'Retour au site');
	}
}

?>