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
require_once 'ArteVODFixtures.php';

abstract class ArteVODHarverstingTestCase extends PHPUnit_Framework_TestCase {
	protected $_web_client;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('ARTE_VOD')
			->setValeur('1');
		Class_AdminVar::getLoader()
			->newInstanceWithId('ARTE_VOD_LOGIN')
			->setValeur('user');
		Class_AdminVar::getLoader()
			->newInstanceWithId('ARTE_VOD_KEY')
			->setValeur('pass');

		$this->_web_client = Storm_Test_ObjectWrapper::mock()
			->whenCalled('setAuth')->with('user', 'pass')->answers(null);

		Class_WebService_ArteVOD::setDefaultWebClient($this->_web_client);
	}
}


class ArteVODHarverstingTwoFilmsInTwoPages extends ArteVODHarverstingTestCase {
	protected $_category_wrapper;
	protected $_album_wrapper;

	public function setUp() {
		parent::setUp();

		$this->_web_client
			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films')
			->answers(ArteVODFixtures::firstPage())

			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films?page_nb=2')
			->answers(ArteVODFixtures::secondPage())

			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films/5540')
			->answers(ArteVODFixtures::firstFilm())

			->whenCalled('open_url')
			->with('http://www.mediatheque-numerique.com/ws/films/5541')
			->answers(ArteVODFixtures::secondFilm())

			->beStrict();


		$this->_category_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')->answers(true);

		$this->_album_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('save')->answers(true);

		$service = new Class_WebService_ArteVOD();
		xdebug_break();
		$service->harvest();
	}


	/** @test */
	public function shouldHaveCreatedCategorie() {
		$this->assertTrue($this->_category_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	public function shouldHaveCreatedTwoAlbums() {
		$this->assertEquals(2, $this->_category_wrapper->methodCallCount('save'));
	}
}