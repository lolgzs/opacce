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

class Storm_Test_ObjectWrapperException extends Exception {}

class Storm_Test_ObjectWrapper {
  protected $_wrapped_object;
  protected $_call_trace;
  protected $_redirections;
	protected $_is_strict = false;

  public function __construct() {
		$this->clearCallTrace();
    $this->_redirections = array();
  }

	/**
	 * @param Object the oject to wrap
	 * @return Storm_Test_ObjectWrapper
	 */
  public static function on($object) {
    $wrapper = new self();
    return $wrapper->wrap($object);
  }


	/**
	 * @return Storm_Test_ObjectWrapper
	 */
  public static function mock() {
		return self::on(new StdClass());
  }


	/**
	 * @return Storm_Test_ObjectWrapper
	 */
  public static function onLoaderOfModel($model_class) {
		$loader = call_user_func(array($model_class, 'getLoader'));
		$wrapper = self::on($loader);
		Storm_Model_Abstract::setLoaderFor($model_class, $wrapper);
		return $wrapper;
	}


	/**
	 * @return Storm_Test_ObjectWrapper
	 */
	public function clearCallTrace() {
		$this->_call_trace = array();
		return $this;
	}


	/**
	 * Raises Exception if arguments don't match when searching redirections
	 * @return Storm_Test_ObjectWrapper
	 */
	public function beStrict() {
		$this->_is_strict = true;
		return $this;
	}


	/**
	 * Compatibility method for fluent interface (before Storm_MethodRedirection::answer
	 * did no return the wrapper
	 * @return Storm_Test_ObjectWrapper
	 */
  public function getWrapper() {
		return $this;
  }


	/**
	 * @param Object the oject to wrap
	 * @return Storm_Test_ObjectWrapper
	 */
  public function wrap($object) {
    $this->_wrapped_object = $object;
    return $this;
  }


	/**
	 * @return Object the wrapped object
	 */
	public function getWrappedObject() {
		return $this->_wrapped_object;
	}


	protected function _raiseRedirectionNotFound($method, $args) {
		ob_start();
		var_dump($args);
		$dump = ob_get_contents();
		ob_end_clean();

		throw new Storm_Test_ObjectWrapperException(sprintf(
																												"Cannot find redirection for %s::%s(%s)",
																												get_class($this->_wrapped_object),
																												$method,
																												$dump));
	}


	protected function _findRedirection($method, $args) {
		foreach($this->_redirections as $redirection) {
			if ($redirection->matchMethodAndArgs($method, $args))
					return $redirection;
		}
		
		if ($this->_is_strict)
			$this->_raiseRedirectionNotFound($method, $args);

		foreach($this->_redirections as $redirection) {
			if ($redirection->matchMethod($method) and !$redirection->expectArgs())
					return $redirection;
		}
	}


  public function __call($method, $args) {
    $this->_call_trace[$method] []= $args;

		if ($redirection = $this->_findRedirection($method, $args))
			return $redirection->getValueToAnswer($args);

    $result = call_user_func_array(array($this->_wrapped_object, $method),
                                   $args);
    if ($result === $this->_wrapped_object)
      return $this;
    return $result;
  }


  public function methodHasBeenCalled($method_name) {
    return array_key_exists($method_name, $this->_call_trace);
  }


  public function methodHasBeenCalledWithParams($method_name, $params) {
		if (!$this->methodHasBeenCalled($method_name))
			return false;

		foreach ($this->_call_trace[$method_name] as $given_params) {
			if ($params == $given_params)
				return true;
		}
		return false;
  }


  public function getAttributesForLastCallOn($method_name) {
		if ($this->methodHasBeenCalled($method_name))
			return end($this->_call_trace[$method_name]);
		throw new Storm_Test_ObjectWrapperException("Method '$method_name' has never been called");
  }


  public function getFirstAttributeForLastCallOn($method_name) {
		$attributes = $this->getAttributesForLastCallOn($method_name);
		return $attributes[0];
	}


	/**
	 * @param string $method_name
	 * @return Storm_Test_MethodRedirection
	 */
  public function whenCalled($method_name) {
    $redirection = Storm_Test_MethodRedirection::onWrapper($this)->setMethod($method_name);
    array_unshift($this->_redirections, $redirection);
    return $redirection;
  }


	/**
	 * @param string $method_name
	 * @return Storm_Test_MethodRedirection
	 */
	public function shouldNotBeCalled($method_name) {
		return $this->whenCalled($method_name)->never();
	}
}

?>