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

class Class_WebService_SIGB_Microbib_InfosAbonneResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;

	protected $_current_operation;

	/**
	 * @return Class_WebService_SIGB_Microbib_InfosAbonneResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public static function emprunteurFromXML($xml) {
		return self::newInstance()->getEmprunteurFromXML($xml);
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteurFromXML($xml) {
		$this->_emprunteur = Class_WebService_SIGB_Emprunteur::newInstance()
			->empruntsAddAll(array())
			->reservationsAddAll(array());

		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
			->setElementHandler($this)
			->parse($xml);

		return $this->_emprunteur;
	}


	public function startListe_Prets() {
		$this->_current_operation = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire();
	}


	public function endListe_Prets() {
		$this->_emprunteur->empruntsAdd($this->_current_operation);
	}


	public function startListe_Reservations() {
		$this->_current_operation = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire();
	}


	public function endListe_Reservations() {
		$this->_emprunteur->reservationsAdd($this->_current_operation);
	}


	public function endTitre($data) {
		$this->_current_operation->setTitre($data);
	}


	public function endDate_Retour($data) {
		$this->_current_operation->setDateRetour(implode('/', array_reverse(explode('-', $data))));
	}


	public function endCode_Barre($data) {
		$this->_current_operation
			->setCodeBarre($data)
			->setId($data);
	}


	public function endAuteur($data) {
		$this->_current_operation->setAuteur($data);
	}
}

?>