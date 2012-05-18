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

class Admin_OpdsController extends Zend_Controller_Action {
	public function init() {
		$this->view->titre = 'Catalogues OPDS';
		$this->view->catalogs = Class_OpdsCatalog::getLoader()->findAllBy(array('order' => 'libelle'));
	}


	public function indexAction() {

	}


	public function addAction() {
		$this->view->titre = 'Ajouter un catalogue OPDS';
		$model = Class_OpdsCatalog::getLoader()->newInstance();

		if ($this->_setupCatalogFormAndSave($model)) {
			$this->_helper->notify(sprintf('Catalogue "%s" ajouté', $model->getLibelle()));
			$this->_redirect('/admin/opds/edit/id/'.$model->getId());
		}
	}


	public function editAction() {
		$this->view->titre = 'Modifier un catalogue OPDS';

		if (!$model = Class_OpdsCatalog::getLoader()->find($this->_getParam('id'))) {
			$this->_redirect('/admin/opds/index');
			return;
		}

		
		if ($this->_setupCatalogFormAndSave($model)) {
			$this->_helper->notify(sprintf('Catalogue "%s" sauvegardé', $model->getLibelle()));
			$this->_redirect('/admin/opds/edit/id/' . $model->getId());
		}
	}


  protected function _setupCatalogFormAndSave($catalog) {
		$form = $this->_getForm($catalog);
		
		$this->view->form = $form;

		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			return $catalog
				->updateAttributes($this->_request->getPost())
				->save();
		}
		return false;
  }


	public function deleteAction() {
		if ($model = Class_OpdsCatalog::getLoader()->find($this->_getParam('id'))) {
			$model->delete();
			$this->_helper->notify(sprintf('Catalogue "%s" supprimé', $model->getLibelle()));
		}
		$this->_redirect('/admin/opds/index');
	}


	public function browseAction() {
		if (!$catalog = Class_OpdsCatalog::getLoader()->find($this->_getParam('id'))) {
			$this->_redirect('/admin/opds/index');
			return;
		}

		if ($entry_url = $this->_getParam('entry'))
			$catalog = $catalog->newForEntry($entry_url);

		$this->view->subview = $this->view->partial('opds/browse.phtml',
																								array('titre' => sprintf('Parcours du catalogue "%s"', $catalog->getLibelle()),
																											'catalog' => $catalog));

		$this->render('index');
	}


	public function importAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if ((!$catalog = Class_OpdsCatalog::getLoader()->find($this->_getParam('id')))
				|| !$this->_getParam('feed') || !$this->_getParam('entry')) {
			$this->_redirect('/admin/opds/index');
			return;
		}

		$catalog = $catalog->newForEntry($this->_getParam('feed'));
		if (!$entry = $catalog->getEntry($this->_getParam('entry')))
			return;
 
		$album = $entry->import();

		$this->_redirect('/admin/album/edit_album/id/' . $album->getId());
	}


	/**
	 * Formulaire d'édition des catalogues
	 * @param Class_OpdsCatalog $model
	 * @return Zend_Form
	 */
	protected function _getForm($model) {
		return $this->view->newForm(array('id' => 'catalog'))
			->addElement('text', 'libelle', array('label' => 'Libellé *',
																						'size'	=> 30,
																						'required' => true,
																						'allowEmpty' => false))
			->addElement('text', 'url', array('label' => 'Url *',
																				'size' => '90',
																				'required' => true,
																				'allowEmpty' => false,
																				'validators' => array('url')))
			->addDisplayGroup(array('libelle', 'url'), 'categorie', array('legend' => 'Catalogue'))
			->populate($model->toArray());
	}
}

?>