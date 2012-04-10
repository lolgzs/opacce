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

class Storm_Test_DeleteHooksTest extends Storm_Test_ModelTestCase {
	/** @var Storm_Test_Mock_User */
	protected $_user;

	/** @var Storm_Test_ObjectWrapper */
	protected $_wrapper;

	public function setUp() {
		$this->_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Storm_Test_Mock_User')
												->whenCalled('beforeDelete')->answers(null)->getWrapper()
												->whenCalled('afterDelete')->answers(null)->getWrapper();

		$this->_user = Storm_Test_Mock_User::getLoader()->newInstanceWithId(1)
										->setTraceHookMock($this->_wrapper)
										->setValid(true);

	}


	/** @test */
	public function withSuccesfulDeleteModelShouldCallAllDeleteHooks() {
		$this->_wrapper->whenCalled('delete')->answers(true);
		$this->_user->delete();
		$this->assertTrue($this->_wrapper->methodHasBeenCalled('beforeDelete'));
		$this->assertTrue($this->_wrapper->methodHasBeenCalled('afterDelete'));
	}


	/** @test */
	public function withFailedDeleteModelShouldNotCallAfterDeleteHook() {
		$this->_wrapper->whenCalled('delete')->answers(false);
		$this->_user->delete();
		$this->assertTrue($this->_wrapper->methodHasBeenCalled('beforeDelete'));
		$this->assertFalse($this->_wrapper->methodHasBeenCalled('afterDelete'));
	}
}
?>