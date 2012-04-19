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


class OAIIdentifyTest extends Storm_Test_ModelTestCase {
	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = new Storm_Test_XPathXML();
		$this->_xpath->registerNameSpace('oai', 'http://www.openarchives.org/OAI/2.0/');
		$this->_response = new Class_WebService_OAI_Response_Identify('http://moulins.fr/oai2/do');
	}


	/** @test */
	public function responseDateShouldBeNow() {
		$this->_xpath->assertXPathContentContains($this->_response->xml(),
																							'//oai:responseDate',
																							date('Y-m-d'));

	}

	
	/** @test */
	public function requestVerbShouldBeIdentify() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:request[@verb="Identify"]',
																							'http://moulins.fr/oai2/do');
	}
}
?>