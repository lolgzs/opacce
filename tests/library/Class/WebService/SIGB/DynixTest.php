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

require('DynixFixtures.php');

class DynixGetServiceTest extends Storm_Test_ModelTestCase {
	protected $_service;

	public function setUp() {
		Class_WebService_SIGB_Dynix::reset();
		$this->_service = Class_WebService_SIGB_Dynix::getService(['url_serveur' => 'http://www.infocom94.fr:8080/capcvm/',
																															 'client_id' => 'myid']);
	}


	/** @test */
	public function getServiceShouldCreateAnInstanceOfDynixService() {
		$this->assertInstanceOf('Class_WebService_SIGB_Dynix_Service',
														$this->_service);
	}
}




abstract class DynixTestCase extends Storm_Test_ModelTestCase {
	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_mock_web_client;

	/** @var Class_WebService_SIGB_Dynix_Service */
	protected $_service;

	public function setUp() {
		parent::setUp();

		Class_Profil::getCurrentProfil()->setCfgNotice(['exemplaires' => []]);

		$this->_mock_web_client = Storm_Test_ObjectWrapper::mock();


		$this->_service = Class_WebService_SIGB_Dynix
			::getService(['url_serveur' => 'http://www.infocom94.fr:8080/capcvm/',
																															 'client_id' => 'SymWS'])
			->setWebClient($this->_mock_web_client);
	}
}




class DynixGetNoticeLeCombatOrdinaire extends DynixTestCase {
	protected $_notice;

	public function setUp() {
		parent::setUp();

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://www.infocom94.fr:8080/capcvm/rest/standard/lookupTitleInfo?clientID=SymWS&titleID=233823&includeItemInfo=true&includeAvailabilityInfo=true')
			->answers(DynixFixtures::xmlLookupTitleInfoLeCombatOrdinaire())
			->beStrict();

		$this->_notice = $this->_service->getNotice('233823');
	}


	/** @test */
	public function shouldAnswerANotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->_notice);
	}


	/** @test */
	public function noticeIdShouldBe233823() {
		$this->assertEquals(233823, $this->_notice->getId());
	}


	/** @test */
	public function getExemplairesShouldReturnAnArrayWithSizeThree() {
		$this->assertEquals(3, count($this->_notice->getExemplaires()));
	}


	/** @test */
	public function firstExemplaireIdShouldBe39410001517933() {
		$this->assertEquals('39410001517933', $this->_notice->exemplaireAt(0)->getId());
	}


	/** @test */
	public function firstCodeBarresShouldBe39410001517933() {
		$this->assertEquals('39410001517933', $this->_notice->exemplaireAt(0)->getCodeBarre());
	}


	/** @test */
	public function firstExemplaireCodeAnnexeShouldBeALFMEDA() {
		$this->assertEquals('ALFMEDA', $this->_notice->exemplaireAt(0)->getCodeAnnexe());
	}


	/** @test */
	public function firstExemplaireShouldBeReservable() {
		$this->assertTrue($this->_notice->exemplaireAt(0)->isReservable());
	}


	/** @test */
	public function firstExemplaireDisponibiliteShouldBeEnPret() {
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_EN_PRET,
												$this->_notice->exemplaireAt(0)->getDisponibilite());
	}


	/** @test */
	public function secondExemplaireCodeAnnexeShouldBeALFAX1() {
		$this->assertEquals('ALFAX1', $this->_notice->exemplaireAt(1)->getCodeAnnexe());
	}


	/** @test */
	public function secondExemplairDisponibiliteShouldBeEnTransit() {
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_TRANSIT,
												$this->_notice->exemplaireAt(1)->getDisponibilite());
	}	


	/** @test */
	public function secondExemplaireShouldBeReservable() {
		$this->assertTrue($this->_notice->exemplaireAt(1)->isReservable());
	}


	/** @test */
	public function thirdExemplaireDisponibiliteShouldBeDisponible() {
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_LIBRE,
												$this->_notice->exemplaireAt(2)->getDisponibilite());
	}	
}




