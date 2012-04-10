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
class Class_MultiUpload_HandlerXhr extends Class_MultiUpload_Handler {
	/**
	 * @param string $path
	 * @return bool
	 * @codeCoverageIgnore
	 */
	public function save($path)	{
		$input		= fopen('php://input', 'r');
		$temp			= tmpfile();
		$realSize	= stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {
			$this->_error = 'Transmitted filesize and declared dont match';
			return false;
		}

		$target = fopen($path, 'w');
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->_request->getParam('qqfile');
	}


	/**
	 * @return int
	 */
	public function getSize() {
		return (int)$this->_request->getServer('CONTENT_LENGTH');
	}
}
?>