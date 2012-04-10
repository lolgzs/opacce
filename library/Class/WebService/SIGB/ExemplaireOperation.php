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

abstract class Class_WebService_SIGB_ExemplaireOperation {
	/** @var Class_WebService_SIGB_Exemplaire */
	protected $_exemplaire;

	/** @var string */
	protected $_id;

	/** @var array */
	protected $_attributes;


	/**
	 * @param string $id
	 * @param Class_WebService_SIGB_Exemplaire $exemplaire
	 */
	public function __construct($id, $exemplaire){
		$this->_id = $id;
		$this->_exemplaire = $exemplaire;
		$this->initialize();
	}


	//hook for subclasses to put initializations
	protected function initialize(){}


	/**
	 * @return string
	 */
	public function getId(){
		return $this->_id;
	}


	/**
	 * @param string $id
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setId($id) {
		$this->_id = $id;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getTitre() {
		return $this->_exemplaire->getTitre();
	}


	/**
	 * @param string $titre
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setTitre($data) {
		$this->_exemplaire->setTitre($data);
		return $this;
	}


	/**
	 * @param string $titre
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setCodeBarre($data) {
		$this->_exemplaire->setCodeBarre($data);
		return $this;
	}


	/**
	 * @return string
	 */
	public function getCodeBarre() {
		return $this->_exemplaire->getCodeBarre();
	}


	/**
	 * @return Class_WebService_SIGB_Exemplaire
	 */
	public function getExemplaire(){
		return $this->_exemplaire;
	}


	/**
	 * @return string
	 */
	public function getBibliotheque() {
		return $this->_exemplaire->getBibliotheque();
	}


	/**
	 * @return string
	 */
	public function getAuteur() {
		return $this->_exemplaire->getAuteur();
	}


	/**
	 * @param string $titre
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setAuteur($data) {
		return $this->_exemplaire->setAuteur($data);
	}
	

	/**
	 * @return string
	 */
	public function getNoticeOPACId() {
		if ($notice = $this->getNoticeOpac())
			return $notice->getId();
		return 0;
	}


	/**
	 * @return Class_Exemplaire
	 */
	public function getExemplaireOPAC() {
		return $this->_exemplaire->getExemplaireOPAC();
	}


	/**
	 * @return Class_NoticeOPAC
	 */
	public function getNoticeOPAC() {
		return $this->_exemplaire->getNoticeOPAC();
	}


	/**
	 * @param exemplaire Class_Exemplaire
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setExemplaireOPAC($exemplaire) {
		$this->_exemplaire->setExemplaireOPAC($exemplaire);
		return $this;
	}


	/**
	 * @param notice Class_Notice
	 * @return Class_WebService_SIGB_ExemplaireOperation
	 */
	public function setNoticeOPAC($notice) {
		$this->_exemplaire->setNoticeOPAC($notice);
		return $this;
	}


	/** @codeCoverageIgnore */
	public function __toString(){
		return "[".$this->_id."] ".$this->_exemplaire;
	}


	/**
	 * @param array $attributes
	 */
	public function parseExtraAttributes($attributes) {
		$this->_attributes = $attributes;

		$this->_exemplaire->setBibliotheque($this->getAttribute('Bibliotheque'));
		$this->_exemplaire->setSection($this->getAttribute('Section'));
		$this->_exemplaire->setAuteur($this->getAttribute('Auteur'));
		$this->_exemplaire->setNoNotice($this->getAttribute('N° de notice'));

		$this->onParseAttributes();
	}


	/**
	 * @codeCoverageIgnore
	 * subclass responsibility
	 */
	protected function onParseAttributes() {}


	/**
	 * @param string $name
	 * @return mixed
	 */
	protected function getAttribute($name) {
		if (!isset($this->_attributes)) return '';
		if (!array_key_exists($name, $this->_attributes)) return '';
		return $this->_attributes[$name];
	}
}

?>