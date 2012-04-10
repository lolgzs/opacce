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

class ReseauxSociauxTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->_mock_web_client = Storm_Test_ObjectWrapper::on(new Class_WebService_SimpleWebClient());
		Class_WebService_ReseauxSociaux::setDefaultWebClient($this->_mock_web_client);
		$this->_rs = new Class_WebService_ReseauxSociaux();
	}

	public function tearDown() {
		Class_WebService_ReseauxSociaux::resetDefaultWebClient();
		parent::tearDown();
	}


	/** @test */
	public function shortenUrlShouldReturnIsGdUrl() {
		$this->_mock_web_client
			->whenCalled('open_url')
			->with(sprintf('http://is.gd/api.php?longurl=%s', 
										 urlencode('http://www.institut-francais.com')))
			->answers('http://is.gd/PkdNgD')
			->beStrict();


		$this->assertEquals('http://is.gd/PkdNgD',
												$this->_rs->shortenUrl('http://www.institut-francais.com'));
	}


	/** @test */
	public function getTwitterUrlViewNoticeShouldReturnShortenUrlWithServerHost() {
		$_SERVER["HTTP_HOST"] = 'localhost';

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://is.gd/api.php?longurl=http%3A%2F%2Flocalhost%2Fafi-opac3%2Frecherche%2Fviewnotice%2Fid%2F2')
			->answers('http://is.gd/PkdNg2')
			->beStrict();

		$this->assertEquals(sprintf('http://twitter.com/home?status=%s', urlencode('http://is.gd/PkdNg2')),
												$this->_rs->getUrl("twitter", '/recherche/viewnotice/id/2'));
	}


	/** @test */
	public function getTwitterUrlViewNoticeWithMessageShouldReturnShortenUrlWithServerHost() {
		$_SERVER["HTTP_HOST"] = 'localhost';

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://is.gd/api.php?longurl=http%3A%2F%2Flocalhost%2Fafi-opac3%2Frecherche%2Fviewnotice%2Fid%2F2')
			->answers('http://is.gd/PkdNg2')
			->beStrict();

		$this->assertEquals(sprintf('http://twitter.com/home?status=%s', urlencode('venez voir ! http://is.gd/PkdNg2')),
												$this->_rs->getUrl("twitter", '/recherche/viewnotice/id/2', 'venez voir !'));
	}


	/** @test */
	public function getFacebookUrlViewNoticeShouldReturnShortenUrlWithServerHost() {
		$_SERVER["HTTP_HOST"] = 'localhost';

		$this->_mock_web_client
			->whenCalled('open_url')
			->with('http://is.gd/api.php?longurl=http%3A%2F%2Flocalhost%2Fafi-opac3%2Frecherche%2Fviewnotice%2Fid%2F2')
			->answers('http://is.gd/PkdNg2')
			->beStrict();

		$this->assertEquals(sprintf('http://www.facebook.com/share.php?u=%s', urlencode('http://is.gd/PkdNg2')),
												$this->_rs->getUrl("facebook", '/recherche/viewnotice/id/2'));
	}


	/** @test */
	public function shortenUrlWithErrorShouldReturnOriginalUrl() {
			$this->_mock_web_client
			->whenCalled('open_url')
			->with(sprintf('http://is.gd/api.php?longurl=%s', 
										 urlencode('http://www.institut-francais.com')))
			->answers('Error')
			->beStrict();


		$this->assertEquals('http://www.institut-francais.com',
												$this->_rs->shortenUrl('http://www.institut-francais.com'));	
	}


	/** @test */
	public function shortenUrlWithNullShouldReturnOriginalUrl() {
			$this->_mock_web_client
			->whenCalled('open_url')
			->with(sprintf('http://is.gd/api.php?longurl=%s', 
										 urlencode('http://www.institut-francais.com')))
			->answers(null)
			->beStrict();


		$this->assertEquals('http://www.institut-francais.com',
												$this->_rs->shortenUrl('http://www.institut-francais.com'));	
	}
}


?>