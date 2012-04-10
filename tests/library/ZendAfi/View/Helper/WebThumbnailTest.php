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
require_once 'ViewHelperTestCase.php';
require_once 'ZendAfi/View/Helper/WebThumbnail.php';

class ViewHelperWebThumbnailTestReturnedUrl extends ViewHelperTestCase {
	public function setUp() {
		$this->helper = new ZendAfi_View_Helper_WebThumbnail();
		$this->thumbnailer = $this->getMock('Mock_Class_Thumbnailer', array('fetchUrlToFile'));
		$this->helper->setThumbnailer($this->thumbnailer);

		$this->google_thumbnail_path = USERFILESPATH.'/web_thumbnails/www_google_com.jpg';
	}

	public function tearDown() {
		if (file_exists($this->google_thumbnail_path))
			unlink($this->google_thumbnail_path);
	}

	public function testGoogleDotComThumbnailUrl() {
		$this->thumbnailer
			->expects($this->once())
			->method('fetchUrlToFile')
			->with('http://www.google.com', 
						 $this->google_thumbnail_path)
			->will($this->returnValue(true));

		$url = $this->helper->webThumbnail('http://www.google.com');
		$this->assertEquals('/afi-opac3/userfiles/web_thumbnails/www_google_com.jpg',
												$url);
	}


	public function testGoogleDotComThumbnailUrlWithExistingFile() {
		touch($this->google_thumbnail_path);

		$this->thumbnailer
			->expects($this->never())
			->method('fetchUrlToFile');

		$url = $this->helper->webThumbnail('http://www.google.com');
		$this->assertEquals('/afi-opac3/userfiles/web_thumbnails/www_google_com.jpg',
												$url);
	}

	public function testSubpageUrlWithParams() {
		$this->thumbnailer
			->expects($this->once())
			->method('fetchUrlToFile')
			->with('http://www.google.fr/search?sourceid=chrome&ie=UTF-8&q=harry+potter', 
						 USERFILESPATH.'/web_thumbnails/www_google_fr_search_sourceid_chrome_ie_UTF-8_q_harry_potter.jpg')
			->will($this->returnValue(true));

		$url = $this->helper->webThumbnail('http://www.google.fr/search?sourceid=chrome&ie=UTF-8&q=harry+potter');
		$this->assertEquals('/afi-opac3/userfiles/web_thumbnails/www_google_fr_search_sourceid_chrome_ie_UTF-8_q_harry_potter.jpg',
												$url);
	}

	public function testSubpageUrlWithSpacesAndHTMLEntites() {
		$this->thumbnailer
			->expects($this->once())
			->method('fetchUrlToFile')
			->with('https://astrolabe.fr/my%20search', 
						 USERFILESPATH.'/web_thumbnails/astrolabe_fr_my_search.jpg')
			->will($this->returnValue(true));

		$url = $this->helper->webThumbnail('https://astrolabe.fr/my%20search');
		$this->assertEquals('/afi-opac3/userfiles/web_thumbnails/astrolabe_fr_my_search.jpg',
												$url);
	}

	public function testThumbnailerCannotFetchImageReturnsEmptyUrl() {
		$this->thumbnailer
			->expects($this->once())
			->method('fetchUrlToFile')
			->with('http://www.google.com', 
						 $this->google_thumbnail_path)
			->will($this->returnValue(false));

		$url = $this->helper->webThumbnail('http://www.google.com');
		$this->assertEquals('',	$url);
	}
}


class ViewHelperWebThumbnailTestThumbnailer extends ViewHelperTestCase {
	public function setUp() {
		$this->helper = new ZendAfi_View_Helper_WebThumbnail();
	}


	public function testThumbnailerDefaultsToWebThumbnailer() {
		$this->assertInstanceOf('WebThumbnailer',
														$this->helper->getThumbnailer());
	}

