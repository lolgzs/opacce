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
	protected $_ouverture_mardi;

	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Ouverture')
			->whenCalled('save')
			->answers(true)

			->whenCalled('findAllBy')
			->answers(array(
											$this->_ouverture_mardi = Class_Ouverture::getLoader()
											->newInstanceWithId(2)
											->setDebutMatin('08:00:00')
											->setFinMatin('12:00:00')
											->setDebutApresMidi('13:30:00')
											->setFinApresMidi('17:00:00')));
	}
}




class OuverturesControllerIndexActionTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/index/site_id/1', true);
	}


	/** @test */
	public function ouvertureAtHeightShouldBeVisible() {
		$this->assertXPathContentContains('//td', '08:00', $this->_response->getBody());
		$this->assertXPathContentContains('//td', '12:00');
		$this->assertXPathContentContains('//td', '13:30');
		$this->assertXPathContentContains('//td', '17:00');
	}


	/** @test */
	function pageShouldContainsButtonToCreateOuverture() {
		$this->assertXPathContentContains('//div[contains(@onclick, "ouvertures/add/site_id/1")]//td', 'Ajouter une plage d\'ouverture');
	}
}




class OuverturesControllerEditOuvertureMardiTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/edit/site_id/1/id/2', true);
	}

	
	/** @test */
	public function formShouldContainsSelectForDebutMatinWithHours() {
		$this->assertXPath('//form//select[@name="debut_matin"]//option[@value="07:30"]');
		$this->assertXPath('//form//select[@name="debut_matin"]//option[@value="08:00"][@selected="selected"]');
		$this->assertXPath('//form//select[@name="debut_matin"]//option[@value="16:00"]');
	}


	/** @test */
	public function formShouldContainsSelectForFinMatin() {
		$this->assertXPath('//form//select[@name="fin_matin"]//option[@value="12:00"][@selected="selected"]');
	}


	/** @test */
	public function formShouldContainsSelectForDebutApresMidi() {
		$this->assertXPath('//form//select[@name="debut_apres_midi"]//option[@value="13:30"][@selected="selected"]');
	}


	/** @test */
	public function formShouldContainsSelectForFinApresMidi() {
		$this->assertXPath('//form//select[@name="fin_apres_midi"]//option[@value="17:00"][@selected="selected"]');
	}
}




class OuverturesControllerPostEditOuvertureMardiTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/ouvertures/edit/site_id/1/id/2',
												array('debut_matin' => '10:30'));
	}

	/** @test */
	public function heureDebutMatinShouldBe_10_30() {
		$this->assertEquals('10:30', $this->_ouverture_mardi->getDebutMatin());
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