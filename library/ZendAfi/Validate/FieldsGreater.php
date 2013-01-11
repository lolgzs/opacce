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

class ZendAfi_Validate_FieldsGreater extends Zend_Validate_Abstract {
	const NOT_GREATER = 'notGreater';
	const NOT_GREATER_OR_EQUALS = 'notGreaterOrEquals';
	
	protected $_messageVariables = array(
		'fieldToCompare' => '_field_to_compare_label',
	);

	protected $_messageTemplates = array(
		self::NOT_GREATER => "Should be greater than '%fieldToCompare%'",
		self::NOT_GREATER_OR_EQUALS => "Should be greater or equals to '%fieldToCompare%'",
	);

	/** @var array */
	protected $_fields_to_compare = array();
	/** @var string */
	protected $_field_to_compare;
	/** @var string */
	protected $_field_to_compare_label;
	/** @var boolean */
	protected $_with_equals = false;


	/**
	 * @param $field_to_compares array
	 */
	public function __construct($fields_to_compare, $with_equals = false) {
		$this->_fields_to_compare = $fields_to_compare;
		$this->_with_equals = $with_equals;
	}

	
	/**
	 * @param $value mixed
	 * @param $fields array
	 * @return boolean
	 */
	public function isValid($value, array $fields_values = array()) {
		$this->_setValue((string)$value);

		foreach ($this->_fields_to_compare as $k => $v) {
			$this->_field_to_compare_label = $v;
			$this->_field_to_compare = $k;

			if (!array_key_exists($this->_field_to_compare, $fields_values))
				continue;

			$result = ($this->_with_equals) ?
					($value < $fields_values[$this->_field_to_compare]) :
					($value <= $fields_values[$this->_field_to_compare]);

			if ($result) {
				$this->_error(($this->_with_equals) ?
					self::NOT_GREATER_OR_EQUALS :
					self::NOT_GREATER);
				return false;
			}
		}

		return true;
	}
}
?>