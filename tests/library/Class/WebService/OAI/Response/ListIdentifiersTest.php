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
class ListIdentifiersValidTest extends Storm_Test_ModelTestCase {
	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		$this->_response = new Class_WebService_OAI_Response_ListIdentifiers('http://moulins.fr/oai2/do');
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('loadNoticesFor')
			->answers(array(Class_Notice::getLoader()
											  ->newInstanceWithId(2)
											  ->setClefAlpha('harrypotter-sorciers')
											  ->setDateMaj('2001-12-14 11:42:42'),
											Class_Notice::getLoader()
											  ->newInstanceWithId(3)
											  ->setClefAlpha('harrypotter-chambresecrets')
											  ->setDateMaj('2005-10-24 11:42:42'),
											Class_Notice::getLoader()
											  ->newInstanceWithId(4)
											  ->setClefAlpha('harrypotter-azkaban')
											  ->setDateMaj('2012-04-03 11:42:42')));
		$this->_xml = $this->_response->xml(array('metadataPrefix' => 'oai_dc'));
	}


	/** @test */
	public function requestVerbShouldBeListIdentifiers() {
		$this->_xpath->assertXPathContentContains($this->_xml,
																							'//oai:request[@verb="ListIdentifiers"]',
																							'http://moulins.fr/oai2/do');
	}


	/** @test */
	public function shouldHaveThreeHeaders() {
		$this->_xpath->assertXpathCount($this->_xml,
																		'//oai:ListIdentifiers/oai:header',
																		3);
	}


	/** @test */
	public function shouldNotHaveMetadata() {
		$this->_xpath->assertNotXpath($this->_xml,
																	'//oai:ListIdentifiers/oai:metadata');
	}

	
	/** @test */
	public function firstIdentifierShouldContainSorciers() {
		$this->_assertIdentifierContentAt('sorciers', 1);
	}


	/** @test */
	public function firstDateShouldBeDecemberFourteen2001() {
		$this->_assertDateContentAt('2001-12-14', 1);
	}



	/** @test */
	public function secondIdentifierShouldContainSecrets() {
		$this->_assertIdentifierContentAt('secrets', 2);
	}


	/** @test */
	public function secondDateShouldBeOctoberTwentyfourth2005() {
		$this->_assertDateContentAt('2005-10-24', 2);
	}



	/** @test */
	public function thirdIdentifierShouldContainAzkaban() {
		$this->_assertIdentifierContentAt('azkaban', 3);
	}


	/** @test */
	public function thirdDateShouldBeAprilThird2013() {
		$this->_assertDateContentAt('2012-04-03', 3);
	}



	protected function _assertIdentifierContentAt($content, $position) {
		$this->_assertHeaderContentAt('identifier', $content, $position);
	}


	protected function _assertDateContentAt($content, $position) {
		$this->_assertHeaderContentAt('datestamp', $content, $position);
	}


	protected function _assertHeaderContentAt($header, $content, $position) {
		$path = sprintf('//oai:ListIdentifiers/oai:header[%s]/oai:%s', 
										$position, $header);
		$this->_xpath->assertXPathContentContains($this->_xml, $path, $content);
	}
}


class ListIdentifiersInvalidParamsTest extends Storm_Test_ModelTestCase {
	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		$this->_response = new Class_WebService_OAI_Response_ListIdentifiers('http://moulins.fr/oai2/do');
	}


	public function tearDown() {
		Class_WebService_OAI_ResumptionToken::defaultCache(null);
	}


	/** @test */
	public function withoutMetadataPrefixErrorCodeShouldBeBadArgument() {
		$this->_xpath->assertXPath($this->_response->xml(array()), '//oai:error[@code="badArgument"]');
	}


	/** @test */
	public function withUnknownFormatErrorCodeShouldBeCannotDisseminateFormat() {
			$this->_xpath->assertXpath($this->_response->xml(array('metadataPrefix' => 'zork')),
																 '//oai:error[@code="cannotDisseminateFormat"]');
	}


	/** @test */
	public function withUnknownSetErrorCodeShouldBeBadArgument() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->answers(array());
		$this->_xpath->assertXPath($this->_response->xml(array('metadataPrefix' => 'oai_dc',
																													 'set' => 'jeunesse:bd')), 
															 '//oai:error[@code="badArgument"]');
	}


	/** @test */
	public function withoutNoticesErrorCodeShouldBeNoRecordsMatch() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->answers(array());
		$this->_xpath->assertXPath($this->_response->xml(array('metadataPrefix' => 'oai_dc')),
															 '//oai:error[@code="noRecordsMatch"]');
	}


	/** @test */
	public function withUnknownResumptionTokenErrorCodeShouldBeBadResumptionToken() {
		$cache = Storm_Test_ObjectWrapper::mock()
			->whenCalled('load')
			->answers(false);
		Class_WebService_OAI_ResumptionToken::defaultCache($cache);
		$this->_xpath->assertXPath($this->_response->xml(array('metadataPrefix' => 'oai_dc',
																													 'resumptionToken' => 'Zork')),
															 '//oai:error[@code="badResumptionToken"]');
	}
}
?>