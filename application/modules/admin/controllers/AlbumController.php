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
class Admin_AlbumController extends Zend_Controller_Action {
	use Trait_Translator;

	protected $_baseUrlOptions = ['module' => 'admin',
		                            'controller' => 'album'];
		
	public function init() {
		$this->view->titre = 'Collections';

		Class_ScriptLoader::getInstance()
			->addJQueryReady('$("input.permalink").click(function(){$(this).select();})');
	}


	public function indexAction() {
		$categories = Class_AlbumCategorie::getLoader()->findAllBy(['parent_id' => 0, 'order' => 'libelle']);
		$categories []= Class_AlbumCategorie::getLoader()
				->newInstanceWithId(0)
				->setLibelle('Albums non classés')
				->setSousCategories(array());

		$this->view->categories = array(array('bib' => Class_Bib::getLoader()->getPortail(),
																					'containers' => $categories));
		$this->view->containersActions = $this->_getTreeViewContainerActions();
		$this->view->itemsActions = $this->_getTreeViewItemActions();
		$this->view->headScript()->appendScript('var treeViewSelectedCategory = '
			. (int)$this->_getParam('id_cat') . ';'
			. 'var treeViewAjaxBaseUrl = "' . $this->view->url(array('action' => 'items-of')) . '"');
		$this->view->headScript()->appendFile(URL_ADMIN_JS . 'tree-view.js');

	}


	public function itemsOfAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		$response = array();

		if (null == ($category = Class_AlbumCategorie::getLoader()->find($this->_getParam('id')))) {
			echo json_encode($response);
			exit;
		}

		new ZendAfi_View_Helper_TreeView();
		$renderStrategy = new TreeViewRenderItemWithIconeSupportStrategy($this->view);
		foreach ($category->getItems() as $item) {
			$response[] = array('label' => $renderStrategy->render($item));
		}

