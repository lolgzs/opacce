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

include_once('Class/WebService/SIGB/Orphee/Service.php');
include_once('OrpheeFixtures.php');

class MapppedSoapClientForTesting extends Class_WebService_MappedSoapClient {
	public $wsdl;
	public $options;

	public function __construct($wsdl, $options) {
		$this->wsdl = $wsdl;
		$this->options = $options;
	}


	public function getId() {
		return GetIdResponse::withIdResult('clong');
	}
}


class Class_WebService_SIGB_Orphee_ServiceForTesting extends Class_WebService_SIGB_Orphee_Service {
	public function __construct($search_client) {
		$this->_search_client = $search_client;
	}


	public function getSearchClient() { 
		$result = $this->_search_client->GetId(new GetId());
		$this->_guid = $result->GetIdResult;
		$this->_search_client->__setCookie('ASP.NET_SessionId', $this->_guid);
		return $this->_search_client;
	}
}




class OrpheeServiceGetServiceTest extends Storm_Test_ModelTestCase {
	protected $_orphee;

	public function setUp() {
		parent::setUp();
		Class_WebService_SIGB_Orphee_Service::setSoapClientClass('MapppedSoapClientForTesting');
		$this->_orphee = Class_WebService_SIGB_Orphee_Service::getService('tests/fixtures/orphee.wsdl');
	}


	/** @test */
	public function searchClientShouldBeAnInstanceOfMappedSoapClient() {
		$this->assertInstanceOf('Class_WebService_MappedSoapClient', $this->_orphee->getSearchClient());
	}


	/** @test */
	public function clientWSDLShouldBeOrpheeWSDL() {
		$this->assertEquals('tests/fixtures/orphee.wsdl', $this->_orphee->getSearchClient()->wsdl);
	}


	/** @test */
	public function integrationTestNoticeWithRealServer() {
		Class_WebService_SIGB_Orphee_Service::setSoapClientClass('Class_WebService_MappedSoapClient');
		$orphee = Class_WebService_SIGB_Orphee_Service::getService('http://opac3.pergame.net/bibliotheque-agglo-stomer.fr/userfiles/webservices/orphee.wsdl');
		$notice = $orphee->getNotice('frOr0493126904');
		$this->assertEquals('0493126904', $notice->getId());
	}


	/** @disabledtest */
	public function integrationTestUserWithRealServer() {
		Class_WebService_SIGB_Orphee_Service::setSoapClientClass('Class_WebService_MappedSoapClient');
		$orphee = Class_WebService_SIGB_Orphee_Service::getService('http://opac3.pergame.net/bibliotheque-agglo-stomer.fr/userfiles/webservices/orphee.wsdl');
		$emprunteur = $orphee->getEmprunteur(Class_Users::getLoader()
																				 ->newInstanceWithId(2)
																				 ->setLogin('90106000084125'));
		$emprunteur->getEmprunts();
		$this->assertEquals('90211356', $emprunteur->getId());
	}
}




abstract class OrpheeServiceTestCase extends Storm_Test_ModelTestCase {
	protected $_search_client;
	protected $_orphee;
	protected $_henry_dupont;

	public function setUp(){
		parent::setUp();
		$this->_search_client = Storm_Test_ObjectWrapper::on(new Class_WebService_MappedSoapClient('tests/fixtures/orphee.wsdl'));
		
		$this->_search_client
			->whenCalled('__setCookie')
			->answers(null);

		$this->_beforeOrpheeServiceCreate();
		$this->_orphee = new Class_WebService_SIGB_Orphee_ServiceForTesting($this->_search_client);
		$this->_orphee->connect();
		$this->_henry_dupont = Class_Users::getLoader()
													->newInstanceWithId(2)
													->setLogin('10900000753');
	}


	public function _beforeOrpheeServiceCreate(){
		$this->_search_client
			->whenCalled('GetId')
			->with(new GetId())
			->answers(GetIdResponse::withIdResult('1234'));
	}
}




class OrpheeServiceTestAutoConnectSuccessful extends OrpheeServiceTestCase {
	protected $_search_client;
	protected $_orphee;

	/** @test */
	public function withSuccessfulGitIdIsConnectedShouldReturnTrue(){
		$this->assertTrue($this->_orphee->isConnected());
	}


	/** @test */
	public function getGUIDShouldReturns1234() {
		$this->assertEquals('1234', $this->_orphee->getGUID());
	}


