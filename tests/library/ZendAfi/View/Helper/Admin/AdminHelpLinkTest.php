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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';

class AdminHelpLinkHelperTest extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		$this->helper = new ZendAfi_View_Helper_Admin_HelpLink();
		$this->request = new Zend_Controller_Request_Simple();
		Zend_Controller_Front::getInstance()->setRequest($this->request);
	}


	protected function setControllerAction($controller, $action = '') {
		$this->request->setControllerName($controller);
		$this->request->setActionName($action);

	}


	protected function assertHelpLink($help_id) {
		$html = $this->helper->helpLink();
		$this->assertXPath($html, 
											 "//a[@href='https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=$help_id']");
	}


	/** @test */
	public function helpForZorkShouldReturnEmptyString() {
		$this->setControllerAction('Zork');
		$this->assertEquals('', $this->helper->helpLink());
	}


	/** @test */
	public function helpForProfilMenusIndexShouldReturnFiche3618() {
		$this->setControllerAction('profil', 'menusindex');
		$this->assertHelpLink(3618);
	}


	/** @test */
	public function helpForProfilIndexShouldReturnFiche3612() {
		$this->setControllerAction('profil', 'index');
		$this->assertHelpLink(3612);
	}


	/** @test */
	public function helpForProfilIndexUpperCaseShouldReturnFiche3612() {
		$this->setControllerAction('ProFil', 'inDex');
		$this->assertHelpLink(3612);
	}


	/** @test */
	public function helpForProfilZorkShouldReturnFiche3612() {
		$this->setControllerAction('profil', 'zork');
		$this->assertHelpLink(3612);
	}

	/** @test */
	public function helpForProfilAccueiShouldReturnFiche3614() {
		$this->setControllerAction('profil', 'accueil');
		$this->assertHelpLink(3614);
	}


	/** @test */
	public function helpForCatalogueShouldReturnFiche3613() {
		$this->setControllerAction('catalogue');
		$this->assertHelpLink(3613);
	}


	/** @test */
	public function helpForCatalogueEditShouldReturnFiche3613() {
		$this->setControllerAction('catalogue', 'edit');
		$this->assertHelpLink(3613);
	}


	/** @test */
	public function helpForAccueilKiosqueSouldReturnFiche3651() {
		$this->setControllerAction('accueil', 'kiosque');
		$this->assertHelpLink(3651);
	}


	/** @test */
	public function helpForAccueilIndexSouldReturnEmptyString() {
		$this->setControllerAction('accueil', 'index');
		$this->assertEquals('', $this->helper->helpLink());
	}


	/** @test */
	public function helpForCmsSouldReturnFiche3611() {
		$this->setControllerAction('cms');
		$this->assertHelpLink(3611);
	}


	/** @test */
	public function helpForIndexSouldReturnFiche4037() {
		$this->setControllerAction('index');
		$this->assertHelpLink(4037);
	}


	/** @test */
	public function helpForModuleRechercheViewNoticeSouldReturnFiche3647() {
		$this->setControllerAction('modules', 'recherche');

		$html = $this->helper->helpLink("recherche_viewnotice");
		$this->assertXPath($html, 
											 "//a[@href='https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=3647']");
	}


	/** @test */
	public function helpForModuleRechercheResultatSouldReturnFiche3643() {
		$this->setControllerAction('modules', 'recherche');

		$html = $this->helper->helpLink("recherche_resultat");
		$this->assertXPath($html, 
											 "//a[@href='https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=3643']");
	}
}

?>