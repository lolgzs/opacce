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
class Class_Profil_I18nTranslator extends Class_Profil_I18n {
	/** @var Class_I18nTranslator */
	protected $_translator;

	public static function newForLocale($locale) {
		if (1 < count(Class_AdminVar::getLangues())) {
			return new self(Class_I18nTranslator::getFor($locale));
		}

		return new Class_Profil_NullTranslator(null);

	}

	/**
	 * @param Class_I18nTranslator $translator
	 * @param Class_Profil $model
	 */
	public function __construct($translator) {
		$this->_translator = $translator;
	}

	/**
	 * @param array $datas
	 * @param string $type
	 */
	public function translate(array $datas, $type) {
		if (!array_key_exists($type, $this->_knownConfigurations))
			return $datas;

		$datas = $this->recursiveTranslate($datas);

		return $datas;
	}

	/**
	 * @param array $datas
	 * @return array
	 */
	public function recursiveTranslate(array $datas) {
		foreach ($datas as $k => $v) {
			if (is_array($v)) {
				$datas[$k] = $this->recursiveTranslate($v);

			} elseif (in_array($k, self::$_knownStringKeys)) {
				$datas[$k] = $this->_translator->translate($v);

			}

		}

		return $datas;

	}
}
?>