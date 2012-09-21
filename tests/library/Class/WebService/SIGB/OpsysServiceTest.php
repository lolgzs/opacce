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

include_once('Class/WebService/SIGB/Opsys/Service.php');

class StubSoapClient {
	public function OuvrirSession($ouvrir_session) {
		$result = new OuvrirSessionResponse();
		$result->OuvrirSessionResult = new RspOuvrirSession;
		$result->OuvrirSessionResult->GUIDSession = '1234';
		return $result;
	}

	public function __call($method, $args) {

	}
}




abstract class OpsysServiceFactoryWithCatalogueWebTestCase extends PHPUnit_Framework_TestCase {
	protected $_service;

	public function setUp() {
		Class_WebService_SIGB_Opsys::reset();
		Class_WebService_SIGB_Opsys_ServiceFactory::setSoapClientClass('StubSoapClient');
	}
}




class OpsysServiceFactoryWithCatalogueWebTest extends OpsysServiceFactoryWithCatalogueWebTestCase {
	public function setUp() {
		parent::setUp();
		$this->_service = Class_WebService_SIGB_Opsys::getService(array('url_serveur' => "http://localhost:8088/mockServiceRechercheSoap?WSDL",
																																		'catalogue_web' => '1'));
	}


	/** @test */
	public function getServiceShouldReturnAnInstanceOfOpsysService() {
		$this->assertInstanceOf('Class_WebService_SIGB_Opsys_Service', $this->_service);
	}


	/** @test */
	public function catalogClientShouldBeAnInstanceOfStupSoapClient() {
		$this->assertInstanceOf('StubSoapClient', $this->_service->getCatalogClient());
	}
}




class OpsysServiceFactoryWithoutCatalogueWebTest extends OpsysServiceFactoryWithCatalogueWebTestCase {
	public function setUp() {
		parent::setUp();
		$this->_service = Class_WebService_SIGB_Opsys::getService(array('url_serveur' => "http://localhost:8088/mockServiceRechercheSoap?WSDL",
																																		'catalogue_web' => '0'));
	}


	/** @test */
	public function catalogClientShouldBeAnInstanceOfNullCatalogClient() {
		$this->assertInstanceOf('NullCatalogSoapClient', $this->_service->getCatalogClient());
	}
}




class OpsysServiceFactoryWithoutParamCatalogueWebTest extends OpsysServiceFactoryWithCatalogueWebTestCase {
	public function setUp() {
		parent::setUp();
		$this->_service = Class_WebService_SIGB_Opsys::getService(array('url_serveur' => "http://localhost:8088/mockServiceRechercheSoap?WSDL"));
	}


	/** @test */
	public function catalogClientShouldBeAnInstanceOfStupSoapClient() {
		$this->assertInstanceOf('StubSoapClient', $this->_service->getCatalogClient());
	}
}




class OpsysServiceTestAutoConnect extends PHPUnit_Framework_TestCase {
	private $ouvre_session_res;
	private $client;

	public function setUp(){
		$this->ouvre_session_res = $this->getMock(
																							'OuvreSessionResponseMock',
																							array('getGUID'));
		$this->ouvre_session_res
			->expects($this->any())
			->method('getGUID')
			->will($this->returnValue("12345"));

		$this->ouvre_session_error = $this->getMock(
																		'OuvreSessionResponseMock',
																		array('getGUID'));
		$this->ouvre_session_error
			->expects($this->any())
			->method('getGUID')
			->will($this->returnValue(""));

		$this->search_client = $this->getMock(
																	 'MappedSoapClientMock',
																	 array('OuvrirSession', 'FermerSession'));
	}


	private function __setConnectExpectation(){
		$this->search_client
			->expects($this->once())
			->method('OuvrirSession')
			->with($this->isInstanceOf('OuvrirSession'))
			->will($this->returnValue($this->ouvre_session_res));
	}


	public function testAutomaticConnect(){
		$this->__setConnectExpectation();
		$opsys = new Class_WebService_SIGB_Opsys_Service($this->search_client);
		$this->assertTrue($opsys->isConnected());
	}


	public function testConnectionError(){
		$this->search_client
			->expects($this->once())
			->method('OuvrirSession')
			->with($this->isInstanceOf('OuvrirSession'))
			->will($this->returnValue($this->ouvre_session_error));
		$opsys = new Class_WebService_SIGB_Opsys_Service($this->search_client);
		$this->assertFalse($opsys->isConnected());
	}
}


class Class_System_OpsysServiceFactoryTestUrls extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->factory = new Class_WebService_SIGB_Opsys_ServiceFactory();
	}

	public function testWithFullUrl() {
		$full_url = 'http://81.80.216.130/websrvaloes/servicerecherche.asmx?WSDL';
		$this->assertEquals('http://81.80.216.130/websrvaloes/servicerecherche.asmx?WSDL',
												$this->factory->getWsdlSearchURL($full_url));
		$this->assertEquals('http://81.80.216.130/websrvaloes/catalogueWeb.asmx?WSDL',
												$this->factory->getWsdlCatalogURL($full_url));
	}

	public function testWithRootUrl() {
		$root_url = 'http://10.0.0.1/websrvaloes/';
		$this->assertEquals('http://10.0.0.1/websrvaloes/servicerecherche.asmx?WSDL',
												$this->factory->getWsdlSearchURL($root_url));
		$this->assertEquals('http://10.0.0.1/websrvaloes/catalogueWeb.asmx?WSDL',
												$this->factory->getWsdlCatalogURL($root_url));
	}
}



