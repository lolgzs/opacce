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

class Storm_Test_Mock_User extends Storm_Model_Abstract {
	protected $_loader_class = 'Storm_Test_Mock_UserLoader';
	protected $_valid = true;
	protected $_trace_hook_mock;
	protected $_default_attribute_values = array('name' => 'Hari',
																							  'first_name' => 'Mata');

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	public function setValid($valid) {
		$this->_valid = $valid;
		return $this;
	}

	public function isValid() {
		return $this->_valid;
	}

	public function setTraceHookMock($mock) {
		$this->_trace_hook_mock = $mock;
		return $this;
	}

	public function beforeSave() {
		if (isset($this->_trace_hook_mock))
			$this->_trace_hook_mock->beforeSave();
	}

	public function afterSave() {
		if (isset($this->_trace_hook_mock))
			$this->_trace_hook_mock->afterSave();
	}

	public function loaderSave() {
		if (isset($this->_trace_hook_mock))
			return $this->_trace_hook_mock->loaderSave();
	}

	public function afterDelete() {
		if (isset($this->_trace_hook_mock))
			return $this->_trace_hook_mock->afterDelete();
	}

	public function beforeDelete() {
		if (isset($this->_trace_hook_mock))
			return $this->_trace_hook_mock->beforeDelete();
	}
}

?>