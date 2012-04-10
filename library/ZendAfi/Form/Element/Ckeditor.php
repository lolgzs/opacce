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
class ZendAfi_Form_Element_Ckeditor extends Zend_Form_Element_Xhtml {
	/** @var boolean */
	protected $_show_file_browser = true;
	
	/** @var string */
	protected $_name;

	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);
		$decorators = $this->_decorators;
		$this->_decorators = array('Ckeditor' => new ZendAfi_Form_Decorator_Ckeditor());

		foreach ($decorators as $name => $value) {
			$this->_decorators[$name] = $value;
		}

		$this->removeDecorator('ViewHelper');
	}


	/**
	 * @param boolean $show
	 * @return ZendAfi_Form_Element_CkEditor
	 */
	public function setShowFileBrowser($show) {
		$this->_show_file_browser = (bool)$show;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getShowFileBrowser() {
		return $this->_show_file_browser;
	}
}
?>