class Class_WebService_SIGB_OpsysServiceTestProxy extends PHPUnit_Framework_TestCase {
	private $factory;
	private $mock_opsys_service;

	public function setUp(){
		$this->factory = $this->getMock('OpsysFactory', array('createOpsysService'));

		$this->mock_opsys_service = $this->getMock(
																				 'Class_WebService_SIGB_Opsys',
																				 array('newOpsysServiceFactory'));

		$this->mock_opsys_service->expects($this->once())
			->method('newOpsysServiceFactory')
			->will($this->returnValue($this->factory));
	}


	public function testCreateServiceWithProxy(){
		Class_WebService_SIGB_Opsys::setProxy('192.168.2.2', '3128', 'login', 'password');

		$this->factory->expects($this->once())
			->method('createOpsysService')
			->with(
						 $this->equalTo('opsys.wsdl'),
						 $this->equalTo(true),
						 $this->equalTo( array(
																	 'proxy_host' => '192.168.2.2',
																	 'proxy_port' => '3128',
																	 'proxy_login' => 'login',
																	 'proxy_password' =>	'password')))
			->will($this->returnValue('anOpsysService'));

		$service = $this->mock_opsys_service->createService('opsys.wsdl');
		$this->assertEquals('anOpsysService', $service);
	}


	public function testCreateServiceWithProxyInZendRegistry(){
		$proxyConfig = array(
												 'proxy_host' => '10.0.0.5',
												 'proxy_port' => '8080',
												 'proxy_user' => 'tintin',
												 'proxy_pass' => 'milou');
		Zend_Registry::set('http_proxy',$proxyConfig);

		$this->factory->expects($this->once())
			->method('createOpsysService')
			->with(
						 $this->equalTo('afi.wsdl'),
						 $this->equalTo(true),
						 $this->equalTo( array(
																	 'proxy_host' => '10.0.0.5',
																	 'proxy_port' => '8080',
																	 'proxy_login' => 'tintin',
																	 'proxy_password' =>	'milou')))
			->will($this->returnValue('anAFIService'));

		$service = $this->mock_opsys_service->createService('afi.wsdl');
		$this->assertEquals('anAFIService', $service);
	}


	public function testCreateServiceWithoutProxy(){
		$this->factory->expects($this->once())
			->method('createOpsysService')
			->with($this->equalTo('opsys.wsdl'))
			->will($this->returnValue('anOpsysService'));

		$service = $this->mock_opsys_service->createService('opsys.wsdl');
		$this->assertEquals('anOpsysService', $service);
	}
}


class OpsysServiceNoticeTestDispoExemplaire extends PHPUnit_Framework_TestCase {
	public function testPopDisponibiliteOnEmptyNoticeReturnsFalse(){
		$notice = new Class_WebService_SIGB_Notice('123');
		$this->assertFalse($notice->popDisponibilite());
	}


	public function testPopDisponibiliteWithThreeExemplaires(){
		$notice = new Class_WebService_SIGB_Notice('123');
		$notice->addAllExemplaires(array(new Class_WebService_SIGB_Exemplaire('1'),
																		 new Class_WebService_SIGB_Exemplaire('2'),
																		 new Class_WebService_SIGB_Exemplaire('3')));
		$this->assertTrue($notice->popDisponibilite());
		$this->assertTrue($notice->popDisponibilite());
		$this->assertTrue($notice->popDisponibilite());
		$this->assertFalse($notice->popDisponibilite());
	}
}




class OpsysServiceNoticeCacheTestGetExemplaire extends PHPUnit_Framework_TestCase {
	private $notices;
	private $cache;

	public function getNotice($notice_id){
		if (array_key_exists($notice_id, $this->notices))
			return $this->notices[$notice_id];
		return NULL;
	}

	public function setUp(){
		$this->notice_potter = new Class_WebService_SIGB_Notice('potter');
		for ($i = 0; $i <= 2; $i++) {
			$this->notice_potter->addExemplaire((new Class_WebService_SIGB_Exemplaire("$i"))
																					->setDisponibiliteLibre()
																					->setCodeBarre("$i")
																					->setCote("HP $i"));
		}

		$notice_vide = new Class_WebService_SIGB_Notice('vide');
		$this->notices = array( "vide"=>$notice_vide,
														"potter"=>$this->notice_potter);

		$this->cache = new Class_WebService_SIGB_NoticeCache($this);
	}


	public function testGetExemplaireWithInexistantNoticeIsIndisponible(){
		$exemplaire = $this->cache->getExemplaire("inexistant", "");
		$this->assertEquals("Indisponible", $exemplaire->getDisponibilite());
	}


	public function testGetExemplaireWithInexistantNoticeIsNotValid(){
		$exemplaire = $this->cache->getExemplaire("inexistant", "");
		$this->assertFalse($exemplaire->isValid());
	}


	public function testGetExemplaireWithEmptyNoticeIsIndisponible(){
		$exemplaire = $this->cache->getExemplaire("vide", "");
		$this->assertEquals("Indisponible", $exemplaire->getDisponibilite());
	}


	public function testGetExemplaireWithEmptyNoticeIsNotValid(){
		$exemplaire = $this->cache->getExemplaire("vide", "");
		$this->assertFalse($exemplaire->isValid());
	}


	public function testGetExemplaireWithPotterPopDisponiblite(){
		$this->assertEquals("Disponible",
												$this->cache->getExemplaire("potter", "0")->getDisponibilite());
	}


