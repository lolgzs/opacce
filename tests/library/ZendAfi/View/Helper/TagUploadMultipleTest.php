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

Class TagUploadMultipleTest extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_TreeView */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();
		$this->_helper = new ZendAfi_View_Helper_TagUploadMultiple();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());

		$this->_html = $this->_helper->tagUploadMultiple(
											'MultipleImagesTest',
											'Un libelle de bouton',
											['modelClass' => '', 'modelId' => '']
									);
	}


	/** @test */
	public function triggererButtonShouldBePresent() {
		$this->assertXpath($this->_html,
			                 '//div[contains(@onclick, "load_MultipleImagesTest();")]');
	}


	/** @test */
	public function triggererButtonLabelShouldBeUnLibelleDeBouton() {
		$this->assertXpathContentContains($this->_html,
			                                '//div[@id="menu_itemmass_upload"]',
			                                'Un libelle de bouton');
	}

	/** @test */
	public function containerShouldBePresent() {
		$this->assertXpath($this->_html, '//div[@id="MultipleImagesTest_conteneur"]');
	}


	/** @test */
	public function scriptShouldBePresent() {
		$this->assertXpathContentContains($this->_html, '//script',
																					'function load_MultipleImagesTest()');
	}


	/** @test */
	public function iframeShouldBePresentInScript() {
		$this->assertXpathContentContains($this->_html, '//script', '<iframe id="MultipleImagesTest_iframe"');
	}
}