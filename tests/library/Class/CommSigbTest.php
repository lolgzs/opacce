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

abstract class CommSigbTestCase extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->comm_sigb = new Class_CommSigb();
		$florence = new stdClass();
		$florence->username			= 'FloFlo';
		$florence->LOGIN				= 'florence';
		$florence->IDABON				= '0123456789';
		$florence->PASSWORD			= 'secret';
		$florence->ID_USER			= 1;
		$florence->ROLE_LEVEL		= 4;
		$florence->ROLE					= "admin_portail";
		$florence->ID_SITE			= 5;
		$florence->confirmed		= true;
		$florence->enabled			= true;
		ZendAfi_Auth::getInstance()->getStorage()->write($florence);
		$this->florence = $florence;

		$this->userModel = Class_Users::getLoader()->newInstanceWithId(1)
			->setLogin('florence')
			->setPassword('secret')
			->setIdSite(5)
			->setIdabon('0123456789');

		$this->_old_zend_cache = Zend_Registry::get('cache');
		Zend_Registry::set('cache', $this->zend_cache = Storm_Test_ObjectWrapper::mock());
		$this->zend_cache
			->whenCalled('test')->answers(false)
			->whenCalled('remove')->answers(true)
			->whenCalled('save')->answers(true);
	}


	public function tearDown() {
		Zend_Registry::set('cache', $this->_old_zend_cache);
		parent::tearDown();
	}


	/** @test */
	public function getDispoExemplairesShouldReturnAnArrayWithPotterInfos() {
		$potter = new Class_WebService_SIGB_Exemplaire('123');
		$potter
			->setDisponibilite('En pret')
			->setReservable(true)
			->setDateRetour('17/02/1978');

		$this->assertTrue($potter->isValid());

		$lord_of_rings = new Class_WebService_SIGB_Exemplaire('456');
		$lord_of_rings
			->setDisponibilitePilonne()
			->setReservable(false);


		$alice_merveilles = new Class_WebService_SIGB_Exemplaire('789');
		$alice_merveilles
			->setDisponibilite('Disponible')
			->setDisponibiliteLabel('Sur demande calligraphiee')
			->setReservable(false)
			->setCodeAnnexe(8)
			->setDateRetour('')
			->setCote('ALI MER')
			->setEmplacement('2');


		$potter_invalide = new Class_WebService_SIGB_Exemplaire(null);
		$this->assertFalse($potter_invalide->isValid());

		$potter_pilonne = new Class_WebService_SIGB_Exemplaire('999');
		$potter_pilonne->setDisponibilitePilonne();
		$this->assertTrue($potter_pilonne->isPilonne());


		$potter_invisible = new Class_WebService_SIGB_Exemplaire('0001');
		$potter_invisible->setVisibleOpac(false);
		$this->assertFalse($potter_invisible->isVisibleOpac());


		$map = array(array('123', 'ABC', $potter),
								 array('456', 'LOR', $lord_of_rings),
								 array('789', 'ALM', $alice_merveilles),
								 array('666', 'PTI', $potter_invalide),
								 array('999', 'PTP', $potter_pilonne),
								 array('0001', 'PTIO', $potter_invisible));

		$this->mock_service
			->expects($this->any())
			->method('getExemplaire')
			->will($this->returnValueMap($map));


		$this->assertEquals(array(array('id_origine' => '123',
																		'code_barres' => 'ABC',
																		'id_bib' => 5,
																		'dispo' => 'En pret',
																		'reservable' => true,
																		'date_retour' => '17/02/1978',
																		'id_exemplaire' => '123',
																		'cote' => 'POT',
																		'emplacement' => '1'),
															
															array('id_origine' => '789',
																		'code_barres' => 'ALM',
																		'id_bib' => 8,
																		'annexe' => 8,
																		'dispo' => 'Sur demande calligraphiee',
																		'reservable' => false,
																		'date_retour' => '',
																		'id_exemplaire' => '789',
																		'cote' => 'ALI MER',
																		'emplacement' => '2'),

															array('id_origine' => '666',
																		'code_barres' => 'PTI',
																		'id_bib' => 5,
																		'dispo' => 'non connue',
																		'reservable' => false)),

												$this->comm_sigb->getDispoExemplaires(array(array('id_origine' => '123',
																																					'code_barres' => 'ABC',
																																					'id_bib' => 5,
																																					'cote' => 'POT',
																																					'emplacement' => '1'),

																																		array('id_origine' => '456',
																																					'code_barres' => 'LOR',
																																					'id_bib' => 5),

																																		array('id_origine' => '789',
																																					'code_barres' => 'ALM',
																																					'id_bib' => 5,
																																					'annexe' => 5,
																																					'cote' => 'A',
																																					'emplacement' => '9'),

																																		array('id_origine' => '666',
																																					'code_barres' => 'PTI',
																																					'id_bib' => 5),

																																		array('id_origine' => '999',
																																					'code_barres' => 'PTP',
																																					'id_bib' => 5),

																																		array('id_origine' => '0001',
																																					'code_barres' => 'PTIO',
																																					'id_bib' => 5))));
	}


	public function createMockForService($name) {
		$this->mock_service = $this->getMockBuilder("Class_WebService_SIGB_".$name."_Service")
												->disableOriginalConstructor()
												->getMock();
		$this->mock_service
			->expects($this->any())
			->method('isConnected')
			->will($this->returnValue(true));

		return $this->mock_service;
	}


	/** @test */
	public function reserverExemplaireShouldReturnAnArrayWithPotterInfos() {
		$this->mock_service
			->expects($this->once())
			->method('reserverExemplaire')
			->with($this->userModel, 
						 Class_Exemplaire::getLoader()->newInstanceWithId(123), 
						 'ABC')
			->will($this->returnValue(array('statut' => 1,
																			'erreur' => '')));

		$this->assertEquals(array('statut' => 1,
															'erreur' => ''),
												$this->comm_sigb->reserverExemplaire(5, '123', 'ABC'));

		return $this->zend_cache;
	}


	/** 
	 * @test 
	 * @depends reserverExemplaireShouldReturnAnArrayWithPotterInfos
	 */
	public function reserverExemplaireShouldClearCacheForUser($zend_cache) {
		$this->assertTrue($zend_cache->methodHasBeenCalled('remove'));
	}


	/** @test */
	public function ficheAbonneShouldReturnAnArrayWithEmprunteurFlorence() {
		$emprunteur_florence = Class_WebService_SIGB_Emprunteur::newInstance('0123456789', 'Florence');

		$this->mock_service
			->expects($this->once())
			->method('getEmprunteur')
			->with($this->userModel)
			->will($this->returnValue($emprunteur_florence));


		$this->assertEquals(array('fiche' => $emprunteur_florence),
												$this->comm_sigb->ficheAbonne($this->florence));
		return $emprunteur_florence;
	}


	/** 
	 * @depends ficheAbonneShouldReturnAnArrayWithEmprunteurFlorence
	 * @test 
	 */
	public function ficheAbonneShouldReturnAnArrayWithEmprunteurFlorenceWhenInCache($emprunteur_florence) {
		$this->zend_cache
			->whenCalled('test')->answers(true)
			->whenCalled('load')->answers(serialize($emprunteur_florence));

		$this->assertEquals(array('fiche' => $emprunteur_florence),	$this->comm_sigb->ficheAbonne($this->florence));
	}


	/** @test */
	public function supprimerReservationShouldReturnStatutOK() {
		$this->mock_service
			->expects($this->once())
			->method('supprimerReservation')
			->with($this->userModel, 345)
			->will($this->returnValue(array('statut' => 1,
																			'erreur' => '')));

		$this->assertEquals(array('statut' => 1,
															'erreur' => ''),
												$this->comm_sigb->supprimerReservation($this->florence, 345));
		return $this->zend_cache;
	}


	/** 
	 * @test 
	 * @depends supprimerReservationShouldReturnStatutOK
	 */
	public function supprimerReservationShouldClearCacheForUser($zend_cache) {
		$this->assertTrue($zend_cache->methodHasBeenCalled('remove'));
	}


	/** @test */
	public function prolongerPretShouldReturnStatutOK() {
		$this->mock_service
			->expects($this->once())
			->method('prolongerPret')
			->with($this->userModel, 456)
			->will($this->returnValue(array('statut' => 1,
																			'erreur' => '')));

		$this->assertEquals(array('statut' => 1,
															'erreur' => ''),
												$this->comm_sigb->prolongerPret($this->florence, 456));
		return $this->zend_cache;
	}


	/** 
	 * @test 
	 * @depends prolongerPretShouldReturnStatutOK
	 */
	public function prolongerPretShouldClearCacheForUser($zend_cache) {
		$this->assertTrue($zend_cache->methodHasBeenCalled('remove'));
	}
}



class CommSigbAstrolabeOpsysTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_astro = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://astrolabe.com/opsys.wsdl'))
			->setCommSigb(2);

		Class_WebService_SIGB_Opsys::setService($this->createMockForService('Opsys'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://astrolabe.com/opsys.wsdl',
															"type" => Class_IntBib::COM_OPSYS,
															"id_bib" => 5),
												$this->bib_astro->getModeComm(5));
	}
}




class CommSigbMoulinsVSmartTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_moulins = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://vpn.agglo-moulins.fr/production/'))
			->setCommSigb(4);

		Class_WebService_SIGB_VSmart::setService($this->createMockForService('VSmart'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://vpn.agglo-moulins.fr/production/',
															"type" => Class_IntBib::COM_VSMART,
															'id_bib' => 5),
												$this->bib_moulins->getModeComm(5));
	}
}




class CommSigbMeuseKohaTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_koha = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl'))
			->setCommSigb(5);


		Class_WebService_SIGB_Koha::setService($this->createMockForService('Koha'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://cat-aficg55.biblibre.com/cgi-bin/koha/ilsdi.pl',
															"type" => Class_IntBib::COM_KOHA,
															'id_bib' => 5),
												$this->bib_koha->getModeComm(5));
	}
}



class CommSigbLocalNanookTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_pontault = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://192.168.2.3:9080/afi_Nanook-0.7.5/ilsdi/'))
			->setCommSigb(7);


		Class_WebService_SIGB_Nanook::setService($this->createMockForService('Nanook'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://192.168.2.3:9080/afi_Nanook-0.7.5/ilsdi/',
															"type" => Class_IntBib::COM_NANOOK,
															'id_bib' => 5),
												$this->bib_pontault->getModeComm(5));
	}
}




class CommSigbCarthameTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_ifr = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://ifr.ro/webservices/index.php'))
			->setCommSigb(6);

		Class_WebService_SIGB_Carthame::setService($this->createMockForService('Carthame'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://ifr.ro/webservices/index.php',
															"type" => Class_IntBib::COM_CARTHAME,
															'id_bib' => 5),
												$this->bib_ifr->getModeComm(5));
	}
}



class CommSigbOrpheeTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_stomer = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://213.144.218.252:8080/wsOrphee/service.asmx?WSDL'))
			->setCommSigb(8);

		Class_WebService_SIGB_Orphee::setService($this->createMockForService('Orphee'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://213.144.218.252:8080/wsOrphee/service.asmx?WSDL',
															"type" => Class_IntBib::COM_ORPHEE,
															'id_bib' => 5),
												$this->bib_stomer->getModeComm(5));
	}
}



class CommSigbMicrobibTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_maze = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://80.11.188.93/webservices/ws_maze.wsdl'))
			->setCommSigb(9);

		Class_WebService_SIGB_Microbib::setService($this->createMockForService('Microbib'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://80.11.188.93/webservices/ws_maze.wsdl',
															"type" => Class_IntBib::COM_MICROBIB,
															'id_bib' => 5),
												$this->bib_maze->getModeComm(5));
	}
}





class CommSigbBiblixNetTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_wormhout = Class_IntBib::getLoader()
			->newInstanceWithId(5)
			->setCommParams(array("url_serveur" => 'http://mediathequewormhout.biblixnet.com/exporte_afi'))
			->setCommSigb(10);

		Class_WebService_SIGB_BiblixNet::setService($this->createMockForService('BiblixNet'));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(array("url_serveur" => 'http://mediathequewormhout.biblixnet.com/exporte_afi',
															"type" => Class_IntBib::COM_BIBLIXNET,
															'id_bib' => 5),
												$this->bib_wormhout->getModeComm(5));
	}
}




class CommSigbDynixTest extends CommSigbTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_pc94 = Class_IntBib::newInstanceWithId(5)
			->setCommParams(["url_serveur" => 'http://www.dynix.fr:8080/capcvm/',
											 'client_id' => 'SymWS'])
			->setCommSigb(11);

		Class_WebService_SIGB_Dynix::setService($this->createMockForService('Dynix'));

		$this->mock_service
			->expects($this->any())
			->method('setClientId')
			->will($this->returnValue($this->mock_service));
	}


	/** @test */
	public function getModeCommShouldReturnAnArrayWithCommParams() {
		$this->assertEquals(["url_serveur" => 'http://www.dynix.fr:8080/capcvm/',
												 "type" => Class_IntBib::COM_DYNIX,
												 'client_id' => 'SymWS',
												 'id_bib' => 5],
												$this->bib_pc94->getModeComm(5));
	}
}




class CommSigbWithNotAbonneTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->user = new stdClass();
		$this->user->ID_SITE = 0;
		$this->user->ID_USER = 66;
		$this->user->IDABON = 6;
		ZendAfi_Auth::getInstance()->getStorage()->write($this->user);
		$this->comm_sigb = new Class_CommSigb();
		Class_Users::getLoader()
			->newInstanceWithId(66)
			->setIdSite(0)
			->setIdabon(3);
	}

	/** @test */
	public function prolongerPretShouldReturnError() {
		$this->assertEquals(['erreur' => 'Communication SIGB indisponible'], 
												$this->comm_sigb->prolongerPret($this->user, 0));
	}


	/** @test */
	public function supprimerReservationShouldReturnError() {
		$this->assertEquals(['erreur' => 'Communication SIGB indisponible'], 
												$this->comm_sigb->supprimerReservation($this->user, 0));
	}

	/** @test */
	public function ficheAbonneShouldReturnError() {
		$this->assertEquals(['erreur' => 'Communication SIGB indisponible'], 
												$this->comm_sigb->ficheAbonne($this->user));
	}


	/** @test */
	public function reserverExemplaireShouldReturnError() {
		$this->assertEquals(['erreur' => 'Communication SIGB indisponible'], 
												$this->comm_sigb->reserverExemplaire(0, 0, 0));
	}


	/** @test */
	public function getDispoExemplairesShouldReturnNonReservable() {
		$this->assertEquals([['id' => 2,
													'id_bib' => 0,
													'id_origine' => 0,
													'code_barres' => 0,
													'dispo' => 'non connue',
													'reservable' => false]],
												$this->comm_sigb->getDispoExemplaires([['id' => 2,
																																'id_origine' => 0,
																																'code_barres' => 0,
																																'id_bib' => 0]]));
	}
}


?>