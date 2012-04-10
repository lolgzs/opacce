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
require_once 'Class/Newsletter.php';

class AdminControllerSitoOKTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		setVar("SITE_OK", 1);
		$this->dispatch('/admin');
	}


	public function testControllerIsIndex() {
		$this->assertController('index');
		$this->assertAction('index');
	}


	public function testMenuUtilisateurInfo() {
		$this->assertQueryContentContains("div.ligne_info", 'sysadmin');
	}
	
	public function testMenuNewsletterLink() {
		$this->assertXPathContentContains("//a[@href='/afi-opac3/admin/newsletter']", 
																			"Lettres d'information");
	}


	/** @test */
	function prettyPhotoCssShouldBeInHead() {
		$this->assertXPath("//head/link[contains(@href, 'css/prettyPhoto.css')]");
	}


	/** @test */
	function jqueryAFIThemeCssShouldBeInHead() {
		$this->assertXPath("//head/link[contains(@href, 'css/jquery.ui.afi.theme.css')]");
	}
}

?>