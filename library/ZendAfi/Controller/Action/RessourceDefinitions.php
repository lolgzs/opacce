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

class ZendAfi_Controller_Action_RessourceDefinitions {
	protected $_definitions;

	public function __construct($definitions_array) {
		$this->_definitions = $definitions_array;
	}


	public function getModelLoader() {
		return Storm_Model_Abstract::getLoaderFor($this->getModelClass());
	}


	public function find($id) {
		return $this->getModelLoader()->find($id);
	}


	public function findAll() {
		return $this->getModelLoader()->findAllBy(array('order' => 'libelle'));
	}


	public function newModel() {
		return $this->getModelLoader()->newInstance();
	}

	
	public function successfulDeleteMessage($model) {
		return sprintf($this->_definitions['messages']['successful_delete'],
									 $model->getLibelle());
	}


	public function successfulSaveMessage($model) {
		return sprintf($this->_definitions['messages']['successful_save'],
									 $model->getLibelle());
	}


	public function successfulAddMessage($model) {
		return sprintf($this->_definitions['messages']['successful_add'],
									 $model->getLibelle());
	}


	public function indexActionTitle() {
		return $this->_definitions['actions']['index']['title'];
	}


	public function editActionTitle() {
		return $this->_definitions['actions']['edit']['title'];
	}


	public function addActionTitle() {
		return $this->_definitions['actions']['add']['title'];
	}


	public function getModelClass() {
		return $this->_definitions['model']['class'];
	}


	public function getModelName() {
		return $this->_definitions['model']['name'];
	}

	
	public function pluralizeModelName() {
		return Storm_Inflector::pluralize($this->getModelName());
	}


	public function addFormElements($form) {
		$element_definitions = $this->getFormElementDefinitions();
		foreach($element_definitions as $name => $definition)
			$form->addElement($definition['element'], $name, $definition['options']);
		return $this;
	}


	public function addDisplayGroups($form) {
		foreach($this->_definitions['display_groups'] as $name => $definition) 
			$form->addDisplayGroup(array_keys($definition['elements']), 
														 $name, 
														 array('legend' => $definition['legend']));
	}


	public function getFormElementDefinitions() {
		$definitions = array();
		foreach($this->_definitions['display_groups'] as $group) 
			$definitions = array_merge($definitions, $group['elements']);

		return $definitions;
	}
}


?>