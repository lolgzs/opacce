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

include_once('MicrobibFixtures.php');

abstract class MicrobibServiceIntegrationTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function infoExemplairesWithRealServer() {
		Class_WebService_SIGB_Microbib::reset();
		$microbib = Class_WebService_SIGB_Microbib::getService(array('url_serveur' => 'http://80.11.188.93/webservices/ws_maze.wsdl'));
		$notice = $microbib->getNotice('5204');
		$this->assertEquals(2, count($notice->getExemplaires()));
	}
}




abstract class MicrobibServiceTestCase extends PHPUnit_Framework_TestCase {
	protected $_search_client;
	protected $_microbib;

	public function setUp() {
		parent::setUp();
		$this->_search_client = Storm_Test_ObjectWrapper::on(new SoapClient('tests/fixtures/ws_maze.wsdl'));
		$this->_microbib = new Class_WebService_SIGB_Microbib_Service($this->_search_client);
	}
}




class MicrobibServiceTestInfosExemplaires5204 extends MicrobibServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('infos_exemplaires')
			->with(5204)
			->answers(MicrobibFixtures::xmlInfosExemplaires5204());
		
		$this->notice = $this->_microbib->getNotice('5204');
	}


	/** @test */
	public function firstExemplaireShouldNotBeEmpty() {
		$exemplaire = array_first($this->notice->getExemplaires());
		$this->assertNotEmpty($exemplaire);
		return $exemplaire;
	}


	/** 
	 * @test 
	 * @depends firstExemplaireShouldNotBeEmpty
	 */
	public function firstExemplaireCodeBarreShouldBe0006260194($exemplaire) {
		$this->assertEquals('0006260194', $exemplaire->getCodeBarre());
	}


	/** 
	 * @test 
	 * @depends firstExemplaireShouldNotBeEmpty
	 */
	public function firstExemplaireIdShouldBe0006260194($exemplaire) {
		$this->assertEquals('0006260194', $exemplaire->getId());
	}


	/** 
	 * @test 
	 * @depends firstExemplaireShouldNotBeEmpty
	 */
	public function firstExemplaireShouldBeDisponible($exemplaire) {
		$this->assertEquals('Disponible', $exemplaire->getDisponibilite());
	}


	/** 
	 * @test 
	 * @depends firstExemplaireShouldNotBeEmpty
	 */
	public function firstExemplaireShouldNotBeReservable($exemplaire) {
		$this->assertFalse($exemplaire->isReservable());
	}


	/** @test */
	public function secondExemplaireShouldNotBeEmpty() {
		$exemplaire = array_at(1, $this->notice->getExemplaires());
		$this->assertNotEmpty($exemplaire);
		return $exemplaire;
	}
	

	/** 
	 * @test 
	 * @depends secondExemplaireShouldNotBeEmpty
	 */
	public function secondExemplaireShouldBeReservable($exemplaire) {
		$this->assertTrue($exemplaire->isReservable());
	}


	/** 
	 * @test 
	 * @depends secondExemplaireShouldNotBeEmpty
	 */
	public function secondExemplaireShouldNotBeDisponible($exemplaire) {
		$this->assertEquals('Indisponible', $exemplaire->getDisponibilite());
	}


	/** 
	 * @test 
	 * @depends secondExemplaireShouldNotBeEmpty
	 */
	public function secondExemplaireDateRetourShouldBe31_03_2012($exemplaire) {
		$this->assertEquals('31/03/2012', $exemplaire->getDateRetour());
	}
}




class MicrobibServiceTestInfosAbonne extends MicrobibServiceTestCase {
	protected $emprunteur;

	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('infos_abonne')
			->with('9999', '0101')
			->answers(MicrobibFixtures::xmlInfosAbonne9999())
			->beStrict();
		
		$this->emprunteur = $this->_microbib->getEmprunteur(Class_Users::getLoader()
																												->newInstanceWithId(9999)
																												->setLogin('9999')
																												->setPassword('0101'));
	}


	/** @test */
	public function emprunteurShouldNotBeEmpty() {
		$this->assertNotEmpty($this->emprunteur);
	}


	/** @test */
	public function emprunteurShouldHaveFivePrets() {
		$this->assertEquals(5, count($this->emprunteur->getEmprunts()));
	}


	/** @test */
	public function firstEmpruntTitreShouldBeLeRoiCasse() {
		$emprunt = array_first($this->emprunteur->getEmprunts());
		$this->assertEquals('ROI CASSÉ (LE)', $emprunt->getTitre());		
		return $emprunt;
	}


	/** 
	 * @test 
	 * @depends firstEmpruntTitreShouldBeLeRoiCasse
	 */
	public function firstEmpruntDateRetourShouldBe25_01_2012($emprunt) {
		$this->assertEquals('25/01/2012', $emprunt->getDateRetour());		
	}


	/** 
	 * @test 
	 * @depends firstEmpruntTitreShouldBeLeRoiCasse
	 */
	public function firstEmpruntCodeBarreShouldBe0200650194($emprunt) {
		$this->assertEquals('0200650194', $emprunt->getCodeBarre());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntTitreShouldBeLeRoiCasse
	 */
	public function firstEmpruntIdShouldBe0200650194($emprunt) {
		$this->assertEquals('0200650194', $emprunt->getId());
	}


	/** @test */
	public function emprunteurShouldHaveTwoReservations() {
		$this->assertEquals(2, count($this->emprunteur->getReservations()));
	}


	/** @test */
	public function firstReservationAuteurShouldBeBIGART() {
		$this->assertEquals('BIGART T.P.', array_first($this->emprunteur->getReservations())->getAuteur());
	}


	/** @test */
	public function firstReservationEtatShouldBeReserve() {
		$this->assertEquals('Réservé', array_first($this->emprunteur->getReservations())->getEtat());
	}


	/** @test */
	public function secondReservationEtatShouldBeDisponible() {
		$this->assertEquals('Disponible', array_last($this->emprunteur->getReservations())->getEtat());
	}
}




