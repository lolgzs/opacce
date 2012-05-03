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
require_once 'AbstractControllerTestCase.php';

class OAIControllerIdentifyTest extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('getEarliestNotice')
			->answers(Class_Notice::getLoader()
								  ->newInstanceWithId(2)
								  ->setDateMaj('2011-07-11'));
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CosmoVar')
			->whenCalled('find')
			->with('mail_admin')
			->answers(Class_CosmoVar::getLoader()
								  ->newInstanceWithId('mail_admin')
								  ->setValeur('user@server.fr'));
		$this->dispatch('/opac/oai/request?verb=Identify');
	}


	/** @test */
	public function controllerShouldBeOai() {
		$this->assertController('oai');
	}


	/** @test */
	public function actionShouldBeIdentify() {
		$this->assertAction('identify');
	}


	/** @test */
	public function responseDateShouldBeNow() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:responseDate',
																							date('Y-m-d'));
	}

	
	/** @test */
	public function requestVerbShouldBeIdentify() {
		$this->_xpath->assertXpath($this->_response->getBody(),
															 '//oai:request[@verb="Identify"]');
	}


	/** @test */
	public function repositoryNameShouldBeAfiOpac3OaiRepository() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:repositoryName',
																							$_SERVER['SERVER_NAME'] . ' Oai repository');
	}


	/** @test */
	public function baseUrlShouldBeMoulinsDotFr() {
		$this->_xpath->assertXpathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:baseURL',
																							'http://' . $_SERVER['SERVER_NAME'] . BASE_URL . '/opac/oai/request');
	}


	/** @test */
	public function protocolVersionShouldBeTwoDotZero() {
	  $this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:protocolVersion',
																							'2.0');
	}


	/** @test */
	public function earliestDateStampShouldBeJulyEleven2011() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:earliestDatestamp',
																							'2011-07-11');
	}


	/** @test */
	public function granularityShouldBeYearMonthDay() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:granularity',
																							'YYYY-MM-DD');
	}


	/** @test */
	public function deletedRecordShouldBeNo() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:deletedRecord',
																							'no');
	}


	/** @test */
	public function adminEmailShouldBeUserAtServerDotfr() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(),
																							'//oai:Identify/oai:adminEmail',
																							'user@server.fr');
	}

}
?>