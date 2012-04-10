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

class Storm_Test_EmptyUserTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->new_user = new Storm_Test_Mock_User();
	}


	/** @test */
	function nameShouldDefaultsToHari() {
		$this->assertEquals('Hari', $this->new_user->getName());
	}

	/** @test */
	function isAttributeExistsNameShouldBeTrue() {
		$this->assertTrue($this->new_user->isAttributeExists('name'));
	}


	/** @test */
	function firstNameShouldDefaultsToMata() {
		$this->assertEquals('Mata', $this->new_user->getFirstName());
	}


	/** 
	 * @expectedException Storm_Model_Exception
	 * @expectedExceptionMessage Tried to call unknown method Storm_Test_Mock_User::getMatricule
	 * @test 
	 */
	function getMatriculeShouldRaiseError() {
		$this->new_user->getMatricule();
	}


	/** 
	 * @expectedException Storm_Model_Exception
	 * @expectedExceptionMessage Tried to call unknown method Storm_Test_Mock_User::zork
	 * @test 
	 */
	function methodZorkShouldRaiseError() {
		$this->new_user->zork();
	}


	/** 
	 * @expectedException Storm_Model_Exception
	 * @expectedExceptionMessage Tried to call unknown method Storm_Test_Mock_User::addZork
	 * @test 
	 */
	function methodAddZorkShouldRaiseError() {
		$this->new_user->addZork('glub');
	}
}

?>