	public function testGetExemplaireWithPotterIsValid(){
		$this->assertTrue($this->cache
											->getExemplaire("potter", "0")
											->isValid());
	}


	public function testGetExemplaireWithCodeBarre() {
		$this->assertEquals("0",
												$this->cache->getExemplaire("potter", "0")->getCodeBarre());
		$this->assertEquals("1",
												$this->cache->getExemplaire("potter", "1")->getCodeBarre());
		$this->assertEquals("2",
												$this->cache->getExemplaire("potter", "2")->getCodeBarre());
	}


	public function testGetExemplaireWithCote() {
		$this->assertEquals("HP 0",
												$this->cache->getExemplaire("potter", "0")->getCote());
		$this->assertEquals("HP 1",
												$this->cache->getExemplaire("potter", "1")->getCote());
		$this->assertEquals("HP 2",
												$this->cache->getExemplaire("potter", "2")->getCote());
	}


	public function testCallLoadNoticeOnlyOncePerNotice(){
		$this->assertEquals("Disponible",
												$this->cache->getExemplaire("potter", "0")->getDisponibilite());
		$this->notices = array();
		$this->assertEquals("Disponible",
												$this->cache->getExemplaire("potter", "1")->getDisponibilite());
		$this->assertEquals("Disponible",
												$this->cache->getExemplaire("potter", "2")->getDisponibilite());
		$this->assertEquals("Indisponible",
												$this->cache->getExemplaire("potter", "3")->getDisponibilite());
	}


	public function testExemplaireReservableIfNoticeReservable(){
		$this->notice_potter->setReservable(true);
		$this->assertTrue($this->cache->getExemplaire("potter", "1")->isReservable());
		$this->notice_potter->setReservable(false);
		$this->assertFalse($this->cache->getExemplaire("potter", "1")->isReservable());
	}
}




abstract class OpsysServiceWithSessionTestCase extends Storm_Test_ModelTestCase {
	protected $opsys;

	public function setUp(){
		$this->ouvre_session_res = $this->mock();
		$this->search_client = $this->mock();
		$this->catalog_client = $this->mock();


		$this->ouvre_session_res
			->whenCalled('getGUID')
			->answers('guid_12345');

		$auth_response = new EmprAuthentifierResponse();
		$auth_response->EmprAuthentifierResult = new RspEmprAuthentifier();
		$auth_response->EmprAuthentifierResult->IDEmprunteur = '000238';
		$auth_response->EmprAuthentifierResult->IdentiteEmpr = 'tintin';
		$auth_response->EmprAuthentifierResult->EmailEmpr = 'tintin@free.fr';
		$auth_response->EmprAuthentifierResult->NombrePrets = 4;
		$auth_response->EmprAuthentifierResult->NombreReservations = 3;
		$auth_response->EmprAuthentifierResult->NombreRetards = 2;

		$this->search_client
			->whenCalled('OuvrirSession')->answers($this->ouvre_session_res)
			->whenCalled('FermerSession')->answers(null)
			->whenCalled('EmprAuthentifier')->answers($auth_response);

		$this->opsys = new Class_WebService_SIGB_Opsys_Service($this->search_client, $this->catalog_client);
	}
}



class OpsysServiceEmprAuthentifierErreurTestCreateEmprunteur extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();


		$auth_response_error = new EmprAuthentifierResponse();
		$auth_response_error->EmprAuthentifierResult = new RspEmprAuthentifier();
		$auth_response_error->ErreurService = new WebSrvErreur();
		$auth_response_error->ErreurService->CodeErreur = '1';

		$this->search_client			
			->whenCalled('EmprAuthentifier')->answers($auth_response_error);

		$this->emprunteur = $this->opsys->getEmprunteur(
													Class_Users::getLoader()->newInstance()
														->setLogin('tintin')
														->setPassword('1234'));
	}


	public function testEmprunteurIsNotValid() {
		$this->assertFalse($this->emprunteur->isValid());
	}
}




class OpsysServiceEmprAuthentifierTestCreateEmprunteur extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();
		$this->emprunteur = $this->opsys->getEmprunteur(
													Class_Users::getLoader()->newInstance()
														->setLogin('tintin')
														->setPassword('1234'));
	}

	public function testIDEmprunteurIs00238() {
		$this->assertEquals('000238', $this->emprunteur->getId());
	}


	public function testEmprunteurNameIsTintin() {
		$this->assertEquals('tintin', $this->emprunteur->getName());
	}


	public function testEmprunteurIsValid() {
		$this->assertTrue($this->emprunteur->isValid());
	}


	public function testGetNbReservationsReturnsThree() {
		$this->assertEquals(3, $this->emprunteur->getNbReservations());
	}


	public function testGetNbEmpruntsReturnsFour() {
		$this->assertEquals(4, $this->emprunteur->getNbEmprunts());
	}

	public function testGetNbPretsRetardReturnsTwo() {
		$this->assertEquals(2, $this->emprunteur->getNbPretsEnRetard());
	}


	public function testGetUserInformationsPopupUrlReturnsNull() {
		$this->assertEquals(null, $this->emprunteur->getUserInformationsPopupUrl(
																Class_Users::getLoader()->newInstance()
																	->setLogin('tintin')
																	->setPassword('1234')));
	}

	public function testGetEmpruntsOfTintin() {
		$liste_prets = new EmprListerEntiteResponse();
		$liste_prets->EmprListerEntiteResult = new RspEmprListerEntite();
		$liste_retards = $liste_prets;

		$this->search_client
			->whenCalled('EmprListerEntite')
			->willDo(function() use ($liste_prets, $liste_retards) { 
					$this->search_client
						->whenCalled('EmprListerEntite')
						->answers($liste_retards);

					return $liste_retards;
				});


		$this->assertEquals(0, count($this->opsys->getEmpruntsOf($this->emprunteur)));;
	}


	public function testGetReservationsOfTintin() {
		$liste_reservations = new EmprListerEntiteResponse();
		$liste_reservations->EmprListerEntiteResult = new RspEmprListerEntite();

		$this->search_client
			->whenCalled('EmprListerEntite')
			->answers($liste_reservations);

		$this->assertEquals(0, count($this->opsys->getReservationsOf($this->emprunteur)));;
	}
}