class DynixGetNoticeHarryPotter extends DynixTestCase {
	protected $_notice;

	public function setUp() {
		parent::setUp();

		$this->_service->setClientId('RealWS');

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://www.infocom94.fr:8080/capcvm/rest/standard/lookupTitleInfo?clientID=RealWS&titleID=353917&includeItemInfo=true&includeAvailabilityInfo=true')
			->answers(DynixFixtures::xmlLookupTitleInfoHarryPotter())
			->beStrict();

		$this->_notice = $this->_service->getNotice('353917');
	}


	/** @test */
	public function firstExemplaireShouldNotBeReservable() {
		$this->assertFalse($this->_notice->exemplaireAt(0)->isReservable());
	}
}




class DynixGetEmprunteurManuLarcinet extends DynixTestCase {
	protected $_manu;

	public function setUp() {
		parent::setUp();

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://www.infocom94.fr:8080/capcvm/rest/security/loginUser?clientID=SymWS&login=0917036&password=secret')
			->answers(DynixFixtures::loginUserManuLarcinetXml())


			->whenCalled('open_url')
			->with('http://www.infocom94.fr:8080/capcvm/rest/patron/lookupMyAccountInfo?includePatronHoldInfo=ACTIVE&includePatronInfo=true&includePatronCheckoutInfo=ALL&clientID=SymWS&sessionToken=497e6380-69fb-4850-b552-40dede41f0b5')
			->answers(DynixFixtures::manuLarcinetAccoutInfoXml())

			->whenCalled('open_url')
			->with('http://www.infocom94.fr:8080/capcvm/rest/security/logoutUser?clientID=SymWS&sessionToken=497e6380-69fb-4850-b552-40dede41f0b5')
			->answers('')
			->beStrict();

		$this->_manu = $this->_service->getEmprunteur(Class_Users::newInstanceWithId(3, ['login' => '0917036', 
																																										 'password' => 'secret']));
	}


	/** @test */
	public function shouldAnswerAnEmprunteur() {
		$this->assertInstanceOf('Class_WebService_SIGB_Emprunteur', $this->_manu);
	}


	/** @test */
	public function idShouldBe0917036() {
		$this->assertEquals('0917036', $this->_manu->getId());
	}


	/** @test */
	public function nbEmpruntsShouldBeThree() {
		$this->assertEquals(3, $this->_manu->getNbEmprunts());
	}


	/** @test */
	public function firstEmpruntCodeBarreShouldBe00406882() {
		$this->assertEquals('00406882', $this->_manu->getEmpruntAt(0)->getCodeBarre());
	}


	/** @test */
	public function firstEmpruntShouldDateRetourShouldBe17_10_2012() {
		$this->assertEquals('17/10/2012', $this->_manu->getEmpruntAt(0)->getDateRetour());
	}


	/** @test */
	public function nbReservationsShouldBeTwo() {
		$this->assertEquals(2, $this->_manu->getNbReservations());
	}


	/** @test */
	public function firstReservationCodeBarreShouldBe00577705() {
		$this->assertEquals('00577705', $this->_manu->getReservationAt(0)->getCodeBarre());		
	}


	/** @test */
	public function firstReservationRangShouldBeOne() {
		$this->assertEquals(1, $this->_manu->getReservationAt(0)->getRang());		
	}


	/** @test */
	public function firstReservationEtatShouldBeReserve() {
		$this->assertEquals('Réservé', $this->_manu->getReservationAt(0)->getEtat());		
	}


	/** @test */
	public function secondReservationRangShouldBeTwo() {
		$this->assertEquals(2, $this->_manu->getReservationAt(1)->getRang());		
	}


	/** @test */
	public function secondReservationEtatShouldBeDisponible() {
		$this->assertEquals('Disponible', $this->_manu->getReservationAt(1)->getEtat());		
	}
}

?>