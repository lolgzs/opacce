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

class Admin_OpdsController extends ZendAfi_Controller_Action {
	public function getRessourceDefinitions() {
		return array(
								 'model' => array('class' => 'Class_OpdsCatalog',
																	'name' => 'catalog'),
								 'messages' => array('successful_add' => 'Catalogue %s ajouté',
																		 'successful_save' => 'Catalogue %s sauvegardé',
																		 'successful_delete' => 'Catalogue %s supprimé'),

								 'actions' => array('edit' => array('title' => 'Modifier un catalogue OPDS'),
																		'add'  => array('title' => 'Ajouter un catalogue OPDS'),
																		'index' => array('title' => 'Catalogues OPDS')),

								 'display_groups' => array('categorie' => array('legend' => 'Catalogue',
																																'elements' => array(
																																										'libelle' => array('element' => 'text',
																																																			 'options' =>  array('label' => 'Libellé *',
																																																													 'size'	=> 30,
																																																													 'required' => true,
																																																													 'allowEmpty' => false)),
																																										'url' => array('element' => 'text',
																																																	 'options' => array('label' => 'Url *',
																																																											'size' => '90',
																																																											'required' => true,
																																																											'allowEmpty' => false,
																																																											'validators' => array('url'))
																																																	 )
																																										)
																																)
																					 )
								 );
	}


	public function browseAction() {
		if (!$catalog = Class_OpdsCatalog::getLoader()->find($this->_getParam('id'))) {
			$this->_redirect('/admin/opds/index');
			return;
		}

		if ($this->_request->isPost()
				&& $catalog->hasSearch()
				&& ($form = $catalog->getSearchForm())
				&& $form->isValid($this->_request->getPost())) {
			$this->_redirect('/admin/opds/browse/id/' . $catalog->getId() . '?entry=' . urlencode($catalog->getSearch()->entryForTerm($form->getValue('search'))));
			return;
		}


		if ($entry_url = $this->_getParam('entry'))
			$catalog = $catalog->newForEntry($entry_url);

		$this->view->subview = $this->view->partial('opds/browse.phtml',
																								array('titre' => sprintf('Parcours du catalogue "%s"', $catalog->getLibelle()),
																											'catalog' => $catalog));

		$this->_forward('index');
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

}

?>