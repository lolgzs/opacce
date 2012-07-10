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

require_once('BiblixNetFixtures.php');

class BiblixNetGetServiceTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		Class_WebService_SIGB_BiblixNet::reset();
		$this->service = Class_WebService_SIGB_BiblixNet::getService(array('url_serveur' => 'http://mediathequewormhout.biblixnet.com/exporte_afi/'));
	}


	/** @test */
	public function getServiceShouldCreateAnInstanceOfBiblixNetService() {
		$this->assertInstanceOf('Class_WebService_SIGB_BiblixNet_Service',
														$this->service);
	}


	/** @test */
	public function serverRootShouldBeLocalBiblixNetIlsdiService() {
		$this->assertEquals('http://mediathequewormhout.biblixnet.com/exporte_afi/',
												$this->service->getServerRoot());
	}
}




abstract class BiblixNetTestCase extends Storm_Test_ModelTestCase {
	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_mock_web_client;

	/** @var Class_WebService_SIGB_BiblixNet_Service */
	protected $_service;

	public function setUp() {
		parent::setUp();

		$this->_mock_web_client = Storm_Test_ObjectWrapper::mock();

		$this->_service = Class_WebService_SIGB_BiblixNet
			::getService(array('url_serveur' => 'http://mediathequewormhout.biblixnet.com/exporte_afi/'))
			->setWebClient($this->_mock_web_client);
	}
}




class BiblixNetGetRecordsLaCenseAuxAlouettesTest extends BiblixNetTestCase {
	public function setUp() {
		parent::setUp();
		
		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://mediathequewormhout.biblixnet.com/exporte_afi/?service=GetRecords&id=3')
			->answers(BiblixNetFixtures::xmlGetRecordsCenseAlouettes())
			->beStrict();
		$this->_notice = $this->_service->getNotice('3');
	}


	/** @test */
	public function getNoticeShouldAnswerAnInstanceOfSIGBNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->_notice);
	}


	/** @test */
	public function noticeIdShouldBe3() {
		$this->assertEquals(3, $this->_notice->getId());
	}


	/** @test */
	public function getExemplairesShouldReturnAnArrayWithSizeTwo() {
		$this->assertEquals(2, count($this->_notice->getExemplaires()));
	}


	/** @test */
	public function firstExemplaireCodeBarreShouldBe1000025966311() {
		$this->assertEquals('1000025966311', $this->_notice->getExemplaires()[0]->getCodeBarre());
	}


	/** @test */
	public function firstExemplaireIdShouldBe1000025966311() {
		$this->assertEquals('1000025966311', $this->_notice->getExemplaires()[0]->getId());
	}


	/** @test */
	public function secondExemplaireCodeBarreShouldBe1000025966311() {
		$this->assertEquals('1000025966323', $this->_notice->getExemplaires()[1]->getCodeBarre());
	}


	/** @test */
	public function firstExemplaireShouldBeDisponible() {
		$this->assertEquals('Disponible', $this->_notice->getExemplaires()[0]->getDisponibilite());
	}


	/** @test */
	public function secondExemplaireShouldBeEnReliure() {
		$this->assertEquals('En reliure', $this->_notice->getExemplaires()[1]->getDisponibilite());
	}


	/** @test */
	public function firstExemplaireShouldBeReservable() {
		$this->assertTrue($this->_notice->getExemplaires()[0]->isReservable());
	}


	/** @test */
	public function secondExemplaireShouldBeReservable() {
		$this->assertTrue($this->_notice->getExemplaires()[1]->isReservable());
	}
}





class BiblixNetGetPatronInfoJustinTicou extends BiblixNetTestCase {
	public function setUp() {
		parent::setUp();
		
		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://mediathequewormhout.biblixnet.com/exporte_afi/?service=GetPatronInfo&patronId=34&show_loans=true&show_holds=true')
			->answers(BiblixNetFixtures::xmlGetPatronJustinTicou())
			->beStrict();

		$this->_emprunteur = $this->_service->getEmprunteur(Class_Users::getLoader()
																												->newInstance()
																												->setIdSigb(34));
	}


	/** @test */
	public function shouldAnswerAnEmprunteur() {
		$this->assertInstanceOf('Class_WebService_SIGB_Emprunteur', $this->_emprunteur);
	}


	/** @test */
	public function emprunteurIdShouldBeThirtyFour() {
		$this->assertEquals(34, $this->_emprunteur->getId());
	}


	/** @test */
	public function emprunterPrenomShouldBeJustin() {
		$this->assertEquals('Justin', $this->_emprunteur->getPrenom());
	}

	/** @test */
	public function emprunteurNomShouldBeTicou() {
		$this->assertEquals('Justin', $this->_emprunteur->getPrenom());
	}


	/** @test */
	public function firstLoanIdShouldBe196895() {
		$this->assertEquals(196895, $this->_emprunteur->getEmprunts()[0]->getId());
	}


	/** @test */
	public function firstLoanNoNoticeShouldBe117661() {
		$this->assertEquals(117661, $this->_emprunteur->getEmprunts()[0]->getNoNotice());
	}


	/** @test */
	public function firstLoanDateRetourShouldBe04_05_2011() {
		$this->assertEquals('04/05/2011', $this->_emprunteur->getEmprunts()[0]->getDateRetour());
	}


	/** @test */
	public function secondLoanIdShouldBe107177() {
		$this->assertEquals(107177, $this->_emprunteur->getEmprunts()[1]->getId());
	}


	/** @test */
	public function firstHoldNoNoticeShouldBe7307() {
		$this->assertEquals(7307, $this->_emprunteur->getReservations()[0]->getNoNotice());
	}


	/** @test */
	public function firstHoldIdShouldBe7105() {
		$this->assertEquals(7105, $this->_emprunteur->getReservations()[0]->getId());
	}


	/** @test */
	public function firstHoldEtatShouldBeEnAttente() {
		$this->assertEquals('En attente', $this->_emprunteur->getReservations()[0]->getEtat());
	}


	/** @test */
	public function secondHoldEtatShouldBeDisponible() {
		$this->assertEquals('Disponible', $this->_emprunteur->getReservations()[1]->getEtat());
	}
}




class BiblixNetOperationsTest extends BiblixNetTestCase {
	/** @test */
	public function reserverExemplairesWithoutErrorShouldReturnSuccess() {
		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://mediathequewormhout.biblixnet.com/exporte_afi/?service=HoldTitle&patronId=34&bibId=1432&pickupLocation=Mediatheque')
			->answers(BiblixNetFixtures::xmlGetPatronJustinTicou())
			->beStrict();


		$this->assertEquals(array('statut' => true, 'erreur' => ''),
												$this->_service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()	->setIdSigb('34'),
													Class_Exemplaire::getLoader()->newInstance()->setIdOrigine('1432'),
													'Mediatheque'
												));
	}
	
}


?>