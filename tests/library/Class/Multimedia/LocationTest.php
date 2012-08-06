
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
require_once 'ModelTestCase.php';

class Multimedia_LocationWithBibTest extends Storm_Test_ModelTestCase {
	protected $_location;

	public function setUp() {
		Class_Bib::newInstanceWithId(3)
			->setLibelle('Bibliothèque Antibes');

		$this->_location = Class_Multimedia_Location::newInstanceWithId(123)
			->setIdSite(3)
			->setLibelle('Antibes')
			->setSlotSize(30)
			->setMaxSlots(4)
			->setHoldDelayMin(0)
			->setHoldDelayMax(60)
			->setOuvertures([Class_Ouverture::chaqueMercredi('08:30', '12:00', '12:00', '17:45')->setId(3)->cache(),
											 Class_Ouverture::chaqueJeudi('10:00', '12:00', '14:00', '19:00')->setId(4)->cache()]);
	}


	/** @test */
	public function bibShouldHaveOuvertureForMercredi() {
		Class_Bib::find(3)->getOuvertures();
		$this->assertEquals(Class_Ouverture::MERCREDI, 
												Class_Bib::find(3)->getOuvertures()[0]->getJourSemaine());
	}


	/** @test */
	public function bibShouldHaveOuvertureForJeudi() {
		$this->assertEquals(Class_Ouverture::JEUDI, 
												Class_Bib::find(3)->getOuvertures()[1]->getJourSemaine());
	}


	/** @test */
	public function ouvertureMercrediShouldBelongsToBibAntibes() {
		$this->assertEquals('Bibliothèque Antibes',
												Class_Ouverture::find(3)->getLibelleBib());
	}


	/** @test */
	public function getMinTimeFor8Aug2012ShouldReturnDebutMatinOfMercredi() {
		$this->assertEquals(strtotime('2012-08-08 08:30:00'), 
												$this->_location->getMinTimeForDate('2012-08-08'),
												'getMinTimeForDate => '.strftime('%Y-%m-%d %H:%M:%S',
																												 $this->_location->getMinTimeForDate('2012-08-08')));
	}


	/** @test */
	public function getMaxTimeFor8Aug2012ShouldReturnFinApresMidiOfMercredi() {
		$this->assertEquals(strtotime('2012-08-08 17:45:00'), 
												$this->_location->getMaxTimeForDate('2012-08-08'));
	}


	/** @test */
	public function getMinTimeFor9Aug2012ShouldReturnDebutMatinOfJeudi() {
		$this->assertEquals(strtotime('2012-08-09 10:00:00'), 
												$this->_location->getMinTimeForDate('2012-08-09'));
	}


	/** @test */
	public function getStartTimesFor8AugShouldReturnAllHalfHoursFrom_0830_to_1730() {
		$this->assertEquals(['08:30' => '08h30', '09:00' => '09h00', '09:30' => '09h30', '10:00' => '10h00', '10:30' => '10h30', 
												 '11:00' => '11h00', '11:30' => '11h30', '12:00' => '12h00', '12:30' => '12h30', '13:00' => '13h00', 
												 '13:30' => '13h30', '14:00' => '14h00', '14:30' => '14h30', '15:00' => '15h00', '15:30' => '15h30', 
												 '16:00' => '16h00', '16:30' => '16h30', '17:00' => '17h00', '17:30' => '17h30'], 
												$this->_location->getStartTimesForDate('2012-08-08'));
		
	}


	/** @test */
	public function getStartTimesFor9AugShouldReturnAllHalfHoursFrom_1000_to_1130_then_1400_to_1830() {
		$this->assertEquals(['10:00' => '10h00', '10:30' => '10h30', '11:00' => '11h00', '11:30' => '11h30', 
												 '14:00' => '14h00', '14:30' => '14h30', '15:00' => '15h00', '15:30' => '15h30', 
												 '16:00' => '16h00', '16:30' => '16h30', '17:00' => '17h00', '17:30' => '17h30',
												 '18:00' => '18h00', '18:30' => '18h30'], 
												$this->_location->getStartTimesForDate('2012-08-09'));
		
	}
}



class Multimedia_LocationWithoutBibTest extends Storm_Test_ModelTestCase {
	protected $_location;

	public function setUp() {
		$this->_location = Class_Multimedia_Location::newInstanceWithId(123)
			->setOuvertures([Class_Ouverture::newInstanceWithId(3)]);
	}


	/** @test */
	public function getOuverturesShouldAnswersEmptyArray() {
		$this->assertEmpty($this->_location->getOuvertures());
	}

}

?>