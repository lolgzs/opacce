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

class Storm_Test_MethodRedirectionException extends Exception{}

class Storm_Test_MethodRedirection {
	/**
	 * @var mixed
	 */
	protected $_value_to_answer;

	/**
	 * @var closure
	 */
	protected $_closure_to_run;

	/**
	 * @var array
	 */
	protected $_expected_params;

	/**
	 * @var string
	 */
	protected $_method;

	/**
	 * @var Storm_Test_ObjectWrapper
	 */
	protected $_wrapper;


	/**
	 * @var boolean
	 */	
	protected $_should_not_be_called = false;

	/**
	 * @param Storm_Test_ObjectWrapper $wrapper
	 * @return Storm_Test_MethodRedirection
	 */
	public static function onWrapper($wrapper) {
		$redirection = new self();
		return $redirection->setWrapper($wrapper);
	}


	/**
	 * @param mixed $value_to_answer
	 * @return Storm_Test_ObjectWrapper
	 */
	public function answers($value_to_answer) {
		$this->_value_to_answer = $value_to_answer;
		return $this->getWrapper();
	}


	/**
	 * @param function $closure
	 * @return Storm_Test_ObjectWrapper
	 */
	public function willDo($closure) {
		$this->_closure_to_run = $closure;
		return $this->getWrapper();
	}


	/**
	 * @return Storm_Test_ObjectWrapper
	 */
	public function getWrapper() {
		return $this->_wrapper;
	}


	/**
	 * @return Storm_Test_ObjectWrapper
	 */
	public function beStrict() {
		return $this->getWrapper()->beStrict();
	}


	/**
	 * @param type $wrapper
	 * @return Storm_Test_MethodRedirection
	 */
	public function setWrapper($wrapper) {
		$this->_wrapper = $wrapper;
		return $this;
	}
	
	
	/*
	 * @return Storm_Test_MethodRedirection
	 */
	public function whenCalled($method_name) {
		return $this->getWrapper()->whenCalled($method_name);
	}


	/*
	 * @return Storm_Test_MethodRedirection
	 */
	public function shouldNotBeCalled($method_name) {
		return $this->getWrapper()->shouldNotBeCalled($method_name);
	}

	/**
	 * @param string $method
	 * @return Storm_Test_MethodRedirection
	 */
	public function setMethod($method) {
		$this->_method = $method;
		return $this;
	}


	/**
	 */
	public function _raiseShouldNotBeCalledException() {
		throw new Storm_Test_MethodRedirectionException(
																										sprintf("Method %s(%s) was not expected to be called",
																														$this->_method,
																														implode(',', $this->_expected_params)));
	}


	/**
	 * @return Storm_Test_MethodRedirection
	 */
	public function never() {
		$this->_should_not_be_called = true;
		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getValueToAnswer($args) {
		if ($this->_should_not_be_called)
			$this->_raiseShouldNotBeCalledException();

		if (null !== $this->_closure_to_run) {
			return call_user_func_array($this->_closure_to_run, $args);
		}
		return $this->_value_to_answer;
	}


	/**
	 * @return Storm_Test_MethodRedirection
	 */
	public function with() {
		$this->_expected_params = func_get_args();
		return $this;
	}

	/**
	 * @param string $method
	 * @return bool
	 */
	public function matchMethod($method) {
		return $method == $this->_method;
	}

	/**
	 * @param string $method
	 * @param array $args
	 * @return bool
	 */
	public function matchMethodAndArgs($method, $args) {
		return $this->matchMethod($method) and $this->matchArgs($args);
	}


	/**
	 * @param array $args
	 * @return bool
	 */
	public function matchArgs($args) {
		return ($args == $this->_expected_params);
	}

	/**
	 * @return bool
	 */
	public function expectArgs() {
		return count($this->_expected_params) > 0;
	}
}

?>