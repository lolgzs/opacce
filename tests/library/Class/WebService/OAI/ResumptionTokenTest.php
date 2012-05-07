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


class ResumptionTokenTest extends PHPUnit_Framework_TestCase {
	protected $_token;
	protected $_cache;
	protected $_request_params;
	protected $_xpath;
	protected $_builder;

	public function setUp() {
		$this->_request_params = array('from' => '2011-12-25',
																	 'until' => '',
																	 'metadataPrefix' => 'oai_dc',
																	 'set' => 'bande_dessinees');
		$this->_cache = Storm_Test_ObjectWrapper::mock();
		Class_WebService_OAI_ResumptionToken::defaultCache($this->_cache);
		$this->_token = Class_WebService_OAI_ResumptionToken::newWithParamsAndListSize($this->_request_params, 10000);
		$this->_cache
			->whenCalled('save')
			->answers(true)

			->whenCalled('load')
			->answers(false)

			->whenCalled('load')
			->with(md5(serialize($this->_token)))
			->answers(serialize($this->_token));

		$this->_token->save();

		$this->_xpath = new Storm_Test_XPathXML();
		$this->_builder = new Class_Xml_Builder();
	}


	/** @test */
	public function saveShouldSerializeTokenIntoCache() {
		$this->assertEquals($this->_token,
												unserialize($this->_cache->getFirstAttributeForLastCallOn('save')));
	}


	/** @test */
	public function cacheKeyShouldBeMd5sumOfSerializedToken() {
		$this->assertEquals(md5(serialize($this->_token)), 
												end($this->_cache->getAttributesForLastCallOn('save')));
	}


	/** @test */
	public function pageNumberShouldBeOne() {
		$this->assertEquals(1, $this->_token->getPageNumber());
	}


	/** @test */
	public function findByMd5ShouldAnswerToken() {
		$this->assertEquals($this->_token, Class_WebService_OAI_ResumptionToken::find(md5(serialize($this->_token))));
	}


	/** @test */
	public function findByUnknownMd5ShouldAnswerNull() {
		$this->assertEquals(null, Class_WebService_OAI_ResumptionToken::find('Zork!!'));
	}


	/** @test */
	public function defaultCacheShouldBeZendRegistryCache() {
		Class_WebService_OAI_ResumptionToken::defaultCache(null);
		$this->assertEquals(Zend_Registry::get('cache'), $this->_token->getCache());
	}


	/** @test */
	public function renderShouldAnswerXml() {
		$this->_xpath->assertXPathContentContains($this->_token->renderOn($this->_builder),
																							'//resumptionToken[@completeListSize="10000"][@cursor="0"]',
																							md5(serialize($this->_token)));
	}


	/** @test  */
	public function renderNextTokenShouldAnswerXml() {
		$next = $this->_token->next(10);
		$this->_xpath->assertXPathContentContains($next->renderOn($this->_builder),
																							'//resumptionToken[@completeListSize="10000"][@cursor="10"]',
																							md5(serialize($next)));
	}	


	/** @test */
	public function nextPageNumberShouldBeTwo() {
		$this->assertEquals(2, $this->_token->next(10)->getPageNumber());
	}


	/** @test */
	public function getParamOfSetShouldReturnBandeDessinees() {
		$this->assertEquals('bande_dessinees', $this->_token->getParam('set'));
	}

	
	/** @test */
	public function getParamOfUnknownShouldReturnNull() {
		$this->assertNull($this->_token->getParam('Zork'));
	}

}
?>