class OpsysServiceGetExemplaireFromCacheTestDisponibilite extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();

		$notice_potter = new Class_WebService_SIGB_Notice('potter');
		for ($i = 0; $i <= 2; $i++) {
			$ex = new Class_WebService_SIGB_Exemplaire("$i");
			$ex->setDisponibiliteLibre();
			$ex->setCodeBarre("$i");
			$notice_potter->addExemplaire($ex);
		}

		$recuperer_notice_res = $this->getMock('RecupererNoticeResponse',
																					 array('createNotice'));
		$recuperer_notice_res
			->expects($this->once())
			->method('createNotice')
			->will($this->returnValue($notice_potter));

		$this->search_client
			->whenCalled('RecupererNotice')
			->answers($recuperer_notice_res);
	}


	public function testFirstExemplaireDisponible(){
		$exemplaire = $this->opsys->getExemplaire("potter", "0");
		$this->assertEquals("Disponible",$exemplaire->getDisponibilite());
		$this->assertTrue($exemplaire->isValid());
	}

	public function testSecondExemplaireDisponible(){
		$exemplaire = $this->opsys->getExemplaire("potter", "1");
		$this->assertEquals("Disponible",$exemplaire->getDisponibilite());
		$this->assertTrue($exemplaire->isValid());
	}

	public function testThirdExemplaireDisponible(){
		$exemplaire = $this->opsys->getExemplaire("potter", "2");
		$this->assertEquals("Disponible",$exemplaire->getDisponibilite());
		$this->assertTrue($exemplaire->isValid());
	}

	public function testFourthExemplaireIndisponible(){
		$exemplaire = $this->opsys->getExemplaire("potter", "3");
		$this->assertEquals("Indisponible", $exemplaire->getDisponibilite());
		$this->assertFalse($exemplaire->isValid());
	}
}



class OpsysServiceTestSupprimerReservation extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();

		$this->resa_response = new EmprReserverResponse();
		$this->resa_response->EmprReserverResult = new RspEmprAction();

		$this->search_client
			->whenCalled('EmprSupprResa')
			->willDo(function($param) {
					$this->assertEquals($param,	new EmprSupprResa('guid_12345', 'res_2345'));
					$this->search_client
						->whenCalled('EmprSupprResa')
						->willDo(function() {$this->fait('EmprSupprResa ne devrait être appelé qu\'une fois');});
					return $this->resa_response;
				});
	}


	public function testEmprSupprResaDataSuccessful() {
		$this->resa_response->EmprReserverResult->Reussite = "true";

		$result = $this->opsys->supprimerReservation(
								Class_Users::getLoader()->newInstance()
									->setLogin('tintin')
									->setPassword('pass'),
								'res_2345'
							);

		$this->assertEquals(array('statut' => 1, 'erreur' =>''), $result);
	}


	public function testEmprSupprResaDataError() {
		$this->resa_response->EmprReserverResult->Reussite = "false";
		$this->resa_response->EmprReserverResult->ErreurService = new WebSrvErreur();
		$this->resa_response->EmprReserverResult->ErreurService->LibelleErreur = 'Document non trouvé';

		$result = $this->opsys->supprimerReservation(
								Class_Users::getLoader()->newInstance()
									->setLogin('tintin')
									->setPassword('pass'),
								'res_2345'
							);

		$this->assertEquals(array('statut' => 0, 'erreur' =>'Document non trouvé'), $result);
	}
}