	/** @test */
	public function cookieShouldHaveBeenSetTo1234() {
		$this->assertTrue($this->_search_client->methodHasBeenCalledWithParams('__setCookie',
																																					 array('ASP.NET_SessionId', '1234')));
	}
}




class OrpheeServiceTestAutoConnectError extends OrpheeServiceTestCase {
	protected $_search_client;
	protected $_orphee;

	public function _beforeOrpheeServiceCreate(){
		$this->_search_client
			->whenCalled('GetId')
			->answers(new GetIdResponse());
	}
	
	
	/** @test */
	public function isConnectedShouldReturnsFalse() {
		$this->assertFalse($this->_orphee->isConnected());
	}


	/** @test */
	public function guidShouldBeEmpty() {
		$this->assertEmpty($this->_orphee->getGUID());
	}


	/** @test */
	public function isConnectedShouldBeFalseOnSoapException() {
		$this->_search_client
			->whenCalled('GetId')
			->willDo(function(){throw new SoapFault('error', 'error');});
		$this->_orphee->connect();
		$this->assertFalse($this->_orphee->isConnected());
	}
}




class OrpheeServiceTestGetLstDmntWithMillenium extends OrpheeServiceTestCase {
	public function _beforeOrpheeServiceCreate(){
		$this->_search_client
			->whenCalled('GetId')
			->with(new GetId())
			->answers(GetIdResponse::withIdResult('azerty'));
	}


	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetLstDmt')
			->with(GetLstDmt::withNtcAndFas('1301700727', 0))
			->answers(GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtMillenium()));
		
		$this->millenium = $this->_orphee->getNotice('frOr1301700727');
	}


	/** @test */
	public function cookieShouldHaveBeenSetToAzerty() {
		$this->assertTrue($this->_search_client->methodHasBeenCalledWithParams('__setCookie',
																																					 array('ASP.NET_SessionId', 'azerty')));
	}


	/** @test */
	public function milleniumShouldBeAnInstanceOfClass_WebService_SIGB_Notice() {
		$this->assertInstanceOf('Class_WebService_SIGB_Notice', $this->millenium);
	}


	/** @test */
	public function exemplaireByCodeBarre00106001488142ShouldNotBeEmpty() {
		$exemplaire = $this->millenium->getExemplaireByCodeBarre('00106001488142');
		$this->assertNotEmpty($exemplaire);
		return $exemplaire;
	}


	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488142ShouldNotBeEmpty
	 */
	public function firstExemplaireIdShouldBe148814($exemplaire) {
		$this->assertEquals('148814', $exemplaire->getId());
	}

	
	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488142ShouldNotBeEmpty
	 */
	public function firstExemplaireDisponibiliteShouldBeSorti($exemplaire) {
		$this->assertEquals('sorti', $exemplaire->getDisponibilite());
	}

	
	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488142ShouldNotBeEmpty
	 */
	public function firstExemplaireShouldBeReservable($exemplaire) {
		$this->assertTrue($exemplaire->isReservable());
	}


	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488142ShouldNotBeEmpty
	 */
	public function firstExemplaireShouldBeValid($exemplaire) {
		$this->assertTrue($exemplaire->isValid());
	}


	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488142ShouldNotBeEmpty
	 */
	public function firstExemplaireDateRetourShouldBe27_03_2012($exemplaire) {
		$this->assertEquals('27/03/2012', $exemplaire->getDateRetour());
	}

	
	/** @test */
	public function exemplaireByCodeBarre00106001488155ShouldNotBeEmpty() {
		$exemplaire = $this->millenium->getExemplaireByCodeBarre('00106001488155');
		$this->assertNotEmpty($exemplaire);
		return $exemplaire;
	}


	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488155ShouldNotBeEmpty
	 */
	public function secondExemplaireIdShouldBe148815($exemplaire) {
		$this->assertEquals('148815', $exemplaire->getId());
	}


	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488155ShouldNotBeEmpty
	 */
	public function secondExemplaireDisponibiliteShouldBeEnRayon($exemplaire) {
		$this->assertEquals('en rayon', $exemplaire->getDisponibilite());
	}

	
	/** 
	 * @test 
	 * @depends exemplaireByCodeBarre00106001488155ShouldNotBeEmpty
	 */
	public function secondExemplaireShouldNotBeReservable($exemplaire) {
		$this->assertFalse($exemplaire->isReservable());
	}

}




