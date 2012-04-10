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

class ExemplaireSIGBTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->profil = new Class_Profil();
		$this->profil
			->setId(4)
			->setCfgNotice(array("exemplaires" => array()));
		Class_Profil::setCurrentProfil($this->profil);

		$this->ex = new Class_WebService_SIGB_Exemplaire(2);
	}

	public function tearDown() {
		$this->profil->setCfgNotice($this->profil->getDefaultValue('cfg_notice'));
		parent::tearDown();
	}


	/** @test */
	public function dispoEnPretShouldBeEnPretByDefault() {
		$this->ex->setDisponibiliteEnPret();
		$this->assertEquals('En prêt', $this->ex->getDisponibilite());
	}


	/** @test */
	public function dispoEnPretShouldBeEmprunteAsInProfilParams() {
		$this->profil
			->setCfgNotice(array("exemplaires" => array('en_pret' => 'Emprunté')));

		$this->ex->setDisponibiliteEnPret();
		$this->assertEquals('Emprunté', $this->ex->getDisponibilite());
	}
}

?>