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
																'auteur' => 'Isaac Asimov',
																'editeur' => 'Gallimard')))
			->setDate('1982')
			->setIdOai('http://gallica.bnf.fr/ark:/12148/bpt6k86704c')
			->setEntrepot(Class_EntrepotOAI::getLoader()
										->newInstanceWithId(2)
										->setLibelle('BNF:Gallica'));

		$this->dispatch('/admin/oai/import/expression/asimov/id/23');

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


	/** @test */
	public function newAlbumEditeurShouldBeGallimard() {
		$this->assertEquals('Gallimard', $this->_new_album->getEditeur());
	}


	/** @test */
	public function newAlbumAnneeShouldBe1982() {
		$this->assertEquals(1982, $this->_new_album->getAnnee());
	}


	/** @test */
	public function newAlbumTypeDocShouldBeOAI() {
		$this->assertEquals(Class_TypeDoc::OAI, $this->_new_album->getTypeDocId());
	}


	/** @test */
	public function idOrigineShouldBeArk() {
		$this->assertEquals('http://gallica.bnf.fr/ark:/12148/bpt6k86704c', 
												$this->_new_album->getIdOrigine());
	}


	/** @test */
	public function isGallicaShouldAnswerTrue() {
		$this->assertTrue($this->_new_album->isGallica());
	}


	/** @test */
	public function pageShouldRedirectToOaiIndexWithExpressionRecherche() {
		$this->assertRedirectTo('/admin/oai/index/expression/asimov');
	}
}




class Admin_OaiControllerSearchAsimovActionTest extends Admin_OaiControllerTestCase  {
	/** @test */
	public function pageShouldRedirectToOaiIndexWithExpressionRecherche() {
		$this->dispatch('/admin/oai/search?expression=asimov', true);
		$this->assertRedirectTo('/admin/oai/index/expression/asimov');
	}
}




abstract class Admin_OaiControllerSearchActionTestCase extends Admin_OaiControllerTestCase  {
	public function setUp() {
		parent::setUp();
		$this->mock_sql = Storm_Test_ObjectWrapper::on(Zend_Registry::get('sql'));
		Zend_Registry::set('sql', $this->mock_sql);
	}
}




class Admin_OaiControllerIndexWithSearchActionTest extends Admin_OaiControllerSearchActionTestCase  {
	public function setUp() {
		parent::setUp();

		$this->mock_sql
			->whenCalled('fetchOne')
			->with("Select count(*) from oai_notices where MATCH(recherche) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE)")
			->answers(1)

			->whenCalled('fetchAll')
			->with("select id from oai_notices where MATCH(recherche) AGAINST('+(POMME POMMES POM)' IN BOOLEAN MODE) order by alpha_titre LIMIT 0,10",
						 false)
			->answers(array(array('id' => 2)));

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


		$this->dispatch('/admin/oai/index/expression/pommes', true);
	}


	/** @test */
	public function tdShouldContainsMangezDesPommes() {
		$this->assertXPathContentContains('//td', 'Mangez des pommes');
	}


	/** @test */
	public function listItemShouldHaveLinkForImport() {
		$this->assertXPath('//td//a[contains(@href, "oai/import/expression/pommes/id/2")]');
	}
}




class Admin_OaiControllerIndexSearchInsignifantWordActionTest extends Admin_OaiControllerSearchActionTestCase  {
	//test de non-régression sur les recherches mots cours
	public function setUp() {
		parent::setUp();

		$this->dispatch('/admin/oai/index/expression/te', true);
	}



	/** @test */
	public function pageShouldContainsPasAssezDeMotsSignifications() {
		$this->assertXPathContentContains('//div[@class="error"]', "Il n'y aucun mot assez significatif pour la recherche");
	}
}



?>