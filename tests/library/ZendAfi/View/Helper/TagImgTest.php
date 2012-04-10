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
class ZendAfi_View_Helper_TagImgTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var ZendAfi_View_Helper_TagImg
	 */
	private $_helper;

	protected function setUp() {
		$this->_helper = new ZendAfi_View_Helper_TagImg();
		$this->_helper->setView(new Zend_View());
	}

	/** @test */
	public function pathShouldAppearAsSrcAttribute() {
		$path = '/url/de/test.txt';

		$html = $this->_helper->tagImg($path);
		$this->assertGreaterThan(0, preg_match('|src="([^"]*)"|ui', $html, $matches));
		$this->assertEquals($path, $matches[1]);

	}

	/** @test */
	public function pathShouldBeFirstAttribute() {
		$path = '/url/de/test.txt';

		$html = $this->_helper->tagImg($path);
		$this->assertTrue('<img src=' == substr($html, 0, 9), $html);
	}

	/** @test */
	public function noAltShouldCreateEmptyAlt() {
		$html = $this->_helper->tagImg('');
		$this->assertGreaterThan(0, preg_match('|alt="([^"]*)"|ui', $html, $matches));
		$this->assertEquals('', $matches[1]);

	}

	/** @test */
	public function arbitraryAttributeShouldAppearAsIs() {
		$html = $this->_helper->tagImg('', array('monAttrib' => 'any value'));
		$this->assertGreaterThan(0, preg_match('|monAttrib="([^"]*)"|ui', $html, $matches));
		$this->assertEquals('any value', $matches[1]);

	}

}
?>