class OrpheeServiceTestGetLstDmntWithLivreEspagnol extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetLstDmt')
			->with(GetLstDmt::withNtcAndFas('0030008850', 0))
			->answers(GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtLivreEspagnol()));

		$this->notice = $this->_orphee->getNotice('frOr0030008850');
	}


	/** @test */
	public function exemplaireByCodeBarreAncien_07086ShouldNotBeEmpty() {
		$exemplaire = array_first($this->notice->getExemplaires());
		$this->assertEquals('Ancien-07086', $exemplaire->getCodeBarre());
	}
}




class OrpheeServiceTestGetLstDmntLAmourDansLeSangReserve extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetLstDmt')
			->with(GetLstDmt::withNtcAndFas('1300802087', 0))
			->answers(GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtLAmourDansLeSangReserve()));

		$this->notice = $this->_orphee->getNotice('frOr1300802087');
	}


	/** @test */
	public function exemplaireShouldBeReservable() {
		$this->assertTrue(array_first($this->notice->getExemplaires())->isReservable());
	}
}




class OrpheeServiceTestGetLstDmntVagabondEnArchivage extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetLstDmt')
			->with(GetLstDmt::withNtcAndFas('1300201571', 0))
			->answers(GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtVagabondEnArchivage()));
	}


	/** @test */
	public function exemplaireShouldNotBeVisible() {
		$notice = $this->_orphee->getNotice('frOr1300201571');
		$this->assertFalse(array_first($notice->getExemplaires())->isVisibleOPAC());
	}
}




class OrpheeServiceTestGetLstDmntMetamausEnCatalogage extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetLstDmt')
			->with(GetLstDmt::withNtcAndFas('1314910159', 0))
			->answers(GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtMetamausEnCatalogage()));
	}


	/** @test */
	public function exemplaireShouldNotBeVisible() {
		$notice = $this->_orphee->getNotice('frOr1314910159');
		$this->assertFalse(array_first($notice->getExemplaires())->isVisibleOPAC());
	}
}




class OrpheeServiceGetLstDmtResponseTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function withStandardXmlShouldGetXMLShouldRetunsIt() {
		$this->assertEquals('<?xml version="1.0" encoding="utf-8"?><datas></datas>', 
												GetLstDmtResponse::withResult('<?xml version="1.0" encoding="utf-8"?><datas></datas>')->getXml());
	}


	/** @test */
	public function withOrpheeCDataXmlShouldCleanXMLShouldRetunsIt() {
		$this->assertEquals('<?xml version="1.0" encoding="utf-8"?><datas></datas>', 
												GetLstDmtResponse::withResult('<![CDATA[<?xml version="1.0" encoding="utf-8"?><datas></datas>]]>')->getXml());
	}


	/** @test */
	public function withLivreEspagnolShouldRemoveAllCDATA() {
		$actual_xml = GetLstDmtResponse::withResult(OrpheeFixtures::xmlGetLstDmtLivreEspagnol())->getXml();
		$this->assertEquals('<?xml version="1.0" encoding="utf-8"?><datas><documents><document><no>30007086</no><ntc>30008850</ntc><carte>Ancien-07086</carte><sit>1</sit><lib_sit>  en rayon</lib_sit><loc>10</loc><lib_loc>  Salle Aubin</lib_loc><loc_ori>10</loc_ori><lib_loc_ori>  Salle Aubin</lib_loc_ori><cote>3769 11-6</cote><anx_ori>1</anx_ori><lib_anx_ori>  Médiathèque Saint Omer</lib_anx_ori><anx_cur>1</anx_cur><lib_anx_cur>  Médiathèque Saint Omer</lib_anx_cur><anx_nxt>1</anx_nxt><lib_anx_nxt>  Médiathèque Saint Omer</lib_anx_nxt><no_coll>0</no_coll><site>106</site><lib_site>  Saint Omer</lib_site><sup>118</sup><lib_sup>  Livre ancien</lib_sup><sec>4</sec><lib_sec>  Fonds ancien</lib_sec><uti>1</uti><lib_uti>  exclu du prêt</lib_uti><sta1>107</sta1><lib_sta1>  Livre FA</lib_sta1><sta2>0</sta2><lib_sta2></lib_sta2><sta3>0</sta3><lib_sta3></lib_sta3><date_last_pret></date_last_pret><date_last_retour></date_last_retour><titre>Varias antiguedades de España, Africa y otras provincias, por el doctor Bernardo Aldrete (Aldrete, Bernardo)</titre><date_edi>1614</date_edi></document></documents></datas>',
												$actual_xml,
												$actual_xml);
	}
}




