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
	protected $_file_writer;

	public function updateAlbum($album) {
		$parts = explode('/', $album->getPoster());
		$filename = array_pop($parts);
		$temp_name = PATH_TEMP.$filename;

		$image = static::getHttpClient()->open_url($album->getPoster());
		$this->getFileWriter()->putContents($temp_name, $image);

		$_FILES['fichier'] = ['name' => $filename,
													'tmp_name' => $temp_name,
													'size' => strlen($image)];

		$album->setUploadMover('fichier', new Class_UploadMover_LocalFile());

		$album->save();
	}


	public function setFileWriter($file_writer) {
		$this->_file_writer = $file_writer;
	}


	public function getFileWriter() {
		return $this->_file_writer;
	}
}

?>