class OpsysServiceTestUpdateInfoEmprunteur extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();

		$this->florence = new Class_WebService_SIGB_Emprunteur('00123', 'Florence Couvreur');
		$this->florence
			->setNom('Couvreur-Neu')
			->setPrenom('Flo')
			->setEmail('flo@astrolabe.fr')
			->setPassword('amstramgram')
			->setService($this->opsys);

		/* Infos lecteur  */
		$sous_champ_nom = new ImportSousChamp();
		$sous_champ_nom->Etiquette = '100$a';
		$sous_champ_nom->_ = 'Couvreur-Neu';

		$sous_champ_prenom = new ImportSousChamp();
		$sous_champ_prenom->Etiquette = '100$b';
		$sous_champ_prenom->_ = 'Flo';

		$champ_info_lecteur = new ImportChamp();
		$champ_info_lecteur->Etiquette = '100';
		$champ_info_lecteur->SousChamps = array($sous_champ_nom, $sous_champ_prenom);

		/* Téléphone, mail  */
		$sous_champ_email = new ImportSousChamp();
		$sous_champ_email->Etiquette = '115$e';
		$sous_champ_email->_ = 'flo@astrolabe.fr';

		$champ_telephone = new ImportChamp();
		$champ_telephone->Etiquette = '115';
		$champ_telephone->SousChamps = array($sous_champ_email);

		/* Autres  */
		$sous_champ_password = new ImportSousChamp();
		$sous_champ_password->Etiquette = '120$a';
		$sous_champ_password->_ = 'amstramgram';

		$champ_autres = new ImportChamp();
		$champ_autres->Etiquette = '120';
		$champ_autres->SousChamps = array($sous_champ_password);

		/* Notice lecteur */
		$notice = new MaNotice();
		$notice->GUIDSession = 'guid_12345';
		$notice->Champs = array($champ_info_lecteur, $champ_telephone, $champ_autres);


		$expected_ecrire_notice = new EcrireNotice();
		$expected_ecrire_notice->CouE = 'E';
		$expected_ecrire_notice->CodeGrille = 'AFI';
		$expected_ecrire_notice->NumNotice = '00123';
		$expected_ecrire_notice->paramModifNotice = $notice;

		$this->ecrire_notice_response = new EcrireNoticeResponse();
		$this->ecrire_notice_response->EcrireNoticeResult = new MaNotice();

		$this->catalog_client
			->whenCalled('EcrireNotice')
			->with($expected_ecrire_notice)
			->answers($this->ecrire_notice_response);
	}

	public function testSaveWihtNoErrorsDoNotRaiseErrors() {
		try {
			$this->florence->save();
			$this->assertTrue(true);
		} catch (Exception $e) {
			$this->fail();
		}
	}

	public function testSaveWithErrorRaisesException() {
		$error = new WebSrvErreur();
		$error->CodeErreur = '10002';
		$error->LibelleErreur = "CSENRNOTICE, Un code grille vide n'est pas autorisé en création de notice";
		$error->DetailErreur = null;
		$this->ecrire_notice_response->EcrireNoticeResult->ErreurService = $error;

		try {
			$this->florence->save();
		} catch (Exception $e) {
			$this->assertEquals("(10002) CSENRNOTICE, Un code grille vide n'est pas autorisé en création de notice",
													$e->getMessage());
			return;
		}
		$this->fail();
	}
}


class OpsysServiceTestProlongerPret extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();

		$this->empr_response = new EmprProlongResponse();
		$this->empr_response->EmprProlongResult = new RspEmprAction();

		$this->search_client
			->whenCalled('EmprProlong')
			->with(new EmprProlong('guid_12345', 'pret_12'))
			->answers($this->empr_response);
	}


	public function testEmprProlongDataSuccessful() {
		$this->empr_response->EmprProlongResult->Reussite = "true";
		$this->empr_response->EmprProlongResult->MessageRetour = '1 prolongation effectuée';

		$result = $this->opsys->prolongerPret(
															Class_Users::getLoader()->newInstance()
																->setLogin('tintin')
																->setPassword('pass'),
															'pret_12'
														);
		$this->assertEquals(array('statut' => 1, 'erreur' => ''), $result);
	}


	public function testEmprProlongNotDone() {
		// Aloes retourne "true" alors qu'aucune prolongation n'a été faite.... Dans ce cas
		// je veux que le retour soit quand même considéré comme une erreur
		$this->empr_response->EmprProlongResult->Reussite = "true";
		$this->empr_response->EmprProlongResult->MessageRetour = 'Aucune prolongation effectuée !';

		$result = $this->opsys->prolongerPret(
															Class_Users::getLoader()->newInstance()
																->setLogin('tintin')
																->setPassword('pass'),
															'pret_12'
														);
		$this->assertEquals(array('statut' => 0, 
															'erreur' => 'La prolongation de ce document est impossible'), 
												$result);
	}


	public function testEmprProlongEmptyMessage() {
		// Aloes ne retourne parfois pas de message lorsque la prolongation a échoué
		$this->empr_response->EmprProlongResult->Reussite = "true";
		$this->empr_response->EmprProlongResult->MessageRetour = '';

		$result = $this->opsys->prolongerPret(
															Class_Users::getLoader()->newInstance()
																->setLogin('tintin')
																->setPassword('pass'),
															'pret_12'
														);
		$this->assertEquals(array('statut' => 0, 
															'erreur' => 'La prolongation de ce document est impossible'), 
												$result);
	}


	public function testEmprProlongDataError() {
		$this->empr_response->EmprProlongResult->Reussite = "false";
		$this->empr_response->EmprProlongResult->ErreurService = new WebSrvErreur();
		$this->empr_response->EmprProlongResult->ErreurService->LibelleErreur = 'Prêt inexistant';

		$result = $this->opsys->prolongerPret(Class_Users::getLoader()->newInstance()
																->setLogin('tintin')
																->setPassword('pass'), 'pret_12');
		$this->assertEquals(array('statut' => 0, 'erreur' => 'Prêt inexistant'), $result);
	}
}



