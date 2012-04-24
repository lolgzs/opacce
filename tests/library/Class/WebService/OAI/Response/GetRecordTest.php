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


class OAIGetRecordTest extends Storm_Test_ModelTestCase {
	const OAI_RECORD_PATH = '//oai:GetRecord/oai:record/';
	const OAI_HEADER_PATH = 'oai:header/';

	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		$this->_xpath->registerNameSpace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
		$this->_response = new Class_WebService_OAI_Response_GetRecord('http://afi-sa.fr/oai/do');

		$potter = Class_Notice::getLoader()
			->newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setTitrePrincipal('Harry Potter a l\'ecole des sorciers')
			->setDateMaj('2001-12-14 11:39:44');

		$this->_response->setNotice($potter);
	}


	/** @test */
	public function requestVerbShouldBeGetRecord() {
		$this->_xpath->assertXpath($this->_response->xml(),
															 '//oai:request[@verb="GetRecord"]');
	}


	/** @test */
	public function requestMetadataPrefixShouldBeOaiDc() {
		$this->_xpath->assertXpath($this->_response->xml(),
															 '//oai:request[@metadataPrefix="oai_dc"]');
	}


	/** @test */
	public function headerShouldContainRecordIdentifier() {
		$this->_xpath->assertXPathContentContains($this->_response->xml(),
																							self::OAI_RECORD_PATH . self::OAI_HEADER_PATH . 'oai:identifier',
																							sprintf('http://localhost%s/recherche/notice/harrypotter-sorciers',
																											BASE_URL));
	}


	/** @test */
	public function recordHeaderDateStampShouldBe2001DecemberFourteen() {
		$this->_xpath->assertXPathContentContains($this->_response->xml(),
																							self::OAI_RECORD_PATH . self::OAI_HEADER_PATH . 'oai:datestamp',
																							'2001-12-14');
	}


	/** @test */
	public function metadataShouldContainOaiDublinCore() {
		$this->_xpath->assertXPath($this->_response->xml(),
															 self::OAI_RECORD_PATH . 'oai:metadata/oai_dc:dc');
	}

}