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
class Class_MultiUpload {
	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var int */
	protected $_sizeLimit = 10485760;

	/** @var Class_MultiUpload_HandlerFactory */
	protected $_handlerFactory;

	/** @var Class_Folder_Manager */
	protected $_folderManager;

	/** @var Class_MultiUpload_Handler */
	protected $_handler;

	/** @var string */
	protected $_error;

	/** @var MultiUploadSettingReader */
	protected $_settingsReader;

	/** @var array */
	protected static $_imagesExtensions = array(
		'jpeg', 'jpg', 'gif', 'png'
	);

	/** @var array */
	protected $_allowedExtensions = array();

	/** @var string */
	protected $_savedFileName;


	/**
	 * @param type $request
	 * @return Class_MultiUpload
	 */
	public static function newInstanceWith($request) {
		return new self($request);
	}


	/**
	 * @param Zend_Controller_Request_Http $request
	 * @param array $allowedExtensions
	 * @param type $sizeLimit
	 */
	public function __construct($request) {
		$this->_request = $request;
		$this->_allowedExtensions = self::$_imagesExtensions;
	}


	/**
	 * @testing
	 * @param MultiUploadSettingReader $reader
	 * @return Class_MultiUpload
	 */
	public function setSettingsReader($reader) {
		$this->_settingsReader = $reader;
		return $this;
	}


	/**
	 * @testing
	 * @return MultiUploadSettingReader
	 * @codeCoverageIgnore
	 */
	public function getSettingsReader() {
		if (null === $this->_settingsReader) {
			$this->_settingsReader = new MultiUploadSettingReader();
		}

		return $this->_settingsReader;
	}


	/**
	 * @param Class_MultiUpload_HandlerFactory $factory
	 * @return Class_MultiUpload
	 */
	public function setHandlerFactory($factory) {
		$this->_handlerFactory = $factory;
		return $this;
	}


	/**
	 * @return Class_MultiUpload_HandlerFactory
	 * @codeCoverageIgnore
	 */
	public function getHandlerFactory() {
		if (null === $this->_handlerFactory) {
			$this->_handlerFactory = new Class_MultiUpload_HandlerFactory();
		}

		return $this->_handlerFactory;
	}


	/**
	 * @category testing
	 * @param Class_Folder_Manager $manager
	 * @return Class_MultiUpload
	 */
	public function setFolderManager($manager) {
		$this->_folderManager = $manager;
		return $this;
	}


	/**
	 * @return Class_Folder_Manager
	 */
	public function getFolderManager() {
		if (null === $this->_folderManager) {
			$this->_folderManager = new Class_Folder_Manager();
		}

		return $this->_folderManager;
	}


	/**
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}


	/**
	 * @return string
	 */
	public function getSavedFileName() {
		return $this->_savedFileName;
	}


	/**
	 * @param string $uploadDirectory
	 * @param string $filename_prefix
	 * @return bool
	 */
	public function handleUpload($uploadDirectory, $filename_prefix) {
		if (!$this->_isPhpSettingsCompatible())
			return false;

		if (!$this->_setHandler())
			return false;

		if (0 == ($size = $this->_handler->getSize())) {
			$this->_error = 'Aucun fichier transmis';
			return false;
		}

		if ($size > $this->_sizeLimit) {
			$this->_error = 'Fichier trop volumineux';
			return false;
		}

		$filename = $filename_prefix . '_' . $this->_handler->getName();

		if (!$this->_ensureDirectory($uploadDirectory))
			return false;

		if (!$this->_handler->save($uploadDirectory . $filename)) {
			$this->_error = $this->_handler->getError();
			return false;
		}

		$this->_savedFileName = $filename;
		return true;
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	protected function _ensureDirectory($path) {
		if (!$this->getFolderManager()->ensure($path)) {
			$this->_error = sprintf('Le dossier "%s" n\'est pas accessible en écriture', $path);
			return false;
		}

		return true;
	}

	/**
	 * @return bool
	 */
	protected function _setHandler() {
		$this->_handler = $this->getHandlerFactory()->getHandlerFor($this->_request);

		if (null === $this->_handler) {
			$this->_error = 'No handler set';
			return false;
		}

		return true;
	}


	/**
	 * @return bool
	 */
	protected function _isPhpSettingsCompatible() {
		$settingsReader = $this->getSettingsReader();
		$postSize		= $this->_toBytes($settingsReader->get('post_max_size'));
		$uploadSize	= $this->_toBytes($settingsReader->get('upload_max_filesize'));

		if (
			($postSize < $this->_sizeLimit )
			|| ($uploadSize < $this->_sizeLimit)
		) {
			$size = max(array(1, $this->_sizeLimit / 1024 / 1024)) . 'M';
			$this->_error = 'Paramétrage du serveur : monter le post_max_size et le upload_max_filesize à ' . $size;
			return false;
		}

		return true;
	}

	/**
	 * @param string $string
	 * @return int
	 */
	private function _toBytes($string) {
		$string = trim($string);

		$val		= (int)substr($string, 0, -1);
		$unit		= strtolower(substr($string, -1));

		switch ($unit) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}

		return $val;
	}
}




/**
 * @testing
 */
class MultiUploadSettingReader {
	protected $_settings = array();
	
	/**
	 * @return MultiUploadSettingReader
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $name
	 * @return string
	 * @codeCoverageIgnore
	 */
	public function get($name) {
		if (!array_key_exists($name, $this->_settings))
			$this->set($name, ini_get($name));
		return $this->_settings[$name];
	}


	public function set($name, $value) {
		$this->_settings[$name] = $value;
		return $this;
	}
}
?>