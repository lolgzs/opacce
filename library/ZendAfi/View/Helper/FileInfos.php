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
class ZendAfi_View_Helper_FileInfos extends Zend_View_Helper_Abstract {
	/** @var array */
	protected $_units = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');


	/**
	 * @param string $path
	 * @return string
	 */
	public function fileInfos($path) {
		return $this->fileName($path) . $this->fileSize($path);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public function fileName($path) {
		return end(explode('/', $path));
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public function fileExtension($path) {
		$parts = explode('.', $path);
		return '.' . end($parts);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public function fileSize($path) {
		if (!file_exists($path)) {
			return '';
		}

		$mod = 1024;
		$size = filesize($path);
		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}

		return ', ' . round($size, 2) . ' ' . $this->_units[$i];
	}
}
?>