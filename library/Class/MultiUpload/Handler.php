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
class Class_MultiUpload_Handler {
	/** @var int */
	protected $_thumbWidth = 160;

	/** @var int */
	protected $_thumbHeight = 120;

	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var string */
	protected $_error;


	/**
	 * @param Zend_Controller_Request_Http $request
	 */
	public function __construct($request) {
		$this->_request = $request;
	}


	/**
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * @codeCoverageIgnore
	 * @param string $path
	 * @return bool
	 */
	public function save($path) {}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getName() {
		return '';
	}


	/**
	 * @codeCoverageIgnore
	 * @return int
	 */
	public function getSize() {
		return 0;
	}
}
?>