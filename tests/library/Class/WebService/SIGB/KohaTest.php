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
include_once('KohaFixtures.php');

class KohaGetServiceTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		Class_WebService_SIGB_Koha::reset();
		$this->service = Class_WebService_SIGB_Koha::getService(array('url_serveur' => 'http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl'));
	}


	/** @test */
	public function getServiceShouldCreateAnInstanceOfKohaService() {
		$this->assertInstanceOf('Class_WebService_SIGB_Koha_Service',
														$this->service);
	}


	/** @test */
	public function serverRootShouldBeCatAFICG55IlsdiPl() {
		$this->assertEquals('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl',
												$this->service->getServerRoot());
	}

	/** @test */
	public function getServiceWithoutSchemeShouldAddHttpScheme() {
		Class_WebService_SIGB_Koha::reset();
		$this->service = Class_WebService_SIGB_Koha::getService(array('url_serveur' => 'cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl'));
		$this->assertEquals('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl',
												$this->service->getServerRoot());
	}

}


abstract class KohaTestCase extends PHPUnit_Framework_TestCase {
	protected $mock_web_client;
	protected $service;

	public function setUp() {
		//Pour avoir les textes de prets par defaut
		Class_Profil::getCurrentProfil()->setCfgNotice(array('exemplaires' => array()));

		$this->mock_web_client = $this->getMock('Class_WebService_SimpleWebClient');

		$this->service = Class_WebService_SIGB_Koha_Service::newInstance()
			->setServerRoot('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl')
			->setWebClient($this->mock_web_client);
	}
}


class KohaServiceGetNoticeJardinEnfantTest extends KohaTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=GetRecords&id=1')
			->will($this->returnValue(KohaFixtures::xmlGetRecordOneJardinEnfance()));

		$this->jardins_enfant = $this->service->getNotice('1');
	}

	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->jardins_enfant);
	}


	/** @test */
	public function noticeIdShouldBe1() {
		$this->assertEquals('1', $this->jardins_enfant->getId());
	}


	/** @test */
	function getExemplairesShouldReturnAnArrayWithSizeOne() {
		$this->assertEquals(1, count($this->jardins_enfant->getExemplaires()));
	}


	/** @test */
	public function firstExemplaireShouldBeReservable() {
		$this->assertTrue($this->jardins_enfant->exemplaireAt(0)->isReservable());
	}


	/** @test */
	function firstExemplaireDisponibiliteShouldBeEmpruntable() {
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_EN_PRET, $this->jardins_enfant->exemplaireAt(0)->getDisponibilite());
	}
}



class KohaServiceGetNoticeHarryPotterTest extends KohaTestCase {
	public function setUp() {
		parent::setUp();
		$this->mock_web_client
			->expects($this->once())
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=GetRecords&id=33233')
			->will($this->returnValue(KohaFixtures::xmlGetRecordHarryPotter()));

		$this->potter = $this->service->getNotice('33233');
	}

	/** @test */
	public function shouldAnswerOneNotice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->potter);
	}


	/** @test */
	public function noticeIdShouldBe33233() {
		$this->assertEquals('33233', $this->potter->getId());
	}


	/** @test */
	public function getExemplairesShouldReturnAnArrayWithSizeSeven() {
		$this->assertEquals(8, count($this->potter->getExemplaires()));
	}

	/** @test */
	public function firstExemplaireCodeBarreShouldBe2661690090() {
		$this->assertEquals('2661690090', $this->potter->exemplaireAt(0)->getCodeBarre());
	}

	/** @test */
	public function firstExemplaireDateRetourShouldBe30_05_2011() {
		$this->assertEquals('30/05/2011', $this->potter->exemplaireAt(0)->getDateRetour());
	}

	/** @test */
	public function firstExemplaireShouldBeReservable()	{
		$this->assertTrue($this->potter->exemplaireAt(0)->isReservable());
	}

	/** @test */
	public function firstExemplaireDisponibiliteShouldBeEnPret()	{
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_EN_PRET, $this->potter->exemplaireAt(0)->getDisponibilite());
	}

	/** @test */
	public function firstExemplaireShouldBeValid()	{
		$this->assertTrue($this->potter->exemplaireAt(0)->isValid());
	}

	/** @test */
	public function secondExemplaireCodeBarreShouldBe2661680090() {
		$this->assertEquals('2661680090', $this->potter->exemplaireAt(1)->getCodeBarre());
	}

	/** @test */
	public function secondExemplaireDateRetourShouldBe15_08_2011() {
		$this->assertEquals('15/08/2011', $this->potter->exemplaireAt(1)->getDateRetour());
	}

	/** @test */
	public function secondExemplaireShouldBeReservable()	{
		$this->assertTrue($this->potter->exemplaireAt(1)->isReservable());

	}

	/** @test */
	public function secondExemplaireDisponibiliteShouldBeEnPret()	{
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_EN_PRET, $this->potter->exemplaireAt(1)->getDisponibilite());
	}

	/** @test */
	public function thirdExemplaireShouldBeReservable()	{
		$this->assertTrue($this->potter->exemplaireAt(2)->isReservable());
	}

	/** @test */
	public function thirdExemplaireDisponibiliteShouldBeEmpruntable()	{
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_LIBRE, $this->potter->exemplaireAt(2)->getDisponibilite());
	}

	/** @test */
	public function fourthExemplaireShouldNotBeReservable()	{
		$this->assertFalse($this->potter->exemplaireAt(3)->isReservable());
	}

	/** @test */
	public function fourthExemplaireDisponibiliteShouldBePilonne()	{
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_PILONNE, $this->potter->exemplaireAt(3)->getDisponibilite());
	}

	/** @test */
	public function fifthExemplaireShouldNotBeReservable()	{
		$this->assertFalse($this->potter->exemplaireAt(4)->isReservable());
	}

	/** @test */
	public function fifthExemplaireDisponibiliteShouldBePerdu()	{
		$this->assertEquals(Class_WebService_SIGB_Exemplaire::DISPO_PERDU, $this->potter->exemplaireAt(4)->getDisponibilite());
	}

	/** @test */
	public function sixthExemplaireShouldNotBeReservable() {
		$this->assertFalse($this->potter->exemplaireAt(5)->isReservable());
	}

	/** @test */
	public function sixthExemplaireDisponibiliteShouldBeEnReparation()	{
		$this->assertEquals("En réparation", $this->potter->exemplaireAt(5)->getDisponibilite());
	}

	/** @test */
	public function seventhExemplaireDisponibiliteShouldBeEnTraitement()	{
		$this->assertEquals("En traitement", $this->potter->exemplaireAt(6)->getDisponibilite());
	}

	/** @test */
	public function seventhExemplaireShouldNotBeReservable() {
		$this->assertFalse($this->potter->exemplaireAt(6)->isReservable());
	}


	/** @test */
	public function eigthExemplaireDisponibiliteShouldBeEnReserve()	{
		$this->assertEquals('En réserve', $this->potter->exemplaireAt(7)->getDisponibilite());
	}


	/** @test */
	public function eigthExemplaireShouldBeReservable() {
		$this->assertTrue($this->potter->exemplaireAt(7)->isReservable());
	}

}



