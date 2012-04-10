<?php
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
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
class Class_I18nTranslator {
	/** @var array */
	public static $_instances = array();

	/** @var bool */
	protected static $_caching = true;

	/** @var array */
	protected $_dictionary = array();

	/**
	 * @param string $language
	 * @return Class_I18nTranslator
	 */
	public static function getFor($language) {
		$language = (string)$language;

		if (!self::$_caching)
			return new self(Class_I18n::getInstance()->read($language));

		if (!array_isset($language, self::$_instances)) {
			$i18n = Class_I18n::getInstance();
			$datas = $i18n->read($language);
			self::$_instances[$language] = new self($datas);
		}

		return self::$_instances[$language];

	}

	/**
	 * @param bool $flag
	 */
	public static function setCaching($flag = true) {
		self::$_caching = (bool)$flag;
	}

	/**
	 * @param array $dictionary
	 */
	private function __construct(array $dictionary) {
		$this->_dictionary = $dictionary;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function translate($value) {
		$key = md5((string)$value);

		return (array_key_exists($key, $this->_dictionary)) ?
							$this->_dictionary[$key] :
							(string)$value;
	}

}
?>