class OpsysServiceRecupererNoticeResponseTestCreateNotice extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$dispo_reserve = new DonneeFille();
		$dispo_reserve->NomDonnee = "Disponibilité";
		$dispo_reserve->ValeurDonnee = "Non disponible";

		$section_enfant = new DonneeFille();
		$section_enfant->NomDonnee = "Section";
		$section_enfant->ValeurDonnee = "Espace Enfant";

		$code_barre_potter = new DonneeFille();
		$code_barre_potter->NomDonnee = "Code barre exemplaire";
		$code_barre_potter->ValeurDonnee = "1234";

		$piege_manquant = new DonneeFille();
		$piege_manquant->NomDonnee = "Piège";
		$piege_manquant->ValeurDonnee = "Manquant";


		$potter = new NoticeFille();
		$potter->NumFille = "potter";
		$potter->Reservable = "true";
		$potter->DonneesFille = new StdClass();
		$potter->DonneesFille->DonneeFille = array($section_enfant, $dispo_reserve, $code_barre_potter, $piege_manquant);

		$dispo_empty = new DonneeFille();
		$dispo_empty->NomDonnee = "Disponibilité";
		$dispo_empty->ValeurDonnee = "";

		$section_adulte = new DonneeFille();
		$section_adulte->NomDonnee = "Section";
		$section_adulte->ValeurDonnee = "Espace Adultes";

		$code_barre_scrap = new DonneeFille();
		$code_barre_scrap->NomDonnee = "Code barre";
		$code_barre_scrap->ValeurDonnee = "5678";

		$cote_scrap = new DonneeFille();
		$cote_scrap->NomDonnee = "Cote";
		$cote_scrap->ValeurDonnee = "SCRAP";


		$scrap = new NoticeFille();
		$scrap->NumFille = "scrap";
		$scrap->Reservable = "false";
		$scrap->DonneesFille = new StdClass();
		$scrap->DonneesFille->DonneeFille = array($dispo_empty, $section_adulte, $code_barre_scrap, $cote_scrap);

		$rsp = new RecupererNoticeResponse();
		$rsp->RecupererNoticeResult = new RspRecupererNotice();
		$rsp->RecupererNoticeResult->Notice = new DetailNotice();
		$rsp->RecupererNoticeResult->Notice->Reservable = "true";
		$rsp->RecupererNoticeResult->Notice->Exemplaires = new	ListeNoticesFilles();
		$rsp->RecupererNoticeResult->Notice->Exemplaires->NoticesFilles = new StdClass();
		$rsp->RecupererNoticeResult->Notice->Exemplaires->NoticesFilles->NoticeFille = array($potter, $scrap);

		$this->response = $rsp;
	}

	public function testPotterNonDispo() {
		$notice = $this->response->createNotice();
		$potter = $notice->exemplaireAt(0);

		$this->assertEquals("potter", $potter->getId());
		$this->assertTrue($potter->isReservable());
		$this->assertEquals("Non disponible (Manquant)", $potter->getDisponibilite());
	}


	public function testScrapDispoLibre() {
		$this->response->RecupererNoticeResult->Notice->Reservable = "false";
		$notice = $this->response->createNotice();

		$scrap = $notice->exemplaireAt(1);
		$this->assertEquals("scrap", $scrap->getId());
		$this->assertFalse($scrap->isReservable());
		$this->assertEquals("Disponible", $scrap->getDisponibilite());
	}


	/** @test */
	public function coteScrapShouldBeSCRAP() {
		$this->assertEquals('SCRAP', $this->response->createNotice()->exemplaireAt(1)->getCote());
	}


	public function testCodeBarre(){
		$notice = $this->response->createNotice();
		$this->assertEquals("1234", $notice->exemplaireAt(0)->getCodeBarre());
		$this->assertEquals("5678", $notice->exemplaireAt(1)->getCodeBarre());
	}
}




class OpsysServiceEmprReserverResponseTest extends PHPUnit_Framework_TestCase {
	private $default_rsp;

	public function setUp(){
		$this->default_rsp = new EmprReserverResponse();
		$this->default_rsp->EmprReserverResult = new RspEmprAction();
	}

	public function testSuccessfulResponse(){
		$success = $this->default_rsp;
		$success->EmprReserverResult->Reussite = "true";

		$this->assertEquals(array("statut" => 1, "erreur" => ""),
												$success->getReussite());
	}

	public function testErrorResponse(){
		$error = $this->default_rsp;
		$error->EmprReserverResult->ErreurService = new WebSrvErreur();
		$error->EmprReserverResult->ErreurService->LibelleErreur = "Document déjà réservé";
		$this->assertEquals(array("statut" => 0, "erreur" => "Document déjà réservé"),
												$error->getReussite());
	}
}


class OpsysServiceEmprunteurAttributesTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$this->opsys_service = $this->getMock('Mock_OpsysService',
																					array('getEmpruntsOf',
																								'getReservationsOf'));

		$this->emprunteur = new Class_WebService_SIGB_Emprunteur('123', 'Jean');
		$this->emprunteur->setEmail('jean@gmail.com');
		$this->emprunteur->setService($this->opsys_service);
		$this->emprunteur->setNbPretsEnRetard(5);
	}

	public function testID(){
		$this->assertEquals('123', $this->emprunteur->getId());
	}

	public function testEmail(){
		$this->assertEquals('jean@gmail.com', $this->emprunteur->getEmail());
	}


	public function testReservations(){
		$resaPotter = new Class_WebService_SIGB_Reservation('24', new Class_WebService_SIGB_Exemplaire('potter'));
		$resaGandalf = new Class_WebService_SIGB_Reservation('36', new Class_WebService_SIGB_Exemplaire('gandalf'));

		$this->opsys_service
			->expects($this->once())
			->method('getReservationsOf')
			->with($this->logicalAnd($this->attributeEqualTo('_id', 123),
															 $this->attributeEqualTo('_name', 'Jean'),
															 $this->isInstanceOf('Class_WebService_SIGB_Emprunteur')))
			->will($this->returnValue(array($resaPotter, $resaGandalf)));

		$this->assertEquals(2, $this->emprunteur->getNbReservations());


		$resaAlice = new Class_WebService_SIGB_Reservation('48', new Class_WebService_SIGB_Exemplaire('alice'));
		$this->emprunteur->reservationsAddAll(array($resaAlice));

 		$this->assertEquals(3, $this->emprunteur->getNbReservations());

		$this->assertEquals($resaGandalf, $this->emprunteur->getReservationAt(1));
	}


	public function testEmprunts(){
		$empruntPotter = new Class_WebService_SIGB_Emprunt('24', new Class_WebService_SIGB_Exemplaire('potter'));
		$empruntPotter->setEnRetard(false);

		$this->opsys_service
			->expects($this->once())
			->method('getEmpruntsOf')
			->with($this->logicalAnd($this->attributeEqualTo('_id', 123),
															 $this->attributeEqualTo('_name', 'Jean'),
															 $this->isInstanceOf('Class_WebService_SIGB_Emprunteur')))
			->will($this->returnValue(array($empruntPotter)));

		$this->assertEquals(1, $this->emprunteur->getNbEmprunts());

		//pour vérifier que le service n'est pas appelé 2 fois
		$this->assertEquals(1, $this->emprunteur->getNbEmprunts());

		$this->assertEquals($empruntPotter, $this->emprunteur->getEmpruntAt(0));

		$empruntMillenium = new Class_WebService_SIGB_Emprunt('34', new Class_WebService_SIGB_Exemplaire('millenium'));
		$empruntMillenium->setEnRetard(true);

		$this->emprunteur->empruntsAddAll(array($empruntMillenium));

		$this->assertEquals(2, $this->emprunteur->getNbEmprunts());
		$this->assertEquals(1, $this->emprunteur->getNbPretsEnRetard());
	}
}


class OpsysServiceReservationAttributesTest extends PHPUnit_Framework_TestCase {
	public function setUp(){
		$this->reservation = new Class_WebService_SIGB_Reservation('23', new Class_WebService_SIGB_Exemplaire('potter'));
		$this->reservation->parseExtraAttributes(array(
																									 'Etat' => 'Venir chercher'));
	}

	public function testRangDefaultToOne() {
		$this->assertEquals(1, $this->reservation->getRang());
	}

	public function testSetRang(){
		$this->reservation->setRang(3);
		$this->assertEquals(3, $this->reservation->getRang());
	}

	public function testEtat(){
		$this->assertEquals('Venir chercher', $this->reservation->getEtat());
	}
}


class EmpruntFixtures {
	public static function potter(){
		$potter = new Class_WebService_SIGB_Emprunt('12', new Class_WebService_SIGB_Exemplaire(123));
		$potter->getExemplaire()->setTitre('Potter');
		$potter->parseExtraAttributes(array(
																				'Dateretourprevue' => '29/10/2010',
																				'Section' => 'Espace jeunesse',
																				'Auteur' => 'JK Rowling',
																				'Bibliotheque' => 'Astrolabe',
																				'N° de notice' => '1234'));
		return $potter;
	}

	public static function alice(){
		$alice = new Class_WebService_SIGB_Emprunt('13', new Class_WebService_SIGB_Exemplaire(456));
		$alice->getExemplaire()->setTitre('Alice');
		$alice->parseExtraAttributes(array(
																			 'Dateretourprevue' => '21/10/2010',
																			 'Section' => 'Espace jeunesse',
																			 'Auteur' => 'Lewis Caroll',
																			 'Bibliothèque' => 'Astrolabe',
																			 'N° de notice' => '5678'));
		return $alice;
	}


	public static function cendrillon(){
		$cendrillon = new Class_WebService_SIGB_Emprunt('14', new Class_WebService_SIGB_Exemplaire(789));
		$cendrillon->getExemplaire()->setTitre('Cendrillon');
		$cendrillon->parseExtraAttributes(array(
																						'Dateretourprevue' => '24/10/2010',
																						'Section' => 'Espace jeunesse',
																						'Auteur' => 'Charles Perrault',
																						'Bibliotheque' => 'Astrolabe',
																						'N° de notice' => '9012'));
		return $cendrillon;
	}
}




class OpsysServiceEmpruntAttributesTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->emprunt = EmpruntFixtures::potter();
	}

	public function testDateRetour(){
		$this->assertEquals('29/10/2010', $this->emprunt->getDateRetour());
	}

	public function testBibliotheque(){
		$this->assertEquals('Astrolabe',
												$this->emprunt->getExemplaire()->getBibliotheque());
	}

	public function testSection(){
		$this->assertEquals('Espace jeunesse',
												$this->emprunt->getExemplaire()->getSection());
	}

	public function testAuteur(){
		$this->assertEquals('JK Rowling',
												$this->emprunt->getExemplaire()->getAuteur());
	}

	public function testRetard(){
		$this->assertTrue($this->emprunt->enRetard());
	}

	public function testNoNotice() {
		$this->assertEquals('1234', $this->emprunt->getExemplaire()->getNoNotice());
	}
}




class OpsysServiceEmpruntRetardAttributesTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->emprunt = EmpruntFixtures::potter();
		$this->emprunt->setEnRetard(true);
	}

	public function testDateRetour(){
		$this->assertEquals('29/10/2010', $this->emprunt->getDateRetour());
	}

	public function testBibliotheque(){
		$this->assertEquals('Astrolabe',
												$this->emprunt->getExemplaire()->getBibliotheque());
	}

	public function testSection(){
		$this->assertEquals('Espace jeunesse',
												$this->emprunt->getExemplaire()->getSection());
	}

	public function testAuteur(){
		$this->assertEquals('JK Rowling',
												$this->emprunt->getExemplaire()->getAuteur());
	}

	public function testRetard(){
		$this->assertTrue($this->emprunt->enRetard());
	}
}




