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

class ZendAfi_Form extends Zend_Form {
	public static function newWithOptions($options = null) {
		return new self($options);
	}


	public function init() {
		parent::init();
		$this
			->getPluginLoader(Zend_Form::ELEMENT)
			->addPrefixPath('ZendAfi_Form_Element', 'ZendAfi/Form/Element');
		$this
			->getPluginLoader(Zend_Form::DECORATOR)
			->addPrefixPath('ZendAfi_Form_Decorator', 'ZendAfi/Form/Decorator');
		$this
			->addElementPrefixPath('ZendAfi_Validate', 'ZendAfi/Validate', 'validate');
	}


	/**
	 * @param $name string
	 * @return Zend_Form_Element
	 */
	public function addRequiredTextNamed($name) {
		$this->addElement('text', $name, array('required' => true, 'allowEmpty' => false));
		return $this->getElement($name);
	}


	/**
	 * Validate the form
	 * 
	 * @param  mixed $data 
	 * @return boolean
	 */
	public function isValid($array_or_model) {
		if (is_array($array_or_model))
			return parent::isValid($array_or_model);

		$valid = parent::isValid($array_or_model->toArray()) & $array_or_model->isValid();
		$this->addModelErrors($array_or_model);		

		$this->_errorsExist = !$valid;
		return $valid;
	}


	/**
	 * @param  Storm_Model_Abstrict $model 
	 */
	public function addModelErrors($model) {
		$model->validate();
		foreach($model->getErrors() as $attribute => $message) {
			if ($element = $this->getElement($attribute))
				$element->addError($message);
		}
	}

}