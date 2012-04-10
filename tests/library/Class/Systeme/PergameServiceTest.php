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

/** Tests de non régression sur requetes avec IDABON non entier */
class PergameServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$user = Class_Users::getLoader()
			->newInstanceWithId(3)
			->setIdabon('A-02')
			->setOrdreabon(2);
		$this->pergame = new Class_Systeme_PergameService($user);
	}

	/** @test */
	function nbPretsEnRetardShouldNotFail() {
		$this->pergame->getNbPretsEnRetard();
	}


	/** @test */
	function getNbEmpruntsShouldNotFail() {
		$this->pergame->getNbEmprunts();
	}


	/** @test */
	function getNbReservationsShouldNotFail() {
		$this->pergame->getNbReservations();
	}


	/** @test */
	function getPretsShouldNotFail() {
		$this->pergame->getPrets();
	}


	/** @test */
	function getReservationsShouldNotFail() {
		$this->pergame->getReservations();
	}
}

?>