class MicrobibServiceInfosAbonneWithoutReservationsAndPretsTest extends MicrobibServiceTestCase {
	protected $emprunteur;

	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('infos_abonne')
			->with('666', '666')
			->answers('')
			->beStrict();
		
		$this->emprunteur = $this->_microbib->getEmprunteur(Class_Users::getLoader()
																												->newInstanceWithId(666)
																												->setLogin('666')
																												->setPassword('666'));
	}


	/** @test */
	public function nbReservationsShouldBe0() {
		$this->assertEquals(0, count($this->emprunteur->getReservations()));
	}


	/** @test */
	public function nbEmpruntsShouldBe0() {
		$this->assertEquals(0, count($this->emprunteur->getEmprunts()));
	}
}




class MicrobibServiceActionsTest extends MicrobibServiceTestCase {
	protected $_user;

	public function setUp() {
		parent::setUp();
		$this->_user = Class_Users::getLoader()
			->newInstanceWithId(9999)
			->setLogin('9999')
			->setPassword('0101');
		
	}


	/** @test */
	public function reservationWithResponsOKShouldBeSuccessfull() {
		$this->_search_client
			->whenCalled('ajout_reservation')
			->with('9999', '1805660020')
			->answers(MicrobibFixtures::xmlAjoutReservationOK())
			->beStrict();

		$exemplaire = Class_Exemplaire::getLoader()
			->newInstanceWithId(123)
			->setIdOrigine('45456656')
			->setCodeBarres('1805660020');

		$response = $this->_microbib->reserverExemplaire($this->_user, $exemplaire, '');
		$this->assertEquals(array('statut' => true, 'erreur' => ''), $response);
	}


	/** @test */
	public function reservationWithResponseNotOkShouldBeError() {
		$this->_search_client
			->whenCalled('ajout_reservation')
			->with('9999', '1805660099')
			->answers(MicrobibFixtures::xmlAjoutReservationError())
			->beStrict();

		$exemplaire = Class_Exemplaire::getLoader()
			->newInstanceWithId(9)
			->setIdOrigine('987')
			->setCodeBarres('1805660099');

		$response = $this->_microbib->reserverExemplaire($this->_user, $exemplaire, '');
		$this->assertEquals(array('statut' => false, 'erreur' => 'Impossible de valider votre demande'), $response);
	}


	/** @test */
	public function prolongePretWithResponsOKShouldBeSuccessfull() {
		$this->_search_client
			->whenCalled('prolonge_pret')
			->with('9999', '1805660020')
			->answers(MicrobibFixtures::xmlProlongePretOK())
			->beStrict();

		$response = $this->_microbib->prolongerPret($this->_user, '1805660020');
		$this->assertEquals(array('statut' => true, 'erreur' => ''), $response);
	}


	/** @test */
	public function prolongePretWithResponseNotOkShouldBeError() {
		$this->_search_client
			->whenCalled('prolonge_pret')
			->with('9999', '1805660099')
			->answers(MicrobibFixtures::xmlProlongePretError())
			->beStrict();

		$response = $this->_microbib->prolongerPret($this->_user, '1805660099');
		$this->assertEquals(array('statut' => false, 'erreur' => 'Impossible de prolonger de prêt...'), $response);
	}


	/** @test */
	public function annuleReservationWithResponsOKShouldBeSuccessfull() {
		$this->_search_client
			->whenCalled('annule_reservation')
			->with('9999', '1805660020')
			->answers(MicrobibFixtures::xmlAnnuleReservationOK())
			->beStrict();

		$response = $this->_microbib->supprimerReservation($this->_user, '1805660020');
		$this->assertEquals(array('statut' => true, 'erreur' => ''), $response);
	}


	/** @test */
	public function annuleReservationWithResponseNotOkShouldBeError() {
		$this->_search_client
			->whenCalled('annule_reservation')
			->with('9999', '1805660099')
			->answers(MicrobibFixtures::xmlAnnuleReservationError())
			->beStrict();

		$response = $this->_microbib->supprimerReservation($this->_user, '1805660099');
		$this->assertEquals(array('statut' => false, 'erreur' => 'Impossible d\'annuler cette réservation...'), $response);
	}
}

?>