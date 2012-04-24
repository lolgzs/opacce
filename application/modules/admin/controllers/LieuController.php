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
class Admin_LieuController extends Zend_Controller_Action {
	public function indexAction() {
		$this->view->titre = $this->view->_('Lieux');
		$this->view->lieux = Class_Lieu::getLoader()->findAllBy(array('order' => 'libelle'));
	}


	public function addAction() {
		$this->view->titre = $this->view->_('Déclarer un nouveau lieu');
		$lieu = new Class_Lieu();
		if ($this->_setupLieuFormAndSave($lieu, 'add')) {
			$lieu->save();
			$this->_helper->notify(sprintf('Lieu "%s" créé',  $lieu->getLibelle()));
			$this->_redirect('admin/lieu/edit/id/'.$lieu->getId());
			return;
		}
		$this->renderScript('lieu/add.phtml');
	}


	public function editAction() {
		if (!$lieu = Class_Lieu::getLoader()->find($this->_getParam('id'))) {
			$this->_redirect('admin/lieu/index');	
			return;
		}
			
		$this->view->titre = $this->view->_('Modifier le lieu: "%s"', $lieu->getLibelle());
		$this->_setupLieuFormAndSave($lieu, 'edit');
		$this->renderScript('lieu/edit.phtml');
	}


	public function deleteAction() {
		if ($lieu = Class_Lieu::getLoader()->find($this->_getParam('id'))) {
			$lieu->delete();
			$this->_helper->notify(sprintf('Lieu "%s" supprimé',  $lieu->getLibelle()));
		}
		$this->_redirect('admin/lieu/index');	
	}


	protected function _setupLieuFormAndSave($lieu, $action) {
		$saved = false;

		$form = $this->_formLieu($lieu, $action);
		$this->view->form = $form;
		$this->view->lieu = $lieu;

		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			$lieu
				->updateAttributes($this->_request->getPost())
				->save();
			return true;
		}

		return false;
	}


	public function _formLieu($lieu, $action) {
		return $this->view
			->newForm(array('id' => 'formLieu'))
			->setMethod('post')
			->setAction($this->view->url(array('action' => $action)))
			->addElement('text', 'libelle', array(
																						'label' => 'Libellé *',
																						'size'	=> 50,
																						'maxlength' => 100,
																						'required' => true,
																						'allowEmpty' => false	))

			->addElement('textarea', 'adresse', array(
																								'label' => 'Adresse',
																								'rows' => 10,
																								'cols' => 50))
			->addElement('text', 'code_postal', array(
																								'label' => 'Code postal',
																								'size' => 10,
																								'maxlength' => 10))
			->addElement('text', 'ville', array(
																					'label' => 'Ville',
																					'size' => 50,
																					'maxlength' => 50))
			->addElement('text', 'pays', array(
																				 'label' => 'Pays',
																				 'size' => 50,
																				 'maxlength' => 50))
			->addDisplayGroup(array('libelle', 
															'adresse',
															'code_postal',
															'ville',
															'pays'),
												'lieu',
												array('legend' => $this->view->_('Lieu')))
			->populate($lieu->toArray());
	}
}

?>