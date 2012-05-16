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
		$this->_properties = array('id' => null,
															 'title' => null,
															 'link' => null,
															 'author' => null);
		$this->_files = array();
	}


	public function addFile($url, $mimeType) {
		$this->_files[] = OPDSEntryFile::newWith($url, $mimeType);
	}


	public function isNotice() {
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


class OPDSEntryFile {
	protected $_url;
	protected $_type;
	protected static $_downloader;

	public static function newWith($url, $mimeType) {
		$instance = new self($url, $mimeType);
		return $instance;
	}


	public static function defaultDownloader($downloader) {
		self::$_downloader = $downloader;
	}


	public function __construct($url, $mimeType) {
		$this->_url = $url;
		$this->_type = $mimeType;
	}


	public function newRessourceInAlbum($album) {
		$ressource = Class_AlbumRessource::getLoader()
			->newInstance()
			->setAlbum($album);
		$ressource->save();

		$fileName = $ressource->getId() . '_' . $album->getTitre() . $this->getExtension();
		$destination = $ressource->getOriginalsPath() . $fileName;
		if (false === $this->getDownloader()->downloadFromUrlToDisk($this->_url, $destination))
			$ressource->delete();

		$ressource
			->setFichier($fileName)
			->save();
	}


	public function getExtension() {
		if ('application/pdf' == strtolower($this->_type)) 
			return '.pdf';

		if ('application/epub+zip' == strtolower($this->_type)) 
			return '.epub';

		return '';
	}


	public function getDownloader() {
		if (null != self::$_downloader) 
			return self::$_downloader;
		return new OPDSEntryDownloader();
	}
}


class OPDSEntryDownloader {
	public function downloadFromUrlToDisk($url, $path) {
		if (!$this->_ensureDirectory(dirname($path))) 
			return false;
		return copy($url, $path);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	protected function _ensureDirectory($path) {
		$folderManager = new Class_Folder_Manager();
		return $folderManager->ensure($path);
	}
}
?>
