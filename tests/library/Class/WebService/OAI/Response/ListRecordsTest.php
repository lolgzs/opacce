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
class ListRecordsTest extends Storm_Test_ModelTestCase {
	protected $_xpath;
	protected $_response;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOaiDc();
		$this->_response = new Class_WebService_OAI_Response_ListRecords('http://afi-sa.fr/oai2/do');
		$this->_response->setNotices(array(Class_Notice::getLoader()
																			   ->newInstanceWithId(2)
																			   ->setClefAlpha('harrypotter-sorciers')
																			   ->setDateMaj('2001-12-14 11:42:42')
																			   ->setTitrePrincipal('Harry Potter a l\'ecole des sorciers'),
																			 Class_Notice::getLoader()
																			   ->newInstanceWithId(3)
																			   ->setClefAlpha('harrypotter-chambresecrets')
																			   ->setDateMaj('2005-10-24 11:42:42'),
																			 Class_Notice::getLoader()
																			   ->newInstanceWithId(4)
																			   ->setClefAlpha('harrypotter-azkaban')
																			   ->setDateMaj('2012-04-03 11:42:42')));
	}


	/** @test */
	public function requestVerbShouldBeListRecords() {
		$this->_xpath->assertXPathContentContains($this->_response->xml(),
																							'//oai:request[@verb="ListRecords"]',
																							'http://afi-sa.fr/oai2/do');
	}


	/** @test */
	public function shouldHaveThreeRecords() {
		$this->_xpath->assertXPathCount($this->_response->xml(),
																		'//oai:ListRecords/oai:record',
																		3);
	}


	/** @test */
	public function firstIdentifierShouldContainSorciers() {
		$this->_assertHeaderContentAt('identifier', 'sorciers', 1);
	}


	/** @test */
	public function secondIdentifierShouldContainSecrets() {
		$this->_assertHeaderContentAt('identifier', 'secrets', 2);
	}


	/** @test */
	public function thirdIdentifierShouldContainAzkaban() {
		$this->_assertHeaderContentAt('identifier', 'azkaban', 3);
	}


	protected function _assertHeaderContentAt($header, $content, $position) {
		$path = sprintf('//oai:ListRecords/oai:record[%s]/oai:header/oai:%s', 
										$position, $header);
		$this->_xpath->assertXPathContentContains($this->_response->xml(), $path, $content);
	}
}

?>