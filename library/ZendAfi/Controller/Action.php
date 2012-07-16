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

class ZendAfi_Controller_Action extends Zend_Controller_Action {
	protected $_definitions;

	public function init() {
		$this->_definitions = new ZendAfi_Controller_Action_RessourceDefinitions($this->getRessourceDefinitions());
	}


	public function indexAction() {
		$var_name = $this->_definitions->pluralizeModelName();
		$this->view->$var_name = $this->_definitions->findAll();
		$this->view->titre = $this->_definitions->indexActionTitle();
	}


	public function deleteAction() {
		if ($model = $this->_definitions->find($this->_getParam('id'))) {
			$model->delete();
			$this->_helper->notify($this->_definitions->successfulDeleteMessage($model));
		}
		$this->_redirectToIndex();
	}


	public function editAction() {
		$this->view->titre = $this->_definitions->editActionTitle();

		if (!$model = $this->_definitions->find($this->_getParam('id'))) {
			$this->_redirectToIndex();
			return;
		}
		
		if ($this->_setupFormAndSave($model)) {
			$this->_helper->notify($this->_definitions->successfulSaveMessage($model));
			$this->_redirectToEdit($model);
		}
		
		$this->_postEditAction($model);
	}


	public function addAction() {
		$this->view->titre = $this->_definitions->addActionTitle();
		$model = $this->_definitions->newModel();

		if ($this->_setupFormAndSave($model)) {
			$this->_helper->notify($this->_definitions->successfulAddMessage($model));
			$this->_redirectToEdit($model);
		}
	}


	protected function _redirectToIndex() {
		$this->_redirect('/admin/'.$this->_request->getControllerName().'/index');
	}


	protected function _redirectToEdit($model) {
		$this->_redirect('/admin/'.$this->_request->getControllerName().'/edit/id/'.$model->getId());
	}


  protected function _setupFormAndSave($model) {
		$form = $this->_getForm($model);
		
		$this->view->form = $form;

		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			$values = $form->getValues();
			return $model
				->updateAttributes($values)
				->save();
		}
		return false;
  }


	/**
	 * Formulaire d'édition des catalogues
	 * @param Storm_Model_Abstract $model
	 * @return Zend_Form
	 */
	protected function _getForm($model) {
		$form = $this->view->newForm(array('id' => $this->_definitions->getModelName()));
		$this->_definitions
			->addFormElements($form)
			->addDisplayGroups($form);
		
		return $form->populate($model->toArray());
	}


	/**
	 * Hook appelé en fin d'action d'édition
	 * @param $model Storm_Model_Abstract
	 */
	protected function _postEditAction($model) {}
}
?>