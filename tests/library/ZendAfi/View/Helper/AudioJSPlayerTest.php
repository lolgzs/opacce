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

class ZendAfi_View_Helper_AudioJsPlayerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var ZendAfi_View_Helper_AudioJsPlayer
	 */
	private $_helper;

	protected function setUp() {
		Class_ScriptLoader::resetInstance();
		$this->_helper = new ZendAfi_View_Helper_AudioJsPlayer();
		$this->_helper->setView(new Zend_View());
		$this->_helper->audioJsPlayer();
	}

	/** @test */
	public function scriptLoaderShouldContainsAudioJS() {
		$this->assertContains('<script src="'.BASE_URL.'/public/admin/js/audiojs/audiojs/audiomin.js?', 
													Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	public function audioJSShouldBeCreated() {
		$this->assertContains('audiojs.createAll', Class_ScriptLoader::getInstance()->html());
	}
}

?>