class OrpheeServiceGetInfoUserCarteHenryDupontWithErrorTest extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->willDo(function() {throw new SoapFault('code', 'error');});
	}


	/** 
	 * @expectedException Class_WebService_Exception
	 * @expectedMessage Le SIGB Orphée a retourné l'erreur suivante: error
	 * @test 
	 */
	public function getEmprunteurShouldThrowExceptionOnSoapFault() {
		$this->emprunteur = $this->_orphee->getEmprunteur($this->_henry_dupont);
	}


	/** @test */
	public function reserverExemplaireShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: error'),
												$this->_orphee->reserverExemplaire($this->_henry_dupont,
																													 new Class_Exemplaire(),
																													 '')); 		
	}


	/** @test */
	public function prolongerPretShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: error'),
												$this->_orphee->prolongerPret($this->_henry_dupont,
																											new Class_Exemplaire())); 		
	}


	/** @test */
	public function supprimerReservationShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: error'),
												$this->_orphee->supprimerReservation($this->_henry_dupont,
																														 new Class_Exemplaire())); 		
	}

}




class OrpheeServiceGetInfoUserCarteHenryDupontActionErrorTest extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$soap_fault_block = function() {throw new SoapFault('code', 'plantage');};

		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('RsvNtcAdh')->willDo($soap_fault_block)
			->whenCalled('DelRsv')->willDo($soap_fault_block)
			->whenCalled('ProlongePret')->willDo($soap_fault_block);
		
		$this->_exemplaire = Class_Exemplaire::getLoader()
			->newInstanceWithId(2)
			->setIdOrigine(3);
	}


	/** @test */
	public function reserverExemplaireShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: plantage'),
												$this->_orphee->reserverExemplaire($this->_henry_dupont, $this->_exemplaire, '')); 		
	}


	/** @test */
	public function prolongerPretShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: plantage'),
												$this->_orphee->prolongerPret($this->_henry_dupont, $this->_exemplaire)); 		
	}


	/** @test */
	public function supprimerReservationShouldReturnError() {
		$this->assertEquals(array('statut' => false, 'erreur' => 'Le SIGB Orphée a retourné l\'erreur suivante: plantage'),
												$this->_orphee->supprimerReservation($this->_henry_dupont, $this->_exemplaire, '')); 		
	}
}




class OrpheeServiceGetInfoUserCarteHenryDupontWithNoXMLTest extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->answers(GetInfoUserCarteResponse::withResult(''));
		$this->emprunteur = $this->_orphee->getEmprunteur($this->_henry_dupont);
	}


	/** @test */
	public function getEmprunteurShouldReturnValidEmpranteurOnSoapFault() {
		$this->assertInstanceOf('Class_WebService_SIGB_Emprunteur', $this->emprunteur);
	}


	/** @test */
	public function emprunteurShouldHaveEmptyReservations() {
		$this->assertEmpty($this->emprunteur->getReservations());
	}


	/** @test */
	public function emprunteurShouldHaveEmptyEmprunts() {
		$this->assertEmpty($this->emprunteur->getEmprunts());
	}
}




class OrpheeServiceGetInfoUserCarteHenryDupontTest extends OrpheeServiceTestCase {
	public function setUp() {
		parent::setUp();

		$ex_potter = Class_Exemplaire::getLoader()
			->newInstanceWithId(23)
			->setCodeBarres('123456')
			->setBib(Class_Bib::getLoader()
							 ->newInstanceWithId(3)
							 ->setLibelle('Annecy Bonlieu'))
			->setNotice(Class_Notice::getLoader()
									->newInstanceWithId(5)
									->setTitrePrincipal('Harry Potter')
									->setAuteurPrincipal('Rowling'));									

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->answers(null)

			->whenCalled('findFirstBy')
			->with(array('code_barres' => '123456'))
			->answers($ex_potter);


		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('GetLstPret')
			->with(GetLstPret::withAdh('100753'))
			->answers(GetLstPretResponse::withResult(OrpheeFixtures::xmlGetLstPretHenryDupont()))

			->whenCalled('GetLstRsv')
			->with(GetLstRsv::withAdh('100753'))
			->answers(GetLstRsvResponse::withResult(OrpheeFixtures::xmlGetLstRsvHenryDupont()));

		$this->emprunteur = $this->_orphee->getEmprunteur($this->_henry_dupont);
	}


