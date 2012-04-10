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

abstract class ImageViewersOptionsTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_Admin_ImageViewersOptions */
	protected $_helper;

	/** @var string */
	protected $_html;


	public function setUp() {
		$this->_helper = new ZendAfi_View_Helper_Admin_ImageViewersOptions;
		$this->_html = $this->_helper->imageViewersOptions($this->_getPreferences());
	}


	/** @return array */
	protected function _getPreferences() {
		return array();
	}
}


class ImageViewersOptionsBasicTest extends ImageViewersOptionsTestCase {
	/** @test */
	public function htmlShouldIncludeScriptsLinks() {
		$this->assertXPath($this->_html, '//script[contains("jquery", @src)]');
	}


	/** @test */
	public function viewersSelectionShouldBePresent() {
		$this->assertXPath($this->_html, '//select[@name="style_liste"]');
	}


	/** @test */
	function optionBookletShouldBePresent() {
		$this->assertXPath($this->_html, '//select[@name="style_liste"]//option[@value="booklet"]');
	}
}




class ImageViewersOptionsJavascriptDiaporamaTest extends ImageViewersOptionsTestCase {
	/** @return array */
	protected function _getPreferences() {
		return array('style_liste' => 'diaporama',
								  'op_transition' => 'fade',
								  'op_hauteur_boite' => '200',
								  'op_largeur_img' => '600',
								 );
	}


	/** @test */
	public function viewerShouldBeDiaporama() {
		$this->assertXPathContentContains($this->_html, 
																			'//select[@name="style_liste"]//option[@selected="selected"]', 
																			'Diaporama');
	}


	/** @test */
	public function optionTransitionShouldBeZoom() {
		$this->assertXPathContentContains($this->_html, 
																			'//select[@name="op_transition"]//option[@selected="selected"]', 
																			'Transparence');
	}


	/** @test */
	public function hauteurBoiteShouldBePresent() {
		$this->assertXPath($this->_html, '//input[@name="op_hauteur_boite"]');
	}


	/** @test */
	public function largeurImageShouldBePresent() {
		$this->assertXPath($this->_html, '//input[@name="op_largeur_img"]');
	}
}



class ImageViewersOptionsJavascriptBookletTest extends ImageViewersOptionsTestCase {
	/** @return array */
	protected function _getPreferences() {
		return array('style_liste' => 'booklet');
	}


	/** @test */
	public function viewerShouldBeLivre() {
		$this->assertXPathContentContains($this->_html, 
																			'//select[@name="style_liste"]//option[@selected="selected"]', 
																			'Livre');
	}
}