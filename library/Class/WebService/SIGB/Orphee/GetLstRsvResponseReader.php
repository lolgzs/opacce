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

class Class_WebService_SIGB_Orphee_GetLstRsvResponseReader extends Class_WebService_SIGB_AbstractXMLNoticeReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var array */
	protected $_reservations = array();

	/** Class_WebService_SIGB_Reservation */
	protected $_current_reservervation;


	/**
	 * @return Class_WebService_SIGB_Orphee_GetLstRsvResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @param string $xml
	 * @return array
	 */
	public function getReservationsFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
			->setElementHandler($this)
			->parse($xml);

		return $this->_reservations;
	}


	public function startDocument() {
		$this->_current_reservation = new Class_WebService_SIGB_Reservation(0, new Class_WebService_SIGB_Exemplaire(0));
		$this->_reservations []= $this->_current_reservation;
	}


	public function endNo_Ntc($data) {
		$this->_current_reservation->setId(trim($data));
		$this->_current_reservation->getExemplaire()->setNoNotice(trim($data));
	}


	public function endNo_Dmt($data) {
		$this->_current_reservation->getExemplaire()->setId(trim($data));
	}


	public function endTit($data) {
		$this->_current_reservation->getExemplaire()->setTitre(trim($data));
	}


	public function endAuteur($data) {
		$this->_current_reservation->getExemplaire()->setAuteur(trim($data));
	}


}

?>