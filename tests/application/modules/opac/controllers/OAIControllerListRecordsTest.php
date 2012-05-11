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

class OAIControllerListRecordsInZorkSetTest extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOaiDc();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->with(array('oai_spec' => 'zork'))
			->answers(array(Class_Catalogue::getLoader()->newInstanceWithId(2)))

			->whenCalled('countNoticesFor')
			->answers(3)

			->whenCalled('loadNoticesFor')
			->answers(array(Class_Notice::getLoader()
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
		$this->dispatch('/opac/oai/request?verb=ListRecords&metadataPrefix=oai_dc&set=zork');
	}


	/** @test */
	public function requestVerbShouldBeListRecords() {
		$this->_xpath->assertXPath($this->_response->getBody(),
																							'//oai:request[@verb="ListRecords"]');
	}


	/** @test */
	public function shouldHaveThreeRecords() {
		$this->_xpath->assertXPathCount($this->_response->getBody(),
																		'//oai:ListRecords/oai:record',
																		3);
	}


	/** @test */
	public function firstIdentifierShouldContainSorciers() {
		$this->_assertHeaderContentAt('identifier', 'sorciers', 1);
	}


	/** @test */
	public function firstSetSpecShouldBeZork() {
		$this->_assertHeaderContentAt('setSpec', 'zork', 1);
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
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), $path, $content);
	}
}


class OAIControllerListRecordsWithoutSetTest extends AbstractControllerTestCase {
	protected $_xpath;
	protected $_xml;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOaiDc();
		$this->dispatch('/opac/oai/request?verb=ListRecords&metadataPrefix=oai_dc');
		$this->_xml = $this->_response->getBody();
	}


	/** @test */
	public function shouldHaveNoRecordsError() {
		$this->_xpath->assertXPath($this->_xml, '//oai:error[@code="noRecordsMatch"]');
	}
}

?>