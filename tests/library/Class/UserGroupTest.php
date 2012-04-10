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
abstract class UserGroupTestCase extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->_florence = Class_Users::getLoader()
			->newInstanceWithId(1)
			->setLogin('flo');
								 
		$this->_agnes = Class_Users::getLoader()
			->newInstanceWithId(2)
			->setLogin('agnes');

		$this->_stagiaires = Class_UserGroup::getLoader()
			->newInstanceWithId(23)
			->setLibelle('Stagiaires')
			->setRightsToken(0x01);
		

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroupMembership')
			->whenCalled('findAllBy')
			->with(array('role' => 'user_group',
									 'model' => $this->_stagiaires))
			->answers(array($this->_florence_stagiaires_membership = Class_UserGroupMembership::getLoader()
											->newInstanceWithId(123)
											->setUserId(1)
											->setUserGroupId(23),

											$this->_agnes_stagiaires_membership = Class_UserGroupMembership::getLoader()
											->newInstanceWithId(223)
											->setUserId(2)
											->setUserGroupId(23)))


			->whenCalled('findAllBy')
			->with(array('role' => 'user',
									 'model' => $this->_florence))
			->answers(array($this->_florence_stagiaires_membership));
	}
}




class UserGroupStagiairesTest extends UserGroupTestCase {
	/** @test */
	public function getUsersShouldReturnArrayWithFlorenceAndAgnes() {
		$this->assertEquals(array($this->_florence, $this->_agnes),
												$this->_stagiaires->getUsers());
	}

	/** @test */
	public function florenceGroupsShouldReturnStagiaires() {
		$this->assertEquals(array($this->_stagiaires),
												$this->_florence->getUserGroups());
	}


	/** @test */
	public function addingFlorenceShouldNotDuplicateEntry() {
		$this->_stagiaires->addUser($this->_florence);
		$this->assertEquals(array($this->_florence, $this->_agnes),
												$this->_stagiaires->getUsers());
	}

	/** @test */
	public function rightsShouldEqualsSuivreFormation() {
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION), 
												$this->_stagiaires->getRights());
	}


	/** @test */
	public function addRightDirigerFormationShouldUpdateRights() {
		$this->_stagiaires->addRightDirigerFormation();
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION, 
															Class_UserGroup::RIGHT_DIRIGER_FORMATION), 
												$this->_stagiaires->getRights());
	}


	/** @test */
	public function clearRightsShouldSetRightsTokenToZero() {
		$this->_stagiaires->clearRights();
		$this->assertEquals(0, $this->_stagiaires->getRightsToken());
	}


	/** @test */
	public function toArrayShouldContainsRights() {
		$to_array = $this->_stagiaires->toArray();
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION),
												$to_array['rights']);
	}


	/** @test */
	public function attributesToArrayShouldNotContainsRights() {
		$attributes = $this->_stagiaires->attributesToArray();
		$this->assertFalse(array_key_exists('rights', $attributes));
	}


	/** @test */
	public function changingRigthsToDirigerFormationShouldUpdateRightsTokenToTwo() {
		$this->_stagiaires->setRights(array(Class_UserGroup::RIGHT_DIRIGER_FORMATION));
		$this->assertEquals(0x2, $this->_stagiaires->getRightsToken());
	}


	/** @test */
	public function changingRigthsToDirigerAndSuivreFormationShouldUpdateRightsTokenToThree() {
		$this->_stagiaires->setRights(array(Class_UserGroup::RIGHT_DIRIGER_FORMATION,
																				Class_UserGroup::RIGHT_SUIVRE_FORMATION));
		$this->assertEquals(0x3, $this->_stagiaires->getRightsToken());
	}


	/** @test */
	public function florenceShouldHaveRightsSuivreFormation() {
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION), 
												$this->_florence->getRights());
	}


	/** @test */
	public function hasRightSuivreFormationShouldReturnsTrue() {
		$this->assertTrue($this->_florence->hasRightSuivreFormation());
	}


	/** @test */
	public function hasRightDirigerFormationShouldReturnsFalse() {
		$this->assertFalse($this->_florence->hasRightDirigerFormation());
	}


	/** @test */
	public function withGroupHavingRightDirigerFormationFloranceRightsShouldHaveDirigerAndSuivre() {
		$this->_florence->addUserGroup(Class_UserGroup::getLoader()
																	 ->newInstanceWithId(98)
																	 ->addRightDirigerFormation()
																	 ->addRightSuivreFormation());
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION, Class_UserGroup::RIGHT_DIRIGER_FORMATION), 
												$this->_florence->getRights());
	}
}


class UserGroupWithNoUsersTest extends UserGroupTestCase {
	protected $_zork_group;

	public function setUp() {
		parent::setUp();
		$this->_zork_group = Class_UserGroup::getLoader()->newInstanceWithId(999999);
	}

	/** @test */
	public function usersShouldReturnEmptyArray() {
		$this->assertSame(array(), $this->_zork_group->getUsers());
	}
}


?>