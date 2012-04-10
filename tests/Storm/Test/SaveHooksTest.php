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

class Storm_Test_SaveHooksTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->trace_hook_mock = $this->getMock('TraceHookMock', array('beforeSave', 
																																	 'afterSave',
																																	 'loaderSave'));

		$this->valid_user = new Storm_Test_Mock_User();
		$this->valid_user
			->setValid(true)
			->setTraceHookMock($this->trace_hook_mock);

		$this->invalid_user = new Storm_Test_Mock_User();
		$this->invalid_user
			->setValid(false)
			->setTraceHookMock($this->trace_hook_mock);
	}


	public function testInvalidModelDoNotCallHooks() {
		$this->trace_hook_mock
			->expects($this->never())
			->method('beforeSave');

		$this->trace_hook_mock
			->expects($this->never())
			->method('afterSave');

		$this->invalid_user->save();
	}


	public function testValidModelCallHooksWithSuccessfullSave() {
		$this->trace_hook_mock
			->expects($this->at(0))
			->method('beforeSave');

		$this->trace_hook_mock
			->expects($this->at(1))
			->method('loaderSave')
			->will($this->returnValue(2));

		$this->trace_hook_mock
			->expects($this->at(2))
			->method('afterSave');

		$this->valid_user->save();
	}


	public function testValidModelCallHooksWithUnsuccessfullSave() {
		$this->trace_hook_mock
			->expects($this->at(0))
			->method('beforeSave');

		$this->trace_hook_mock
			->expects($this->at(1))
			->method('loaderSave')
			->will($this->returnValue(0));

		$this->trace_hook_mock
			->expects($this->once())
			->method('afterSave');

		$this->valid_user->save();
	}
}

?>