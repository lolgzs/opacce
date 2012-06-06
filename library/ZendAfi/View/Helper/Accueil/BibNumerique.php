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



	public function getTreeRenderer() {
		return new ZendAfi_View_Helper_Accueil_BibNumerique_TreeRenderer($this->view, $this->preferences);
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
		$this->contenu = $this->getTreeRenderer()->render();
		return $this;
	}
}

?>