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

include_once 'NanookFixtures.php';

class NanookGetServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_WebService_SIGB_Nanook::reset();
		$this->service = Class_WebService_SIGB_Nanook::getService(array('url_serveur' => 'http://localhost:8080/afi_Nanook/ilsdi/'));
	}


	/** @test */
	public function getServiceShouldCreateAnInstanceOfNanookService() {
		$this->assertInstanceOf('Class_WebService_SIGB_Nanook_Service',
														$this->service);
	}


	/** @test */
	public function serverRootShouldBeLocalNanookIlsdiService() {
		$this->assertEquals('http://localhost:8080/afi_Nanook/ilsdi/',
												$this->service->getServerRoot());
	}

	/** @test */
	public function getServiceWithoutSchemeShouldAddHttpScheme() {
		Class_WebService_SIGB_Nanook::reset();
		$this->service = Class_WebService_SIGB_Nanook::getService(array('url_serveur' => 'localhost:8080/afi_Nanook/ilsdi/'));
		$this->assertEquals('http://localhost:8080/afi_Nanook/ilsdi/',
												$this->service->getServerRoot());
	}


	/** @test */
	public function getServiceWithoutTrailingSlashShouldAddIt() {
		Class_WebService_SIGB_Nanook::reset();
		$this->service = Class_WebService_SIGB_Nanook::getService(array('url_serveur' => 'localhost:8080/afi_Nanook/ilsdi'));
		$this->assertEquals('http://localhost:8080/afi_Nanook/ilsdi/',
												$this->service->getServerRoot());
	}
}




abstract class NanookTestCase extends Storm_Test_ModelTestCase {
	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_mock_web_client;

	/** @var Class_WebService_SIGB_Nanook_Service */
	protected $_service;

	public function setUp() {
		parent::setUp();

		$this->_mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');

		$this->_service = Class_WebService_SIGB_Nanook_Service::newInstance()
			->setServerRoot('http://localhost:8080/afi_Nanook/ilsdi/')
			->setWebClient($this->_mock_web_client);

		$annexe_cran = Class_CodifAnnexe::getLoader()->newInstanceWithId(3)
								->setLibelle('Annexe Cran-Gevrier')
								->setIdBib(3)
			          ->setCode(10);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CodifAnnexe')
			->whenCalled('findFirstBy')->answers(null)
			->whenCalled('findFirstBy')->with(array('id_bib' => 3))->answers($annexe_cran)
			->whenCalled('findFirstBy')->with(array('code' => 10))->answers($annexe_cran);
	}
}




abstract class NanookServiceErrorTestCase extends NanookTestCase {
	/** @test */
	public function reserverExemplaireShouldReturnFailure() {
		$this->assertEquals(
				array('statut' => false, 'erreur' => 'Service indisponible'),
				$this->_service->reserverExemplaire(
								Class_Users::getLoader()->newInstance()->setIdSigb(1),
								Class_Exemplaire::getLoader()->newInstance()->setIdOrigine(''),
								''));
	}


	/** @test */
	public function supprimerReservationShouldReturnFailure() {
		$this->assertEquals(
				array('statut' => false, 'erreur' => 'Service indisponible'),
				$this->_service->supprimerReservation(
										Class_Users::getLoader()->newInstance()->setIdSigb(1), ''));
	}


	/** @test */
	public function prolongerPretShouldReturnFailure() {
		$this->assertEquals(
				array('statut' => false, 'erreur' => 'Service indisponible'),
				$this->_service->prolongerPret(
										Class_Users::getLoader()->newInstance()->setIdSigb(1), ''));
	}


	/** @test */
	public function getNoticeShouldReturnNull() {
		$this->assertNull($this->_service->getNotice('00133066'));
	}
}




class NanookNoConnectionTest extends NanookServiceErrorTestCase {
	public function setUp() {
		parent::setUp();

		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->will($this->throwException(
				new Zend_Http_Client_Adapter_Exception('Unable to connect')
			));
	}
}




class NanookHtmlResponseTest extends NanookServiceErrorTestCase {
	public function setUp() {
		parent::setUp();

		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->will($this->returnValue(NanookFixtures::htmlTomcatError()));
	}

	/** @test */
	public function getEmprunteurShouldReturnEmptyEmprunteur() {
		$emprunteur = $this->_service->getEmprunteur(Class_Users::getLoader()
																								 ->newInstance()
																								 ->setIdSigb(1));
		$this->assertNotNull($emprunteur);
		$this->assertEmpty($emprunteur->getReservations());
		$this->assertEmpty($emprunteur->getPretsEnRetard());
		$this->assertEmpty($emprunteur->getEmprunts());
	}
}




