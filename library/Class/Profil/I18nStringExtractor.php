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
class Class_Profil_I18nStringExtractor extends Class_Profil_I18n {
	protected $_dictionary = array();


	/**
	 * @return array
	 * @throws RuntimeException
	 */
	public function extract() {
		if (null === $this->_model)
			throw new RuntimeException('Cannot extract without model');

		$this->_dictionary = array();

		$this->_extractConfigurations();

		return $this->_dictionary;
	}


	protected function _extractConfigurations() {
		foreach ($this->_knownConfigurations as $configCalls) {
			$config = $this->_model->_getRawCfgAsArrayNamed($configCalls[self::CONFIG_GETTER_KEY]);

			$visitable = new $configCalls[self::VISITABLE_CLASS_KEY]();
			$visitable->setData($config)->acceptVisitor($this);
		}
	}


	/**
	 * @param string $value
	 * @param array $dictionary
	 */
	protected function _visitArrayAction($value) {
		$value = (string)$value;

		if ('' == $value) return;

		$sum = md5($value);

		if (!array_key_exists($sum, $this->_dictionary)) {
			$this->_dictionary[$sum] = $value;

		}
	}
}

?>