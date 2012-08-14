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



class DynamicUserGroupAbonneSIGBTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('findAllBy')
			->with(['role_level' => 2,
							'limit' => 50])
			->answers([$this->_baptiste = Class_Users::newInstanceWithId(3)
								 ->setPrenom('Baptiste')
								 ->setUserGroups([])
								 ->beAbonneSIGB(),

								 $this->_xavier = Class_Users::newInstanceWithId(4)
								 ->setPrenom('Xavier')
								 ->setUserGroups([$this->_group_multimedia = Class_UserGroup::newInstanceWithId(9)
																	                           ->setLibelle('Multimédia')])
								 ->beAbonneSIGB()]);


		$this->_abonnnes_sigb = Class_UserGroup::newInstanceWithId(3)
			->beDynamic()
			->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroup')
			->whenCalled('findAllBy')
			->with(['role_level' => ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB,
							'group_type' => Class_UserGroup::TYPE_DYNAMIC])
			->answers([$this->_abonnnes_sigb]);

	}

	
	/** @test */
	public function groupShouldBeDynamic() {
		$this->assertTrue($this->_abonnnes_sigb->isDynamic());
	}

	
	/** @test */
	public function usersShouldContainsBaptisteAndXavier() {
		$this->assertEquals(['Baptiste', 'Xavier'],
												array_map(function($user) { return $user->getPrenom(); },
																	$this->_abonnnes_sigb->getUsers()));
	}

	
	/** @test */
	public function baptisteShouldBeAbonneSIGB() {
		$this->assertEquals(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB, 
												$this->_baptiste->getRoleLevel());
	}


	/** @test */
	public function baptisteGroupsShouldBeOnlyAbonneSIGB() {
		$this->assertEquals([$this->_abonnnes_sigb], $this->_baptiste->getUserGroups());
	}
}


?>