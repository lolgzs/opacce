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

require_once 'TelephoneAbstractControllerTestCase.php';


class Telephone_RechercheControllerSimpleSeveralInexistingWordsActionTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/telephone/recherche/simple', 
												array('expressionRecherche' => 'zzriuezz greuieub brfauiok'));
	}

	
	/** @test */
	public function pommeShouldBePresent() {
		$this->assertXPathContentContains('//div', 'zzriuezz greuieub brfauiok');
	}


	/** @test */
	public function toolbarUrlRetourShouldBeRoot() {
		$this->assertXPath('//div[@class="toolbar"]//a[@href="/"]');
	}


	/** @test */
	public function modeRechercheShouldNotBePertinence() {
		$this->assertFalse($_SESSION['recherche']['selection']['pertinence']);
	}


	/** @test */
	public function pageShouldContainsLinkToElargirLaRecherche() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/simple?pertinence=1")]', 
																			'Elargir la recherche');
	}


	/** @test */
	public function pageShouldDisplayAucunResultat() {
		$this->assertXPathContentContains('//h2', 'Aucun résultat trouvé');
	}

}




class Telephone_RechercheControllerSimpleOneInexistingWordActionTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/telephone/recherche/simple', 
												array('expressionRecherche' => 'zzriuezz'));
	}


	/** @test */
	public function pageShouldNotContainsLinkToElargirLaRecherche() {
		$this->assertNotXPath('//a[contains(@href, "recherche/simple?pertinence=1")]');
	}


	/** @test */
	public function pageShouldDisplayAucunResultat() {
		$this->assertXPathContentContains('//h2', 'Aucun résultat trouvé');
	}
}




class Telephone_RechercheControllerSimpleByPertinenceActionTest extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/telephone/recherche/simple', array('expressionRecherche' => 'pomme',
																														 'pertinence' => 1));
	}


	/** @test */
	public function modeRechercheShouldBePertinence() {
		$this->assertTrue($_SESSION['recherche']['selection']['pertinence']);
	}


	/** @test */
	public function pageShouldNotContainsLinkToElargirLaRecherche() {
		$this->assertNotXPath('//a[contains(@href, "recherche/simple?pertinence=1")]');
	}


	/** @test */
	public function pageShouldNotDisplayAucunResultat() {
		$this->assertNotXPathContentContains('//h2', 'Aucun résultat trouvé');
	}
}


?>