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


	public function findAll($request) {
		$params = array('order' => $this->getOrder());
		if (($scope_field = $this->getScope()) && 
				($scope_value = $request->getParam($scope_field, false)))
			$params[$scope_field] = $scope_value;
		return $this->sort($this->getModelLoader()->findAllBy($params));
	}


	public function newModel() {
		return $this->getModelLoader()->newInstance();
	}


	public function doAfterAdd() {
		if (isset($this->_definitions['after_add']))
			$this->_definitions['after_add']();
	}


	public function doAfterEdit() {
		if (isset($this->_definitions['after_edit']))
			$this->_definitions['after_edit']();
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
		return $this->titleForAction('index');
	}


	public function titleForAction($action) {
		if (isset($this->_definitions['actions'][$action]['title']))
			return $this->_definitions['actions'][$action]['title'];
		return '';
	}


	public function editActionTitle() {
		return $this->titleForAction('edit');
	}


	public function addActionTitle() {
		return $this->titleForAction('add');
	}


	public function getModelClass() {
		return $this->_definitions['model']['class'];
	}

	
	public function getOrder() {
		if (isset($this->_definitions['model']['order']))
			return $this->_definitions['model']['order'];
		return 'libelle';
	}


	public function getScope() {
		if (isset($this->_definitions['model']['scope']))
			return $this->_definitions['model']['scope'];
		return null;
	}


	public function hasScope() {
		$scope = $this->getScope();
		return !empty($scope);
	}


	public function getModelName() {
		return $this->_definitions['model']['name'];
	}

	
	public function pluralizeModelName() {
		return Storm_Inflector::pluralize($this->getModelName());
	}


	public function addFormElements($form) {
		$element_definitions = $this->getFormElementDefinitions();
		
		foreach($element_definitions as $name => $definition) {
			$options = isset($definition['options']) ? $definition['options'] : array();

			$form->addElement($definition['element'], $name, $options);

			if ($label = $form->getElement($name)->getDecorator('label'))
				$label->setOption('escape', false);
		}
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


	public function sort($instances) {
		if (isset($this->_definitions['sort']))
			usort($instances, $this->_definitions['sort']);
		return $instances;
	}
}


?>