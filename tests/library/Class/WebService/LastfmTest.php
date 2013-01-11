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
 * GNU AFFERO GENERAL PUBLIC LICENSE for more detail.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

abstract class LastfmParserTestCase extends PHPUnit_Framework_TestCase {
	protected 
		$_http_client, 
		$_last_fm;


	public function setUp() {
		parent::setUp();

		$this->_http_client = Storm_Test_ObjectWrapper::mock();

		Class_WebService_Lastfm::setDefaultHttpClient($this->_http_client);
		Class_Xml::setDefaultHttpClient($this->_http_client);

		$this->_last_fm = new Class_WebService_Lastfm();
	}


	public function tearDown() {
		Class_WebService_Lastfm::setDefaultHttpClient(null);
		Class_Xml::setDefaultHttpClient(null);
		parent::tearDown();
	}
}




class LastfmFixParserErrorTest extends LastfmParserTestCase {
	public function setUp() {
		parent::setUp();

		$this->_http_client
			->whenCalled('open_url')
			->with('http://ws.audioscrobbler.com/2.0/?api_key=76d470da0d3d5cb08a7025aae2c8686a&method=album.search&album=ANNEES+PELERINAGE')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/as_wrong_franz_liszt_pelerinage.xml'))

			->whenCalled('open_url')
			->with('http://www.last.fm/music/Franz+Liszt/Liszt+:+Les+Annees+De+Pelerinage+-+Ciccolini')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/lastfm_wrong_franz_liszt_pelerinage.html'))

			->beStrict();

		$this->_album = $this->_last_fm->getMorceaux('Les années de pélerinage', 'Franz Liszt');
	}



	/** @test */
	public function firstMorceauShouldHaveTitleLaChapelleDeGuillaumeTell() {
		$this->assertEquals('No. 1. La chapelle de Guillaume Tell (The Chapel of William Tell)',
												$this->_album['morceaux'][1][1]['titre']);
	}


	/** @test */
	public function lastMorceauShouldHaveTitleSursumCorda() {
		$this->assertEquals('No. 7. Sursum corda (Lift up Your Hearts)',
												$this->_album['morceaux'][1][26]['titre']);
	}

}




class LastfmParserTest extends LastfmParserTestCase {
	public function setUp() {
		parent::setUp();

		$this->_http_client 
			->whenCalled('open_url')
			->with('http://ws.audioscrobbler.com/2.0/?api_key=76d470da0d3d5cb08a7025aae2c8686a&method=album.search&album=ANNEES+PELERINAGE')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/as_right_franz_liszt_pelerinage.xml'))

			->whenCalled('open_url')
			->with('http://www.last.fm/music/Franz+Liszt/Liszt:+Annees+De+Pelerinage,+Vol.++2')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/lastfm_right_franz_liszt_pelerinage.html'))

			->beStrict();

		$this->_album = $this->_last_fm->getMorceaux('Les années de pélerinage', 'Franz Liszt');
	}


	/** @test */
	public function fourthMorceauShouldHaveTitleSonnet47() {
		$this->assertEquals('No. 4. Sonetto 47 del Petrarca (Sonnet 47 of Petrarch)',
												$this->_album['morceaux'][1][4]['titre']);
	}


	/** @test */
	public function lastMorceauShouldHaveTitleTarantella() {
		$this->assertEquals('III. Tarantella',
												$this->_album['morceaux'][1][10]['titre']);
	}

}

?>