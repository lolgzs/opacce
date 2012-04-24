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
class ListMetadataFormatsTest extends Storm_Test_ModelTestCase {
	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		$this->_response = new Class_WebService_OAI_Response_ListMetadataFormats('http://afi-sa.fr/oai2/do');
	}


	/** @test */
	public function requestVerbShouldBeListMetadataFormats() {
		$this->_xpath->assertXPathContentContains($this->_response->xml(),
																							'//oai:request[@verb="ListMetadataFormats"]',
																							'http://afi-sa.fr/oai2/do');
	}


	/** @test */
	public function shouldHaveOneMetadataFormat() {
		$this->_xpath->assertXpathCount($this->_response->xml(), 
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
																	Class_WebService_OAI_Response_ListMetadataFormats::OAI_DC_SCHEMA, 
																	1);
	}


	/** @test */
	public function namespaceShouldBeDublinCore() {
		$this->_assertFormatContentAt('metadataNamespace', 
																	Class_WebService_OAI_Response_ListMetadataFormats::OAI_DC_NAMESPACE, 
																	1);
	}


	protected function _assertFormatContentAt($name, $content, $position) {
		$path = sprintf('//oai:ListMetadataFormats/oai:metadataFormat[%s]/oai:%s',
										$position, $name);
		$this->_xpath->assertXpathContentContains($this->_response->xml(), $path,	$content);
	}
}
?>