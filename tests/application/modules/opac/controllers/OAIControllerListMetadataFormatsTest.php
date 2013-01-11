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

abstract class OAIControllerListMetadataFormatsTestCase extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
	}
}




class OAIControllerListMetadataFormatsValidTest extends OAIControllerListMetadataFormatsTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=ListMetadataFormats');
	}


	/** @test */
	public function xmlShouldBeValid() {
		$dom = new DOMDocument();
		$dom->loadXml($this->_response->getBody());
		$dom->schemaValidate('tests/application/modules/opac/controllers/OAI-PMH.xsd');
	}


	/** @test */
	public function requestVerbShouldBeListMetadataFormats() {
		$this->_xpath->assertXPath($this->_response->getBody(),
															 '//oai:request[@verb="ListMetadataFormats"]');
	}


	/** @test */
	public function shouldHaveOneMetadataFormat() {
		$this->_xpath->assertXpathCount($this->_response->getBody(), 
																		'//oai:ListMetadataFormats/oai:metadataFormat',
																		1);
	}


	/** @test */
	public function metadataPrefixShouldBeOaiDublinCore() {
		$this->_assertFormatContentAt('metadataPrefix', 'oai_dc', 1);
	}


	/** @test */
	public function schemaShouldbeOaiDublinCore() {
		$this->_assertFormatContentAt('schema', 
																	'http://www.openarchives.org/OAI/2.0/oai_dc.xsd', 
																	1);
	}


	/** @test */
	public function namespaceShouldBeDublinCore() {
		$this->_assertFormatContentAt('metadataNamespace', 
																	'http://www.openarchives.org/OAI/2.0/oai_dc/',
																	1);
	}


	protected function _assertFormatContentAt($name, $content, $position) {
		$path = sprintf('//oai:ListMetadataFormats/oai:metadataFormat[%s]/oai:%s',
										$position, $name);
		$this->_xpath->assertXpathContentContains($this->_response->getBody(), $path,	$content);
	}


	/** @test */
	public function shouldReturnErrorIdDoesNotExist() {
		$this->_xpath->assertNotXPath($this->_response->getBody(),	 '//oai:error');
	}
}




class OAIControllerListMetadataFormatsErrorsTest extends OAIControllerListMetadataFormatsTestCase {
	/** @test */
	public function withWrongIdentifierShouldReturnErrorIdDoesNotExist() {
		$this->dispatch('/opac/oai/request?verb=ListMetadataFormats&identifier=really_wrong_id', true);
		$this->_xpath->assertXPath($this->_response->getBody(),
															 '//oai:error[@code="idDoesNotExist"]');
	}
}
?>