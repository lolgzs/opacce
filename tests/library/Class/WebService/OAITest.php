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
require_once 'Class/WebService/OAI.php';

class OAITestGetSets extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$xml_mock = $this->getMock('Class_XMLMock',
																		array('open_url'));
		$xml_mock
			->expects($this->any())
			->method('open_url')
			->with('http://oai.bnf.fr/oai2/OAIHandler?verb=ListSets')
			->will($this->returnValue(
										file_get_contents(dirname(__FILE__).'/OAIListSets.xml')));

		Class_WebService_OAI::setDefaultWebClient($xml_mock);
	  $this->oai_service = new Class_WebService_OAI();
		$this->oai_service->setOAIHandler('http://oai.bnf.fr/oai2/OAIHandler');
	}


	public function testListSetsReturnXMLData() {
		$this->assertEquals(file_get_contents(dirname(__FILE__).'/OAIListSets.xml'),
												$this->oai_service->listSets());
	}


	public function testSetsCountEqualsOneHundred() {
		$this->assertEquals(100,
												count($this->oai_service->getSets()));
	}


	public function testFistSetIsBanqueImages() {
		$sets = $this->oai_service->getSets();
		$this->assertEquals('Banque d\'images',
												$sets['banqueimages']);
	}	
}




class OAITestGetRecordsOfSetGallica extends PHPUnit_Framework_TestCase {
	protected $oai_service;

	public function setUp() {
		$xml_mock = $this->getMock('Class_XMLMock',
															 array('open_url'));
		$xml_mock
			->expects($this->any())
			->method('open_url')
			->with($this->equalTo('http://oai.bnf.fr/oai2/OAIHandler?verb=ListRecords&metadataPrefix=oai_dc&set=gallica'))
			->will($this->returnValue(
										file_get_contents(dirname(__FILE__).'/OAIListRecords.xml')));


		Class_WebService_OAI::setDefaultWebClient($xml_mock);
	  $this->oai_service = new Class_WebService_OAI();
		$this->records = $this->oai_service
			->setOAIHandler('http://oai.bnf.fr/oai2/OAIHandler')
			->getRecordsFromSet('gallica');
	}


	public function testNoticeCountIsOneHundred() {
		$this->assertEquals(100, count($this->records));
	}


	public function testFirstRecordIsVoyageEgypte() {
		$first = $this->records[0];
		$this->assertEquals('http://gallica.bnf.fr/ark:/12148/bpt6k852111',
												$first['id_oai']);
	}


	public function testFifthIsPremieresOeuvres() {
		$premieres_oeuvres = $this->records[4];		
		$this->assertEquals('http://gallica.bnf.fr/ark:/12148/bpt6k701371',
												$premieres_oeuvres['id_oai']);
		$this->assertEquals('Les premières oeuvres de M. Régnier. Au Roy',
												$premieres_oeuvres['titre']);
		$this->assertEquals('Régnier, Mathurin (1573-1613)',
												$premieres_oeuvres['auteur']);
		$this->assertEquals('T. Du Bray (Paris)',
												$premieres_oeuvres['editeur']);
		$this->assertEquals('1608',
												$premieres_oeuvres['date']);
	}


	public function testHasNextRecordsReturnTrue() {
		$this->assertTrue($this->oai_service->hasNextRecords());
	}


	public function testGetTotalNumberOfRecordsReturns980266 () {
		$this->assertEquals(980266, $this->oai_service->getTotalNumberOfRecords());
	}


	public function testNextRecordsUseResumptionTokenAndFetchNextRecords() {
		$xml_mock = $this->getMock('Class_XMLMock',
															 array('open_url'));
		$this->oai_service->setWebClient($xml_mock);
		$xml_mock
			->expects($this->any())
			->method('open_url')
			->with($this->equalTo('http://oai.bnf.fr/oai2/OAIHandler?verb=ListRecords&resumptionToken='.urlencode('2!2!2758354!158!100!980266!oai_dc')))
			->will($this->returnValue(
										file_get_contents(dirname(__FILE__).'/OAIListRecords2.xml')));

		$this->oai_service->getNextRecords();
		$this->assertFalse($this->oai_service->hasNextRecords());
		$this->assertEquals(800, $this->oai_service->getListRecordsResumptionToken()->getCursor());
	}
}

?>
