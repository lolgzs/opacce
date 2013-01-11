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

abstract class Admin_UsergroupControllerMultimediaTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('MULTIMEDIA_KEY')
			->setValeur('5018f5e08f14a');
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroup')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(true)

			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array(Class_UserGroup::getLoader()
											->newInstanceWithId(3)
											->setLibelle('Supers')
											->setUsers(array( Class_Users::getLoader()
																				->newInstanceWithId(31)
																				->setLogin('batman')
																				->setNom('Wayne')
																				->setPrenom('Bruce'),
																				
																				Class_Users::getLoader()
																				->newInstanceWithId(32)
																				->setLogin('spiderman')
																				->setNom('Parker')
																				->setPrenom('Peeter'))),
											));
	}
}



class Admin_UsergroupControllerMultimediaAddTest extends Admin_UsergroupControllerMultimediaTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/usergroup/add');
	}


	/** @test */
	public function dayQuotaShouldBePresentWithDefaultZero() {
		$this->assertXPath('//input[@name="max_day"][@value="0"]');
	}


	/** @test */
	public function weekQuotaShouldBePresentWithDefaultZero() {
		$this->assertXPath('//input[@name="max_week"][@value="0"]');
	}


	/** @test */
	public function monthQuotaShouldBePresentWithDefaultZero() {
		$this->assertXPath('//input[@name="max_month"][@value="0"]');
	}
}




class Admin_UsergroupControllerMultimediaAddValidPostTest extends Admin_UsergroupControllerMultimediaTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('admin/usergroup/add',
			array('libelle' => 'Vilains',
				    'max_day' => '120',
				    'max_week' => '120',
				    'max_month' => '360'));
	}


	/** @test */
	public function newGroupDayQuotaShouldBe120() {
		$this->assertEquals(120, Class_UserGroup::getLoader()
			                         ->getFirstAttributeForLastCallOn('save')->getMaxDay());
	}



	/** @test */
	public function newGroupWeekQuotaShouldBe120() {
		$this->assertEquals(120, Class_UserGroup::getLoader()
			                         ->getFirstAttributeForLastCallOn('save')->getMaxWeek());
	}



	/** @test */
	public function newGroupMonthQuotaShouldBe360() {
		$this->assertEquals(360, Class_UserGroup::getLoader()
			                         ->getFirstAttributeForLastCallOn('save')->getMaxMonth());
	}
		

	/** @test */
	public function responseShouldRedirectToDefaultAction() {
		$this->assertRedirectTo('/admin/usergroup');
	}
}




class Admin_UsergroupControllerMultimediaAddInvalidPostTest extends Admin_UsergroupControllerMultimediaTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('admin/usergroup/add',
			array('libelle' => 'Vilains',
				    'max_day' => '',
				    'max_week' => '120',
				    'max_month' => '2'));
	}


	/** @test */
	public function newGroupShouldNotBeCreated() {
		$this->assertFalse(Class_UserGroup::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function errorsShouldContainsUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]', 'Une valeur est requise');
	}
}