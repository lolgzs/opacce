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
	protected $_service;

	public function setUp() {
		parent::setUp();

		$potter = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
			->setId(11)
			->setTitre('Harry Potter')
			->setAuteur('JKR')
			->setBibliotheque('Annecy')
			->setDateRetour('23/45/6789')
			->setNoticeOPAC(Class_Notice::getLoader()->newInstanceWithId(45));
 
		$alice = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire()
			->setId(12)
			->setTitre('Alice au pays des merveilles')
			->setAuteur('PBA')
			->setBibliotheque('Cran-Gevrier')
			->setDateRetour('24/45/6789')
			->setNoticeOPAC(Class_Notice::getLoader()->newInstanceWithId(46));

		$emprunteur = Class_WebService_SIGB_Emprunteur::newInstance()
			->empruntsAddAll(array($potter, $alice))

			->reservationsAddAll(array(Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire()
																 ->setTitre('Star Wars')
																 ->setId(123)));

		$user = Class_Users::getLoader()->getIdentity()
			->setIdabon(23)
			->setFicheSIGB(array('type_comm' => Class_CommSigb::COM_VSMART,
													 'fiche' => $emprunteur));

		Class_IntBib::getLoader()->newInstanceWithId($user->getIdSite())
			->setCommParams(array())
			->setCommSigb(Class_CommSigb::COM_VSMART);
	}
}




class AbonneControllerTelephoneFicheTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('abonne/fiche');
	}


	/** @test */
	public function pageShouldDisplayLoanCount() {
		$this->assertXPathContentContains('//span[@class="ui-li-count"]', '2',
																			$this->_response->getBody());
	}


	/** @test */
	public function pageShouldDisplayFirstEmpruntLink() {
		$this->assertXPath('//a[contains(@href, "viewnotice/id/45")]');
	}


	/** @test */
	public function pageShouldDisplayFirstRenewLink() {
		$this->assertXPath('//a[contains(@href, "abonne/prolongerpret/id_pret/11")]');
	}


	/** @test */
	public function pageShouldDisplayHoldCount() {
		$this->assertXPathContentContains('//span[@class="ui-li-count"]', '1');
	}


	/** @test */
	public function contextShouldExpectation() {
		$this->assertXPath('//a[contains(@href, "cancel-hold/id/123")]');
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



class AbonneControllerTelephoneCancelHoldTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('abonne/cancel-hold/id/123');
	}


  /** @test */
	public function pageShouldContainConfirmationDialog() {
		$this->assertXPathContentContains('//p', 'Star Wars');
	}


	/** @test */
	public function pageShouldContainConfirmationLink() {
		$this->assertXPathContentContains('//a[contains(@href, "cancel-hold/id/123/confirmed/1")]', 'Supprimer');
	}


	/** @test */
	public function pageShouldContainBackLink() {
		$this->assertXPathContentContains('//a', 'Annuler');
	}
}


class AbonneControllerTelephoneConfirmedCancelHoldTest extends AbonneControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();

		$this->_service = Storm_Test_ObjectWrapper::mock()
			->whenCalled('supprimerReservation')
			->answers(true)

			->whenCalled('isConnected')
			->answers(true);

		Class_WebService_SIGB_VSmart::setService($this->_service);

		$this->dispatch('abonne/cancel-hold/id/123/confirmed/1', true);
	}


	/** @test */
	public function shouldCallHoldDeletion() {
		$this->assertTrue($this->_service->methodHasBeenCalled('supprimerReservation'));
	}


  /** @test */
	public function shouldRedirectToFiche() {
		$this->assertRedirectTo('/abonne/fiche');
	}
}

?>