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

abstract class Telephone_RechercheControllerHarryPotterTestCase extends TelephoneAbstractControllerTestCase {
	protected $_summary;

	public function setUp() {
		parent::setUp();

		$this->_summary = 'Apres la mort < tragique de Lily et James Potter, Harry est recueilli par sa tante Petunia, la soeur de Lily et son oncle Vernon. Son oncle et sa tante, possedant une haine feroce envers les parents d\'Harry, le maltraitent et laissent leur fils Dudley l\'humilier. Harry ne sait rien sur ses parents. On lui a toujours dit qu\'ils etaient morts dans un accident de voiture.';

		$potter = Class_Notice::getLoader()
			->newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setTitrePrincipal('Harry Potter à l\'ecole des sorciers')
			->setAuteurPrincipal('J.K. Rowling')
			->setDateMaj('2012-04-23')
			->setAnnee('2012')
			->setResume($this->_summary)
			->setMatieres(array('Potions', 'Etude des runes'))
			->setEditeur('Gallimard')
			->setLangueCodes(array('fre'))
			->setIsbn('978-2-07-054127-0')
			->setEan('')
			->setUrlVignette('http://amazon.fr/potter.jpg')
			->beLivre();
	}
}




class Telephone_RechercheControllerHarryPotterViewNoticeTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/viewnotice/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToRechercheInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/simple")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function auteurShouldBeJKRowling() {
		$this->assertXPathContentContains('//h1', 'J.K. Rowling');
	}


	/** @test */
	public function pageShouldContainsEditeurGallimard() {
		$this->assertXPathContentContains('//div', 'Editeur(s) : Gallimard');
	}


	/** @test */
	public function pageShouldContainsAnnee2012() {
		$this->assertXPathContentContains('//div', 'Année : 2012');
	}


	/** @test */
	public function pageShouldContainsVignetteForHarryPotter() {
		$this->assertXPath('//a[contains(@href, "recherche/grandeimage")]//img[contains(@src, "potter.jpg")]');
	}


	/** @test */
	public function pageShouldContainsIconeSupport() {
		$this->assertXPath('//img[contains(@src, "famille_livre_small.png")]');
	}


	/** @test */
	public function pageShouldContainsLinkToNoticeDetaillee() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/detail/id/4")]', 'Notice détaillée');
	}


	/** @test */
	public function pageShouldContainsLinkToAvis() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/avis/id/4")]', 'Avis');
	}


	/** @test */
	public function pageShouldContainsLinkToExemplaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/exemplaires/id/4")]', 'Exemplaires');
	}


	/** @test */
	public function pageShouldContainsLinkToResumes() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/resume/id/4")]', 'Résumés, analyses');
	}


	/** @test */
	public function pageShouldContainsLinkToTags() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/tags/id/4")]', 'Tags');
	}


	/** @test */
	public function pageShouldContainsLinkToBiographies() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/biographie/id/4")]', 'Biographies');
	}


	/** @test */
	public function pageShouldContainsLinkToNoticesSimilaires() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/similaires/id/4")]', 'Notices similaires');
	}


	/** @test */
	public function pageShouldContainsLinkToRessourcesNumeriques() {
		$this->assertXPathContentContains('//a[contains(@href, "recherche/ressourcesnumeriques/id/4")]', 'Ressources numériques');
	}
}




class Telephone_RechercheControllerHarryPotterGrandeImageTest extends Telephone_RechercheControllerHarryPotterTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/telephone/recherche/grandeimage/id/4', true);
	}


	/** @test */
	public function pageShouldContainsUrlToViewNoticeInToolbar() {
		$this->assertXPath('//div[@class="toolbar"]//a[contains(@href, "recherche/viewnotice/id/4")]');
	}


	/** @test */
	public function titleShouldBeHarryPotter() {
		$this->assertXPathContentContains('//h1', 'Harry Potter à l\'ecole des sorciers');
	}


	/** @test */
	public function pageShouldContainsPotterJpg() {
		$this->assertXPath('//a[contains(@href, "recherche/viewnotice/id/4")]//img[contains(@src, "potter.jpg")]');		
	}
}


?>