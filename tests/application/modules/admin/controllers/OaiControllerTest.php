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
require_once 'AdminAbstractControllerTestCase.php';


abstract class Admin_OaiControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$gallica = Class_EntrepotOAI::getLoader()
			->newInstanceWithId(4)
			->setLibelle('Gallica')
			->setHandler('http://oai.bnf.fr/oai2/OAIHandler');

		$open_archive = Class_EntrepotOAI::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Open Archives')
			->setHandler('http://hal.archives-ouvertes.fr/oai/oai.php');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_EntrepotOAI')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array($gallica, $open_archive));
	}
}




class Admin_OaiControllerIndexActionTest extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/oai');
	}


	/** @test */
	public function pageShouldContainsLinkToEditEntrepotGallica() {
		$this->assertXPath('//a[contains(@href, "admin/oai/edit/id/4")]',
											 $this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsLinkToDeleteEntrepotGallica() {
		$this->assertXPath('//a[contains(@href, "admin/oai/delete/id/4")]',
											 $this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsLinkToBrowseEntrepotGallica() {
		$this->assertXPath('//a[contains(@href, "admin/oai/delete/id/4")]',
											 $this->_response->getBody());
	}


	/** @test */
	public function pageShouldContainsSearchForm() {
		$this->assertXPath('//form[contains(@action, "search")][@method="get"]');
	}


	/** @test */
	public function searchFormShouldHaveImputForExpressionRecherche() {
		$this->assertXPath('//form//input[@name="expression"]');
	}
}




class Admin_OaiControllerAddActionTest extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/oai/add');
	}

	
	/** @test */
	public function pageShouldRenderEntrepotForm() {
		$this->assertXPath('//input[@name="libelle"]');
	}
}




class Admin_OaiControllerEditActionTest extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/oai/edit/id/4');
	}

	
	/** @test */
	public function pageShouldRenderEntrepotForm() {
		$this->assertXPath('//input[@name="libelle"][@value="Gallica"]');
	}
}




class Admin_OaiControllerBrowseGallicaActionTest extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$web_client = Storm_Test_ObjectWrapper::mock();
		Class_WebService_OAI::setDefaultWebClient($web_client);

		$web_client
			->whenCalled('open_url')
			->answers(file_get_contents('tests/library/Class/WebService/OAIListSets.xml'));
		$this->dispatch('/admin/oai/browse/id/4');
	}


	/** @test */
	public function h2ShouldContainsParcoursGallica() {
		$this->assertXPathContentContains('//h2', 'Parcours de l\'entrepôt "Gallica"');
	}


	/** @test */
	public function harvestFormShouldContainsSetAfi() {
		$this->assertXPathContentContains('//form//select//option[@value="afi"]', 'Catalogue AFI', $this->_response->getBody());
	}
}




class Admin_OaiControllerImportIsaacAsimovFoundationTest extends Admin_OaiControllerTestCase  {
	protected $_new_album;
	
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')
			->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('save')
			->answers(true);


		$foundation = Class_NoticeOAI::getLoader()
			->newInstanceWithId(23)
			->setData(serialize(array('titre' => 'Prelude to foundation',
																'auteur' => 'Isaac Asimov')));

		$this->dispatch('/admin/oai/import/id/23');

		$this->_new_album = Class_Album::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function newAlbumTitreShouldBePreludeToFoundation() {
		$this->assertEquals('Prelude to foundation', $this->_new_album->getTitre());
	}


	/** @test */
	public function newAlbumAuteurShouldBeIsaacAsimov() {
		$this->assertEquals('Isaac Asimov', $this->_new_album->getAuteur());
	}

}




abstract class Admin_OaiControllerSearchActionTestCase extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->old_sql = Zend_Registry::get('sql');
		$this->mock_sql = Storm_Test_ObjectWrapper::on($this->old_sql);
		Zend_Registry::set('sql', $this->mock_sql);
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->old_sql);
		parent::tearDown();
	}
}



class Admin_OaiControllerSearchInsignifantWordActionTest extends Admin_OaiControllerSearchActionTestCase  {
	//test de non-régression sur les recherches mots cours
	public function setUp() {
		parent::setUp();

		$this->dispatch('/admin/oai/search/expression/te', true);
	}



	/** @test */
	public function pageShouldContainsPasAssezDeMotsSignifications() {
		$this->assertXPathContentContains('//div[@class="error"]', "Il n'y aucun mot assez significatif pour la recherche");
	}
}



?>