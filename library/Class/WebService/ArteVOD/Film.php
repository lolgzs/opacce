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

class Class_WebService_ArteVOD_Film {
	const TYPE_POSTER = 'poster';
	const TYPE_TRAILER = 'trailer';
	const TYPE_PHOTOS = 'photo';
	const TYPE_EXTERNAL_URI = 'external_uri';

	protected $_id;
	protected $_external_uri;
	protected $_title;
	protected $_description;
	protected $_year;
	protected $_authors = array();
	protected $_posters = array();
	protected $_trailers = array();
	protected $_photos = array();


	public function setId($id) {
		$this->_id = $id;
		return $this;
	}


	public function getId() {
		return $this->_id;
	}


	public function setExternalUri($uri) {
		$this->_external_uri = $uri;
		return $this;
	}


	public function getExternalUri() {
		return $this->_external_uri;
	}


	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}


	public function getTitle() {
		return trim($this->_title);
	}


	public function setDescription($description) {
		$this->_description = $description;
		return $this;
	}


	public function getDescription() {
		return $this->_description;
	}


	public function setYear($year) {
		$this->_year = $year;
		return $this;
	}


	public function getYear() {
		return $this->_year;
	}


	public function addAuthor($author) {
		$this->_authors[] = $author;
		return $this;
	}


	public function getAuthors() {
		return $this->_authors;
	}


	public function addPoster($url) {
		$this->_posters[] = $url;
		return $this;
	}


	public function getPosters() {
		return $this->_posters;
	}


	public function addTrailer($url) {
		$this->_trailers[] = $url;
		return $this;
	}


	public function getTrailers() {
		return $this->_trailers;
	}


	public function addPhoto($url) {
		$this->_photos[] = $url;
		return $this;
	}

	public function getPhotos() {
		return $this->_photos;
	}


	public function import() {
		if ($this->isAlreadyHarvested())
			return;

		$category = $this->_ensureArteVODCategory();
		$album = Class_Album::getLoader()->newInstance()
			->setTitre($this->getTitle())
			->setAuteur(implode(', ', $this->getAuthors()))
			->setAnnee($this->getYear())
			->setIdOrigine($this->getId())
			->setUrlOrigine(Class_WebService_ArteVOD::BASE_URL)
			->setCategorie($category)
			->setNotes($this->getSerializedNotes())
			->beArteVOD();
		if ($album->save())
			Class_WebService_ArteVOD_Vignette::getInstance()->updateAlbum($album);
	}


	public function isAlreadyHarvested() {
		$album = Class_Album::getLoader()
			->findFirstBy(array('url_origine' => Class_WebService_ArteVOD::BASE_URL,
													'id_origine' => $this->_id));

		return (null != $album);
	}


	protected function _ensureArteVODCategory() {
		$category = Class_AlbumCategorie::getLoader()
			->findFirstBy(array('libelle' => Class_WebService_ArteVOD::CATEGORY_LABEL,
													'parent_id' => 0));

		if (null != $category) 
			return $category;

		$category = Class_AlbumCategorie::getLoader()
			->newInstance()
			->setLibelle(Class_WebService_ArteVOD::CATEGORY_LABEL);
		$category->save();

		return $category;
	}


	public function getSerializedNotes() {
		$notes = array();
		foreach ($this->_posters as $url)
			$notes[] = $this->_getUnimarcForType(self::TYPE_POSTER, $url);

		foreach ($this->_trailers as $url)
			$notes[] = $this->_getUnimarcForType(self::TYPE_TRAILER, $url);

		foreach ($this->_photos as $url)
			$notes[] = $this->_getUnimarcForType(self::TYPE_PHOTOS, $url);

		$notes []= $this->_getUnimarcForType(self::TYPE_EXTERNAL_URI, $this->_external_uri);
		return serialize($notes);
	}


	protected function _getUnimarcForType($type, $url) {
		return ['field' => '856', 
						'data' => array('x' => $type, 'a' => $url)];
	}
}