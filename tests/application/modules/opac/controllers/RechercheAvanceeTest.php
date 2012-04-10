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
require_once 'AbstractControllerTestCase.php';

class RechercheAvanceeTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$_SESSION["histo_recherche"] = array(array("type" => 1,
																							 "selection" => array("expressionRecherche" => "harry potter",
																																		"type_doc" => 1,
																																		"selection_bib" => "b1")),
																				 array("type" => 1,
																							 "selection" => array("expressionRecherche" => "alice",
																																		"type_doc" => 1,
																																		"selection_bib" => "b1")));
		$this->dispatch('recherche/avancee?statut=reset');
	}


	/** @test */
	public function controllerShouldBeRecherche() {
		$this->assertController('recherche');
	}


	/** @test */
	public function dernieresRecherchesShouldContainsAlice() {
		$this->assertXPathContentContains("//tr[3]//td[2]", 'simple');
		$this->assertXPathContentContains("//tr[3]//td[3]", 'alice');
	}


	/** @test */
	public function dernieresRecherchesShouldContainsHarryPotter() {
		$this->assertXPathContentContains("//tr[4]//td[2]", 'simple');
		$this->assertXPathContentContains("//tr[4]//td[3]", 'harry potter');
	}

}

?>