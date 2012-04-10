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
abstract class ZendAfi_Form_Element_AbstractSuggest extends Zend_Form_Element_Xhtml {
	/** @var string */
	protected $_rubrique;
	/** @var string */
	protected $_name;

	abstract public function newSuggestDecorator();

	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);
		$decorators = $this->_decorators;
		$this->_decorators = array('Suggest' => $this->newSuggestDecorator());

		foreach ($decorators as $name => $value) {
			$this->_decorators[$name] = $value;
		}

		$this->removeDecorator('ViewHelper');
	}


	/**
	 * @param string $name
	 * @return ZendAfi_Form_Element_ListeSuggestion
	 */
	public function setName($name) {
		$this->_name = (string)$name;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}


	/**
	 * @param string $name
	 * @return ZendAfi_Form_Element_ListeSuggestion
	 */
	public function setRubrique($rubrique) {
		$this->_rubrique = (string)$rubrique;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @return string
	 */
	public function getRubrique() {
		return $this->_rubrique;
	}
}
?>