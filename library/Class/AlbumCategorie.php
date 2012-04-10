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

class AlbumCategorieLoader extends Storm_Model_Loader {
	/** 
	 * @return array 
	 */
	public function getCollections() {
		return $this->findAllBy(array('parent_id' => 0, 
																	'order' => 'libelle'));
	}


	/** @return array */
	public function findAlbumsRecursively() {
		$albums = array();

		$collections = $this->getCollections();
		foreach ($collections as $collection) {
			$collection->addAlbumsRecursivelyTo($albums);
		}

		return $albums;
	}


	public function getAllLibelles() {
		$categories = Class_AlbumCategorie::getLoader()->findAll();
		$libelles = array();
		foreach($categories as $categorie) {
			$libelles[$categorie->getId()] = $categorie->getAbsolutePath();
		}
		asort($libelles);
		return $libelles;
	}
}



class Class_AlbumCategorie extends Storm_Model_Abstract {
	protected $_loader_class = 'AlbumCategorieLoader';
	protected $_table_name = 'album_categorie';
	protected $_table_primary = 'id';
	protected $_belongs_to = array('parent_categorie' => array('model' => 'Class_AlbumCategorie',
																														  'referenced_in' => 'parent_id'));

	protected $_has_many = array('sous_categories' => array('model' => 'Class_AlbumCategorie',
																													 'role' => 'parent',
																													 'dependents' => 'delete'),

                                 'albums' => array('model' => 'Class_Album',
                                                   'role' => 'categorie',
																									 'dependents' => 'delete'));


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @param array $datas
	 */
	public function addAlbumsRecursivelyTo(array &$datas) {
		foreach ($this->getAlbums() as $album) {
			$datas[] = $album;
		}

		foreach ($this->getSousCategories() as $categorie) {
			$categorie->addAlbumsRecursivelyTo($datas);
		}
	}


	/**
	 * @param array $hierarchy
	 */
	public function getHierarchyOn(array &$hierarchy) {
		$hierarchy[] = $this;
		$this->_getParentHiearchyOn($hierarchy);
	}


	/**
	 * @return String
	 */
	public function getAbsolutePath() {
		$path = $this->getLibelle();
		if ($this->hasParentCategorie())
			$path = $this->getParentCategorie()->getAbsolutePath().'>'.$path;
		return $path;
	}


	/**
	 * @return array()
	 */
	public function getHierarchy() {
		$hierarchy = array();
		$this->_getParentHiearchyOn($hierarchy);
		return $hierarchy;
	}


	/**
	 * @param array $hierarchy
	 */
	protected function _getParentHiearchyOn(array &$hierarchy) {
		if (null !== ($parent = $this->getParentCategorie())) {
			$parent->getHierarchyOn($hierarchy);
		}
	}
}

?>