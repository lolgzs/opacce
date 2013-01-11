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

class Class_Upload {
	use Trait_Translator;
	
	/** @var string */
	protected $_name;

	/** @var string */
	protected $_baseName;

	/** @var string */
	protected $_basePath;

	/** @var string */
	protected $_error;

	/** @var UploadMover */
	protected $_uploadMover;

	/** @var Class_Folder_Manager */
	protected $_folderManager;

	/** @var string */
	protected $_savedFile;

	/** @var bool */
	protected $_required = false;

	/** @var array */
	protected $_allowedExtensions = [];
	

	/**
	 * @param string $inputName
	 * @return Class_Upload
	 */
	public static function newInstanceFor($inputName) {
		return new self($inputName);
	}


	/**
	 * @param string $inputName
	 */
	public function __construct($inputName) {
		$this->_name = $inputName;
	}


	/**
	 * @param bool $flag
	 * @return Class_Upload
	 */
	public function setRequired($flag) {
		$this->_required = (bool)$flag;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isRequired() {
		return $this->_required;
	}


	/** 
	 * @param array $extensions
	 * @return Class_Upload
	 */
	public function setAllowedExtensions(array $extensions) {
		$this->_allowedExtensions = $extensions;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getAllowedExtensions() {
		return $this->_allowedExtensions;
	}


	/**
	 * @return bool
	 */
	public function receive() {
		if (!$this->validate()) {
			return false;
		}

		$fileName			= $this->getBaseName() . '_' . $_FILES[$this->_name]['name'];
		$destination	= $this->getBasePath() . $fileName;

		if (!$this->getUploadMover()->moveTo($_FILES[$this->_name]['tmp_name'], $destination)) {
			$this->setError($this->getUploadMover()->getError());
			return false;
		}

		$this->setSavedFileName($fileName);

		return true;
	}


	/**
	 * @param string $name
	 * @return Class_Upload
	 */
	public function setBaseName($name) {
		$this->_baseName = (string)$name;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getBaseName() {
		return $this->_baseName;
	}


	/**
	 * @param string $name
	 * @return Class_Upload
	 */
	public function setBasePath($name) {
		$this->_basePath = (string)$name;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getBasePath() {
		return $this->_basePath;
	}


	/**
	 * @param string $error
	 * @return Class_Upload
	 */
	public function setError($error) {
		$this->_error = (string)$error;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}


	/**
	 * @return Class_Upload
	 */
	public function resetError() {
		$this->_error = null;
		return $this;
	}


	/**
	 * @category testing
	 * @param UploadMover $mover
	 * @return Class_Upload
	 */
	public function setUploadMover($mover) {
		$this->_uploadMover = $mover;
		return $this;
	}


	/**
	 * @return UploadMover
	 */
	public function getUploadMover() {
		if (null === $this->_uploadMover) {
			$this->_uploadMover = new Class_UploadMover_HttpPost();
		}

		return $this->_uploadMover;
	}


	/**
	 * @param string $path
	 * @return Class_Upload
	 */
	public function setSavedFileName($path) {
		$this->_savedFile = $path;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getSavedFileName() {
		return $this->_savedFile;
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
	 * @codeCoverageIgnore
	 * @return Class_Folder_Manager
	 */
	public function getFolderManager() {
		if (null === $this->_folderManager) {
			$this->_folderManager = new Class_Folder_Manager();
		}

		return $this->_folderManager;
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	protected function _ensureDirectory($path) {
		return $this->getFolderManager()->ensure($path);
	}


	/** @return bool */
	public function validate() {
		if ('' == $this->_name) {
			$this->setError($this->_('Transfert impossible, ce formulaire est mal configuré'));
			return false;
		}

		if (!array_key_exists($this->_name, $_FILES)) {
			$this->setError($this->_('Transfert impossible, champ de fichier introuvable'));
			return false;
		}

		if (0 == (int)$_FILES[$this->_name]['size']) {
			$this->setError($this->_('Le fichier était vide ou un problème réseau est survenu'));
			return false;
		}

		if (!$this->_ensureDirectory($this->getBasePath())) {
			$this->setError('Transfert impossible, le répertoire de destination n\'a pas pu être créé.');
			return false;
		}

		$parts = explode('.', $_FILES[$this->_name]['name']);
		$ext = end($parts);

		$allowedExtensions = $this->getAllowedExtensions();
		if ((0 < count($allowedExtensions))
				&& (!in_array($ext, $allowedExtensions))) {
			$this->setError(sprintf($this->_('Le fichier n\'est pas de type %s'), implode(', ', $allowedExtensions)));
			return false;
		}

		return true;
	}
}

?>