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

class SocialNetworkShareActionTest extends AbstractControllerTestCase {
	protected $_mock_web_client;

	public function setUp() {
		parent::setUp();
		$this->_mock_web_client = Storm_Test_ObjectWrapper::on(new Class_WebService_SimpleWebClient());
		Class_WebService_ReseauxSociaux::setDefaultWebClient($this->_mock_web_client);
	}


	public function tearDown() {
		Class_WebService_ReseauxSociaux::resetDefaultWebClient();
		parent::tearDown();
	}


	protected function _expectClientOpenUrlWithLongUrlAndAnswer($url, $answer) {
			$this->_mock_web_client
				->whenCalled('open_url')
				->with(sprintf('http://is.gd/api.php?longurl=%s', urlencode($url)))
				->answers($answer)
				->beStrict();
	}


	/** @test */
	public function shareOnTwitterShouldInjectShortUrlAndMessage() {
		$this->_expectClientOpenUrlWithLongUrlAndAnswer('http://www.institut-francais.com',
																										'http://is.gd/PkdNgD');
		$this->dispatch('/social-network/share/on/twitter?url='.urlencode('http://www.institut-francais.com').'&message='.urlencode('Vive bucarest !'), true);

		$this->assertEquals(sprintf('window.open(\'http://twitter.com/home?status=%s\',\'_blank\',\'location=yes, width=800, height=410\');',
																urlencode('Vive bucarest ! http://is.gd/PkdNgD')),
												$this->_response->getBody());
	}


	/** @test */
	public function shareOnFacebookShouldInjectShortUrlAndMessage() {
		$this->_expectClientOpenUrlWithLongUrlAndAnswer('http://localhost'. BASE_URL .'/recherche/viewnotice/id/2',
																										'http://is.gd/PkdNg2');
		$this->dispatch('/social-network/share/on/facebook?url='.urlencode('/recherche/viewnotice/id/2'), true);

		$this->assertEquals(sprintf('window.open(\'http://www.facebook.com/share.php?u=%s\',\'_blank\',\'location=yes, width=800, height=410\');',
																urlencode('http://is.gd/PkdNg2')),
												$this->_response->getBody());
	}
}


?>