class NanookGetNoticeLiliGrisbiAndCoTest extends NanookTestCase {
	/** @var Class_WebService_SIGB_Notice */
	protected $_notice;


	public function setUp() {
		parent::setUp();
		//Pour avoir les textes de prets par defaut
		Class_Profil::getCurrentProfil()->setCfgNotice(array('exemplaires' => array()));

		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/GetRecords/id/9842')
			->will($this->returnValue(NanookFixtures::xmlGetRecordLiliGrisbiAndCo()));

		$this->_notice = $this->_service->getNotice('9842');

	}


	/** @test */
	public function shouldAnswerANotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->_notice);
	}


	/** @test */
	public function noticeIdShouldBe9842() {
		$this->assertEquals('9842', $this->_notice->getId());
	}


	/** @test */
	public function getExemplairesShouldReturnAnArrayWithSizeThree() {
		$this->assertEquals(3, count($this->_notice->getExemplaires()));
	}


	/** @test */
	public function firstExemplaireIdShouldBe10713() {
		$this->assertEquals('10713', $this->_notice->exemplaireAt(0)->getId());
	}


	/** @test */
	public function firstExemplaireCodeBarreShouldBeL007552() {
		$this->assertEquals('L-007552', $this->_notice->exemplaireAt(0)->getCodeBarre());
	}


	/** @test */
	public function firstExemplaireDisponibiliteShouldBeSeRenseigner() {
		$this->assertEquals('Se renseigner a l\'accueil',
												$this->_notice->exemplaireAt(0)->getDisponibilite());
	}


	/** @test */
	public function firstExemplaireDisponibiliteLabelShouldBeSeRenseigner() {
		$this->assertEquals('Se renseigner a l\'accueil',
												$this->_notice->exemplaireAt(0)->getDisponibiliteLabel());
	}


	/** @test */
	public function firstExemplaireShouldNotBeHoldable() {
		$this->assertFalse($this->_notice->exemplaireAt(0)->isReservable());
	}


	/** @test */
	public function secondExemplaireIdShouldBe10714() {
		$this->assertEquals('10714', $this->_notice->exemplaireAt(1)->getId());
	}


	/** @test */
	public function firstExemplaireBibliothequeShouldBeAnnecy() {
		$this->assertEquals('Annecy', $this->_notice->exemplaireAt(0)->getBibliotheque());
	}


	/** @test */
	public function firstExemplaireCodeAnnexeShouldBe3() {
		$this->assertEquals(3, $this->_notice->exemplaireAt(0)->getCodeAnnexe());
	}


	/** @test */
	public function secondExemplaireCodeBarreShouldBeL072666() {
		$this->assertEquals('L-072666', $this->_notice->exemplaireAt(1)->getCodeBarre());
	}


	/** @test */
	public function secondExemplaireShouldBeEnPret() {
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_EN_PRET,
														$this->_notice->exemplaireAt(1)->getDisponibilite());
	}


	/** @test */
	public function secondExemplaireShouldBeHoldable() {
		$this->assertTrue($this->_notice->exemplaireAt(1)->isReservable());
	}


	/** @test */
	public function secondExemplaireDateRetourShouldBe12012029() {
		$this->assertEquals('12/01/2029',
																$this->_notice->exemplaireAt(1)->getDateRetour());
	}


	/** @test */
	public function thirdExemplaireBibliothequeShouldBeCran() {
		$this->assertEquals('Annexe Cran-Gevrier', $this->_notice->exemplaireAt(2)->getBibliotheque());
	}

	/** @test */
	public function thirdExemplaireCodeAnnexeShouldBeThree() {
		$this->assertEquals(3, $this->_notice->exemplaireAt(2)->getCodeAnnexe());
	}
}




class NanookGetNoticeWithErrorTest extends NanookTestCase {
	/** @test */
	public function noticeShouldBeNull() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/GetRecords/id/666')
			->will($this->returnValue(NanookFixtures::xmlGetRecordError()));

		$this->assertNull($this->_service->getNotice('666'));
	}
}




class NanookGetEmprunteurChristelDelpeyrouxTest extends NanookTestCase {
	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;


	public function setUp() {
		parent::setUp();

		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/GetPatronInfo/patronId/1')
			->will($this->returnValue(NanookFixtures::xmlGetPatronChristelDelpeyroux()));

		$this->_emprunteur = $this->_service->getEmprunteur(
														Class_Users::getLoader()
															->newInstance()
															->setIdSigb(1)
													);
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->answers(null);
	}


	/** @test */
	public function shouldAnswerAnEmprunteur() {
		$this->assertInstanceOf('Class_WebService_SIGB_Emprunteur', $this->_emprunteur);
	}


	/** @test */
	public function idShouldBeOne() {
		$this->assertEquals('1', $this->_emprunteur->getId());
	}


