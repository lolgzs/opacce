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

abstract class Telephone_RechercheControllerBibleSouvignyTestCase extends TelephoneAbstractControllerTestCase {
	protected $_summary;

	public function setUp() {
		parent::setUp();

		$souvigny = Class_Notice::getLoader()
			->newInstanceWithId(9)
			->setClefAlpha('bible-souvigny')
			->setTitrePrincipal('Bible de Souvigny')
			->setUrlVignette('http://moulins.fr/souvigny.jpg')
			->setExemplaire(Class_Exemplaire::getLoader()
											->newInstanceWithId(34)
											->setIdOrigine(111))
			->beLivreNumerique();


		Class_Album::getLoader()
			->newInstanceWithId(111)
			->beLivreNumerique()
			->setRessources(array());
	}
}



class Telephone_RechercheControllerBibleSouvignyViewNoticeTest extends Telephone_RechercheControllerBibleSouvignyTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/viewnotice/id/9', true);
	}

	
	/** @test */
	public function pageShouldContainsLinkToRessouresNumeriques() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/ressourcesnumeriques/id/9")]', 'Feuilleter le livre');
	}

}



class Telephone_RechercheControllerBibleSouvignyRessourcesNumeriquesTest extends Telephone_RechercheControllerBibleSouvignyTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/ressourcesnumeriques/id/9', true);
	}


	/** @test */
	public function titleShouldBeBibleSouvigny() {
		$this->assertXPathContentContains('//h1', 'Bible de Souvigny', $this->_response->getBody());
	}

	
}

?>