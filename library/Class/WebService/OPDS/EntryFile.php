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

class Class_WebService_OPDS_EntryFile {
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
		return new Class_WebService_OPDS_EntryDownloader();
	}
}

?>