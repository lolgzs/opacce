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


class OAIControllerListIdentifiersValidTest extends AbstractControllerTestCase {
	protected $_xpath;
	protected $_response;
	protected $_xml;	

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
	
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('countNoticesFor')
			->answers(3)

			->whenCalled('findAllBy')
			->with(array('oai_spec' => 'zork'))
			->answers(array(Class_Catalogue::getLoader()->newInstanceWithId(2)))

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
		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=oai_dc&set=zork');
		$this->_xml = $this->_response->getBody();
	}

	
	/** @test */
	public function controllerShouldBeOai() {
		$this->assertController('oai');
	}


	/** @test */
	public function actionShouldBeRequest() {
		$this->assertAction('list-identifiers');
	}


	/** @test */
	public function xmlShouldBeValid() {
		$dom = new DOMDocument();
		$dom->loadXml($this->_response->getBody());
		$dom->schemaValidate('tests/application/modules/opac/controllers/OAI-PMH.xsd');
	}


	/** @test */
	public function requestVerbShouldBeListIdentifiers() {
		$this->_xpath->assertXPath($this->_xml,
															 '//oai:request[@verb="ListIdentifiers"]');
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
	public function shouldNotHaveResumptionToken() {
		$this->_xpath->assertNotXpath($this->_xml,
																	'//oai:resumptionToken');
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
	public function firstSetSpecShouldBeZork() {
		$this->_assertHeaderContentAt('setSpec', 'zork', 1);
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


class OAIControllerListIdentifiersWithPaginatorTest extends AbstractControllerTestCase {
	protected $_xpath;
	protected $_xml;
	protected $_cache;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('countNoticesFor')
			->answers(10000)

			->whenCalled('findAllBy')
			->with(array('oai_spec' => 'zork'))
			->answers(array(Class_Catalogue::getLoader()->newInstanceWithId(2)))

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
		$this->_cache = Storm_Test_ObjectWrapper::mock()
			->whenCalled('save')
			->answers(true);
		Class_WebService_OAI_ResumptionToken::defaultCache($this->_cache);

		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=oai_dc&set=zork');
		$this->_xml = $this->_response->getBody();
	}


	public function tearDown() {
		Class_WebService_OAI_ResumptionToken::defaultCache(null);
		parent::tearDown();
	}

	
	/** @test */
	public function shouldHaveResumptionToken() {
		$this->_xpath->assertXPath($this->_xml, '//oai:resumptionToken');
	}


	/** @test */
	public function shouldHaveSavedToken() {
		$this->assertTrue($this->_cache->methodHasBeenCalled('save'));
	}
}


class OAIControllerListIdentifiersInvalidParamsTest extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOai();
	}


	public function tearDown() {
		Class_WebService_OAI_ResumptionToken::defaultCache(null);
		parent::tearDown();
	}


	/** @test */
	public function withUnknownFormatErrorCodeShouldBeCannotDisseminateFormat() {
		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=zork');
		$this->_xpath->assertXpath($this->_response->getBody(),
															 '//oai:error[@code="cannotDisseminateFormat"]');
	}


	/** @test */
	public function withUnknownSetErrorCodeShouldBeBadArgument() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->answers(array());
		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=oai_dc&set=' . urlencode('jeunesse:bd'));
		$this->_xpath->assertXPath($this->_response->getBody(), 
															 '//oai:error[@code="badArgument"]');
	}


	/** @test */
	public function withoutNoticesErrorCodeShouldBeNoRecordsMatch() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->with(array('oai_spec' => 'zork'))
			->answers(array(Class_Catalogue::getLoader()->newInstanceWithId(2)));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('countBy')
			->answers(0);

		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=oai_dc&set=zork');
		$this->_xpath->assertXPath($this->_response->getBody(),
															 '//oai:error[@code="noRecordsMatch"]');
	}


	/** @test */
	public function withUnknownResumptionTokenErrorCodeShouldBeBadResumptionToken() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->with(array('oai_spec' => 'zork'))
			->answers(array(Class_Catalogue::getLoader()->newInstanceWithId(2)))
			
			->whenCalled('countNoticesFor')
			->answers(10000);

		$cache = Storm_Test_ObjectWrapper::mock()
			->whenCalled('load')
			->answers(false);
		Class_WebService_OAI_ResumptionToken::defaultCache($cache);
		$this->dispatch('/opac/oai/request?verb=ListIdentifiers&metadataPrefix=oai_dc&set=zork&resumptionToken=Zork');
		$this->_xpath->assertXPath($this->_response->getBody(),
															 '//oai:error[@code="badResumptionToken"]');
	}
}
?>