	public function testWebThumbnailerGetBluga() {
		$this->assertInstanceOf('Bluga_Webthumb',
														$this->helper->getThumbnailer()->getBlugaWebthumb());
	}
}


class WebThumbnailerTestFetchFile extends ViewHelperTestCase {
	public function setUp() {
		$this->thumbnailer = new WebThumbnailer();
		$this->bluga = $this->getMock('Mock_Bluga_Webthumb', array('setApiKey', 
																															 'addUrl', 
																															 'submitRequests', 
																															 'readyToDownload',
																															 'checkJobStatus',
																															 'fetchToFile'));

		$this->thumbnailer->setBlugaWebthumb($this->bluga);
		$this->thumbnailer->setTryTimeout(0);
	}


	public function testFetchGoogleWithoutProxy() {
		Zend_Registry::set('cfg', 
											 new Zend_Config(array('proxy' => array('host' => null,
																															'port' => null))));

		$this->_fetchGoogle();
		$this->assertEquals(null, 
												$this->bluga->httpRequestAdapter->proxy);
		$this->assertFalse(array_key_exists('Proxy-Authorization',
																				$this->bluga->httpRequestAdapter->headers));
	}


	public function testFetchGoogleThroughProxy() {
		Zend_Registry::set('cfg', 
											 new Zend_Config(array('proxy' => array('host' => '192.168.2.1',
																															'port' => '8180',
																															'user' => 'afi',
																															'pass' => 'pafgjl'))));

		$this->_fetchGoogle();
		$this->assertEquals(new Bluga_HTTP_Request_Uri('tcp://192.168.2.1:8180'), 
												$this->bluga->httpRequestAdapter->proxy);
		$this->assertEquals(" Basic ".base64_encode('afi:pafgjl'),
												$this->bluga->httpRequestAdapter->headers['Proxy-Authorization']);
	}


	public function testFetchGoogleThroughProxyWithoutAuth() {
		Zend_Registry::set('cfg', 
											 new Zend_Config(array('proxy' => array('host' => '192.168.2.3',
																															'port' => '3128',
																															'user' => null,
																															'pass' => null))));

		$this->_fetchGoogle();
		$this->assertEquals(new Bluga_HTTP_Request_Uri('tcp://192.168.2.3:3128'), 
												$this->bluga->httpRequestAdapter->proxy);
		$this->assertFalse(array_key_exists('Proxy-Authorization',
																				$this->bluga->httpRequestAdapter->headers));
	}


	protected function _fetchGoogle() {
		$this->bugla_api_key = new Class_AdminVar();
		$this->bugla_api_key
			->setId('BLUGA_API_KEY')
			->setValeur('12345');
		Class_AdminVar::getLoader()->cacheInstance($this->bugla_api_key);


		$job = $this->getMock('Mock_Webthumb_Job');

		$this->bluga
			->expects($this->at(0))
			->method('setApiKey')
			->with('12345');

		$this->bluga
			->expects($this->at(1))
			->method('addUrl')
			->with('http://google.com', 'small')
			->will($this->returnValue($job));
			
		$this->bluga
			->expects($this->at(2))
			->method('submitRequests');

		$this->bluga
			->expects($this->at(3))
			->method('readyToDownload')
			->will($this->returnValue(false));

		$this->bluga
			->expects($this->at(4))
			->method('checkJobStatus');

		$this->bluga
			->expects($this->at(5))
			->method('readyToDownload')
			->will($this->returnValue(true));

		$this->bluga
			->expects($this->at(6))
			->method('fetchToFile')
			->with($job,'thumbnails/google_com.jpg');

		$this->assertTrue($this->thumbnailer->fetchUrlToFile('http://google.com', 
																												 'thumbnails/google_com.jpg'));
	}

	public function testFetchGoogleWithoutKeyReturnsFalse() {
		$this->assertFalse($this->thumbnailer->fetchUrlToFile('http://google.com', 
																													'thumbnails/google_com.jpg'));
	}
}


?>