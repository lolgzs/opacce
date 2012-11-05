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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';


class ReservationsTestWithConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$helper = new ZendAfi_View_Helper_Accueil_Reservations(2, [
			'type_module'=>'RESERVATIONS',
			'division' => '1',
			'preferences' => [
			'titre' => 'Mes reservations']]);
		$account = new StdClass();
		$account->ID_USER = '123456';
		ZendAfi_Auth::getInstance()->getStorage()->write($account);
		$user=Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']);
		$propaganda = new Class_WebService_SIGB_Reservation('13', new Class_WebService_SIGB_Exemplaire(456));
		$propaganda->getExemplaire()
					->setTitre('Propaganda')
					->setNoticeOPAC(Class_Notice::newInstanceWithId(1234));

		$propaganda->setEtat('Pas disponible');

		$en_suivant_emma = new Class_WebService_SIGB_Reservation('13', new Class_WebService_SIGB_Exemplaire(456));
		$en_suivant_emma->getExemplaire()
					->setTitre('En suivant Emma')
					->setNoticeOPAC(Class_Notice::newInstanceWithId(333));

		$en_suivant_emma->setEtat('Disponible');
		$emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Estelle');
		$user->setFicheSigb(['fiche'=>$emprunteur]);

		$emprunteur->reservationsAddAll(array( $propaganda,$en_suivant_emma));
		$this->html = $helper->getBoite();
	}
	

	/** @test  */
	public function h1ShouldContainsMesReservations () {
		$this->assertXPathContentContains($this->html,'//h1','Mes reservations');
	}

	
	/** @test */
	public function listShouldDisplayPropagandaNotice() {
		$this->assertXPathContentContains($this->html,'//ul//li','Propaganda');
	}

	/** @test */
	public function listShouldDisplayEnSuivantEmmaNotice() {
		$this->assertXPathContentContains($this->html,'//ul//li','En suivant Emma');
	}


	/** @test */
	public function etatPasDisponibleShouldBeDisplayed () {
		$this->assertXPathContentContains($this->html,'//ul//li','Pas disponible');
	}


	/** @test */
	public function titlePropagandaShouldBeLinkedToNotice () {
		$this->assertXPath($this->html,'//ul//li//a[contains(@href,"/recherche/viewnotice/clef//id/1234")]',$this->html);
	}



	/** @test */
	public function titleShouldBeLinkedToAbonneReservations () {
		$this->assertXPath($this->html,'//h1//a[contains(@href,"/abonne/reservations")]',$this->html);
	}
	

}


class ReservationsTestWithNonConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$this->helper = new ZendAfi_View_Helper_Accueil_Reservations(2, [
			'type_module'=>'RESERVATIONS',
			'division' => '1',
			'preferences' => 	['titre' => 'Mes reservations']]
		);
		$this->html = $this->helper->getBoite();
	}
	

	/** @test */
	public function boiteReservationsShouldNotBeDisplayed () {
		$this->assertEmpty($this->html);
	}


	/** @test */
	public function boiteReservationsShouldNotCacheContents () {
		$this->assertFalse($this->helper->shouldCacheContent());
	}

}

?>