class KohaGetEmprunteurLaurentLaffontTest extends KohaTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->expects($this->at(0))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=LookupPatron&id=llaffont&id_type=cardnumber')
			->will($this->returnValue(KohaFixtures::xmlLookupPatronLaurent()));


		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=GetPatronInfo&patron_id=572&show_contact=0&show_loans=1&show_holds=1')
			->will($this->returnValue(KohaFixtures::xmlGetPatronInfoLaurent()));

		$this->laurent = $this->service->getEmprunteur(
											Class_Users::getLoader()->newInstance()
												->setLogin('llaffont')
												->setPassword('afi')
										);
	}


	/** @test */
	function getIdShouldReturn572() {
		$this->assertEquals(572, $this->laurent->getId());
	}


	/** @test */
	function nbReservationsShouldReturnOne() {
		$this->assertEquals(1, $this->laurent->getNbReservations());
	}


	/** @test */
	function reservationTitleShouldBeHarryPotter() {
		$this->assertEquals("Harry Potter et la chambre des secrets", $this->laurent->getReservationAt(0)->getTitre());
	}


	/** @test */
	function reservationAuteurShouldBeJKRowling() {
		$this->assertEquals("J. K. Rowling", $this->laurent->getReservationAt(0)->getAuteur());
	}


	/** @test */
	function reservationRangShouldBeOne() {
		$this->assertEquals(2, $this->laurent->getReservationAt(0)->getRang());
	}


	/** @test */
	function reservationIdShouldBe27136() {
		$this->assertEquals(27136, $this->laurent->getReservationAt(0)->getId());
	}


	/** @test */
	function nbEmpruntsShouldReturnZero() {
		$this->assertEquals(0, $this->laurent->getNbEmprunts());
	}

	/** @test */
	function prenomShouldBeLaurent() {
		$this->assertEquals('laurent', $this->laurent->getPrenom());
	}
}



