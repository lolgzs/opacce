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

abstract class AdminCatalogueControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->bib_annecy = Class_Bib::getLoader()
			->newInstanceWithId(2)
			->setLibelle('Annecy')
			->setResponsable('Ludivine')
			->setAffZone('')
			->setVille('Annecy')
			->setUrlWeb('http://www.annecy.fr')
			->setMail('jp@annecy.com')
			->setTelephone('04 50 51 32 12')
			->setArticleCategories(array());


		$this->bib_cran = Class_Bib::getLoader()
			->newInstanceWithId(3)
			->setLibelle('Cran-Gévrier');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAll')
			->answers(array($this->bib_annecy, $this->bib_cran));
	}
}


class CatalogueControllerWithModoPortailIndexTest extends AdminCatalogueControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::MODO_PORTAIL;
		$account->ROLE = 'modo_portail';
	}


	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/catalogue/index');
	}


	/** @test */
	function responseToIndexShouldNotBeARedirectToAccueil() {
		$this->assertNotRedirect();
	}


	/** @test */
	public function titreShouldBeDefinitionDesCataloguesDynamiques() {
		$this->assertXPathContentContains('//h1', 'Définition des catalogues dynamiques');
	}


	/** @test */
	public function pageShouldDisplayOAIBaseUrl() {
		$this->assertXPath(sprintf('//input[@class="permalink"][@readonly="true"][@value="http://localhost%s/opac/oai/request"]', 
															 BASE_URL));
	}
}




class CatalogueControllerWithAdminBibAndNoOAIIndexTest extends AdminCatalogueControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ROLE = 'admin_bib';
	}

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('OAI_SERVER')
			->setValeur('0');

		$this->dispatch('admin/catalogue/index');
	}


	/** @test */
	public function titreShouldBeDefinitionDesCataloguesDynamiques() {
		$this->assertXPathContentContains('//h1', 'Définition des catalogues dynamiques');
	}


	/** @test */
	public function pageShouldNotDisplayOAIBaseUrl() {
		$this->assertNotXPath(sprintf('//input[@class="permalink"]'));
	}
}



class CatalogueControllerWithAModoBibIndexTest extends AdminCatalogueControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::MODO_BIB;
		$account->ROLE = 'modo_bib';
	}

	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/catalogue/index');
	}


	/** @test */
	public function pageShouldNotBeDisplayed() {
		$this->assertNotXPathContentContains('//h1', 'Définition des catalogues dynamiques');
	}
}



class CatalogueControllerActionTesterTest extends AdminCatalogueControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::SUPER_ADMIN;
	}

	public function setUp() {
		parent::setUp();

		$catalogue_nouveautes = Class_Catalogue::getLoader()
			->newInstanceWithId(6)
			->setLibelle('nouveautés')
			->setTypeDoc('1;3;4;5')
			->setAnneeDebut(2012)
			->setAnneeFin(2012)
			->setAnnexe(0)
			->setDewey(78308)
			->setBibliotheque(1);

		Class_Matiere::getLoader()
			->newInstanceWithId(78308);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->answers(array(Class_Notice::getLoader()->newInstanceWithId(2)));

		
		$this->dispatch('admin/catalogue/tester/id_catalogue/6');
	}


	/** @test */
	public function pageShouldDisplayRequest() {
		$this->assertContains("select * from notices  where MATCH(facettes) AGAINST(' +(B1) +( D78308*)' IN BOOLEAN MODE) and type_doc IN (1, 3, 4, 5) and annee >= '2012' and annee <= '2012' order by alpha_titre  LIMIT 0,20",
													$this->_response->getBody());
	}


	/** @test */
	public function findAllByRequestShouldHaveSameWhereAsGetRequetes() {
		$params = Class_Notice::getLoader()->getFirstAttributeForLastCallOn('findAllBy');
		$this->assertEquals('MATCH(facettes) AGAINST(\' +(B1) +( D78308*)\' IN BOOLEAN MODE) and type_doc IN (1, 3, 4, 5) and annee >= \'2012\' and annee <= \'2012\'',
												$params['where']);
	}


	/** @test */
	public function findAllByRequestShouldHaveOrderByAlphaTitre() {
		$params = Class_Notice::getLoader()->getFirstAttributeForLastCallOn('findAllBy');
		$this->assertEquals('alpha_titre',
												$params['order']);
	}
	
}




abstract class CatalogueControllerFormTestCase extends AdminCatalogueControllerTestCase {
	protected $_catalogue_adultes;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('OAI_SERVER')
			->setValeur('0');
		

		$this->_catalogue_adultes = Class_Catalogue::getLoader()
			->newInstanceWithId(6)
			->setLibelle('Adultes')
			->setDescription('Mon catalogue')
			->setOaiSpec('adultes')
			->setTypeDoc('1;3;4;5')
			->setAnneeDebut(2012)
			->setAnneeFin(2012)
			->setAnnexe(0)
			->setDewey(78308)
			->setBibliotheque(1);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Catalogue')
			->whenCalled('save')
			->answers(true)

