<?php
/**
 * Copyright (c) 2012, Agence FranÃ§aise Informatique (AFI). All rights reserved.
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

class FnactTest extends PHPUnit_Framework_TestCase {
	protected $_fnac;

	public function setup() {
		$this->_http_client = Storm_Test_ObjectWrapper::mock();
		Class_WebService_Fnac::setDefaultHttpClient($this->_http_client);

		$this->_http_client
			->whenCalled('open_url')
			->with('http://www3.fnac.com/advanced/book.do?isbn=2070572676')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_harry_potter_front.html'))

			->whenCalled('open_url')
			->with('http://livre.fnac.com/a1715839/Harry-Potter-T6-Harry-Potter-et-le-Prince-de-Sang-Mele-J-K-Rowling')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_harry_potter_suite.html'))
			->beStrict();

		$this->_fnac = new Class_WebService_Fnac();
	}


	/** @test */
	public function getResumeShouldFetchItFromPotterSuite() {
		$resume = $this->_fnac->getResume('2-07-057267-6');
		$this->assertContains('Harry, Ron et Hermione entrent',	$resume);
		$this->assertContains('Le sens des responsabilités et du sacrifice, revêtent',	$resume);
	}


	public function tearDown() {
		Class_WebService_Fnac::setDefaultHttpClient(null);
		parent::tearDown();
	}
}

?>