class OpsysServiceEmpruntTestSort extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->opsys_service = $this->getMock('Mock_OpsysService',
																					array('getEmpruntsOf', 'getReservationsOf'));
		$this->opsys_service
			->expects($this->any())
			->method('getEmpruntsOf')
			->will($this->returnValue(array(
																			EmpruntFixtures::cendrillon(),
																			EmpruntFixtures::alice(),
																			EmpruntFixtures::potter()
																			)));

		$this->opsys_service
			->expects($this->any())
			->method('getReservationsOf')
			->will($this->returnValue(array(
																			EmpruntFixtures::alice(),
																			EmpruntFixtures::potter(),
																			EmpruntFixtures::cendrillon(),
																			)));

		$this->emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Yoda');
		$this->emprunteur->setService($this->opsys_service);
	}


	public function testOrderEmprunt(){
		$this->assertEquals($this->emprunteur->getEmpruntAt(0)->getTitre(), 'Alice');
		$this->assertEquals($this->emprunteur->getEmpruntAt(1)->getTitre(), 'Cendrillon');
		$this->assertEquals($this->emprunteur->getEmpruntAt(2)->getTitre(), 'Potter');
	}

	/** @test */
	public function cendrillonBibliothequeShouldBeAstrolabe() {
		$this->assertEquals('Astrolabe', $this->emprunteur->getEmpruntAt(1)->getBibliotheque());
	}

	/** @test */
	public function aliceBibliothequeShouldBeAstrolabe() {
		$this->assertEquals('Astrolabe', $this->emprunteur->getEmpruntAt(0)->getBibliotheque());
	}
}




class OpsysServiceEmprunteurTestPretsEnRetard extends PHPUnit_Framework_TestCase {
	/** @var Class_WebService_SIGB_Emprunteur */
	protected $emprunteur;

	public function setUp() {
		$this->emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Yoda');
		$this->emprunteur->empruntsAddAll(array(
												EmpruntFixtures::cendrillon(),
												EmpruntFixtures::alice(),
												EmpruntFixtures::potter()
											));
	}

	public function testNbPretsEnRetard() {
		$this->assertEquals(3, $this->emprunteur->getNbPretsEnRetard());
		$this->emprunteur->setNbPretsEnRetard(null)
											->getEmpruntAt(1)->setEnRetard(false);
		$this->assertEquals(2, $this->emprunteur->getNbPretsEnRetard());

	}
}



class OpsysServiceTestReserverExemplaire extends OpsysServiceWithSessionTestCase {
	public function setUp() {
		parent::setUp();
		$reserverResponse = new EmprReserverResponse();
		$reserverResponse->EmprReserverResult = new RspEmprAction();
		$reserverResponse->EmprReserverResult->Reussite = "true";

		$this->search_client
			->whenCalled('EmprReserver')
			->with(new EmprReserver('guid_12345', 'cb344', 'melun'))
			->answers($reserverResponse);
	}


	public function testReserverSuccessful() {
		$result = $this->opsys->reserverExemplaire(
															Class_Users::getLoader()->newInstance()
																->setLogin('tintin')
																->setPassword('pass'),
															Class_Exemplaire::getLoader()->newInstanceWithId(12)->setIdOrigine('cb344'),
															'melun');
		$this->assertEquals(array('statut' => 1, 'erreur' => ''), $result);
	}
}




class OpsysServiceTestEntiteEmprWithPret extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
		  ->with(array('code_barres' => 'C0002054291'))
			->answers(Class_Exemplaire::getLoader()->newInstanceWithId(34));


		
		$entite = new EntiteEmp();
		$entite->LibelleDonnee = new StdClass();
		$entite->LibelleDonnee->string = array('No Prêt',
																					  'Code barre',
																					  'Titre',
																					  'Support',
																				    'Section',
																						'Cote',
																						'A rendre le');
		$entite->Donnees = new StdClass();
		$entite->Donnees->Lignes = array($first_pret = new DonneeEmp());
		$first_pret->ValeursDonnees = new StdClass();
		$first_pret->ValeursDonnees->string = array('5486439',
																								'C0002054291',
																								'Petit Grounch à l\'école / Yak Rivais. - l\'Ecole des loisirs, 1988',
																								'Livre',
																								'Fiction jeunesse',
																								'RIV',
																								'27/06/2012');
		$this->emprunts = $entite->getExemplaires('Class_WebService_SIGB_Emprunt');
	}


	/** @test */
	public function empruntsShouldHaveSizeOfOne() {
		$this->assertEquals(1, count($this->emprunts));
		return $this->emprunts[0];
	}


	/**
	 * @depends empruntsShouldHaveSizeOfOne
	 * @test
	 */
	public function exemplaireOPACShouldBeSet($emprunt) {
		$this->assertEquals(34, $emprunt->getExemplaireOpac()->getId());
	}


	/**
	* @depends empruntsShouldHaveSizeOfOne
	* @test
	*/
	public function titreShouldBePetitGrounch($emprunt) {
		$this->assertContains('Petit Grounch', $emprunt->getTitre());
	}


	/** 
	* @test 
	* @depends empruntsShouldHaveSizeOfOne
	*/
	public function dateRetourShouldBe27_06_2012($emprunt) {
		$this->assertEquals('27/06/2012', $emprunt->getDateRetour());
	}
}

?>