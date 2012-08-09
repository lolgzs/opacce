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
	protected $_ouverture_mardi_cran;
	protected $_ouverture_jeudi_annecy;
	protected $_ouverture_tous_mercredis_cran;

	public function setUp() {
		parent::setUp();

		Class_Bib::newInstanceWithId(1)->setLibelle('Cran-Gévrier');
		Class_Bib::newInstanceWithId(3)->setLibelle('Annecy');
		

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Ouverture')
			->whenCalled('save')
			->answers(true)

			->whenCalled('findAllBy')->with(['order' => '', 'id_site' => 1])
			->answers([
								 $this->_ouverture_mardi_cran = Class_Ouverture::newInstanceWithId(2)
								 ->setIdSite(1)
								 ->setJour('2012-07-23')
								 ->setDebutMatin('08:00:00')
								 ->setFinMatin('12:00:00')
								 ->setDebutApresMidi('13:30:00')
								 ->setFinApresMidi('17:00:00'),

								$this->_ouverture_tous_mercredis_cran = Class_Ouverture::newInstanceWithId(4)
								 ->setIdSite(1)
								 ->setJourSemaine(Class_Ouverture::MERCREDI)
								 ->setJour('0000-00-00')
								 ->setDebutMatin('10:00:00')
								 ->setFinMatin('12:00:00')
								 ->setDebutApresMidi('12:00:00')
								 ->setFinApresMidi('17:00:00')])


			->whenCalled('findAllBy')->with(['order' => '', 'id_site' => 3])
			->answers([
								 $this->_ouverture_jeudi_annecy = Class_Ouverture::newInstanceWithId(45)
								 ->setIdSite(3)
								 ->setJour('2012-07-26')
								 ->setDebutMatin('08:30')
								 ->setFinApresMidi('17:00:00')]);
	}
}




class OuverturesControllerIndexActionSiteCranTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/index/id_site/1', true);
	}


	/** @test */
	public function ouvertureHoursShouldBeVisible() {
		$this->assertXPathContentContains('//td', '08:00');
		$this->assertXPathContentContains('//td', '12:00');
		$this->assertXPathContentContains('//td', '13:30');
		$this->assertXPathContentContains('//td', '17:00');
	}


	/** @test */
	public function jourShouldBeVisibleForOuvertureMardiOnSecondLine() {
		$this->assertXPathContentContains('//tr[2]//td', '23/07/2012');
	}


	/** @test */
	public function jourShouldBeMercrediVisibleForOuvertureMercrediOnFirstLine() {
		$this->assertXPathContentContains('//tr[1]//td', 'Mercredi');
	}


	/** @test */
	function pageShouldContainsButtonToCreateOuverture() {
		$this->assertXPathContentContains('//div[contains(@onclick, "ouvertures/add/id_site/1")]//td', 'Ajouter une plage d\'ouverture');
	}


	/** @test */
	public function titleShouldBePlagesDouvertureDeLaBibliothequeCranGevrier() {
		$this->assertXPathContentContains('//h1', 'Cran-Gévrier: plages d\'ouverture');
	}
}




class OuverturesControllerIndexActionSiteAnnecyTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/index/id_site/3', true);
	}


	/** @test */
	public function ouvertureAtHeightHalfShouldBeVisible() {
		$this->assertXPathContentContains('//td[2]', '08:30');
		$this->assertXPathContentContains('//td[3]', '12:00');
		$this->assertXPathContentContains('//td[4]', '12:00');
		$this->assertXPathContentContains('//td[5]', '17:00');
	}


	/** @disabledtest */
	function pageShouldContainsButtonToCreateOuverture() {
		$this->assertXPathContentContains('//div[contains(@onclick, "ouvertures/add/id_site/3")]//td', 'Ajouter une plage d\'ouverture');
	}
}




class OuverturesControllerIndexActionWithoutSiteTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/index', true);
	}


	/** @test */
	public function answerShouldRedirectToIndexBib() {
		$this->assertRedirectTo('/admin/bib');
	}
}




class OuverturesControllerEditOuvertureMardiTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/edit/id_site/1/id/2', true);
	}


	/** @test */
	public function formShouldContainsSelectForJour() {
		$this->assertXPath('//form//input[@name="jour"][@value="23/07/2012"]');
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


	/** @test */
	public function titleShouldBeCranGevrierModifierUnePlageDouverture() {
		$this->assertXPathContentContains('//h1', 'Cran-Gévrier: modifier une plage d\'ouverture');
	}
}




class OuverturesControllerPostEditOuvertureMardiCranTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/ouvertures/edit/id/2',
												['debut_matin' => '10:30',
												 'id_site' => 1,
												 'jour' => '23/07/2012']);
	}

	/** @test */
	public function heureDebutMatinShouldBe_10_30() {
		$this->assertEquals('10:30', $this->_ouverture_mardi_cran->getDebutMatin());
	}


	/** @test */
	public function responseShouldRedirectToOuverturesIndexSiteOne() {
		$this->assertRedirectTo('/admin/ouvertures/index/id_site/1');
	}


	/** @test */
	public function jourShouldBe2012_07_23() {
		$this->assertEquals('2012-07-23', $this->_ouverture_mardi_cran->getJour());
	}
}




class OuverturesControllerAddOuvertureCranTest extends OuverturesControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/ouvertures/add/id_site/1', true);
	}

	
	/** @test */
	public function formShouldContainsSelectForDebutMatin() {
		$this->assertXPath('//form//select[@name="debut_matin"]');
	}


	/** @test */
	public function hiddenFieldIdSiteShouldHaveValueOne() {
		$this->assertXPath('//input[@name="id_site"][@type="hidden"][@value="1"]');
	}


	/** @test */
	public function titleShouldBeCranGevrierAjouteUnePlageDouverture() {
		$this->assertXPathContentContains('//h1', 'Cran-Gévrier: ajouter une plage d\'ouverture');
	}


	/** @test */
	public function selectJourSemaineShouldContainsLundiToDimancheAndAucun() {
		$this->assertXPath('//form//select[@name="jour_semaine"]//option[@value="0"][@label="Aucune"]');
		$this->assertXPath('//form//select[@name="jour_semaine"]//option[@value="1"][@label="Tous les lundis"]');
		$this->assertXPath('//form//select[@name="jour_semaine"]//option[@value="7"][@label="Tous les dimanches"]');
	}
}




class OuverturesControllerPostAddOuvertureCranTest extends OuverturesControllerTestCase {
	protected $_new_ouverture;

	public function setUp() {
		parent::setUp();

		Class_Ouverture::whenCalled('save')->willDo(function($model) { 
																									$model->setId(99); 
																									return true;
																								});

		$this->postDispatch('/admin/ouvertures/add',	['debut_matin' => '10:30',
																									 'fin_matin' => '11:30',
																									 'id_site' => 3,
																									 'jour_semaine' => 2,
																									 'jour' => '23/10/2012']);
		$this->_new_ouverture = Class_Ouverture::getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function newOuvertureSiteIdShouldBeThree() {
		$this->assertEquals(3, $this->_new_ouverture->getIdSite());
	}


	/** @test */
	public function responseShouldRedirectToOuverturesIndexSiteThree() {
		$this->assertRedirectTo('/admin/ouvertures/index/id_site/3');
	}

	/** @test */
	public function jourShouldBeEmpty() {
		$this->assertEquals(null, $this->_new_ouverture->getJour());
	}


	/** @test */
	public function jourSemainShouldBeMardi() {
		$this->assertEquals(Class_Ouverture::MARDI, $this->_new_ouverture->getJourSemaine());
	}

	/** @test */
	public function formattedJourShouldBeMardi() {
		$this->assertEquals('Mardi', $this->_new_ouverture->getFormattedJour());
	}
}


?>