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

abstract class OAIControllerRequestTestCase extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('OAI_SERVER')
			->setValeur('1');	

		$this->_xpath = new Storm_Test_XPathXML();
		$this->_xpath->registerNameSpace('oai', 'http://www.openarchives.org/OAI/2.0/');
	}


	/** @test */
	public function controllerShouldBeOai() {
		$this->assertController('oai');
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




class OAIControllerIndentifyRequestTest extends OaiControllerRequestTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=Identify', true);		
	}


	/** @test */
	public function shouldReturnIdentifyResponse() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
															 '//oai:request[@verb="Identify"]');
	}


	/** @test */
	public function responseShouldNotHaveError() {
		$this->_xpath->assertNotXPath($this->_response->getBody(), 
																	'//oai:error');
	}
}




class OAIControllerIndentifyRequestWithIllegalParameterTest extends OaiControllerRequestTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=Identify&test=test', true);
	}


	/** @test */
	public function shouldReturnErrorBadArgumentWithIllegalParameter() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
															 '//oai:error[@code="badArgument"]');
	}
}




class OAIControllerRequestWithoutOAIEnabledTest extends AbstractControllerTestCase {
	/** @test */
	public function responseShouldBeEmpty() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('OAI_SERVER')
			->setValeur('0');	
		$this->dispatch('/opac/oai/request?verb=Identify');
		$this->assertEmpty($this->_response->getBody());
	}
}




class OaiControllerListSetsRequestTest extends OaiControllerRequestTestCase {
	protected $_xpath;
	 
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('findAllBy')
			->with(array('where' => 'oai_spec is not null',
									 'where' => 'oai_spec !=\'\'',
									 'order' => 'oai_spec'))
			->answers(array());

		$this->dispatch('/opac/oai/request?verb=ListSets');
	}


	/** @test */
	public function shouldReturnListSetsResponse() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
												'//oai:request[@verb="ListSets"]');
	}
}



class OaiControllerGetRecordRequestTest extends OaiControllerRequestTestCase {
	protected $_xpath;
	 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/oai/request?verb=GetRecord&identifier=zork&metadataPrefix=oai_dc');
	}


	/** @test */
	public function shouldReturnGetRecordResponse() {
		$this->_xpath->assertXPath($this->_response->getBody(), 
												'//oai:request[@verb="GetRecord"]');
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




class OAIControllerResultatActionTest extends AbstractControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->mock_sql = Storm_Test_ObjectWrapper::on(Zend_Registry::get('sql'));
		Zend_Registry::set('sql', $this->mock_sql);

		$this->mock_sql
			->whenCalled('fetchOne')
			->with('select count(*) from exemplaires')
			->answers(0)

			->whenCalled('fetchAll')
			->with("select distinct(id_entrepot) from oai_notices", false)
			->answers(array(array('id_entrepot' => 1)))


			->whenCalled('fetchOne')
			->with("select libelle from oai_entrepots where id=1")
			->answers('Gallica')

			->whenCalled('fetchOne')
			->with("Select count(*) from oai_notices where MATCH(recherche) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE)")
			->answers(1)

			->whenCalled('fetchAll')
			->with("select id from oai_notices where MATCH(recherche) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE) order by alpha_titre LIMIT 0,10",
						 false)
			->answers(array(array('id' => 2)))
			->beStrict();

		$pommes = Class_NoticeOAI::getLoader()
			->newInstanceWithId(2)
			->setTitre('Mangez des pommes')
			->setEntrepot(Class_EntrepotOAI::getLoader()
										->newInstanceWithId(3)
										->setLibelle('Gallica'));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_NoticeOAI')
			->whenCalled('findAllBy')
			->with(array('id' => array(2)))
			->answers(array($pommes))
			->beStrict();

		$this->dispatch('/opac/rechercheoai/resultat/expressionRecherche/pomme', true);
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->old_sql);
		parent::tearDown();
	}


	/** @test */
	public function tdShouldContainsMangezDesPommes() {
		$this->assertXPathContentContains('//td', 'Mangez des pommes', $this->_response->getBody());
	}
}



class OAIControllerViewNoticeGallicaTest extends AbstractControllerTestCase  {
	public function setUp() {
		parent::setUp();
		Class_NoticeOAI::getLoader()
			->newInstanceWithId(2)
			->setTitre('Fleurs de nice')
			->setIdOai('http://gallica.bnf.fr/ark://12345')
			->setEntrepot(Class_EntrepotOAI::getLoader()
										->newInstanceWithId(3)
										->setLibelle('Gallica')
										->setHandler('http://oai.bnf.fr'));

		$this->dispatch('/opac/rechercheoai/viewnotice/id/2', true);
	}


	/** @test */
	public function playerGallicaShouldBeEmbedded() {
		$this->assertXPath('//object//param[@name="FlashVars"][contains(@value, "12345")]', $this->_response->getBody());
	}
}


?>