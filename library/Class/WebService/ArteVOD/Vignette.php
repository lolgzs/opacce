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

class Class_WebService_ArteVOD_Vignette  extends Class_WebService_Abstract {
	protected static $_instance;
	protected $_file_writer;
	protected $_updload_mover;
	protected $_url_validator;


	public static function getInstance() {
		if (!isset(static::$_instance))
			static::$_instance = new static();
		return static::$_instance;
	}


	public static function resetInstance() {
		static::$_instance = null;
	}


	public function __construct() {
		$this->_upload_mover = new Class_UploadMover_LocalFile();
		$this->_url_validator = new ZendAfi_Validate_Url();
	}


	public function updateAlbum($album) {
		$url_poster = $album->getPoster();
		if (!($url_poster && $this->_url_validator->isValid($url_poster)))
			return $this;

		if (!$image = static::getHttpClient()->open_url($url_poster))
			return $this;

		$parts = explode('/', $url_poster);
		$filename = array_pop($parts);
		$temp_name = PATH_TEMP.$filename;
		if (false === $this->getFileWriter()->putContents($temp_name, $image))
			return $this;

		$_FILES['fichier'] = ['name' => $filename,
													'tmp_name' => $temp_name,
													'size' => strlen($image)];

		$album->setUploadMover('fichier', $this->_upload_mover);

		$album->receiveFile();
		$album->save();

		return $this;
	}


	public function setFileWriter($file_writer) {
		$this->_file_writer = $file_writer;
	}


	public function getFileWriter() {
		if (!isset($this->_file_writer))
			$this->_file_writer = new Class_FileWriter();
		return $this->_file_writer;
	}
}

?>