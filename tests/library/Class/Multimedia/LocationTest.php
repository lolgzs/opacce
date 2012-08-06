
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

class Multimedia_LocationTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		Class_Bib::newInstanceWithId(3)
			->setLibelle('Bibliothèque Antibes');

		$loc = Class_Multimedia_Location::newInstanceWithId(123)
			->setIdSite(3)
			->setLibelle('Antibes')
			->setSlotSize(30)
			->setMaxSlots(4)
			->setHoldDelayMin(0)
			->setHoldDelayMax(60)
			->setOuvertures([Class_Ouverture::chaqueMercredi('08:30', '12:00', '12:00', '17:45')->setId(3)->cache(),
											 Class_Ouverture::chaqueJeudi('08:30', '12:00', '12:00', '17:45')->setId(4)->cache()]);
	}


	/** @test */
	public function bibShouldHaveOuvertureForMercredi() {
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
}

?>