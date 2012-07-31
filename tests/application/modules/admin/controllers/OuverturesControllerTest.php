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

abstract class OuverturesControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Ouverture')
			->whenCalled('findAll')
			->answers(array(
											Class_Ouverture::getLoader()
											->newInstanceWithId(2)
											->setDebutMatin('08h00')));
	}
}



class OuverturesControllerIndexActionTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures', true);
	}

	/** @test */
	public function ouvertureAtHeightShouldBeVisible() {
		$this->assertXPath('//td', '08h00');
	}
}




class OuverturesControllerEditOuvertureMardiTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/edit/site_id/1/id/2', true);
	}

	
	/** @test */
	public function formShouldContainsSelectForDebutMatin() {
		$this->assertXPath('//form//select');
	}
}




class OuverturesControllerAddOuvertureTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/add/site_id/1', true);
	}

	
	/** @test */
	public function formShouldContainsSelectForDebutMatin() {
		$this->assertXPath('//form//select');
	}
}



?>