<?php 
/*
STORM is under the MIT License (MIT)

Copyright (c) 2010-2011 Agence Française Informatique http://www.afi-sa.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class Storm_Test_ObjectWrapperOnUserLoaderTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Storm_Test_Mock_User')
			->whenCalled('find')->answers('something')
			->whenCalled('findAll')->answers('array_of_users')
			->whenCalled('findByNameAndAge')->with('Manon', 1)->answers('manon')
			->whenCalled('findByNameAndAge')->with('Mario', 6)->answers('mario')
			->whenCalled('find')->with(5)->answers('zork')
			->shouldNotBeCalled('find')->with('glurp')
			->getWrapper();
	}


	public function tearDown() {
		Storm_Test_Mock_User::unsetLoaders();
	}


  /** @test	*/
	public function getFirstAttributeForLastCallOnFindShouldReturnThree() {
		$this->assertEquals('something', Storm_Test_Mock_User::getLoader()->find(3));
		$this->assertEquals(3, $this->wrapper->getFirstAttributeForLastCallOn('find'));
	}


	/**
	 * @expectedException Storm_Test_ObjectWrapperException
	 * @test
	 */
	public function inStrictModeFindThreeSomethingShouldRaiseException() {
		$this->wrapper->beStrict();
		Storm_Test_Mock_User::getLoader()->find(3);
	}


	/** @test */
	function findFiveShouldReturnZork() {
		$this->assertEquals('zork', Storm_Test_Mock_User::getLoader()->find(5));
	}


	/** @test */
	function findAllShouldReturnArrayOfUsers() {
		$this->assertEquals('array_of_users', Storm_Test_Mock_User::getLoader()->findAll());
	}


	/** @test */
	function findByNameManonAndAgeOneShouldAnswersManon() {
		$this->assertEquals('manon', Storm_Test_Mock_User::getLoader()->findByNameAndAge('Manon', 1));
	}


	/** @test */
	function findByNameMarioAndAgeSixShouldAnswersManon() {
		$this->assertEquals('mario', Storm_Test_Mock_User::getLoader()->findByNameAndAge('Mario', 6));
	}


	/** @test */
	function findByNameMarioAndAgeZeroShouldNotRedirect() {
		$this->assertFalse(Storm_Test_Mock_User::getLoader()->findByNameAndAge('Mario', 0));
	}


	/**
	 * @expectedException Storm_Test_MethodRedirectionException
	 * @expectedExceptionMessage Method find(glurp) was not expected to be called
	 * @test
	 */
	function findByGlurpShouldRaiseException() {
		Storm_Test_Mock_User::getLoader()->find('glurp');
	}
}

?>