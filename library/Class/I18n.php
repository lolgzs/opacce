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

class Class_I18n {
	const BASE_PATH = '/i18n/';
	const MASTER_NAME = 'master';

	protected $_profilExtractor;

	protected static $_filePaths = array();

	/**
	 * @var Class_I18n
	 */
	protected static $_instance;

	/**
	 * @return Class_I18n
	 */
	public static function getInstance() {
		if (null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}


	/**
	 * @testing
	 */
	public static function reset() {
		self::$_instance = null;
	}

	/**
	 * @testing
	 * @param Class_I18n $instance
	 */
	public static function setInstance($instance){
		self::$_instance = $instance;
	}

	public function __construct() {
		$destination = USERFILESPATH . self::BASE_PATH;
		if (!file_exists($destination))
			mkdir($destination);
	}

	/**
	 * @codeCoverageIgnore
	 */
	private function __clone() {}


	public function setProfilExtractor($extractor) {
		$this->_profilExtractor = $extractor;
		return $this;
	}

	/**
	 * @return array
	 */
	public function generate() {
		$dictionary = array();
		$this->_extractFromProfiles($dictionary);
		$content = $this->_buildSerialized($dictionary);
		$this->_writeToMaster($content);
	}

	/**
	 * @param string $language
	 * @return array
	 */
	public function read($language = null) {
		$language = (string)$language;

		if ('' === $language)
			$language = 'master';

		if (!file_exists($this->_getFilePathFor($language)))
			$this->_createFileFor($language);

		ob_start();
		$datas = include $this->_getFilePathFor($language);
		ob_end_clean();

		return $datas;
	}

	/**
	 * @param string $language
	 * @param string $key
	 * @param string $value
	 */
	public function update($language, $key, $value) {
		$dictionary = $this->read($language);

		if (
			isset($dictionary[(string)$key])
			&& ($dictionary[(string)$key] == (string)$value)
		) {
			return;
		}

		$dictionary[(string)$key] = (string)$value;

		$this->_write($language, $this->_buildSerialized($dictionary));

	}

	/**
	 * @param string $language
	 * @param array $values
	 */
	public function updateAll($language, array $dictionary) {
		$this->_write($language, $this->_buildSerialized($dictionary));
	}

	/**
	 * @param array $dictionary
	 */
	protected function _extractFromProfiles(array &$dictionary) {
		if (null !== $this->_profilExtractor) {
			$profils = Class_Profil::getLoader()->findAll();

			foreach ($profils as $profil) {
				$this->_profilExtractor->setModel($profil);
				$dictionary = array_merge($dictionary, $this->_profilExtractor->extract());
			}
		}
	}

	/**
	 * @param string $content
	 */
	protected function _writeToMaster($content) {
		$this->_write(self::MASTER_NAME, $content);

	}

	/**
	 *
	 * @param string $language
	 * @param string $content
	 */
	protected function _write($language, $content) {
		$destination = USERFILESPATH . self::BASE_PATH;
		file_put_contents($this->_getFilePathFor($language), (string)$content);
	}

		/**
	 * @param array $dictionary
	 * @return string
	 */
	protected function _buildSerialized(array &$dictionary) {
		$serializationPattern = '<?php
return array(
%s
);
?>';

		$serializedDictionary = '';

		foreach ($dictionary as $k => $v) {
			if ('' != $v) {
				$serializedDictionary .= "'". $k ."' => '" . str_replace(array('\\', "'"), array('\\\\', "\'"), $v) . "',
";
			}
		}

		return sprintf($serializationPattern, $serializedDictionary);

	}

	/**
	 * @param string $language
	 */
	protected function _getFilePathFor($language) {
		if (!array_key_exists($language, self::$_filePaths))
			self::$_filePaths[$language] = USERFILESPATH . self::BASE_PATH . $language . '.php';

		return self::$_filePaths[$language];

	}

	/**
	 * @param string $language
	 */
	protected function _createFileFor($language) {
		if (file_exists($this->_getFilePathFor($language)))
			return;

		file_put_contents($this->_getFilePathFor($language), $this->_getEmptySerialization());

	}

	/**
	 * @return array
	 */
	public function _getEmptySerialization() {
		$empty = array();
		return $this->_buildSerialized($empty);
	}

}

?>