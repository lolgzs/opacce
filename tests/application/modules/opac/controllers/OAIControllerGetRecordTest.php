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

abstract class OAIControllerGetRecordTestCase extends AbstractControllerTestCase {
	const OAI_RECORD_PATH = '//oai:GetRecord/oai:record/';
	const OAI_HEADER_PATH = 'oai:header/';

	protected $_xpath;
	protected $_xml;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOaiDc();
	}
}


class OAIControllerGetRecordNoIdentifierTest extends OAIControllerGetRecordTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=GetRecord&metadataPrefix=oai_dc');
		$this->_xml = $this->_response->getBody();
	}


	/** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@verb="GetRecord"][@metadataPrefix="oai_dc"]');
	}


	/** @test */
	public function errorCodeShouldBeBadArgument() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:error[@code="badArgument"]');
	}
}


class OAIControllerGetRecordNoMetadataPrefixTest extends OAIControllerGetRecordTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=GetRecord&identifier=toto');
		$this->_xml = $this->_response->getBody();
	}


	/** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@verb="GetRecord"][@identifier="toto"]');
	}


	/** @test */
	public function errorCodeShouldBeBadArgument() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:error[@code="badArgument"]');
	}
}



class OAIControllerGetRecordNotFoundParamsTest extends OAIControllerGetRecordTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('getNoticeByClefAlpha')
			->with('harrypotter-sorciers')
			->answers(null);

		$this->dispatch('/opac/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier=harrypotter-sorciers', true);
		$this->_xml = $this->_response->getBody();
	}


	/** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@verb="GetRecord"][@identifier="harrypotter-sorciers"][@metadataPrefix="oai_dc"]');
	}


	/** @test */
	public function errorCodeShouldBeIdDoesNotExist() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:error[@code="idDoesNotExist"]');
	}
}


class OAIControllerGetRecordNotSupportedPrefixTest extends OAIControllerGetRecordTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('getNoticeByClefAlpha')
			->with('harrypotter-sorciers')
			->answers(Class_Notice::getLoader()
								  ->newInstanceWithId(4)
								  ->setClefAlpha('harrypotter-sorciers')
								  ->setTitrePrincipal('Harry Potter a l\'ecole des sorciers')
								  ->setDateMaj('2001-12-14 11:39:44'));

		$this->dispatch('/opac/oai/request?verb=GetRecord&metadataPrefix=not_supported&identifier=harrypotter-sorciers');		
		$this->_xml = $this->_response->getBody();
	}


  /** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@verb="GetRecord"][@identifier="harrypotter-sorciers"][@metadataPrefix="not_supported"]');
	}


	/** @test */
	public function errorCodeShouldBeCannotDisseminateFormat() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:error[@code="cannotDisseminateFormat"]');
	}
}


class OAIControllerGetRecordValidParamsTest extends OAIControllerGetRecordTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('getNoticeByClefAlpha')
			->with('harrypotter-sorciers')
			->answers(Class_Notice::getLoader()
								  ->newInstanceWithId(4)
								  ->setClefAlpha('harrypotter-sorciers')
								  ->setTitrePrincipal('Harry Potter a l\'ecole des sorciers')
								  ->setDateMaj('2001-12-14 11:39:44'));

		$this->dispatch('/opac/oai/request?verb=GetRecord&metadataPrefix=oai_dc&identifier=harrypotter-sorciers', true);
		$this->_xml = $this->_response->getBody();
	}


	/** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@verb="GetRecord"][@identifier="harrypotter-sorciers"][@metadataPrefix="oai_dc"]');
	}


	/** @test */
	public function requestMetadataPrefixShouldBeOaiDc() {
		$this->_xpath->assertXpath($this->_xml,
															 '//oai:request[@metadataPrefix="oai_dc"]');
	}


	/** @test */
	public function shouldContainOneHeader() {
		$this->_xpath->assertXpathCount($this->_xml,
																		self::OAI_RECORD_PATH . 'oai:header',
																		1);
	}


	/** @test */
	public function headerShouldContainRecordIdentifier() {
		$this->_xpath->assertXPathContentContains($this->_xml,
																							self::OAI_RECORD_PATH . self::OAI_HEADER_PATH . 'oai:identifier',
																							'oai:localhost:harrypotter-sorciers');
	}


	/** @test */
	public function recordHeaderDateStampShouldBe2001DecemberFourteen() {
		$this->_xpath->assertXPathContentContains($this->_xml,
																							self::OAI_RECORD_PATH . self::OAI_HEADER_PATH . 'oai:datestamp',
																							'2001-12-14');
	}


	/** @test */
	public function metadataShouldContainOaiDublinCore() {
		$this->_xpath->assertXPath($this->_xml,
															 self::OAI_RECORD_PATH . 'oai:metadata/oai_dc:dc');
	}


	/** @test */
	public function shouldContainOneMetadata() {
		$this->_xpath->assertXpathCount($this->_xml,
																		self::OAI_RECORD_PATH . 'oai:metadata',
																		1);
	}

}