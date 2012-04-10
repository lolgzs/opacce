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
include_once('VSmartFixtures.php');

abstract class  VSmartServiceTestCase extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CodifAnnexe')
			->whenCalled('findFirstBy')
			->with(array('code' => 'MCFC'))
			->answers(Class_CodifAnnexe::getLoader()->newInstanceWithId(2)
								->setCode('MCFC')
								->setLibelle('Médiathèque communautaire'))
			->getWrapper()

			->whenCalled('findFirstBy')
			->with(array('code' => 'AVER'))
			->answers(Class_CodifAnnexe::getLoader()->newInstanceWithId(4)
								->setCode('AVER')
								->setLibelle('Avermes'))
			->getWrapper();
	}
}


class VSmartServiceDummyFunctionsTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->service = Class_WebService_SIGB_VSmart_Service::newInstance();
	}

	/** @test */
	public function isConnectedShouldReturnTrue() {
		$this->assertTrue($this->service->isConnected());
	}


	/** @test */
	public function saveEmprunteurShouldDoNothing() {
		$this->assertEmpty($this->service->saveEmprunteur(null));
	}

	/** @test */
	function defaultWebClientShouldBeAnInstanceOfSimbleWebClient() {
		$this->assertInstanceOf('Class_WebService_SimpleWebClient', $this->service->getWebClient());
	}
}


class VSmartGetServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_WebService_SIGB_VSmart::reset();
		$this->service = Class_WebService_SIGB_VSmart::getService(array('url_serveur' => 'vpn.moulins.fr'));
	}


	/** @test */
	public function getServiceShouldCreateAnInstanceOfVSmartService() {
		$this->assertInstanceOf('Class_WebService_SIGB_VSmart_Service',
														$this->service);
	}


	/** @test */
	public function serverRootShouldBeMoulinsDotFr() {
		$this->assertEquals('http://vpn.moulins.fr/VubisSmartHttpApi.csp',
												$this->service->getServerRoot());
	}
}


class VSmartServiceWithEmprunteurEvelyneTest extends VSmartServiceTestCase {
	public function setUp() {
		parent::setUp();
		$mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');
		$mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://86.64.58.38/formation/VubisSmartHttpApi.csp?fu=GetBorrower&MetaInstitution=RES&BorrowerId=04051972')
			->will($this->returnValue(VSmartFixtures::xmlBorrowerEvelyne()));

		$this->evelyne = Class_WebService_SIGB_VSmart_Service::newInstance()
			->setServerRoot('86.64.58.38/formation/')
			->setWebClient($mock_web_client)
			->getEmprunteur(Class_Users::getLoader()->newInstance()
																							->setLogin('04051972')
																							->setPassword(''));
	}


	/** @test */
	public function shouldReturnAnInstanceOfEmprunteur() {
		$this->assertInstanceOf('Class_WebService_SIGB_Emprunteur',
														$this->evelyne);
	}


	/** @test */
	public function getReservationsShouldReturnAnEmptyArray() {
		$this->assertEquals(array(), $this->evelyne->getReservations());
	}


	/** @test */
	public function getNomShouldReturnSERVIER() {
		$this->assertEquals('SERVIER', $this->evelyne->getNom());
	}


	/** @test */
	public function getPrenomShouldReturnEvelyne() {
		$this->assertEquals('Evelyne', $this->evelyne->getPrenom());
	}


	/** @test */
	public function getIdShouldReturn04051972() {
		$this->assertEquals('04051972', $this->evelyne->getId());
	}


	/** @test */
	public function getEmpruntsShouldReturnAnArrayWithSizeOfThree() {
		$this->assertEquals(3, count($this->evelyne->getEmprunts()));
	}