		echo json_encode($response);
		exit;	
	}


	public function importeadAction() {
		$this->view->titre = 'Import EAD';

		$form = $this->_formImportEAD();
		$this->view->form_import_ead = $form;
		
		if (!$this->_request->isPost())
			return;

		if ($form->isValid($this->_request->getPost()) && $form->ead->receive()) {
			$ead = new Class_EAD();
			$ead->loadFile($form->ead->getFileName());
			$this->_helper->notify(sprintf('%d albums importés', count($ead->getAlbums())));
			$this->_redirect('admin/album');
			return;
		} 
			
		$this->_helper->notify('Le fichier reçu n\'est pas valide');
		$this->_redirect('admin/album/importead');
	}

	
	protected function _formImportEAD() {
		return $this->view
			->newForm(array('id' => 'import_ead',
											'class' => 'form'))
			->setMethod('post')
			->setAttrib('enctype', 'multipart/form-data')
			->setAction($this->view->url(array('action' => 'import_ead')))
			->addElement($this->view->newFormElementFile('ead', 'xml'), 'ead')
			->addElement('submit', 'submit', array('label' => 'Importer le fichier EAD'));
	}


	public function addcategorietoAction() {
		$parent_id				= $this->_getParam('id');
		$parent_categorie	= Class_AlbumCategorie::getLoader()->find($parent_id);

		if (!$parent_categorie->hasParentCategorie()) {
			$titre = 'Ajouter une catégorie à la collection "'
																				. $parent_categorie->getLibelle() . '"';

		} else {
			$titre = 'Ajouter une sous-catégorie à la catégorie "'
																				. $parent_categorie->getLibelle() . '"';
		}

		$categorie = Class_AlbumCategorie::getLoader()
			->newInstance()
			->setParentCategorie($parent_categorie);

		$this->_renderCategoryForm($categorie, $titre);
	}


	public function addcategorieAction() {
		$this->_renderCategoryForm(Class_AlbumCategorie::getLoader()->newInstance(),
															 'Ajouter une collection');
	}


	public function editcategorieAction() {
		$id = $this->_getParam('id');
		$categorie = Class_AlbumCategorie::getLoader()->find($id);

		$this->_renderCategoryForm(
			$categorie,
			'Modification de la collection "' . $categorie->getLibelle() . '"');
	}


	public function deletecategorieAction() {
		Class_AlbumCategorie::getLoader()
			->find($this->_getParam('id'))
			->delete();
		$this->_redirect('admin/album/index');
	}


	public function addalbumtoAction() {
		if (null === ($categorie = Class_AlbumCategorie::getLoader()
																							->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$album = Class_Album::getLoader()
							->newInstance()
							->setCategorie($categorie);

		$this->_renderAlbumForm(
			$album,
			'Ajouter un album dans la collection "' . $categorie->getLibelle() . '"'
			);
	}


	public function editalbumAction() {
		$this->_renderAlbumForm(
			$album = Class_Album::getLoader()->find($this->_getParam('id')),
			'Modifier l\'album "' . $album->getTitre() . '"'
		);
	}


	public function deletealbumAction() {
		Class_Album::getLoader()
			->find($this->_getParam('id'))
			->delete();
		$this->_redirect('admin/album/index');
	}


	public function previewalbumAction() {
		$album = Class_Album::getLoader()->find($this->_getParam('id'));
		$form = $this->_thumbnailsForm($album);
		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			if ($album
					->updateAttributes($this->_request->getPost())
					->save()) {
				$this->_helper->notify('Paramètres sauvegardés');
				$this->_redirect('admin/album/preview_album/id/'.$album->getId());
				return;
			}
		}

		$this->view->titre = sprintf('Visualisation de l\'album "%s"',
																 $album->getTitre());
		$this->view->album = $album;
		$this->view->form = $form;
	}


	public function editimagesAction() {
		if (null === ($album = Class_Album::getLoader()->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$this->view->album			= $album;
		$this->view->ressources	= $album->getRessources();

		$this->view->titre = 'Médias de l\'album "'
				. $album->getTitre()
				. '" dans la collection "'
				. $album->getCategorie()->getLibelle() . '"';
	}


	public function addRessourceAction () {
		if (null === ($album = Class_Album::find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$this->view->album = $album;
		$ressource = Class_AlbumRessource::newInstance()->setAlbum($album);

		if ($this->_setupRessourceFormAndSave($ressource)) {
			$this->_helper->notify('Média "' . $ressource->getTitre() . '" sauvegardé');
			$this->_redirect('admin/album/edit_ressource/id/' . $ressource->getId());
			return;
		}

		$this->view->errors = $ressource->getErrors();
	}

		
	public function editressourceAction() {
		if (null === ($ressource = Class_AlbumRessource::getLoader()
																							->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		if ($this->_setupRessourceFormAndSave($ressource)) {
			$this->_helper->notify('Média "' . $ressource->getTitre() .  '" sauvegardé');
			$this->_redirect('admin/album/edit_ressource/id/' . $ressource->getId());
			return;
		}

		$this->view->errors = $ressource->getErrors();
		$this->view->form->getElement('fichier')
				->setValue($ressource->getFichier());
		$this->view->form->getElement('poster')
				->setValue($ressource->getPoster());

		$this->view->ressource	= $ressource;
	}


	protected function _setupRessourceFormAndSave($model) {
		$form = $this->_ressourceForm($model);
		
		$this->view->form = $form;

		if ($this->_request->isPost()) {
			$model->updateAttributes($this->_request->getPost());
			
			return $form->isValid($model)
				     && $model->save()
					   && $model->receiveFiles()
				     && $model->getAlbum()->save();
		}

		return false;
  }
		

	public function sortressourcesAction() {
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));
		$album->sortRessourceByFileName()->save();
		$this->_helper->notify('Médias réordonnés par nom de fichier'); 
		$this->_redirect('admin/album/edit_images/id/'.$album->getId());
	}


	public function massRessourceDeleteAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);
		if ((!$ids = $this->_getParam('ids'))
				 || (!$id = $this->_getParam('id'))) {
			$this->_helper->notify('Paramètres manquants dans la requête de suppression');
			return;
		}

		$ids = explode(',', $ids);
		if (empty($ids)) {
			$this->_helper->notify('Rien à supprimer');
			return;
		}

		$deleted_count = 0;
		foreach ($ids as $ressource_id) {
			if (!$ressource = Class_AlbumRessource::find($ressource_id))
				continue;

			if (!$ressource->getAlbum())
				continue;

			if ($id != $ressource->getAlbum()->getId())
				continue;

			$ressource->delete();
			++$deleted_count;
		}

		$this->_helper->notify($deleted_count . ' média(s) supprimé(s)');
	}


	public function deleteimageAction() {
		if (null === ($ressource = Class_AlbumRessource::getLoader()
																							->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$ressource->delete();
		$this->_redirect('admin/album/edit_images/id/'
																							. $ressource->getAlbum()->getId());

	}


	public function moveImageAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);

		if (null === ($ressource = Class_AlbumRessource::getLoader()
																							->find($this->_getParam('id')))) {
			return;
		}

		$ressource
			->getAlbum()
			->moveRessourceAfter($ressource, (int)$this->_getParam('after'));
	}


	public function albumDeleteVignetteAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);

		if (null === ($album = Class_Album::getLoader()	->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$album->deleteVignette();
		$this->_redirect('admin/album/edit_album/id/' . $album->getId());
	}


	public function albumDeletePdfAction() {
		$this->_helper->getHelper('viewRenderer')->setNoRender(true);

		if (null === ($album = Class_Album::getLoader()	->find($this->_getParam('id')))) {
			$this->_redirect('admin/album');
			return;
		}

		$album->deletePdf();
		$this->_redirect('admin/album/edit_album/id/' . $album->getId());
	}


	/**
	 * Formulaire d'édition des catégories
	 * @param Class_AlbumCategorie $categorie
	 * @return Zend_Form
	 */
	protected function _categorieForm($categorie) {
		$form = new Zend_Form(array('id' => 'categorie'));

		$form->addElement(new Zend_Form_Element_Text('libelle', array(
						'label' => 'Libellé *',
						'size'	=> 30,
						'required' => true,
						'allowEmpty' => false
					)))
					->addDisplayGroup(
							array('libelle'), 'categorie',
							array('legend' => ($categorie->hasParentCategorie())
																		? 'Catégorie'
																		: 'Collection'))
					->populate($categorie->toArray());


		return $form;
	}


	/**
	 * @param Class_AlbumCategorie $categorie
	 * @return null
	 */
	public function _validateAndAddCategorie($categorie) {
		$form = $this->_categorieForm($categorie);
		$this->view->form = $form;

		if (!$this->getRequest()->isPost() or !$form->isValid($_POST))
			return;

		$categorie
			->updateAttributes($_POST)
			->save();
		$this->_redirect('admin/album/index');
	}


	/**
	 * @param Class_AlbumCategorie $categorie
	 * @param string $titre
	 */
	protected function _renderCategoryForm($categorie, $titre) {
		$this->_validateAndAddCategorie($categorie);

		$this->view->titre = $titre;
		$this->render('categorie_form');
	}


	/**
	 * @param Class_Album $album
	 * @return Zend_Form
	 */
	protected function _albumForm($album) {
		return ZendAfi_Form_Album::newWith($album);
	}


	/**
	 * @param Class_AlbumRessource $ressource
	 * @return Zend_Form
	 */
	protected function _ressourceForm($ressource) {
		return ZendAfi_Form_Album_Ressource::newWith($ressource);
	}

		/**
	 * @param Class_Album $album
	 * @return null
	 */
	public function _validateAndSaveAlbum($album) {
		$form = $this->_albumForm($album);
		$this->view->form = $form;

		if (
			!$this->_request->isPost()
			or !$form->isValid($this->_request->getPost())
		)
			return;

		$values = $form->getValues();
		unset($values['fichier']);
		unset($values['pdf']);

		$droits_precision = $values['droits_precision'];
		unset($values['droits_precision']);
		$values['droits'] = (2 == $values['droits']) ?
				$droits_precision : 'Domaine public';

		$album->updateAttributes($values);

		if ($album->save()
			  && $album->receiveFile()
			  && $album->receivePDF()) {
			$this->_helper->notify('Album sauvegardé');
			$this->_redirect('admin/album/edit_album/id/' . $album->getId());
		} 
	}


	/**
	 * @return ZendAfi_Form
	 */
	public function _thumbnailsForm($album) {
		if (!$form = ZendAfi_Form_Album_DisplayAbstract::forAlbum($album, ['id' => 'thumbnails']))
			return;
		return $form->populate($album->toArray());
	}


	/**
	 * @param Class_Album $album
	 * @param string $titre
	 */
	protected function _renderAlbumForm($album, $titre) {
		$this->_validateAndSaveAlbum($album);
		
		$this->view->titre	= $titre;
		$this->view->errors	= $album->getErrors();
		$this->view->album = $album;
		$this->view->form->getElement('fichier')
				->setValue($album->getFichier());
		$this->view->form->getElement('pdf')
				->setValue($album->getPdf());
		$this->render('album_form');
	}


	protected function _getTreeViewContainerActions() {
		$actions = Class_AdminVar::isBibNumEnabled() 
			? [['url' => $this->_getUrlForAction('add_categorie_to'),
					'icon' => 'ico/add_cat.gif',
					'label' => 'Ajouter une sous-catégorie'],

				 ['url' => $this->_getUrlForAction('add_album_to'),
					'icon' => 'ico/add_news.gif',
					'label' => 'Ajouter un album']]

			: [];

		return array_merge($actions, 
											 [['url' => $this->_getUrlForAction('edit_categorie'),
												 'icon' => 'ico/edit.gif',
												 'label' => 'Modifier la catégorie'],

												['url' => $this->_getUrlForAction('delete_categorie'),
												 'icon' => 'ico/del.gif',
												 'label' => 'Supprimer la catégorie',
												 'condition' => 'hasNoChild',
												 'anchorOptions' => ['onclick' => "return confirm('Etes-vous sûr de vouloir supprimer cette catégorie ?')"]]]);
	}


	protected function _getTreeViewItemActions() {
		return [['url' => $this->_getUrlForAction('edit_album'),
				     'icon' => 'ico/edit.gif',
				     'label' => "Modifier l'album"],
			      ['url' => $this->_getUrlForAction('edit_images'),
						 'icon' => 'ico/album_images.png',
						 'label' => "Gérer les médias",
						 'caption' => 'formatedCount'],
			      ['url' => $this->_getUrlForAction('preview_album'),
						 'icon' => function($model) {return $model->isVisible() ? 'ico/show.gif' : 'ico/hide.gif';},
						 'label' => "Visualisation de l'album"],
			      ['url' => $this->_getUrlForAction('delete_album'),
						 'icon' => 'ico/del.gif',
						 'label' => "Supprimer l'album",
						 'anchorOptions' => ['onclick' => "return confirm('Êtes-vous sûr de vouloir supprimer cet album');"]]];
	}


	protected function _getUrlForAction($action) {
		return $this->view->url($this->_baseUrlOptions + array('action' => $action), null, true) . '/id/%s';
	}
}