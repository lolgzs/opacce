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
}




class CatalogueControllerWithAdminBibIndexTest extends AdminCatalogueControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
		$account->ROLE = 'admin_bib';
	}

	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/catalogue/index');
	}


	/** @test */
	public function titreShouldBeDefinitionDesCataloguesDynamiques() {
		$this->assertXPathContentContains('//h1', 'Définition des catalogues dynamiques');
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

?>