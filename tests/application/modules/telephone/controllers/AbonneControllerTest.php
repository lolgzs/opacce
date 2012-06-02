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
require_once 'TelephoneAbstractControllerTestCase.php';

abstract class AbonneControllerTelephoneTestCase extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$emprunteur = Class_WebService_SIGB_Emprunteur::newInstance()
			->empruntsAddAll(array(Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
														 ->setTitre('Harry Potter')
														 ->setNoticeOPAC(Class_Notice::getLoader()->newInstanceWithId(45)),

														 Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
														 ->setTitre('Alice au pays des merveilles')))
			->reservationsAddAll(array(														 
																 Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire()
																 ->setTitre('Star Wars')));

		Class_Users::getLoader()->getIdentity()
			->setIdabon(23)
			->setFicheSIGB(array('type_comm' => Class_CommSigb::COM_NANOOK,
													 'fiche' => $emprunteur));
	}
}




class AbonneControllerTelephoneFicheTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('abonne/fiche');
	}


	/** @test */
	public function pageShouldDisplayUnPretEnCoursLink() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/prets")]', 'Vous avez 2 prêts en cours');
	}


	/** @test */
	public function pageShouldDisplayAucunReservationLink() {
		$this->assertXPathContentContains('//a[contains(@href, "abonne/reservations")]', 'Vous avez 1 réservation en cours',
																			$this->_response->getBody());
	}


	/** @test */
	public function pageShouldDisplayLogoutLink() {
		$this->assertXPathContentContains('//a[contains(@href, "auth/logout")]', 'Se déconnecter');
	}


	/** @test */
	public function pageShouldDisplayLintToIndex() {
		$this->assertXPath('//a[@href = "/"]');
	}
}




class AbonneControllerTelephonePretsTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('abonne/prets');
	}


	/** @test */
	public function pageShouldDisplayLinkToHarryPotter() {
		$this->assertXPathContentContains('//li', 'Harry Potter');
	}


	/** @test */
	public function pageShouldDisplayLinkToAlice() {
		$this->assertXPathContentContains('//li', 'Alice au pays des merveilles');
	}
}




class AbonneControllerTelephoneReservationsTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('abonne/reservations');
	}


	/** @test */
	public function pageShouldDisplayLinkToHarryPotter() {
		$this->assertXPathContentContains('//li', 'Star Wars');
	}
}

?>