rf<?php
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

abstract class OaiControllerRequestTestCase extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();
		$this->_xpath = new Storm_Test_XPathXML();
		$this->_xpath->registerNameSpace('oai', 'http://www.openarchives.org/OAI/2.0/');
	}


	/** @test */
	public function controllerShouldBeOai() {
		$this->assertController('oai');
	}



	/** @test */
	public function actionShouldBeRequest() {
		$this->assertAction('request');
	}


	/** @test */
	public function xmlVersionShouldOneDotZero() {
		$this->_xpath->assertXmlVersion($this->_response->getBody(), "1.0");
	}


	/** @test */
	public function xmlEncodingShouldBeUtf8() {
		$this->_xpath->assertXmlEncoding($this->_response->getBody(), "UTF-8");
	}
}




class OaiControllerIndentifyRequestTest extends OaiControllerRequestTestCase {
	protected $_xpath;
	 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=Identify');
	}


	/** @test */
	public function shouldReturnIdentifyResponse() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
															 '//oai:request[@verb="Identify"]');
	}
}




class OaiControllerListSetsRequestTest extends OaiControllerRequestTestCase {
	protected $_xpath;
	 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=ListSets');
	}


	/** @test */
	public function shouldReturnListSetsResponse() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
												'//oai:request[@verb="ListSets"]');
	}
}



class OaiControllerUnknownVerbRequestTest extends OaiControllerRequestTestCase {
	protected $_xpath;
	 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=DoASpecialThing');
	}


	/** @test */
	public function shouldReturnErrorResponse() {
		$this->_xpath->assertXpathContentContains($this->_response->getBody(),
																							'//oai:error[@code="badVerb"]',
																							'Illegal OAI verb');
	}

}
?>