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

class ArteVOD_FilmReaderTest extends PHPUnit_Framework_TestCase {
	protected $_film;

	public function setUp() {
		$reader = new Class_WebService_ArteVOD_FilmReader();
		$this->_film = new Class_WebService_ArteVOD_Film();
		$reader->parseContentOn('<?xml version="1.0" encoding="utf-8"?><wsObjectQuery>
<film><pk>5540</pk><externalUri>http://www.mediatheque-numerique.com/films/blanche-neige</externalUri><editorial><title>Blanche Neige</title><description>Une adaptation drôle et poétique du conte des frères Grimm, dans une collection de théâtre pour jeune public.</description><original_title></original_title><body>La pomme, les sept nains, le cercueil de verre, le prince à cheval, le miroir magique... : le metteur en scène Nicolas Liautard a parié sur les images évoquées dans le conte pour faire du théâtre sans texte. Une succession de tableaux vivants, où le langage du corps, les jeux de lumière et la scénographie créent une féerie intemporelle qui sollicite l\'imaginaire des enfants.</body><genre code="drama"><label lang="fr">Dramatique</label></genre><tags></tags></editorial><technical><duration>70</duration><target_audience code="all-1"><label lang="fr">target_audience_all_1</label></target_audience><production_year>2011</production_year><production_countries><country code="FR"><label lang="fr">France</label></country></production_countries><codes><code type="ARTE">131333</code></codes><release_dates></release_dates><languages><language code="fr"><label lang="fr">Français</label></language></languages><copyright></copyright></technical><staff><authors><person><first_name>Florent</first_name><last_name>Trochel</last_name><full_name>Florent Trochel</full_name></person></authors><actors></actors></staff><media><posters><media src="http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg"><modificationDate>2012-06-13T11:45:26</modificationDate></media></posters><trailers><media src="http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4"><modificationDate>2012-04-03T08:31:24</modificationDate></media></trailers><photos><media src="http://media.universcine.com/7d/f8/7df8bc21-7d56-11e1-baed-69499da4469c.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/14/7e142c4c-7d56-11e1-bef3-a980e4936291.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/19/7e199359-7d56-11e1-9d9b-a9fbcefd86db.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/1d/7e1dc11e-7d56-11e1-99aa-8775a2d902d1.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media><media src="http://media.universcine.com/7e/28/7e28cbe1-7d56-11e1-a80d-d78d88d4aa56.jpg"><modificationDate>2012-04-03T08:31:03</modificationDate></media></photos></media></film></wsObjectQuery>',
														$this->_film);
	}


	/** @test */
	public function titleShouldBeBlancheNeige() {
		$this->assertEquals('Blanche Neige', $this->_film->getTitle());
	}


	/** @test */
	public function externalUriShouldBeCorrect() {
		$this->assertEquals('http://www.mediatheque-numerique.com/films/blanche-neige',
												$this->_film->getExternalUri());
	}


	/** @test */
	public function descriptionShouldContainLaPomme() {
		$this->assertTrue(false !== strpos($this->_film->getDescription(), 'La pomme'));
	}


	/** @test */
	public function yearShouldBe2011() {
		$this->assertEquals('2011', $this->_film->getYear());
	}


	/** @test */
	public function shouldHaveOneAuthor() {
		$this->assertEquals(1, count($this->_film->getAuthors()));
	}


	/** @test */
	public function authorShouldBeFlorentTrochel() {
		$authors = $this->_film->getAuthors();
		$this->assertEquals('Florent Trochel', current($authors));
	}


	/** @test */
	public function shouldHavePosterUrl() {
		$this->assertEquals('http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg',
												$this->_film->getPosters()[0]);
	}


	/** @test */
	public function shouldHaveTrailerUrl() {
		$this->assertEquals('http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4',
												$this->_film->getTrailers()[0]);
	}


	/** @test */
	public function shouldHaveFivePhotos() {
		$this->assertEquals(5, count($this->_film->getPhotos()));
	}


	/** @test */
	public function lastPhotoShouldBeCorrect() {
		$this->assertEquals('http://media.universcine.com/7e/28/7e28cbe1-7d56-11e1-a80d-d78d88d4aa56.jpg',
												$this->_film->getPhotos()[4]);
	}
}