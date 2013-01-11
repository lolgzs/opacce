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

class ZendAfi_View_Helper_ComboCodificationTest extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_ComboCodification */
	protected $_helper;

	public function setUp() {
		parent::setUp();
		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->_helper = new ZendAfi_View_Helper_ComboCodification();
		$this->_helper->setView($view);

		$this->_html = $this->_helper->comboCodification('type_doc', null);
	}


	/** @test */
	public function selectShouldBePresent() {
		$this->assertXPath($this->_html, '//select[@id="select_type_doc"][@name="type_doc"]');
	}


	/** @test */
	public function allTypesShouldBePresent() {
		$this->assertXPath($this->_html, '//option[@value=""]');
	}


	/** @test */
	public function livreTypeShouldBePresent() {
		$this->assertXPath($this->_html, '//option[@value="' . Class_TypeDoc::LIVRE . '"]');
	}
}