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

class AbonneControllerSuggestionAchatFormTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/suggestion-achat', true);
	}

	/** @test */
	public function pageTitleShouldBeSuggestionAchat() {
		$this->assertXPathContentContains('//title', 'Suggestion d\'achat');
	}


	/** @test */
	public function boiteShouldHaveTitleSuggestionAchat() {
		$this->assertXPathContentContains('//div[@class="boiteMilieu"]//h1', 'Suggestion d\'achat');
	}


	/** @test */
	public function formShouldContainsInputForTitre() {
		$this->assertXPath('//form//input[@name="titre"][@placeholder="Harry Potter à l\'école des sorciers"]');
	}


	/** @test */
	public function formShouldContainsInputForAuteur() {
		$this->assertXPath('//form//input[@name="auteur"][@placeholder="Joanne Kathleen Rowling"]');
	}


	/** @test */
	public function formShouldContainsInputForDescriptionUrl() {
		$this->assertXPath('//form//input[@type="url"][@name="description_url"][@placeholder="http://fr.wikipedia.org/wiki/Harry_Potter_à_l\'école_des_sorciers"]');
	}
}

?>