class KohaGetEmprunteurJeanAndreTest extends KohaTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->expects($this->at(0))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=LookupPatron&id=SANTONI&id_type=cardnumber')
			->will($this->returnValue(KohaFixtures::xmlLookupPatronJeanAndre()));


		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=GetPatronInfo&patron_id=419&show_contact=0&show_loans=1&show_holds=1')
			->will($this->returnValue(KohaFixtures::xmlGetPatronInfoJeanAndre()));

		$this->jean = $this->service->getEmprunteur(
										Class_Users::getLoader()->newInstance()
												->setLogin('SANTONI')
												->setPassword('zork')
									);
	}


	/** @test */
	function idShouldBe419() {
		$this->assertEquals(419, $this->jean->getId());
	}


	/** @test */
	function prenomShouldBeJeanAndre() {
		$this->assertEquals('Jean-André', $this->jean->getPrenom());
	}


	/** @test */
	function nbEmpruntsShouldBeTwo() {
		$this->assertEquals(2, $this->jean->getNbEmprunts());
	}


	/** @test */
	function nbReservationsShouldReturnZero() {
		$this->assertEquals(0, $this->jean->getNbReservations());
	}


	/** @test */
	function firstEmpruntTitreShouldBeLaGuitareEn10Lecons() {
		$this->assertEquals("La guitare en 10 leçons", $this->jean->getEmpruntAt(0)->getTitre());
	}


	/** @test */
	function firstEmpruntDateRetourShouldBe18_04_2009() {
		$this->assertEquals('18/04/2009', $this->jean->getEmpruntAt(0)->getDateRetour());
	}

	/** @test */
	function firstEmpruntCodeBorreShouldBe2700017UUU() {
		$this->assertEquals('2700017UUU', $this->jean->getEmpruntAt(0)->getCodeBarre());
	}


	/** @test */
	function secondEmpruntTitreShouldBeLIleAuTresor() {
		$this->assertEquals("L'Île au trésor", $this->jean->getEmpruntAt(1)->getTitre());
	}


	/** @test */
	function secondEmpruntIdShouldBe4454() {
		$this->assertEquals("4454", $this->jean->getEmpruntAt(1)->getId());
	}


	/** @test */
	function secondEmpruntExemplaireIdShouldBe4454() {
		$this->assertEquals("4454", $this->jean->getEmpruntAt(1)->getExemplaire()->getId());
	}


	/** @test */
	function secondEmpruntAuteurShouldBeRobertLouisStevenson() {
		$this->assertEquals("Robert Louis Stevenson", $this->jean->getEmpruntAt(1)->getAuteur());
	}

}


class KohaOperationsTest extends KohaTestCase {
	public function setUp() {
		parent::setUp();

		$this->mock_web_client
			->expects($this->at(0))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=LookupPatron&id=llaffont&id_type=cardnumber')
			->will($this->returnValue(KohaFixtures::xmlLookupPatronLaurent()));


		$this->_exemplaire_mireille_abeille = Class_Exemplaire::getLoader()
																							->newInstanceWithId(123)
																							->setIdOrigine('89863');
	}


	/** @test */
	function supprimerReservationShouldCallCancelHoldServiceAndReturnResultOK() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=CancelHold&patron_id=572&item_id=24426')
			->will($this->returnValue('<CancelHold><code>Canceled</code></CancelHold>'));


		$this->assertEquals(array('statut' => true,'erreur' => ''),
												$this->service->supprimerReservation(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), '24426'));
	}


	/** @test */
	function supprimerReservationShouldReturnErrorIfNotCanceled() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=CancelHold&patron_id=572&item_id=24426')
			->will($this->returnValue('<CancelHold><code>NotCanceled</code></CancelHold>'));


		$this->assertEquals(array('statut' => false,'erreur' => 'NotCanceled'),
												$this->service->supprimerReservation(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), '24426'));
	}


	/** @test */
	function prolongerPretShouldReturnErrorIfTooMany() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=RenewLoan&patron_id=572&item_id=24426')
			->will($this->returnValue('<RenewLoan>
                                   <success>0</success>
                                   <error>
                                     <message>too_many</message>
                                   </error>
                                   <date_due>2009-06-22</date_due>
                                </RenewLoan>'));


		$this->assertEquals(array('statut' => false, 'erreur' => 'Prolongation impossible'),
												$this->service->prolongerPret(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), '24426'));
	}


	/** @test */
	function prolongerPretShouldReturnSuccessIfNoErrors() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=RenewLoan&patron_id=572&item_id=24426')
			->will($this->returnValue('<?xml version="1.0" encoding="ISO-8859-1" ?>
                                 <RenewLoan>
                                  <success>0</success>
                                  <renewals>5</renewals>
                                  <date_due>2011-05-11</date_due>
                                 </RenewLoan>'));


		$this->assertEquals(array('statut' => true, 'erreur' => ''),
												$this->service->prolongerPret(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), '24426'));
	}



	/** @test */
	function reserverExemplaireShouldReturnSuccessIfNoErrors() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=HoldTitle&patron_id=572&bib_id=89863&request_location=127.0.0.1')
			->will($this->returnValue('<HoldTitle>
                                   <title>Mireille l\'abeille</title>
                                   <pickup_location>Bibliothèque Départementale de la Meuse</pickup_location>
                                 </HoldTitle>'));

		$this->assertEquals(array('statut' => true, 'erreur' => ''),
												$this->service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), $this->_exemplaire_mireille_abeille, ''));
	}



	/** @test */
	function reserverExemplaireShouldReturnErrorIfFail() {
		$this->mock_web_client
			->expects($this->at(1))
			->method('open_url')
			->with('http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl?service=HoldTitle&patron_id=572&bib_id=89863&request_location=127.0.0.1')
			->will($this->returnValue('<HoldTitle>
                                    <code>NotHoldable</code>
                                 </HoldTitle>'));

		$this->assertEquals(array('statut' => false, 'erreur' => 'Réservation impossible'),
												$this->service->reserverExemplaire(
													Class_Users::getLoader()->newInstance()
														->setLogin('llaffont')
														->setPassword('afi'), $this->_exemplaire_mireille_abeille, ''));
	}
}


?>