	/** @test */
	public function lastNameShouldBeDelpeyroux() {
		$this->assertEquals('DELPEYROUX', $this->_emprunteur->getNom());
	}


	/** @test */
	public function firstNameShouldBeChristel() {
		$this->assertEquals('Christel', $this->_emprunteur->getPrenom());
	}


	/** @test */
	public function nbEmpruntsShouldBeThree() {
		$this->assertEquals(3, $this->_emprunteur->getNbEmprunts());
	}


	/** @test */
	public function firstEmpruntTitreShouldBeBeartEnPublic() {
		$this->assertEquals('Béart en public',
																$this->_emprunteur->getEmpruntAt(0)->getTitre());
	}


	/** @test */
	public function firstEmpruntAuthorShouldBeGuyBeart() {
		$this->assertEquals('Guy Béart',
																$this->_emprunteur->getEmpruntAt(0)->getAuteur());
	}


	/** @test */
	public function firstEmpruntDateRetourShouldBeMayFourth2011() {
		$this->assertEquals('04/05/2011',
														$this->_emprunteur->getEmpruntAt(0)->getDateRetour());
	}


	/** @test */
	public function firstEmpruntIdShouldBe196895() {
		$this->assertEquals('196895',
																$this->_emprunteur->getEmpruntAt(0)->getId());
	}


	/** @test */
	public function firstEmpruntNoticeNumberShouldBe117661() {
		$this->assertEquals('117661',
						$this->_emprunteur->getEmpruntAt(0)->getExemplaire()->getNoNotice());
	}


	/** @test */
	public function firstEmpruntShouldBeEnRetard() {
		$this->assertTrue($this->_emprunteur->getEmpruntAt(0)->enRetard());
	}


	/** @test */
	public function firstEmpruntBibliothequeShouldBeSitePrincipal() {
		$this->assertEquals('Site Principal', $this->_emprunteur->getEmpruntAt(0)->getBibliotheque());
	}


	/** @test */
	public function secondEmpruntTitreShouldBeLesFinancesPubliquesEtc() {
		$this->assertEquals('Les Finances publiques et la réforme budgétaire',
																$this->_emprunteur->getEmpruntAt(1)->getTitre());
	}


	/** @test */
	public function secondEmpruntAuthorShouldBeEmpty() {
		$this->assertEquals('',
												$this->_emprunteur->getEmpruntAt(1)->getAuteur());
	}


	/** @test */
	public function firstEmpruntBibliothequeShouldBeSiteSecondaire() {
		$this->assertEquals('Site Secondaire', $this->_emprunteur->getEmpruntAt(1)->getBibliotheque());
	}


	/** @test */
	public function secondEmpruntDateRetourShouldBeMayFourth2029() {
		$this->assertEquals('04/05/2029',
														$this->_emprunteur->getEmpruntAt(1)->getDateRetour());
	}


	/** @test */
	public function secondEmpruntIdShouldBe107177() {
		$this->assertEquals('107177',
																$this->_emprunteur->getEmpruntAt(1)->getId());
	}


	/** @test */
	public function secondEmpruntNoticeNumberShouldBe83413() {
		$this->assertEquals('83413',
						$this->_emprunteur->getEmpruntAt(1)->getExemplaire()->getNoNotice());
	}


	/** @test */
	public function secondEmpruntShouldNotBeEnRetard() {
		$this->assertFalse($this->_emprunteur->getEmpruntAt(1)->enRetard());
	}


	/** @test */
	public function nbReservationShouldBeThree() {
		$this->assertEquals(3, $this->_emprunteur->getNbReservations());
	}


	/** @test */
	public function firstReservationIdShouldBe7105() {
		$this->assertEquals('7105', $this->_emprunteur->getReservationAt(0)->getId());
	}


	/** @test */
	public function firstReservationTitreShouldBeContesDesQuatreVents() {
		$this->assertEquals('Contes des quatre vents',
														$this->_emprunteur->getReservationAt(0)->getTitre());
	}


	/** @test */
	public function firstReservationAuthorShouldBeNathaCaputo() {
		$this->assertEquals('Natha Caputo',
														$this->_emprunteur->getReservationAt(0)->getAuteur());
	}


	/** @test */
	public function firstReservationNoticeNumberShouldBe7307() {
		$this->assertEquals('7307',
					$this->_emprunteur->getReservationAt(0)->getExemplaire()->getNoNotice());
	}


	/** @test */
	public function firstReservationRangShouldBeOne() {
		$this->assertEquals('1', $this->_emprunteur->getReservationAt(0)->getRang());
	}


	/** @test */
	public function firstReservationEtatShouldBePasDisponibleAvantLe15Juin2012() {
		$this->assertEquals('Pas disponible avant le 15/06/2012', 
												$this->_emprunteur->getReservationAt(0)->getEtat());
	}

