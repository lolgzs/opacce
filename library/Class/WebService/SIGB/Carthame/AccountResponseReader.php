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
class Class_WebService_SIGB_Carthame_AccountResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;

	/** @var Class_WebService_SIGB_Emprunt */
	protected $_current_emprunt;

	/** @var Class_WebService_SIGB_Reservation */
	protected $_current_reservation;

	/**
	 * @return Class_WebService_SIGB_Carthame_AccountResponseReader
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getAccountFromXml($xml) {
		$this->_emprunteur = Class_WebService_SIGB_Emprunteur::newInstance()
														->empruntsAddAll(array())
														->reservationsAddAll(array());

		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser
			->setElementHandler($this)
			->parse($xml);

		return $this->_emprunteur;

	}

	public function startF400() {
		$this->_current_emprunt = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire();
	}

	public function endF400() {
		$this->_emprunteur->empruntsAdd($this->_current_emprunt);
	}

	public function startF500() {
		$this->_current_reservation = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire();
	}

	public function endF500() {
		$this->_emprunteur->reservationsAdd($this->_current_reservation);
	}

	public function endSFa($data) {
		$data = (string)$data;

		if ($this->_xml_parser->inParents('F100')) {
			$parts = explode(',', $data);

			$this->_emprunteur->setNom(trim($parts[0]))
												->setPrenom(trim($parts[1]))
												;
		}

		if ($this->_xml_parser->inParents('F400')) {
			$this->_current_emprunt->getExemplaire()->setNoNotice($data);
		}

		if ($this->_xml_parser->inParents('F500')) {
			$this->_current_reservation->getExemplaire()->setNoNotice($data);
		}

	}

	public function endSFc($data) {
		$data = (string)$data;

		if ($this->_xml_parser->inParents('F400')) {
			$date = substr($data, strlen($data)-2) . '/' . substr($data, 4, 2). '/' . substr($data, 0, 4);
			$this->_current_emprunt->setDateRetour($date);

		}

	}

	public function endSFd($data) {
		if ($this->_xml_parser->inParents('F500')) {
			$status = '';
			switch ((string)$data) {
				case 'A': $status = 'En attente';	break;
				case 'D': $status = 'Disponible';	break;
				case 'T': $status = 'En transfert';	break;
			}

			$this->_current_reservation->setEtat($status);
		}
	}

	public function endSFh($data) {
		if ($this->_xml_parser->inParents('F500')) {
			$this->_current_reservation->setId((string)$data);
		}
	}

	public function endSFk($data) {
		if ($this->_xml_parser->inParents('F400')) {
			$id = (string)$data;
			$this->_current_emprunt->setId($id);
			$this->_current_emprunt->getExemplaire()->setId($id);
		}
	}

}
?>