	/** @test */
	public function empruntAtZero() {
		$emprunt = $this->evelyne->getEmpruntAt(0);
		$this->assertNotEmpty($emprunt);
		return $emprunt;
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function empruntAtZeroShouldReturnANTHOLOGIE($emprunt) {
		$this->assertEquals('ANTHOLOGIE DE LA LITTERAT', $emprunt->getTitre());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function empruntAtZeroDateRetourTimeStampShouldBeTimeStampFor_05_02_2011($emprunt) {
		$this->assertEquals(mktime(0,0,0, 2, 5, 2011),
												$emprunt->getDateRetourTimestamp());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function bibliothequeShouldBeRES_MCFC($emprunt) {
		$this->assertEquals('Médiathèque communautaire', $emprunt->getBibliotheque());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function empruntAtZeroDateRetourShouldBe_05_02_2011($emprunt) {
		$this->assertEquals('05/02/2011',
												$emprunt->getDateRetour());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function firstEmpruntExemplaireIdShouldBe0078010148($emprunt) {
		$this->assertEquals('0078010148', $emprunt->getExemplaire()->getId());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function firstEmpruntIdShouldBe0078010148($emprunt) {
		$this->assertEquals('0078010148', $emprunt->getId());
	}


	/**
	 * @depends empruntAtZero
	 * @test
	 */
	public function firstEmpruntShouldBeEnRetard($emprunt) {
		$this->assertTrue($emprunt->enRetard());
	}


	/** @test */
	public function empruntAtOneShouldReturnNoticeTest3() {
		$emprunt = $this->evelyne->getEmpruntAt(1);
		$this->assertEquals('notice test 3 pour le por', $emprunt->getTitre());
		return $emprunt;
	}


	/** @test */
	public function empruntAtTwoShouldReturnNoticeTest2() {
		$emprunt = $this->evelyne->getEmpruntAt(2);
		$this->assertEquals('notice test 2 pour le por', $emprunt->getTitre());
		return $emprunt;
	}


	/**
	 * @depends empruntAtTwoShouldReturnNoticeTest2
	 * @test
	 */
	public function empruntAtTwoShouldHaveBibEqualsMTIL($emprunt) {
		$this->assertEquals('RES/MTIL', $emprunt->getBibliotheque());
	}


	/**
	 * @depends empruntAtTwoShouldReturnNoticeTest2
	 * @test
	 */
	public function empruntAtTwoShouldNotBeEnRetard($emprunt) {
		$this->assertFalse($emprunt->enRetard());
	}


	/**
	 * @depends empruntAtTwoShouldReturnNoticeTest2
	 * @test
	 */
	public function empruntAtTwoAuteurShouldBeEmptyString($emprunt) {
		$this->assertEquals('', $emprunt->getAuteur());
	}
}



class VSmartServiceWithEmprunteurFranckTest extends  VSmartServiceTestCase {
	public function setUp() {
		parent::setUp();
		$mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');
		$mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://46.20.169.8/production/VubisSmartHttpApi.csp?fu=GetBorrower&MetaInstitution=RES&BorrowerId=30101964')
			->will($this->returnValue(VSmartFixtures::xmlBorrowerFranck()));

		$this->franck = Class_WebService_SIGB_VSmart_Service::newInstance()
			->setServerRoot('http://46.20.169.8/production')
			->setWebClient($mock_web_client)
			->getEmprunteur(Class_Users::getLoader()->newInstance()
												->setLogin('30101964')
												->setPassword('mdpasse'));
	}


	/** @test */
	public function getIdShouldReturn30101964() {
		$this->assertEquals('30101964', $this->franck->getId());
	}


	/** @test */
	public function getPasswordShouldReturnMdpasse() {
		$this->assertEquals('mdpasse', $this->franck->getPassword());
	}


	/** @test */
	public function getEmpruntsShouldReturnAnEmptyArray() {
		$this->assertEquals(array(), $this->franck->getEmprunts());
	}


	/** @test */
	public function getReservationsShouldReturnAnArrayWithSizeOfTwo() {
		$this->assertEquals(2, count($this->franck->getReservations()));
	}


	/** @test */
	public function reservationAtOne() {
		$reservation = $this->franck->getReservationAt(1);
		$this->assertNotEmpty($reservation);
		return $reservation;
	}


	/**
	 * @depends reservationAtOne
	 * @test
	 */
	public function secondReservationIdShouldBe32272073523224($reservation) {
		$this->assertEquals('32272073523224', $reservation->getId());
	}


	/**
	 * @depends reservationAtOne
	 * @test
	 */
	public function secondReservationExemplaireIdShouldBe32272073523224($reservation) {
		$this->assertEquals('32272073523224', $reservation->getExemplaire()->getId());
	}


	/**
	 * @depends reservationAtOne
	 * @test
	 */
	public function secondReservationExemplaireCodeBarreShouldBe32272073523224($reservation) {
		$this->assertEquals('32272073523224', $reservation->getExemplaire()->getCodeBarre());
	}


	/**
	 * @depends reservationAtOne
	 * @test
	 */
	public function secondReservationTitleShouldBeEspagnolGrammaire($reservation) {
		$this->assertEquals('Espagnol, grammaire', $reservation->getTitre());
	}


	/**
	 * @depends reservationAtOne
	 * @test
	 */
	public function secondReservationRangShouldBeOne($reservation) {
		$this->assertEquals(1, $reservation->getRang());
	}


	/** @test */
	public function reservationAtZero() {
		$reservation = $this->franck->getReservationAt(0);
		$this->assertNotEmpty($reservation);
		return $reservation;
	}


	/**
	 * @depends reservationAtZero
	 * @test
	 */
	public function firstReservationRangShouldBeFour($reservation) {
		$this->assertEquals(4, $reservation->getRang());
	}


	/**
	 * @depends reservationAtZero
	 * @test
	 */
	public function firstReservationBibliothequeShouldBeBPVP_MELV($reservation) {
		$this->assertEquals('Avermes', $reservation->getBibliotheque());
	}


	/**
	 * @depends reservationAtZero
	 * @test
	 */
	public function firstReservationAuteurShouldBeEmptyString($emprunt) {
		$this->assertEquals('', $emprunt->getAuteur());
	}
}



class VSmartServiceBibGetAnthologieLitteratureTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');
		$mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://86.64.58.38/formation/VubisSmartHttpApi.csp?fu=BibSearch&Application=Bib&Database=1&RequestType=RecordNumber&Request=47918')
			->will($this->returnValue(VSmartFixtures::xmlNoticeAnthologie()));

		$this->anthologie = Class_WebService_SIGB_VSmart_Service::newInstance()
			->setServerRoot('http://86.64.58.38/formation')
			->setWebClient($mock_web_client)
			->getNotice('1/47918');
	}


	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->anthologie);
	}


	/** @test */
	public function noticeIdShouldBe1_47907() {
		$this->assertEquals('1/47918', $this->anthologie->getId());
	}


	/** @test */
	public function getExemplairesShouldReturnAnArrayWithSizeOne() {
		$this->assertEquals(1, count($this->anthologie->getExemplaires()));
	}


	/** @test */
	public function firstExemplaireShouldNotBeReservable() {
		$this->assertFalse($this->anthologie->exemplaireAt(0)->isReservable());
	}

}



class VSmartServiceBibGetHarryPotterTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');
		$mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://vpn.agglo-moulins.fr/formation/VubisSmartHttpApi.csp?fu=BibSearch&Application=Bib&Database=2&RequestType=RecordNumber&Request=3066')
			->will($this->returnValue(VSmartFixtures::xmlNoticeHarryPotter()));

		$this->service = Class_WebService_SIGB_VSmart_Service::newInstance()
			                                ->setServerRoot('vpn.agglo-moulins.fr/formation/')
			                                ->setWebClient($mock_web_client);

		$this->potter = $this->service->getNotice('2/3066');
	}


	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->potter);
	}


	/** @test */
	public function getExemplairePotterShouldReturnIt() {
		$exemplaire = $this->service->getExemplaire('2/3066', '1042770148');
		$this->assertEquals('1042770148', $exemplaire->getCodeBarre());
	}


	/** @test */
	public function noticeIdShouldBe2_3066() {
		$this->assertEquals('2/3066', $this->potter->getId());
	}


	/** @test */
	public function nbExemplairesShouldReturnFour() {
		$this->assertEquals(4, $this->potter->nbExemplaires());
	}


	/** @test */
	public function firstExemplaire() {
		$first_exemplaire = $this->potter->exemplaireAt(0);
		$this->assertInstanceOf('Class_WebService_SIGB_Exemplaire', $first_exemplaire);
		return $first_exemplaire;
	}


	/**
	 * @depends firstExemplaire
	 * @test
	 */
	public function firstExemplaireCodeBarreShouldBe1042770148($first_exemplaire) {
		$this->assertEquals('1042770148', $first_exemplaire->getCodeBarre());
	}


	/**
	 * @depends firstExemplaire
	 * @test
	 */
	public function firstExemplaireGetNoticeIdShouldBe2_3066($first_exemplaire) {
		$this->assertEquals('2/3066', $first_exemplaire->getNotice()->getId());
	}


	/**
	 * @depends firstExemplaire
	 * @test
	 */
	public function firstExemplaireShouldNotBeReservable($first_exemplaire) {
		$this->assertFalse($first_exemplaire->isReservable());
	}


	/**
	 * @depends firstExemplaire
	 * @test
	 */
	public function firstExemplaireDisponibiliteShouldBeDisponible($first_exemplaire) {
		$this->assertEquals('Disponible', $first_exemplaire->getDisponibilite());
	}


	/**
	 * @depends firstExemplaire
	 * @test
	 */
	public function firstExemplaireIdShouldBe1042770148($first_exemplaire) {
		$this->assertEquals('1042770148', $first_exemplaire->getId());
	}


	/** @test */
	public function secondExemplaire() {
		$second_exemplaire = $this->potter->exemplaireAt(1);
		$this->assertInstanceOf('Class_WebService_SIGB_Exemplaire', $second_exemplaire);
		return $second_exemplaire;
	}


	/**
	 * @depends secondExemplaire
	 * @test
	 */
	public function secondExemplaireShouldBeReservable($second_exemplaire) {
		$this->assertTrue($second_exemplaire->isReservable());
	}


	/**
	 * @depends secondExemplaire
	 * @test
	 */
	public function secondExemplaireDateRetourShouldBe10_03_2011($second_exemplaire) {
		$this->assertEquals('10/03/2011', $second_exemplaire->getDateRetour());
	}


	/**
	 * @depends secondExemplaire
	 * @test
	 */
	public function secondExemplaireDisponibiliteShouldBeEnPret($second_exemplaire) {
		$this->assertEquals('En prêt', $second_exemplaire->getDisponibilite());
	}


	/**
	 * @depends secondExemplaire
	 * @test
	 */
	public function secondExemplaireIdShouldBe1032650148($second_exemplaire) {
		$this->assertEquals('1032650148', $second_exemplaire->getId());
	}


	/** @test */
	public function thirdExemplaireDisponibiliteShouldBeInconnu() {
		$this->assertEquals('Inconnu', $this->potter->exemplaireAt(2)->getDisponibilite());
	}

}



class VSmartServiceFunctionsTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');

		$this->service = Class_WebService_SIGB_VSmart_Service::getService('86.64.58.38/formation/');
		$this->service->setWebClient($this->mock_web_client);
	}


	protected function _setupExpectationsFor($expected_params, $returnValue = null) {
		if (!$returnValue)
			$returnValue = '<VubisSmart>
												<Header>
													<Function>ReservationTitle</Function>
													<ErrorCode>0</ErrorCode>
												</Header>
											</VubisSmart>';
		$this->mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://86.64.58.38/formation/VubisSmartHttpApi.csp?'.http_build_query($expected_params))
			->will($this->returnValue($returnValue));
	}


	/** testDisabled */
	public function evelyneReserveHarryPotterShouldAnswerOK() {
		$this->_setupExpectationsFor(array('fu' => 'ReservationTitle',
																			 'BorrowerId' => '04051972',
																			 'Database' => 2,
																			 'ReserveArea' => 'RES',
																			 'BibRecord' => '3066',
																			 'PickupLocation' => 'INST/LOC',
																			 'MetaInstitution' => 'RES',
																			 'Language' => 'fre'));

		$this->assertEquals(array('statut' => true,
															'erreur' => ''),
												$this->service->reserverExemplaire('04051972', 
																													 
																													 '', 
																													 
																													 Class_Exemplaire::getLoader()
																													 ->newInstanceWithId(234)
																													 ->setIdOrigine('2/3066'), 
																													  
																													 'RES'));
	}



	/** testDisabled */
	public function franckReserveAnthologieShouldAnswerOK() {
		$this->_setupExpectationsFor(array('fu' => 'ReservationTitle',
																			 'BorrowerId' => '30101964',
																			 'Database' => 1,
																			 'ReserveArea' => 'MCFC',
																			 'BibRecord' => '47918',
																			 'PickupLocation' => 'INST/LOC',
																			 'MetaInstitution' => 'RES',
																			 'Language' => 'fre'));

		$this->assertEquals(array('statut' => true,
															'erreur' => ''),
												$this->service->reserverExemplaire('30101964', '', '1/47918', 'MCFC'));
	}


	/** testDisabled */
	public function franckReserveHarryPotterShouldAnswerError() {
		$this->_setupExpectationsFor(array('fu' => 'ReservationTitle',
																			 'BorrowerId' => '30101964',
																			 'Database' => 2,
																			 'ReserveArea' => 'RES',
																			 'BibRecord' => '3066',
																			 'PickupLocation' => 'INST/LOC',
																			 'MetaInstitution' => 'RES',
																			 'Language' => 'fre'),

																 '<VubisSmart>
																		<Header>
  																			<Function>ReservationTitle</Function>
																				<ErrorCode>2</ErrorCode>
																				<ErrorText>Document non-réservable</ErrorText>
																		</Header>
																	</VubisSmart>');

		$this->assertEquals(array('statut' => false,
															'erreur' => 'Document non-réservable'),
												$this->service->reserverExemplaire('30101964', '', '2/3066', 'RES'));
	}


	/** @test */
	public function evelyneSupprimerReservationHarryPotterShouldAnswerOK() {
		$this->_setupExpectationsFor(array('fu' => 'ReservationCancel',
																			 'BorrowerId' => '04051972',
																			 'ItemId' => '1032650148',
																			 'MetaInstitution' => 'RES',
																			 'Language' => 'fre'));

		$this->assertEquals(array('statut' => true,
															'erreur' => ''),
												$this->service->supprimerReservation(
																Class_Users::getLoader()->newInstance()
																	->setLogin('04051972')
																	->setPassword(''),
																'1032650148'));
	}


	/** @test */
	public function evelyneProlongerPretHarryPotterShouldAnswerOK() {
		$this->_setupExpectationsFor(array('fu' => 'Renewal',
																			 'BorrowerId' => '04051972',
																			 'ItemId' => '1032650148',
																			 'MetaInstitution' => 'RES',
																			 'Language' => 'fre'));

		$this->assertEquals(array('statut' => true,
															'erreur' => ''),
												$this->service->prolongerPret(
																Class_Users::getLoader()->newInstance()
																	->setLogin('04051972')
																	->setPassword(''),
																'1032650148'));
	}


	/** @test */
	public function getExemplaireFromNoticeShouldReturnInvalidExemplaire() {
		$this->_setupExpectationsFor(array('fu' => 'BibSearch',
																			 'Application' => 'Bib',
																			 'Database' => '2',
																			 'RequestType' => 'RecordNumber',
																			 'Request' => '234'));

		$this->assertFalse($this->service->getExemplaire('2/234', '987')->isValid());
	}

}


?>