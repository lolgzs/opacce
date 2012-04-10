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

class Class_WebService_SIGB_Orphee_GetInfoUserCarteResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;


	protected $_total_nb_reservations = 0;


	/**
	 * @return Class_WebService_SIGB_Orphee_GetInfoUserCarteResponseReader
	 */
	public static function newInstance() {
		return new self();
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


	public function startUser() {
		$this->_emprunteur = Class_WebService_SIGB_Emprunteur::newInstance();
	}


	public function endNo($data) {
		$this->_emprunteur->setId(trim($data));
	}


	public function endNom($data) {
		$this->_emprunteur->setNom(trim($data));
	}


	public function endPrenom($data) {
		$this->_emprunteur->setPrenom(trim($data));
	}


	public function endNb_Prets($data) {
		$this->_emprunteur->setNbEmprunts($data);
	}


	public function endNb_Rsv_Aff($data) {
		$this->_total_nb_reservations +=  (int)$data;
		$this->_emprunteur->setNbReservations($this->_total_nb_reservations);
	}


	public function endNb_Rsv_Att($data) {
		$this->_total_nb_reservations +=  (int)$data;
		$this->_emprunteur->setNbReservations($this->_total_nb_reservations);
	}


	public function endMail($data) {
		$this->_emprunteur->setEmail(trim($data));
	}
}


?>
