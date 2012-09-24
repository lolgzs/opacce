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


abstract class FnacTestCase extends PHPUnit_Framework_TestCase {
	protected $_fnac;
	protected $_http_client;

	public function setUp() {
		$this->_fnac = new Class_WebService_Fnac();

		$this->_http_client = Storm_Test_ObjectWrapper::mock();
		Class_WebService_Fnac::setDefaultHttpClient($this->_http_client);
	}


	public function tearDown() {
		Class_WebService_Fnac::setDefaultHttpClient(null);
		parent::tearDown();
	}
}




class FnactHarryPotterTest extends FnacTestCase {
	public function setup() {
		parent::setUp();

		$this->_http_client
			->whenCalled('open_url')
			->with('http://www3.fnac.com/advanced/book.do?isbn=2070572676')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_harry_potter_front.html'))

			->whenCalled('open_url')
			->with('http://livre.fnac.com/a1715839/Harry-Potter-T6-Harry-Potter-et-le-Prince-de-Sang-Mele-J-K-Rowling')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_harry_potter_suite.html'))
			->beStrict();
	}


	/** @test */
	public function getResumeShouldFetchItFromPotterSuite() {
		$resume = $this->_fnac->getResume('2-07-057267-6');
		$this->assertContains('Harry, Ron et Hermione entrent',	$resume);
		$this->assertContains('Le sens des responsabilités et du sacrifice, revêtent',	$resume);
	}
}




class FnacMilleniumTest extends FnacTestCase {
	public function setup() {
		parent::setUp();

		$this->_http_client
			->whenCalled('open_url')
			->with('http://www3.fnac.com/advanced/book.do?isbn=9782742765010')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_millenium_front.html'))

			->whenCalled('open_url')
			->with('http://livre.fnac.com/a1891354/Millenium-T2-La-fille-qui-revait-d-un-bidon-d-essence-et-d-une-allumette-Stieg-Larsson')
			->answers(file_get_contents(realpath(dirname(__FILE__)). '/../../../fixtures/fnac_millenium_suite.html'))
			->beStrict();
	}


	/** @test */
	public function getResumeShouldFetchItFromPotterSuite() {
		$resume = $this->_fnac->getResume('978-2-7427-6501-0');
		$this->assertEquals('Lisbeth et Mickael sont de retour dans un roman aussi trépidant que le premier. Nos deux anti-héros sont à nouveau plongés dans une aventure passionnante. Un livre époustoufflant, plein d\'humour et d\'effroi. Vivement le tome 3. Anais, libraire à la Fnac Clermont Q',	
												$resume);
	}
}




class FnactNoLinkFoundTest extends FnacTestCase {
	public function setup() {
		parent::setUp();

		$this->_http_client
			->whenCalled('open_url')
			->with('http://www3.fnac.com/advanced/book.do?isbn=2070572676')
			->answers('bla bla bla')
			->beStrict();
	}


	/** @test */
	public function getResumeShourdReturnEmptyString() {
		$resume = $this->_fnac->getResume('2-07-057267-6');
		$this->assertEmpty($resume);
	}
}

?>