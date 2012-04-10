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

abstract class PergameServiceTestCase extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->notice_potter = Class_Notice::getLoader()
			->newInstanceWithId(1)
			->setTitrePrincipal('Harry Potter')
			->setAuteurPrincipal('JK Rowling');
	
		$this->potter_annecy = Class_Exemplaire::getLoader()
			->newInstanceWithId(23)
			->setIdNotice(1)
			->setCodeBarres('A-23')
			->setIdOrigine('1HP')
			->setActivite('En rayon - Discotheque')
			->setIdBib(1);

		$this->potter_cran_prete = Class_Exemplaire::getLoader()
			->newInstanceWithId(24)
			->setIdNotice(1)
			->setCodeBarres('C-24')
			->setIdOrigine('1HP')
			->setActivite('En rayon')
			->setIdBib(2);

		$this->potter_cran_reserve = Class_Exemplaire::getLoader()
			->newInstanceWithId(25)
			->setIdNotice(1)
			->setCodeBarres('C-25')
			->setIdOrigine('1HP')
			->setActivite('En rayon')
			->setIdBib(2);

		$this->potter_cran_dispo = Class_Exemplaire::getLoader()
			->newInstanceWithId(26)
			->setIdNotice(1)
			->setCodeBarres('C-26')
			->setIdOrigine('1HP')
			->setActivite('En rayon')
			->setIdBib(2);


		Class_IntBib::getLoader()
			->newInstanceWithId(1)
			->setCommParams(serialize(array("Autoriser_docs_disponibles" => 1)));

		Class_IntBib::getLoader()
			->newInstanceWithId(2)
			->setCommParams(serialize(array("Autoriser_docs_disponibles" => 0)));

		Class_Bib::getLoader()
			->newInstanceWithId(2)
			->setLibelle('Cran-Gevrier');


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->with(array('id_bib' => 2, 'code_barres' => 'C-24'))
			->answers($this->potter_cran_prete)

			->whenCalled('findFirstBy')
			->with(array('id_origine' => '1HP'))
			->answers($this->potter_annecy);


		$this->_service_cran = Class_WebService_SIGB_Pergame_Service::getService(2);
	}
}


class PergameServiceGetEmprunteurTest extends PergameServiceTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Pret')
			->whenCalled('findAllBy')
			->with(array('IDABON' => 23, 'ORDREABON' => 2, 'EN_COURS' => 1))
			->answers(array(Class_Pret::getLoader()
											->newInstanceWithId(59)
											->setCodeBarres('C-24')
											->setIdNoticeOrigine(1)
											->setIdPergame('1HP')
											->setEnCours(1)
											->setDateRetour('2010-09-07')
											->setIdSite(2)
											->setIdabon(23)
											->setOrdreabon(2)))

			->whenCalled('countBy')
			->with(array('ID_NOTICE_ORIGINE' => 1, 
									 'EN_COURS' => 1))
			->answers(2);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Reservation')
			->whenCalled('findAllBy')
			->with(array('IDABON' => 23, 'ORDREABON' => 2))
			->answers(array(Class_Reservation::getLoader()
											->newInstanceWithId(76)
											->setIdNoticeOrigine('1HP')
											->setIdPergame('1HP')
											->setDateResa('2011-12-25')
											->setIdSite(2)
											->setIdabon(23)
											->setOrdreabon(2)))
			->whenCalled('countBy')
			->with(array('ID_NOTICE_ORIGINE' => 1, 
									 'where' => sprintf('DATE_RESA<"%s"', '2011-12-25')))
			->answers(2);

		$jc = Class_Users::getLoader()->newInstanceWithId(23)
			->setLogin('jc')
			->setIdabon(23)
			->setOrdreabon(2);
		$this->emprunteur_jc = $this->_service_cran->getEmprunteur($jc);
		$this->_first_emprunt = array_first($this->emprunteur_jc->getEmprunts());
		$this->_first_reservation = array_first($this->emprunteur_jc->getReservations());
	}


	/** @test */
	public function emprunteurIdShouldBe23() {
		$this->assertEquals(23, $this->emprunteur_jc->getId());
	}


	/** @test */
	public function firstEmpruntCodeBarreShouldBeC24() {
		$this->assertEquals('C-24', $this->_first_emprunt->getCodeBarre());
	}


	/** @test */
	public function firstEmpruntBibliothequeShouldBeCranGevrier() {
		$this->assertEquals('Cran-Gevrier', $this->_first_emprunt->getBibliotheque());
	}


	/** @test */
	public function firstEmpruntExemplaireOpacShouldBePotterCranPrete() {
		$this->assertEquals($this->potter_cran_prete, $this->_first_emprunt->getExemplaireOpac());
	}


	/** @test */
	public function firstEmpruntTitreShouldBeHarryPotter() {
		$this->assertEquals('Harry Potter', $this->_first_emprunt->getTitre());
	}


	/** @test */
	public function firstEmpruntAuteurShouldBeJKRowling() {
		$this->assertEquals('JK Rowling', $this->_first_emprunt->getAuteur());
	}


	/** @test */
	public function firstEmpruntDateRetourShouldBe2010_09_07() {
		$this->assertEquals('07/09/2010', $this->_first_emprunt->getDateRetour());
	}


	/** @test */
	public function firstEmpruntShouldNotBeRenewable() {
		$this->assertFalse($this->_first_emprunt->isRenewable());
	}


	/** @test */
	public function firstReservationIdShouldBe76() {
		$this->assertEquals(76, $this->_first_reservation->getId());
	}	


	/** @test */
	public function firstReservationTitreShouldBeHarryPotter() {
		$this->assertEquals('Harry Potter', $this->_first_reservation->getTitre());
	}


	/** @test */
	public function firstReservationAuteurShouldBeJKRowling() {
		$this->assertEquals('JK Rowling', $this->_first_reservation->getAuteur());
	}


	/** @test */
	public function firstReservationRangShouldBeThree() {
		$this->assertEquals(3, $this->_first_reservation->getRang());
	}


	/** @test */
	public function firstReservationEtatShouldBeEnPret() {
		$this->assertEquals('En prêt', $this->_first_reservation->getEtat());
	}
}


