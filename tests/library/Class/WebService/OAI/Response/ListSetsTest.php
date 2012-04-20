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

class ListSetsTest extends Storm_Test_ModelTestCase {
	protected $_response;
	protected $_xpath;

	public function setUp() {
		parent::setUp();

		$this->_xpath = TestXPathFactory::newOai();

		$catalogue_bd = Class_Catalogue::getLoader()->newInstanceWithId(1)
			->setLibelle('BDs')
			->setOaiSpec('livres:bd');
		$catalogue_musique = Class_Catalogue::getLoader()->newInstanceWithId(2)
			->setLibelle('Musique')
			->setDescription('La musique qui balance')
			->setOaiSpec('music');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->with(array('where' => 'oai_spec is not null',
									 'where' => 'oai_spec !=\'\'',
									 'order' => 'oai_spec'))
			->answers(array($catalogue_bd, $catalogue_musique));


		$this->_response = new Class_WebService_OAI_Response_ListSets('http://afi.fr/oai');
	}


	/** @test */
	public function setMusiqueShouldContainSpecMusic() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:ListSets/oai:set/oai:setSpec',
																							'music');
	}


	/** @test */
	public function setMusiqueShouldContainNameMusique() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:ListSets/oai:set/oai:setName',
																							'Musique');
	}


  /** @test */
	public function setMusiqueShouldContainDescriptionLaMusiqueQuiBalance() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:ListSets/oai:set/oai:setDescription',
																							'La musique qui balance');
	}



	/** @test */
	public function setBdsShouldContainsSpecLivresColonBds() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:ListSets/oai:set/oai:setSpec',
																							'livres:bd');
	}


	/** @test */
	public function setBdsShouldContainsNameBds() {
		$this->_xpath->assertXpathContentContains($this->_response->xml(),
																							'//oai:ListSets/oai:set/oai:setName',
																							'BDs'); 		
	}


  /** @test */
	public function setBdsShouldNotContainDescription() {
		$this->_xpath->assertNotXpath($this->_response->xml(),
																	'//oai:ListSets/oai:set/oai:setSpec[text()="livres:bd"][following-sibling::oai:setDescription]');
	}

}
?>