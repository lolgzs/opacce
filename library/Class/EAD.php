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

class EADParser extends Class_WebService_XMLParser {
	/**
	 * @return EADParser
	 */
	public static function newInstance() {
		return new self();
	}


	public function isDataToBeResetOnTag($tag) {
		return $tag !== 'EMPH';
	}
}




class Class_EAD {
	protected $_xml_parser;
	protected $_albums;
	protected $_current_album;
	protected $_current_folio;
	protected $_import_categorie;
	protected $_indexation;
	protected $_type_physfacet;
	protected $_current_control_access_title;

	public static function moulinsToFolio($folio_moulins) {
		$side = strpos(strtolower($folio_moulins), 'v') ? 'V' : 'R';

		$matches = array();
		if (preg_match('/\d+/', $folio_moulins, $matches))
			return sprintf('%04d%s', $matches[0], $side);
		return '';
	}


	public static function cleanString($data) {
		return trim(preg_replace("'«|»|\s+'", ' ', $data));
	}


	public function getIndexation() {
		if (!isset($this->_indexation))
			$this->_indexation = new Class_Indexation();
		return $this->_indexation;
	}


	/** 
	 * @param string $xml
	 * @return Class_EAD
	 */
	public function load($xml) {
		$this->_albums = array();
		$this->_import_categorie = Class_AlbumCategorie::getLoader()
			->newInstance()
			->setLibelle(sprintf('import du %s', date('d M Y')));

		$this->_xml_parser = EADParser::newInstance();
		$this->_xml_parser
			->setElementHandler($this)
			->parse($xml);


		return $this->saveAlbums();
	}


	/** 
	 * @return Class_EAD
	 */ 
	public function saveAlbums() {
		if ($this->_import_categorie->hasAlbums())
			$this->_import_categorie->save();

		foreach ($this->_albums as $album)
			$album->save();
		return $this;
	}


	/** 
	 * @param string $filename
	 * @return Class_EAD
	 */
	public function loadFile($filename) {
		return $this->load(file_get_contents($filename));
	}


	/** 
	 * @return array
	 */
	public function getAlbums() {
		return $this->_albums;
	}


	/** 
	 * @return boolean
	 */
	public function isInFolio() {
		return null !== $this->_current_folio;
	}


	public function startC($attributes) {
		if ($attributes['OTHERLEVEL'] == 'notice')
			return $this->_initCurrentAlbum($attributes);

		if ($attributes['OTHERLEVEL'] == 'sous-notice')
			$this->_current_folio = array();
	}


	public function endC($data) {
		if ($this->isInFolio()) {
			$this->_initCurrentFolio();
			$this->_current_folio = null;
		}
		else
			$this->_current_album = null;
	}


	public function endP($data) {
		if ($this->_current_album && $this->_xml_parser->inParents('custodhist'))
			$this->_current_album->setProvenance($data);
	}


	public function endUnitTitle($data) {
		$this->_applyTitre($data);
	}


	public function endTitle($data) {
		if (isset($this->_current_control_access_title))
			return;
		$this->_current_control_access_title = $data;
		$this->_applyTitre($data);
	}


	public function endSubject($data) {
		$code_alpha = $this->getIndexation()->alphaMaj($data);

		if (!$matiere = Class_Matiere::getLoader()->findFirstBy(array('code_alpha' => $code_alpha))) {
			$matiere = new Class_Matiere();
			$matiere
				->setCodeAlpha($code_alpha)
				->setLibelle($data)
				->save();
		}

		$this->_current_album->addMatiere($matiere);
	}


	public function endUnitId($data) {
		if ($this->isInFolio()) 
			$this->_current_folio['no'] = $data;
		else if ($this->_current_album)
			$this->_current_album->setCote(sprintf('MS%03d', $data));
	}


	public function endPersname($data) {
		if (!$this->_current_album)
			return;

		$this->_current_album->setAuteur(self::cleanString($data));
	}


	public function startPhysFacet($attributes) {
		$this->_type_physfacet = $attributes['TYPE'];
	}


	public function endPhysFacet($data) {
		if (!$this->_current_album)
			return;
	
		if ($this->_type_physfacet == 'reliure')
			return $this->_current_album->addNote('316$a', self::cleanString($data));

		if ($this->_type_physfacet == 'support')
			return $this->_current_album->addNote('200$b', self::cleanString($data));
	}


	public function startLanguage($attributes) {
		if (!$this->_current_album)
			return;
		$this->_current_album->setIdLangue($attributes['LANGCODE']);
	}


	public function startUnitDate($attributes) {
		if (!$this->_current_album)
			return;

		$this->_current_album->setAnnee(array_first(explode('/', $attributes['NORMAL'])));
	}


	public function endUnitDate($data) {
		if (!$this->_current_album)
			return;

		if ( (0 < (int)$data) && (strlen(trim($data)) == 4) ) {
			$this->_current_album->setAnnee((int)$data);
			return;
		}
	
		$this->_current_album->setAnnee(null);
		$this->_current_album->addNote('305$a', self::cleanString($data));
	}


	protected function _initCurrentAlbum($attributes) {
		$id_origine = $attributes['ID'];
		$this->_current_album = Class_Album::getLoader()->findFirstBy(array('id_origine' => $id_origine));

		if (null == $this->_current_album) {
			$this->_current_album =  Class_Album::getLoader()
				->newInstance()
				->setIdOrigine($id_origine)
				->setCategorie($this->_import_categorie);

			$this->_import_categorie->addAlbum($this->_current_album);
		}
		$this->_current_album
			->beLivreNumerique()
			->clearNotes();
		$this->_albums []= $this->_current_album;

		unset($this->_current_control_access_title);

		return $this;
	}


	protected function _initCurrentFolio() {
		$folio = sprintf('MS_%03d_%s',
										 str_replace('MS', '', $this->_current_album->getCote()),
										 self::moulinsToFolio($this->_current_folio['no']));

		if (!$ressource = $this->_current_album->getRessourceByFolio($folio))
			return $this;

		if (!$ressource->hasTitre()) //ne pas écraser les titres déjà présents dans l'OPAC
			$ressource->setTitre(self::cleanString($this->_current_folio['titre']));

		return $this;
	}


	protected function _applyTitre($data) {
		if (null === $this->_current_album)
			return;

		if ($this->isInFolio()) {
			$this->_current_folio['titre'] = $data;
			return;
		}

		$this->_current_album->setTitre(self::cleanString($data));
	}
}


?>