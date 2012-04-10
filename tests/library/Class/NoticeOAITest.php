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
require_once 'Class/NoticeOAI.php';
require_once 'Class/WebService/OAI.php';

class MockTableNoticesOAI {
	public function __construct() {
		$this->inserted_data = array();
	}

	public function insertOrUpdate($data) {
		$this->inserted_data []= $data;
	}

	public function getInsertedData() {
		return $this->inserted_data;
	}

	public function getInsertedDataAt($index) {
		return $this->inserted_data[$index];
	}
}


class OAINoticeTestHarverstWithOneRecord extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->mockTableNotices = new MockTableNoticesOAI();
		$this->noticeOAI = new Class_NoticeOAI();
		$this->noticeOAI->setTableNoticesOAI($this->mockTableNotices);

		$this->premieres_oeuvres = array(
										'id_oai' => 'http://gallica.bnf.fr/ark:/12148/bpt6k701371',
										'titre' => 'Les premières oeuvres de M. Régnier. Au Roy',
										'auteur' => 'T. Du Bray (Paris)',
										'date' => '1608',
										'editeur' => 'T. Du Bray (Paris)',
										'description' => '');

		$oai_service = $this->getMock('Class_WebServiceOAI', 
																	array('getRecordsFromSet', 
																				'setOAIHandler', 
																				'getNextRecords',
																				'getListRecordsResumptionToken'));

		$oai_service
			->expects($this->once())
			->method('setOAIHandler')
			->with($this->equalTo('http://oai.bnf.fr/oai2/OAIHandler'))
			->will($this->returnValue($oai_service));

		$oai_service
			->expects($this->once())
			->method('getRecordsFromSet')
			->with($this->equalTo('gallica'))
			->will($this->returnValue(array($this->premieres_oeuvres)));


		$oai_service
			->expects($this->once())
			->method('getListRecordsResumptionToken')
			->will($this->returnValue(new Class_WebService_ResumptionToken()));


		$this->noticeOAI->setOAIService($oai_service);

		$entrepot = new Class_EntrepotOAI();
		$entrepot
			->setId(1)
			->setLibelle('BNF gallica')
			->setHandler('http://oai.bnf.fr/oai2/OAIHandler');

		$this->noticeOAI->harvestSet($entrepot,
																 'gallica');

		$this->inserted_data_in_db = $this->mockTableNotices->getInsertedDataAt(0);
	}

	public function testDateIs1608() {
		$this->assertEquals('1608', 
												$this->inserted_data_in_db['date']);

	}

	public function testIdOaiIsGallica() {
		$this->assertEquals('http://gallica.bnf.fr/ark:/12148/bpt6k701371', 
												$this->inserted_data_in_db['id_oai']);
	}

	public function testAlphaTitre() {
		$this->assertEquals('LES PREMIERES OEUVRES DE M  REGNIER  AU ROY',
												$this->inserted_data_in_db['alpha_titre']);
	}

	public function testIdEntrepot() {
		$this->assertEquals(1,
												$this->inserted_data_in_db['id_entrepot']);
	}

	public function testDataIsFullRecord() {
		$this->assertEquals(addslashes(serialize($this->premieres_oeuvres)),
												$this->inserted_data_in_db['data']);
	}

}


class OAINoticeTestResume extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->noticeOAI = new Class_NoticeOAI();
		$this->entrepot = new Class_EntrepotOAI();
		$this->entrepot
			->setId(1)
			->setLibelle('BNF gallica')
			->setHandler('http://oai.bnf.fr/oai2/OAIHandler');

		$this->oai_service = $this->getMock('Class_WebServiceOAI', 
																				array('getRecordsFromSet', 
																							'setOAIHandler', 
																							'hasNextRecords',
																							'getNextRecords',
																							'getListRecordsResumptionToken',
																							'setListRecordsResumptionToken'));
		$this->oai_service
			->expects($this->once())
			->method('setOAIHandler')
			->with($this->equalTo('http://oai.bnf.fr/oai2/OAIHandler'))
			->will($this->returnValue($this->oai_service));

		$this->noticeOAI->setOAIService($this->oai_service);
	}

	public function testResumptionWhenNoRecordsRemaining() {
		$token = new Class_WebService_ResumptionToken();

		$this->oai_service
			->expects($this->once())
			->method('setListRecordsResumptionToken')
			->will($this->returnValue($this->oai_service));

		$this->oai_service
			->expects($this->once())
			->method('hasNextRecords')
			->will($this->returnValue(false));

		$this->assertEquals(null,
												$this->noticeOAI->resumeHarvest($this->entrepot, $token));
	}


	public function testResumptionWhenRecordsRemaining() {
		$token = new Class_WebService_ResumptionToken();

		$this->oai_service->
			expects($this->once())
			->method('setListRecordsResumptionToken')
			->will($this->returnValue($this->oai_service));

		$this->oai_service
			->expects($this->once())
			->method('hasNextRecords')
			->will($this->returnValue(true));

		$this->oai_service
			->expects($this->once())
			->method('getNextRecords')
			->will($this->returnValue(array()));

		$token = new Class_WebService_ResumptionToken();
		$this->oai_service
			->expects($this->once())
			->method('getListRecordsResumptionToken')
			->will($this->returnValue($token));

		$this->assertEquals($token,
												$this->noticeOAI->resumeHarvest($this->entrepot, $token));
	}
}
?>