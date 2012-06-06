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
class ZendAfi_View_Helper_Telephone_BibNumerique_TreeRenderer 
  extends ZendAfi_View_Helper_Accueil_BibNumerique_TreeRenderer {

	public function render() {
		if ('' == $this->preferences['id_categories'])
			return '<ul data-role="listview" data-inset="true">' . $this->_getCollections() . '</ul>';
		

		$html = '';
		$ids = explode('-', $this->preferences['id_categories']);
		foreach ($ids as $id) {
			if (null !== ($categorie = Class_AlbumCategorie::getLoader()->find((int)$id))) {
				$html .= $this->_getCategoriesOf($categorie) . $this->_getAlbumsOf($categorie);
			}
		}

		return '<ul data-role="listview" data-inset="true">' . $html . '</ul>';
	}


	/**
	 * @return string
	 */
	protected function _getCollections() {
		$content = '';
		foreach (Class_AlbumCategorie::getLoader()->getCollections() as $collection) {
			$content .= '<li>' . $this->_getLinkedItemFor($collection, 'view-categorie') . '</li>';
		}
		
		return $content;
	}


	/**
	 * @param Class_AlbumCategorie $collection
	 * @return string
	 */
	protected function _getCategoriesOf($collection) {
		$content = '';
		if (0 < count($categories = $collection->getSousCategories())) {
			foreach ($categories as $categorie) {
				$content .= '<li>' . $this->_getLinkedItemFor($categorie, 'view-categorie') . '</li>';
			}
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
			foreach ($albums as $album) {
				$content .= '<li>' . $this->_getLinkedItemFor($album, 'view-album') . '</li>';
			}
		}
		return $content;
	}
}
?>