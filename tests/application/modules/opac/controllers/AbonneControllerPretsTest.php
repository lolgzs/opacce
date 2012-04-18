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
require_once 'AbstractControllerTestCase.php';

abstract class AbstractAbonneControllerPretsTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = 2;
		$account->ID_USER = '123456';
		$account->PSEUDO = "Florence";
		$this->account = $account;
	}

	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')->whenCalled('save')->answers(true);
		$this->florence = Class_Users::getLoader()->newInstanceWithId('123456');

	}
}


class AbonneControllerPretsListTwoPretsTest extends AbstractAbonneControllerPretsTestCase {
	public function setUp() {
		parent::setUp();

		$potter = new Class_WebService_SIGB_Emprunt('12', new Class_WebService_SIGB_Exemplaire(123));
		$potter->getExemplaire()->setTitre('Potter');
		$potter->parseExtraAttributes(array(
																				'Dateretourprevue' => '29/10/2010',
																				'Section' => 'Espace jeunesse',
																				'Auteur' => 'JK Rowling',
																				'Bibliotheque' => 'Astrolabe',
																				'N° de notice' => '1234'));

		$alice = new Class_WebService_SIGB_Emprunt('13', new Class_WebService_SIGB_Exemplaire(456));
		$alice->getExemplaire()->setTitre('Alice');
		$alice->parseExtraAttributes(array(
																			 'Dateretourprevue' => '21/10/2010',
																			 'Section' => 'Espace jeunesse',
																			 'Auteur' => 'Lewis Caroll',
																			 'Bibliotheque' => 'Almont',
																			 'N° de notice' => '5678'));

		$emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Florence');
		$emprunteur->empruntsAddAll(array($potter, $alice));

		$fiche_sigb = array('type_comm' => 2, //OPSYS
												'fiche' => $emprunteur,
												'message' => '',
												'erreur' => '',
												'nom_aff' => 'FloFlo');

		$this->florence->setFicheSigb($fiche_sigb)
									->setPseudo('FloFlo');

		$this->dispatch('/opac/abonne/prets');
	}


	public function testPageIsRendered() {
		$this->assertController('abonne');
		$this->assertAction('prets');
	}


	public function testNomAffiche() {
		$this->assertQueryContentContains("div.abonneTitre", 'FloFlo');
	}

	public function testViewTitreAlice() {
		$this->assertXPathContentContains("//tr[2]//td", 'Alice');
	}

	public function testViewBibAliceIsAlmont() {
		$this->assertXPathContentContains("//tr[2]//td", 'Almont');
	}

	public function testViewAuteurAliceIsLewisCaroll() {
		$this->assertXPathContentContains("//tr[2]//td", 'Lewis Caroll');
	}

	public function testViewDateRetourAliceIsTwentyOneOctober() {
		$this->assertXPathContentContains("//tr[2]//td", '21/10/2010');
	}

	public function testLinkProlongerForAlice() {
		$this->assertXPathContentContains("//tr[2]//td//a[@href='/abonne/prolongerPret/id_pret/13']",
																			'Prolonger');
	}

	public function testViewTitrePotter() {
		$this->assertXPathContentContains("//tr[3]//td", 'Potter');
	}

	public function testViewBibPotterIsAstrolabe() {
		$this->assertXPathContentContains("//tr[3]//td", 'Astrolabe');
	}

	public function testViewAuteurPotterIsJKRolling() {
		$this->assertXPathContentContains("//tr[3]//td", 'JK Rowling');
	}

	public function testViewDateRetourPotterIsTwentyNineOctober() {
		$this->assertXPathContentContains("//tr[3]//td", '29/10/2010');
	}

	public function testLinkProlongerForPotter() {
		$this->assertXPathContentContains("//tr[3]//td//a[@href='/abonne/prolongerPret/id_pret/12']",
																			'Prolonger');
	}

}


class AbonneControllerPretsListReservationTest extends AbstractAbonneControllerPretsTestCase {
	protected $_potter;

	public function setUp() {
		parent::setUp();

		$potter = new Class_WebService_SIGB_Reservation('12', new Class_WebService_SIGB_Exemplaire(123));
		$this->_potter = $potter->getExemplaire()->setTitre('Potter');
		$potter->parseExtraAttributes(array('Etat' => 'Réservation émise',
																				'Rang' => '2',
																				'Bibliotheque' => 'Tombouctou'));

		$emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Florence');
		$emprunteur->reservationsAddAll(array($potter));

		$fiche_sigb = array('type_comm' => 2, //OPSYS
												'fiche' => $emprunteur,
												'message' => '',
												'erreur' => '',
												'nom_aff' => 'FloFlo');

		$this->florence->setFicheSigb($fiche_sigb)
									->setPseudo('FloFlo');

		$this->dispatch('/opac/abonne/reservations');
	}


	/** @test */
	public function controllerShouldBeAbonne() {
		$this->assertController('abonne');
	}


	/** @test */
	public function actionShouldBeReservations() {
		$this->assertAction('reservations');
	}


	/** @test */
	public function nomShouldBeFloFlo() {
		$this->assertQueryContentContains('div.abonneTitre', 'FloFlo');
	}


	/** @test */
	public function titreShouldBePotter() {
		$this->assertXPathContentContains('//tr[2]//td', 'Potter');
	}


	/** @test */
	public function etatShouldBeReservationEmise() {
		$this->assertXPathContentContains('//tr[2]//td', 'Réservation émise');
	}


	/** @test */
	public function rangShouldBeTwo() {
		$this->assertXPathContentContains('//tr[2]//td', '2');
	}


	/** @test */
	public function linkToDeleteShouldBeAsExpected() {
		$this->assertXPath("//tr[2]//td//a[@href='/abonne/reservations/id_delete/12']");
	}


	/** @test */
	public function bibliothequeShouldBeTombouctou() {
		$this->assertXPathContentContains('//tr[2]//td', 'Tombouctou', $this->_response->getBody());
	}

}


?>