			->whenCalled('delete')
			->answers(true);
	}
}




class CatalogueControllerEditUnknownCatalogueTest extends CatalogueControllerFormTestCase { 
	/** @test */
	public function responseShouldRedirectToPageIndex() {
		$this->dispatch('/admin/catalogue/edit/id_catalogue/1293234');		
		$this->assertRedirectTo('/admin/catalogue/index');
	}
}




class CatalogueControllerEditCatalogueTest extends CatalogueControllerFormTestCase { 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/catalogue/edit/id_catalogue/6');
	}


	/** @test */
	public function inputLibelleShouldContainsAdultes() {
		$this->assertXPath('//input[@name="libelle"][@value="Adultes"]');
	}


	/** @test */
	public function inputAnneDebutShouldContains2012() {
		$this->assertXPath('//input[@name="annee_debut"][@value="2012"]');
	}


	/** @test */
	public function inputAnneFinShouldContains2012() {
		$this->assertXPath('//input[@name="annee_fin"][@value="2012"]');
	}


	/** @test */
	public function inputOAISpecShouldNotExists() {
		$this->assertNotXPath('//input[@name="oai_spec"]');
	}


	/** @test */
	public function textAreaDescriptionShouldBeVisible() {
		$this->assertXPath('//textarea[@name="description"]', 'Mon catalogue');
	}
}




class CatalogueControllerEditCatalogueWithOAIServerTest extends CatalogueControllerFormTestCase { 
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('OAI_SERVER')
			->setValeur('1');

		$this->dispatch('/admin/catalogue/edit/id_catalogue/6');
	}


	/** @test */
	public function inputOAISpecShouldExists() {
		$this->assertXPath('//input[@name="oai_spec"][@value="adultes"]');
	}
}




class CatalogueControllerEditCataloguePostTest extends CatalogueControllerFormTestCase { 
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/catalogue/edit/id_catalogue/6', array('libelle' => 'Jeunes',
																																			'pcdm4' => '5'));
	}


	/** @test */
	public function libelleShouldBeJeunes() {
		$this->assertEquals('Jeunes', $this->_catalogue_adultes->getLibelle());
	}


	/** @test */
	public function catalogueShouldHaveBeenSaved() {
		$this->assertTrue(Class_Catalogue::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function responseShouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/catalogue/index');
	}
}




class CatalogueControllerAddCatalogueTest extends CatalogueControllerFormTestCase { 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/catalogue/add');
	}

	/** @test */
	public function inputDeweyShouldBePresent() {
		$this->assertXPath('//input[@name="dewey"]');
	}


	/** @test */
	public function inputLibelleShouldContainsNouveauCatalogue() {
		$this->assertXPath('//input[@name="libelle"][@value="** nouveau catalogue **"]');
	}
}




class CatalogueControllerAddCataloguePostTest extends CatalogueControllerFormTestCase { 
	protected $_new_catalogue;

	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/catalogue/add', array('libelle' => 'Geeks',
																											'pcdm4' => '5',
																											'annee_debut' => '20',
																											'annee_fin' => '2020'));

		$this->_new_catalogue = Class_Catalogue::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function libelleShouldBeGeeks() {
		$this->assertEquals('Geeks', $this->_new_catalogue->getLibelle());
	}


	/** @test */
	public function catalogueShouldHaveBeenSaved() {
		$this->assertTrue(Class_Catalogue::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function responseShouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/catalogue/index');
	}


	/** @test */
	public function anneeDebutShouldBeEmpty() {
		$this->assertEmpty($this->_new_catalogue->getAnneeDebut());
	}


	/** @test */
	public function anneeFinShouldBeEmpty() {
		$this->assertEmpty($this->_new_catalogue->getAnneeFin());
	}
}



class CatalogueControllerAddCatalogueInvalidePostTest extends CatalogueControllerFormTestCase { 
	protected $_new_catalogue;

	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/catalogue/add', array('libelle' => '',
																											'pcdm4' => '5',
																											'annee_debut' => '2012',
																											'annee_fin' => '2010'));
	}


	/** @test */
	public function pageShouldDisplayErrorsLibelleRequis() {
		$this->assertXPathContentContains('//p[@class="error"]', 'Le libellé est requis');
	} 


	/** @test */
	public function pageShouldDisplayErrorAnneeFinSuperieurAnneeDebut() {
		$this->assertXPathContentContains('//p[@class="error"]', "L'année de début doit être inférieure ou égale à l'année de fin");
	} 
}



class CatalogueControllerDeleteAction extends CatalogueControllerFormTestCase { 
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/catalogue/delete/id_catalogue/6');
	}

	
	/** @test */
	public function responseShouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/catalogue/index');	
	}


	/** @test */
	public function catalogueShouldHaveBeenDeleted() {
		$this->assertTrue(Class_Catalogue::getLoader()->methodHasBeenCalled('delete'));
	}

}

?>