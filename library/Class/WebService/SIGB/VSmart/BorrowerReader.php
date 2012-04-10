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

class Class_WebService_SIGB_VSmart_BorrowerReader {
	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;

	/** @var Class_WebService_SIGB_AbstractService */
	protected $_sigb_service;

	protected $_xml_parser;


	/**
	 *
	 * @param Class_WebService_SIGB_AbstractService $sigb_service
	 * @return Class_WebService_SIGB_VSmart_BorrowerReader
	 */
	public static function newInstanceForService($sigb_service) {
		$instance = new self();
		return $instance->setSIGBService($sigb_service);
	}


	/**
	 * @param Class_WebService_SIGB_AbstractService $sigb_service
	 * @return Class_WebService_SIGB_VSmart_BorrowerReader
	 */
	public function setSIGBService($sigb_service) {
		$this->_sigb_service = $sigb_service;
		return $this;
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteurFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
													->setElementHandler($this)
													->parse($xml);

		return $this->_emprunteur;
	}


	/*
	 * XMLParser callbacks
	 */

	/**
	 * @param array $attributes
	 */
	public function startBorrower($attributes) {
		$this->_emprunteur = Class_WebService_SIGB_Emprunteur::newInstance()
			->setService($this->_sigb_service);
	}


	/**
	 * @param string $data
	 */
	public function endOriginalBarcode($data) {
		$this->_emprunteur->setId($data);
	}


	/**
	 * @param string $data
	 */
	public function endLastName($data) {
		$this->_emprunteur->setNom($data);
	}


	/**
	 * @param array $attributes
	 */
	public function startLoan($attributes) {
		$this->_current_exemplaire_operation = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire();
	}


	/**
	 * @param array $attributes
	 */
	public function startReservation($attributes) {
		$this->_current_exemplaire_operation = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire();
		$this->_emprunteur->reservationsAdd($this->_current_exemplaire_operation);
	}


	/**
	 * @param string $data
	 */
	public function endLoan($data) {
		$this->_emprunteur->empruntsAdd($this->_current_exemplaire_operation);
	}


	/**
	 * @param string $data
	 */
	public function endFirstName($data) {
		$this->_emprunteur->setPrenom($data);
	}


	/**
	 * @param string $data
	 */
	public function endTitle($data) {
		$this->_current_exemplaire_operation->getExemplaire()->setTitre($data);
	}


	/**
	 * @param string $data
	 */
	public function endDueDate($data) {
		$this->_current_exemplaire_operation->setDateRetour($data);
	}


	/**
	 * @param string $data
	 */
	public function endItemBarcode($data) {
		$this->_current_exemplaire_operation->setId($data)
			->getExemplaire()
			->setId($data)
			->setCodeBarre($data);
	}


	/**
	 * @param string $data
	 */
	public function endPlaceInQueue($data) {
		$this->_current_exemplaire_operation->setRang($data);
	}


	/**
	 * @param string $data
	 */
	public function endReservationNumber($data) {
		$this->_current_exemplaire_operation->setId($data);
	}


	/**
	 * @param string $data
	 */
	public function endLoanLocation($data) {
		$this->_setExemplaireBib($data);

	}


	/**
	 * @param string $data
	 */
	public function endPickupLocation($data) {
		$this->_setExemplaireBib($data);
	}


	public function _setExemplaireBib($data) {
		$code_bib = array_last(explode('/', $data));
		$bib = Class_CodifAnnexe::getLoader()->findFirstBy(array('code' => $code_bib));
		$libelle = $bib ? $bib->getLibelle() : $data;
		$this->_current_exemplaire_operation->getExemplaire()->setBibliotheque($libelle);
	}
}

?>