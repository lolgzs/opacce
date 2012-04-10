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
require_once realpath(dirname(__FILE__)) . '/ViewHelperTestCase.php';

Class TagUploadTest extends ViewHelperTestCase {
	protected $_html;

	public function setUp() {
		parent::setUp();

		$upload = new ZendAfi_View_Helper_TagUpload();
		$upload->setView(new ZendAfi_Controller_Action_Helper_View());
		$this->_html = $upload->TagUpload('carte', 'territoire', 'carte.png');
		
	}


	/** @test */
	public function iframeShouldContainsFileNameCartePng() {
		$this->assertXPath($this->_html,
											 '//iframe[contains(@src, "filename=carte.png&")]',
											 $this->_html);
	}


	/** @test */
	public function iframeSrcShouldContainsPathPhotobib() {
		$this->assertXPath($this->_html,
											 '//iframe[contains(@src, "path=photobib&")]');
	}


	/** @test */
	public function inputNameShouldBeCarte() {
		$this->assertXPath($this->_html,
											 '//input[@name="carte"]');
	}
}

?>