class PergameServiceGetExemplairePotterTest extends PergameServiceTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findAllBy')
			->with(array('role' => 'notice', 
									 'model' => $this->notice_potter))
			->answers(array($this->potter_annecy, 
											$this->potter_cran_prete, 
											$this->potter_cran_reserve, $this->potter_cran_dispo))

			->whenCalled('findFirstBy')
			->with(array('id_origine' => '1HP', 
									 'id_bib' => 1))
			->answers($this->potter_annecy)

			->whenCalled('findFirstBy')
			->with(array('id_origine' => '1HP', 
									 'id_bib' => 2))
			->answers($this->potter_cran_prete);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Pret')
			->whenCalled('findFirstBy')
			->answers(null)

			->whenCalled('findFirstBy')
			->with(array('id_site' => 2, 
									 'id_notice_origine' => '1HP', 
									 'code_barres' => 'C-24', 
									 'EN_COURS' => 1))
			->answers(Class_Pret::getLoader()
								->newInstanceWithId(59)
								->setCodeBarres('C-24')
								->setIdNoticeOrigine(1)
								->setIdPergame('1HP')
								->setEnCours(1)
								->setDateRetour('2010-09-07')
								->setIdSite(2));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Reservation')
			->whenCalled('findAllBy')
			->answers(array())

			->whenCalled('findAllBy')
			->with(array('id_site' => 2, 'id_notice_origine' => '1HP'))
			->answers(array(Class_Reservation::getLoader()
											->newInstanceWithId(34)
											->setIdSite(2)
											->setIdNoticeOrigine(1)));

		$this->_exemplaire_annecy = Class_WebService_SIGB_Pergame::getService(array('id_bib' => 1))->getExemplaire('1HP', 'A-23');
		$this->_exemplaire_cran_prete = Class_WebService_SIGB_Pergame::getService(array('id_bib' => 2))->getExemplaire('1HP', 'C-24');
		$this->_exemplaire_cran_reserve = $this->_service_cran->getExemplaire('1HP', 'C-25');
		$this->_exemplaire_cran_dispo = $this->_service_cran->getExemplaire('1HP', 'C-26');
	}


	/** @test */
	public function exemplaireSIGBAnnecyNoNoticeShouldBe1HP() {
		$this->assertEquals('1HP', $this->_exemplaire_annecy->getNoNotice());
	}


	/** @test */
	public function exemplaireSIGBAnnecyShouldBeValid() {
		$this->assertTrue($this->_exemplaire_annecy->isValid());
	}


	/** @test */
	public function exemplaireSIGBEAnnecyShouldBeEnRayonDisco() {
		$this->assertEquals('En rayon - Discotheque', $this->_exemplaire_annecy->getDisponibilite());
	}


	/** @test */
	public function exemplaireSIGBAnnecyShouldBeReservable() {
		$this->assertTrue($this->_exemplaire_annecy->isReservable());
	}


	/** @test */
	public function exemplaireSIGBCranPreteNoNoticeShouldBe1HP() {
		$this->assertEquals('1HP', $this->_exemplaire_cran_prete->getNoNotice());
	}


	/** @test */
	public function exemplaireSIGBCranPreteDateRetourShouldBe2010_09_07() {
		$this->assertEquals('2010-09-07', $this->_exemplaire_cran_prete->getDateRetour());
	}

	/** @test */
	public function exemplaireSIGBECranPreteShouldBeEnPret() {
		$this->assertEquals('En prêt', $this->_exemplaire_cran_prete->getDisponibilite());
	}


	/** @test */
	public function exemplaireSIGBCranPreteShouldBeReservable() {
		$this->assertTrue($this->_exemplaire_cran_prete->isReservable());
	}


	/** @test */
	public function exemplaireSigbCranReserveDispoShouldBeReserve() {
		$this->assertEquals('Réservé', $this->_exemplaire_cran_reserve->getDisponibilite());
	}


	/** @test */
	public function exemplaireSIGBCranReserveShouldBeReservable() {
		$this->assertTrue($this->_exemplaire_cran_reserve->isReservable());
	}


	/** @test */
	public function exemplaireSIGBCranDispoShouldBeEnRayon() {
		$this->assertEquals('En rayon', $this->_exemplaire_cran_dispo->getDisponibilite());
	}


	/** @test */
	public function exemplaireSIGBCranDispoShouldNotBeReservable() {
		$this->assertFalse($this->_exemplaire_cran_dispo->isReservable());
	}


	/** @test */
	public function getUnknowmExemplaireShouldNotBeValid() {
		$exemplaire = $this->_service_cran->getExemplaire('9999999', 'XX');
		$this->assertFalse($exemplaire->isValid());
	}


	/** @test */
	public function getUnknowmExemplaireOfExistingNoticeShouldNotBeValid() {
		$exemplaire = $this->_service_cran->getExemplaire('1HP', 'XX');
		$this->assertFalse($exemplaire->isValid());
	}
}

?>