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
		$this->_definitions = new ZendAfi_Controller_Action_RessourceDefinitions($this->_ressource_definition);
	}


	public function deleteAction() {
		if ($model = $this->_definitions->find($this->_getParam('id'))) {
			$model->delete();
			$this->_helper->notify($this->_definitions->successfulDeleteMessage($model));
		}
		$this->_redirect('/admin/'.$this->_request->getControllerName().'/index');
	}


	/**
	 * Formulaire d'édition des catalogues
	 * @param Class_OpdsCatalog $model
	 * @return Zend_Form
	 */
	protected function _getForm($model) {
		$form = $this->view->newForm(array('id' => $this->_definitions->getModelName()));
		$this->_definitions
			->addFormElements($form)
			->addDisplayGroups($form);
		
		return $form->populate($model->toArray());
	}
}


?>