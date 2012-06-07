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
class Class_WebService_OPDS_CatalogEntry {
	protected $_properties;
	protected $_isNotice;
	protected $_files;
	
	public function __construct() {
		$this->_isNotice = false;
		$this->_properties = array('id' => null,
															 'title' => null,
															 'link' => null,
															 'author' => null);
		$this->_files = array();
	}


	public function addFile($url, $mimeType) {
		$this->_files[] = Class_WebService_OPDS_EntryFile::newWith($url, $mimeType);
	}


	public function isNotice() {
		return $this->_isNotice;
	}


	public function beNotice() {
		$this->_isNotice = true;
	}


	public function hasFiles() {
		return 0 < count($this->_files);
	}


	public function import() {
		$category = Class_AlbumCategorie::getLoader()->newInstance()
			->setLibelle(sprintf('import opds du %s', date('d M Y')));
		$category->save();

		$album = Class_Album::getLoader()->newInstance()
			->setTitre($this->getTitle())
			->setAuteur($this->getAuthor())
			->setCategorie($category)
			->beEPUB();
		$album->save();

		foreach ($this->_files as $file)
			$file->newRessourceInAlbum($album);

		return $album;
	}


	public function __call($name, $args) {
		$prefix = substr($name, 0, 3);
		$key = strtolower(substr($name, 3));
		if ('set' == $prefix
				&& $this->_hasPropertyNamed($key)
				&& 1 == count($args)) {
			$this->_properties[$key] = $args[0];
			return $this;
		}

		if ('get' == $prefix
				&& $this->_hasPropertyNamed($key)) {
			return $this->_properties[$key];
		}

		throw new RuntimeException(sprintf('Call to undefined "%s" method', $name));
	}


	protected function _hasPropertyNamed($name) {
		return in_array($name, array_keys($this->_properties));
	}
}

?>
