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


abstract class BibNumeriqueControllerTelephoneTestCase extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$categorie = Class_AlbumCategorie::getLoader()
			->newInstanceWithId(3)
			->setLibelle('epubs')
			->setAlbums(array(Class_Album::getLoader()
												->newInstanceWithId(234)
												->setCatId(3)
												->beEPUB()
												->setTitre('Monuments')
												->setRessources(array(Class_AlbumRessource::getLoader()
																							->newInstanceWithId(123)
																							->setFichier('versailles.epub')
																							->setIdAlbum(234))),

												Class_Album::getLoader()
												->newInstanceWithId(567)
												->setTitre('Histoires')
												));
	}
}




class BibNumeriqueControllerTelephoneViewCategorieEPUBTest extends BibNumeriqueControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/bib-numerique/view-categorie/id/3');
	}


	/** @test */
	public function liShouldContainsLinkToAlbumMonuments() {
		$this->assertXPathContentContains('//li/a[contains(@href, "bib-numerique/view-album/id/234")][@data-ajax="false"]', 'Monuments');
	}


	/** @test */
	public function liShouldContainsLinkToAlbumHistoires() {
		$this->assertXPathContentContains('//li/a[contains(@href, "bib-numerique/view-album/id/567")][@data-ajax="false"]', 'Histoires');
	}
}




class BibNumeriqueControllerTelephoneViewAlbumMonumentsTest extends BibNumeriqueControllerTelephoneTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/bib-numerique/view-album/id/234', true);
	}


	/** @test */
	public function pageShouldContainsLinkToDownloadEPub() {
		$this->assertXPath('//a[contains(@href, "media/versailles.epub")]');
	}
}



class BibNumeriqueControllerTelephoneViewAlbumMultiMedia extends TelephoneAbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Album::newInstanceWithId(999)
			->beDiaporama()
			->setTitre('Antigone')
			->setDateMaj('2012-02-17 10:00:00')
			->setAuteur('Sophocle')
			->setCategorie(Class_AlbumCategorie::getLoader()
										 ->newInstanceWithId(12)
										 ->setLibelle('antiquité'))
			->setRessources([Class_AlbumRessource::newInstanceWithId(12)
											 ->setFichier('introduction.mp3')
											 ->setTitre('Introduction'),

											 Class_AlbumRessource::newInstanceWithId(13)
											 ->setFichier('choeur.mp3')
											 ->setTitre('Le Choeur')]);

		$this->dispatch('/bib-numerique/view-album/id/999', true);
	}


	/** @test */
	public function pageShouldContainsLinkToXSPFPlayList() {
		$this->assertXPath('//a[contains(@href, "bib-numerique/album-xspf-playlist/id/999.xspf")]');
	}


	/** @test */
	public function pageShouldContainsLinkToRSSPodcast() {
		$this->assertXPath('//a[contains(@href, "bib-numerique/Album-rss-feed/id/999.xml")]');
	}

	
	/** @test */
	public function pageShouldContainLinkIntroductionMp3() {
	  $this->assertXPath('//a[contains(@href, "media/introduction.mp3")]');
	}

}


?>