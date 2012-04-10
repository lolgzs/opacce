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

/* 
 * Tests de non-régression onglets invisibles sur iPhone
 */
class ModulesAppliValeursParDefautTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->modules_appli = new Class_Systeme_ModulesAppli();
	}

	/**
	 * @test
	 */
	public function forControllerRechercheAndActionResultat() {
		$def_val = $this->modules_appli->getValeursParDefaut("recherche", "resultat");
		$this->assertEquals("Résultat", $def_val["barre_nav"]);
		$this->assertEquals("AMDPZ", $def_val["tags_codes"]);
	}


	/**
	 * @test
	 */
	public function forControllerRechercheAndActionViewNotice() {
		$def_val = $this->modules_appli->getValeursParDefaut("recherche", "viewnotice");
		$this->assertEquals("Notice", $def_val["barre_nav"]);
		$this->assertEquals(2, $def_val["onglets"]["exemplaires"]["aff"]);
	}


	/**
	 * @test
	 */
	public function forControllerAuthAndActionLogin() {
		$def_val = $this->modules_appli->getValeursParDefaut("auth", "login");
		$this->assertEquals("Connexion", $def_val["barre_nav"]);
	}
	
}


?>