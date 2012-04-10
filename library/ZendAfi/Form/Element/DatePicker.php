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
class ZendAfi_Form_Element_DatePicker extends Zend_Form_Element_Xhtml {
	/** @var string */
	protected $_date_min;

	/** @var string */
	protected $_date_max;
	
	/** @var string */
	protected $_name;


	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);
		$decorators = $this->_decorators;
		$this->_decorators = array('DatePicker' => new ZendAfi_Form_Decorator_DatePicker());

		foreach ($decorators as $name => $value) {
			$this->_decorators[$name] = $value;
		}

		$this->removeDecorator('ViewHelper');
	}


	/**
	 * @param string $date_min
	 * @return ZendAfi_Form_Element_DatePicker
	 */
	public function setDateMin($date_min) {
		$this->_date_min = (string)$date_min;
		return $this;
	}


	/**
	 * @param string $date_max
	 * @return ZendAfi_Form_Element_DatePicker
	 */
	public function setDateMax($date_max) {
		$this->_date_max = (string)$date_max;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getDateMin() {
		if (!isset($this->_date_min))
			$this->_date_min = (int)date('Y') - 1;
		return $this->_date_min;
	}

	/**
	 * @return string
	 */
	public function getDateMax() {
		if (!isset($this->_date_max))
			$this->_date_max = $this->getDateMin() + 10;
		return $this->_date_max;
	}
}
?>