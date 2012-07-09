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
}


?>