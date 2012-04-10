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
class Class_Folder_Manager {
	/** @var string */
	protected $_allowedBasePath;


	/**
	 * @param string $path
	 * @return Class_Folder_Manager
	 */
	public static function newInstanceLimitedTo($path) {
		$instance = new self();
		return $instance->setAllowedBasePath($path);
	}


	/**
	 * @category testing
	 * @param string $path
	 * @return Class_Folder_Manager
	 */
	public function setAllowedBasePath($path) {
		$this->_allowedBasePath = $path;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getAllowedBasePath() {
		if (null === $this->_allowedBasePath) {
			$this->_allowedBasePath = USERFILESPATH;
		}

		return $this->_allowedBasePath;
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	public function ensure($path) {
		if (0 !== strpos($path, $this->getAllowedBasePath())) {
			return false;
		}

		if (file_exists($path)) {
			return true;
		}

		return @mkdir($path, 0777, true);
	}
}
?>