	/** @test */
	public function secondReservationIdShouldBe14586() {
		$this->assertEquals('14586', $this->_emprunteur->getReservationAt(1)->getId());
	}


	/** @test */
	public function secondReservationTitreShouldBeLeChantDuLac() {
		$this->assertEquals('Le Chant du lac',
														$this->_emprunteur->getReservationAt(1)->getTitre());
	}


	/** @test */
	public function secondReservationAuthorShouldBeOlympeBhelyQuenum() {
		$this->assertEquals('Olympe Bhêly-Quénum',
														$this->_emprunteur->getReservationAt(1)->getAuteur());
	}


	/** @test */
	public function secondReservationNoticeNumberShouldBe12501() {
		$this->assertEquals('12501',
					$this->_emprunteur->getReservationAt(1)->getExemplaire()->getNoNotice());
	}


	/** @test */
	public function secondReservationRangShouldBeFourtyNine() {
		$this->assertEquals('49', $this->_emprunteur->getReservationAt(1)->getRang());
	}


	/** @test */
	public function secondReservationEtatShouldBeDisponible() {
		$this->assertEquals('Disponible', $this->_emprunteur->getReservationAt(1)->getEtat());
	}
}




class NanookGetEmprunteurWithErrorTest extends NanookTestCase {
	/** @test */
	public function emprunteurShouldBeEmpty() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/GetPatronInfo/patronId/666')
			->will($this->returnValue(NanookFixtures::xmlGetPatronError()));

		$emprunteur = $this->_service->getEmprunteur(
																								 Class_Users::getLoader()
																								 ->newInstance()
																								 ->setIdSigb('666')
																								 );
		$this->assertNotNull($emprunteur);
		$this->assertEmpty($emprunteur->getReservations());
		$this->assertEmpty($emprunteur->getPretsEnRetard());
		$this->assertEmpty($emprunteur->getEmprunts());
	}
}




class NanookOperationsTest extends NanookTestCase {
	/** @test */
	public function prolongerPretShouldReturnSuccessIfNoErrors() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/RenewLoan/patronId/1/itemId/196895')
			->will($this->returnValue(NanookFixtures::xmlRenewLoanSucces()));

		$this->assertEquals(
			array('statut' => true, 'erreur' => ''),
			$this->_service->prolongerPret(
				Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
				'196895'
			)
		);
	}


	/** @test */
	public function prolongerPretShouldReturnFailureIfErrors() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/RenewLoan/patronId/1/itemId/196895')
			->will($this->returnValue(NanookFixtures::xmlRenewLoanError()));

		$this->assertEquals(
			array('statut' => false, 'erreur' => 'Prolongation impossible'),
			$this->_service->prolongerPret(
				Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
				'196895'
			)
		);
	}


	/** @test */
	public function reserverExemplaireOnExistingAnnexeWithNoErrorsShouldReturnSuccess() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/HoldTitle/bibId/196895/patronId/1/pickupLocation/10')
			->will($this->returnValue(NanookFixtures::xmlHoldTitleSuccess()));

		$this->assertEquals(array('statut' => true, 'erreur' => ''),
												$this->_service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
													Class_Exemplaire::getLoader()->newInstance()->setIdOrigine('196895'),
													'3'
												));
	}


	/** @test */
	public function reserverExemplaireShouldReturnFailureIfErrors() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/HoldTitle/bibId/196895/patronId/1/pickupLocation/Site+Principal')
			->will($this->returnValue(NanookFixtures::xmlHoldTitleError()));

		$this->assertEquals(array('statut' => false, 'erreur' => 'Réservation impossible'),
												$this->_service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
													Class_Exemplaire::getLoader()->newInstance()->setIdOrigine('196895'),
													'Site Principal'
												));
	}


	/** @test */
	public function supprimerReservationShouldReturnSuccessIfNoErrors() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/CancelHold/patronId/1/itemId/196895')
			->will($this->returnValue(NanookFixtures::xmlCancelHoldSuccess()));

		$this->assertEquals(array('statut' => true, 'erreur' => ''),
												$this->_service->supprimerReservation(
													Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
													'196895'
												));
	}


	/** @test */
	public function supprimerReservationShouldReturnFailureIfErrors() {
		$this->_mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://localhost:8080/afi_Nanook/ilsdi/service/CancelHold/patronId/1/itemId/196895')
			->will($this->returnValue(NanookFixtures::xmlCancelHoldError()));

		$this->assertEquals(array('statut' => false, 'erreur' => 'Annulation impossible'),
												$this->_service->supprimerReservation(
													Class_Users::getLoader()->newInstance()	->setIdSigb('1'),
													'196895'
												));
	}
}