	/** @test */
	public function emprunteurShouldNotBeEmpty() {
		$this->assertNotEmpty($this->emprunteur);
	}


	/** @test */
	public function getIdShouldAnswer100753() {
		$this->assertEquals('100753', $this->emprunteur->getId());
	}


	/** @test */
	public function getNomShouldAnswerDupont() {
		$this->assertEquals('Dupont', $this->emprunteur->getNom());
	}


	/** @test */
	public function getPrenomShouldAnswerHenry() {
		$this->assertEquals('Henry', $this->emprunteur->getPrenom());
	}


	/** @test */
	public function getNbEmpruntsShouldAnswerOne() {
		$this->assertEquals(1, $this->emprunteur->getNbEmprunts());
	}


	/** @test */
	public function getNbReservationsShouldAnswerThree() {
		$this->assertEquals(3, $this->emprunteur->getNbReservations());
	}


	/** @test */
	public function getMailShouldAnswerHenryDupontAtMailDotCom() {
		$this->assertEquals('henry.dupont@mail.com', $this->emprunteur->getEMail());
	}


	/** @test */
	public function firstEmpruntShouldNotBeEmpty() {
		$emprunt = array_first($this->emprunteur->getEmprunts());
		$this->assertNotEmpty($emprunt);
		return $emprunt;
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntTitreShouldBeDerriereLaColline($emprunt) {
		$this->assertEquals('Derrière la colline', $emprunt->getTitre());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntAuteurShouldBeChapouton($emprunt) {
		$this->assertEquals('Chapouton, Anne-Marie (1939-....)', $emprunt->getAuteur());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntNoNoticeShouldBe974767802($emprunt) {
		$this->assertEquals('974767802', $emprunt->getExemplaire()->getNoNotice());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntDateRetourShouldBe24_04_2009($emprunt) {
		$this->assertEquals('24/04/2009', $emprunt->getDateRetour());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntShouldBeEnRetard($emprunt) {	
		$this->assertTrue($emprunt->enRetard());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntCodeBarreShouldBe1875890109($emprunt) {	
		$this->assertEquals('1875890109', $emprunt->getExemplaire()->getCodeBarre());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntIdShouldBe187589($emprunt) {	
		$this->assertEquals('187589', $emprunt->getId());
	}


	/** 
	 * @test 
	 * @depends firstEmpruntShouldNotBeEmpty
	 */
	public function empruntExemplaireIdShouldBe187589($emprunt) {	
		$this->assertEquals('187589', $emprunt->getExemplaire()->getId());
	}


	/** @test */
	public function firstReservationShouldNotBeEmpty() {
		$reservation = array_first($this->emprunteur->getReservations());
		$this->assertNotEmpty($reservation);
		return $reservation;
	}


	/** @test */
	public function secondEmpruntShouldNotBeEmpty() {
		$emprunt = array_at(1, $this->emprunteur->getEmprunts());
		$this->assertNotEmpty($emprunt);
		return $emprunt;
	}


	/** 
	 * @test 
	 * @depends secondEmpruntShouldNotBeEmpty
	 */
	public function secondEmpruntAuteurShouldBeRowling($emprunt) {
		$this->assertEquals('Rowling', $emprunt->getAuteur());
	}


	/** 
	 * @test 
	 * @depends secondEmpruntShouldNotBeEmpty
	 */
	public function secondEmpruntTitreShouldBeHarryPotter($emprunt) {
		$this->assertEquals('Harry Potter', $emprunt->getTitre());
	}


	/** 
	 * @test 
	 * @depends secondEmpruntShouldNotBeEmpty
	 */
	public function secondEmpruntBibliothequeShouldBeAnnecyBonlieu($emprunt) {
		$this->assertEquals('Annecy Bonlieu', $emprunt->getBibliotheque());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationNoNoticeShouldBe974898302($reservation) {	
		$this->assertEquals('974898302', $reservation->getExemplaire()->getNoNotice());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationIdShouldBe974898302($reservation) {	
		$this->assertEquals('974898302', $reservation->getId());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationExemplaireIdShouldBe123($reservation) {	
		$this->assertEquals('123', $reservation->getExemplaire()->getId());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationTitreShouldBeLeChemin($reservation) {	
		$this->assertEquals('Le Chemin', $reservation->getTitre());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationAuteurShouldBeKyo($reservation) {	
		$this->assertEquals('Kyo', $reservation->getAuteur());
	}


	/** 
	 * @test 
	 * @depends firstReservationShouldNotBeEmpty
	 */
	public function firstReservationRangShouldBeOne($reservation) {	
		$this->assertEquals(1, $reservation->getRang());
	}


	/** @test */
	public function thirdReservationShouldNotBeEmpty() {
		$reservation = array_at(2, $this->emprunteur->getReservations());
		$this->assertNotEmpty($reservation);
		return $reservation;
	}
}




class OrpheeServiceReservationTest extends OrpheeServiceTestCase {
	/** @test */
	public function testReservationSuccessful() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('RsvNtcAdh')
			->with(RsvNtcAdh::withNoticeUserNo('1301700727', 100753))
			->answers(RsvNtcAdhResponse::withResult('<datas><msg><code><![CDATA[1]]></code><libelle><![CDATA[Réservation mise en attente]]></libelle></msg></datas>'));

		$this->assertEquals(array('statut' => true, 'erreur' => ''), 
												$this->_orphee->reserverExemplaire($this->_henry_dupont, 
																													 
																													 Class_Exemplaire::getLoader()
																													 ->newInstanceWithId(234)
																													 ->setIdOrigine('frOr1301700727'), 
																													 
																													 ''));
	}

	
	/** @test */
	public function testReservationNotAllowed() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('RsvNtcAdh')
			->with(RsvNtcAdh::withNoticeUserNo('401700727', 100753))
			->answers(RsvNtcAdhResponse::withResult('<datas><msg><code><![CDATA[0]]></code><libelle><![CDATA[Réservation refusée]]></libelle></msg></datas>'));

		$this->assertEquals(array('statut' => false, 'erreur' => 'Réservation refusée'), 
												$this->_orphee->reserverExemplaire($this->_henry_dupont, 
																													 
																													 Class_Exemplaire::getLoader()
																													 ->newInstanceWithId(234)
																													 ->setIdOrigine('frOr401700727'), 
																													 
																													 ''));
	}	
}



class OrpheeServiceSupprimerReservationTest extends OrpheeServiceTestCase {
		/** @test */
	public function testSuppressionReservationSuccessful() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('DelRsv')
			->with(DelRsv::withNoticeUserNo('1301700727', 100753))
			->answers(DelRsvResponse::withResult(1));

		$this->assertEquals(array('statut' => true, 'erreur' => ''), 
												$this->_orphee->supprimerReservation($this->_henry_dupont, '1301700727'));
	}

	
	/** @test */
	public function testEchecSuppressionReservation() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('DelRsv')
			->with(DelRsv::withNoticeUserNo('401700727', 100753))
			->answers(DelRsvResponse::withResult(0));

		$this->assertEquals(array('statut' => false, 'erreur' => 'La suppression a échoué'), 
												$this->_orphee->supprimerReservation($this->_henry_dupont, '401700727'));
	}
}




class OrpheeServiceProlongetPretTest extends OrpheeServiceTestCase {
	/** @test */
	public function testProlongationEffectuee() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('ProlongePret')
			->with(ProlongePret::withDocumentUser('1301700727', 100753))
			->answers(ProlongePretResponse::withResult('<datas><msg><code><![CDATA[1]]></code><libelle><![CDATA[Prolongation effectuée]]></libelle></msg></datas>'));

		$this->assertEquals(array('statut' => true, 'erreur' => ''), 
												$this->_orphee->prolongerPret($this->_henry_dupont, '1301700727'));
	}

	
	/** @test */
	public function testProlongationRefusee() {
		$this->_search_client
			->whenCalled('GetInfoUserCarte')
			->with(GetInfoUserCarte::withNo('10900000753'))
			->answers(GetInfoUserCarteResponse::withResult(OrpheeFixtures::xmlGetInfoUserCarteHenryDupont()))

			->whenCalled('ProlongePret')
			->with(ProlongePret::withDocumentUser('401700727', 100753))
			->answers(ProlongePretResponse::withResult('<datas><msg><code><![CDATA[0]]></code><libelle><![CDATA[Prolongation refusée]]></libelle></msg></datas>'));

		$this->assertEquals(array('statut' => false, 'erreur' => 'Prolongation refusée'), 
												$this->_orphee->prolongerPret($this->_henry_dupont, '401700727'));
	}	
}

?>