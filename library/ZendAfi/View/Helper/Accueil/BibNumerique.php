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
class ZendAfi_View_Helper_Accueil_BibNumerique extends ZendAfi_View_Helper_Accueil_Base {
	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var Class_Album */
	protected $_currentAlbum;

	/** @var array */
	protected $_currentHierarchy = array();

	/** @var Class_AlbumCategorie */
	protected $_currentCategorie;
	protected $_slideshow;
	
	public function __construct($id_module,$params)	{
		parent::__construct($id_module,$params);
		$this->preferences = array_merge(array('id_albums' => ''),
																		 $this->preferences);

		$album = Class_Album::getLoader()->find((int)$this->preferences['id_albums']);
		$this->_slideshow = $this->view->getHelper('TagSlideshow')
			->setAlbum($album)
			->setPreferences($this->preferences);
	}


	protected function _renderHeadScriptsOn($script_loader) {
		if (!array_isset('id_albums', $this->preferences))
			return $this;

		if ($this->isDisplayDiaporama()) 
			$this->_slideshow->renderSlideShowScripts();

		if ($this->isDisplayBooklet())
			Class_ScriptLoader::getInstance()->loadBooklet($this->preferences['id_albums'],
																										 '#booklet_'.$this->id_module);
	}


	public function isDisplayAlbumTeaser() {
		return $this->preferences['type_aff'] == Class_Systeme_ModulesAccueil_BibliothequeNumerique::DISPLAY_ALBUM_TEASER;
	}

	/** @return array */
	public function getHTML() {
		$this->titre = $this->preferences['titre'];

		if ($this->isDisplayAlbumTeaser())
			$this->_renderAlbumTeaser();
		else
			$this->_renderTree();

		return $this->getHtmlArray();
	}

	/**
	 * @return ZendAfi_View_Helper_Accueil_BibNumerique
	 */
	public function _renderAlbumTeaser() {
		if ($this->isDisplayDiaporama() or $this->isDisplayListe()) {
			$this->contenu .= $this->_slideshow->renderAlbumMedias();
		} else {
			$this->contenu .= sprintf('<div id="booklet_%d" class="bib-num-album"></div>',
																$this->id_module);

			$this->titre = $this->view->tagAnchor(array('controller' => 'bib-numerique',
																									'action' => 'booklet',
																									'id' => $this->preferences['id_albums']),
																						$this->titre);
		}

		return $this;
	}


	/**
	 * @return ZendAfi_View_Helper_Accueil_BibNumerique
	 */
	protected function _renderTree() {
		$this->_init();
		if ('' == $this->preferences['id_categories']) {
			$this->contenu = '<div class="bib-numerique liste">' . $this->_getCollections() . '</div>';
			return $this->getHtmlArray();
		}

		$ids = explode('-', $this->preferences['id_categories']);
		foreach ($ids as $id) {
			if (null !== ($categorie = Class_AlbumCategorie::getLoader()->find((int)$id))) {
				$this->contenu .= $this->_getCategoriesOf($categorie) . $this->_getAlbumsOf($categorie);
			}
		}

		$this->contenu = '<div class="bib-numerique">' . $this->contenu . '</div>';
		return $this;
	}


	/**
	 * @return string
	 */
	protected function _getCollections() {
		$content = '';
		foreach (Class_AlbumCategorie::getLoader()->getCollections() as $collection) {
			$options = ($this->_isCurrentCategorie($collection)) ? array('class' => 'selected') : array();
			$content .= '<li class="lien">' . $this->_getLinkedItemFor($collection, 'view-categorie', $options)
				. $this->_getCategoriesOf($collection)
				. '</li>';
		}
		
		return '<ul>' . $content . '</ul>';
	}


	/**
	 * @param Class_AlbumCategorie $collection
	 * @return string
	 */
	protected function _getCategoriesOf($collection) {
		$content = '';
		if (0 < count($categories = $collection->getSousCategories())) {
			$content .= '<ul>';
			foreach ($categories as $categorie) {
				$options = ($this->_isCurrentCategorie($categorie)) ? array('class' => 'selected') : array();
				$content .= '<li>' 
					. $this->_getLinkedItemFor($categorie, 'view-categorie', $options)
					. $this->_getAlbumsOf($categorie)
					. '</li>';
			}
			$content .= '</ul>';
		}
		return $content;
	}


	/**
	 * @param Class_AlbumCategorie $categorie
	 * @return string
	 */
	protected function _getAlbumsOf($categorie) {
		$content = '';
		if (0 < count($albums = $categorie->getAlbums())) {
			$content .= '<ul>';
			foreach ($albums as $album) {
				$options = ($this->_isCurrentAlbum($album)) ? array('class' => 'selected') : array();
				$content .= '<li>' . $this->_getLinkedItemFor($album, 'view-album', $options) . '</li>';
			}
			$content .= '</ul>';
		}

		return $content;
	}


	/**
	 * @param Storm_Model_Abstract $item
	 * @param string $action
	 * @param array $options
	 * @return string
	 */
	protected function _getLinkedItemFor($item, $action, array $options = array()) {
		return $this->view->tagAnchor($this->view->url(array('module' => 'opac',
																													'controller' => 'bib-numerique',
																													'action' => $action,
																													'id' => $item->getId()), 
																										null, true), 
																	$item->getLibelle(),
																	$options);
	}


	/**
	 * @param Class_Album $album
	 * @return bool 
	 */
	protected function _isCurrentAlbum($album) {
		if (null === $this->_currentAlbum) {
			return false;
		}

		return ($album->getId() == $this->_currentAlbum->getId());
	}

	/**
	 * @param Class_AlbumCategorie 
	 * @return bool 
	 */
	protected function _isCurrentCategorie($categorie) {
		if ((null !== $this->_currentCategorie) && ($categorie->getId() == $this->_currentCategorie->getId())) {
			return true;
		}

		if (0 == count($this->_currentHierarchy)) {
			return false;
		}

		foreach ($this->_currentHierarchy as $item) {
			if ($categorie->getId() == $item->getId()) {
				return true;
			}
		}

		return false;
	}


	protected function _init() {
		$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		if ('bib-numerique' == $this->_request->getControllerName()) {
			if ('view-album' == $this->_request->getActionName()) {
				$this->_currentAlbum = Class_Album::getLoader()
					->find((int)$this->_request->getParam('id'));
				if (null !== $this->_currentAlbum) {
					$this->_currentHierarchy = $this->_currentAlbum->getHierarchy();
				}
			}

			if ('view-categorie' == $this->_request->getActionName()) {
				$this->_currentCategorie = Class_AlbumCategorie::getLoader()
					->find((int)$this->_request->getParam('id'));
				if (null !== $this->_currentCategorie) {
					$this->_currentHierarchy = $this->_currentCategorie->getHierarchy();
				}
			}
		}
	}
}

?>