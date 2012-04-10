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

class TranslatePluralTest extends PHPUnit_Framework_TestCase {
	function setUp() {
		$translations = array("Pas d'enfants manquant" => "No children missing",
													"%d enfant manquant sur %d" =>  "%d child missing among %d",
													"%d enfants manquants sur %d" => "%d children missing among %d");
		$this->translate = new ZendAfi_Translate('array', $translations, 'en');
	}



	/** @test */
	function withZeroShouldReturnNoChildren() {
		$translation = $this->translate->plural(0, 
																						"Pas d'enfants manquant", 
																						"%d enfant manquant sur %d", 
																						"%d enfants manquants sur %d");
		$this->assertEquals("No children missing", $translation);
	}


	/** @test */
	function withOneShouldReturnOneChildMissing() {
		$translation = $this->translate->plural(1, 
																						"Pas d'enfants manquant", 
																						"%d enfant manquant sur %d", 
																						"%d enfants manquants sur %d", 
																						1, 
																						20);
		$this->assertEquals("1 child missing among 20", $translation);
	}


	/** @test */
	function withFiveShouldReturnFiveChilrendMissing() {
		$translation = $this->translate->plural(5, 
																						"Pas d'enfants manquant", 
																						"%d enfant manquant sur %d", 
																						"%d enfants manquants sur %d", 
																						5, 
																						12);
		$this->assertEquals("5 children missing among 12", $translation);
	}
}


?>