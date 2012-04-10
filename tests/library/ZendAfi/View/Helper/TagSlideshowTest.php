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
class ZendAfi_View_Helper_TagSlideshowNullAlbumTest extends PHPUnit_Framework_TestCase {
	private $_helper;

	public function setUp() {
		parent::setUp();
		Class_ScriptLoader::resetInstance();
		$this->_helper = new ZendAfi_View_Helper_TagSlideshow();
		$this->_helper->setView(new Zend_View());
	}

	/** @test */
	public function tagSlideshowShouldRenderEmptyString() {
		$this->assertEquals('', $this->_helper->tagSlideshow(null));
	}


	/** @test */
	public function renderSlideShowScriptsShouldNotRenderScripts() {
		$this->_helper->renderSlideShowScripts();
		$this->assertNotContains('slideshow', Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	public function renderAlbumMediasShouldReturnEmptyString() {
		$this->assertEquals('', $this->_helper->renderAlbumMedias());
	}
}
?>