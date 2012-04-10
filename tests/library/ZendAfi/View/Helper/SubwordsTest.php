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
class ZendAfi_View_Helper_SubwordsTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var ZendAfi_View_Helper_Subwords
	 */
	private $_helper;

	protected function setUp() {
		$this->_helper = new ZendAfi_View_Helper_Subwords();
		$this->_helper->setView(new Zend_View());
	}

	/** @test */
	public function emptyStringShouldNotBeModified() {
		$this->assertEquals('', $this->_helper->subwords('', 23));
	}

	/** @test */
	public function leadingSpacesShouldBeTrimed() {
		$this->assertEquals('Trimed texte', $this->_helper->subwords(' Trimed texte   ', 23));
	}

	/** @test */
	public function tooShortStringShouldNotBeModified() {
		$this->assertEquals('A string too short', $this->_helper->subwords('A string too short', 42));
	}

	/** @test */
	public function tooLongStringShouldBeShortened() {
		$this->assertEquals('A string little too long...', $this->_helper->subwords('A string little too long to display in context', 5));
	}

	/** @test */
	public function customSuffixShouldBeApplied() {
		$this->assertEquals('A string little too long >>>', $this->_helper->subwords('A string little too long to display in context', 5, ' >>>'));
	}
	
}
?>