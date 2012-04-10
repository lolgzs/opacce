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


/**
 * Used to generate on-the-fly method names and attributes for Storm_Model_Abstract mainly
 */
class Storm_Inflector {
	protected static $_singular = array (
			'/eux$/i' => 'eu',
			'/aux$/i' => 'al',
			'/s$/i' => ''
	);

	protected static $_plural = array(
			'/eu$/i'=> 'eux',
			'/al$/i'=> 'aux',
			'/s$/i'=> 's',
			'/$/'=> 's'
		);

	protected static $_uncountable = array('avis');

	/**
	 * Returns given_word as CamelCased
	 *
	 * @param string $word 
	 * @return string
	 */
	public static function camelize($word) {
		return str_replace(' ',
											 '', 
											 ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',strtolower($word))));
	}


	/**
	 * Returns GivenWord as variable_name_with_underscores
	 *
	 * @param string $word 
	 * @return string
	 */
	public static function underscorize($word) {
		return strtolower(preg_replace('/([^A-Z_])([A-Z])/', "$1_$2", $word));
	}


	/**
	 * @param string $word
	 * @return string
	 */
	public static function singularize($word)	{
		if (self::_isUncountable($word))
			return $word;

		
		foreach (self::$_singular as $rule => $replacement) {
			if ($word != ($singular = preg_replace($rule, $replacement, $word)))
				return $singular;
		}
		
		return $word;
	}


	/**
	 * @param string $word
	 * @return string
	 */
	public static function pluralize($word)	{
		if (self::_isUncountable($word))
			return $word;
			
		foreach (self::$_plural as $rule => $replacement) {
			if ($word !== ($plural = preg_replace($rule, $replacement, $word)))
				return $plural;
		}
	}


	protected static function _isUncountable($word) {
		return (in_array(strtolower($word), self::$_uncountable));
	}
}
