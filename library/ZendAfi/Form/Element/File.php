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

class ZendAfi_Form_Element_File extends Zend_Form_Element_Xhtml {
	/** @var string Default view helper */
	public $helper = 'formFile';

	/** @var string */
	protected $_basePath = '';

	/** @var string */
	protected $_baseUrl = '';

	/** @var string */
	protected $_thumbnailUrl = '';

	/** @var string */
	protected $_actionUrl;


	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);

		$this->_insertDecoratorsBefore(['File' => new ZendAfi_Form_Decorator_File(),
				                            'DeleteButton' => new ZendAfi_Form_Decorator_DeleteButton()]);
	}
	

	/**
	 * @param $decoratorsToInsert array
	 */
	protected function _insertDecoratorsBefore($decoratorsToInsert) {
		if (!$decoratorsToInsert)
			return;

		$decorators = $this->_decorators;
		$this->_decorators = $decoratorsToInsert;

		foreach ($decorators as $name => $value)
			$this->_decorators[$name] = $value;
	}
	

	/**
	 * @param string $path
	 * @return ZendAfi_Form_Element_File
	 */
	public function setBasePath($path) {
		$this->_basePath = $path;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getBasePath() {
		return $this->_basePath;
	}


	/**
	 * @param string $url
	 * @return ZendAfi_Form_Element_File
	 */
	public function setBaseUrl($url) {
		$this->_baseUrl = $url;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_baseUrl;
	}


	/**
	 * @param string $url
	 * @return ZendAfi_Form_Element_File
	 */
	public function setThumbnailUrl($url) {
		$this->_thumbnailUrl = $url;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getThumbnailUrl() {
		return $this->_thumbnailUrl;
	}


	/**
	 * @param type $url
	 * @return ZendAfi_Form_Element_ImageDelete
	 */
	public function setActionUrl($url) {
		$this->_actionUrl = (string)$url;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getActionUrl() {
		return $this->_actionUrl;
	}
}
?>