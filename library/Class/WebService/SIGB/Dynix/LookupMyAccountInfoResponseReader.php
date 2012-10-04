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

class Class_WebService_SIGB_Dynix_LookupMyAccountInfoResponseReader {
	use Trait_Translator;

	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;

	/** @var Class_WebService_SIGB_ExemplaireOperation */
	protected $_current_operation;


	/**
	 * @return Class_WebService_SIGB_Dynix_LookupMyAccountInfoResponseReader
	 */
	public static function newInstance() {
		return new static();
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteurFromXML($xml) {
		$this->_emprunteur = Class_WebService_SIGB_Emprunteur::newInstance();

		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
			->setElementHandler($this)
			->parse($xml);

		return $this->_emprunteur;
	}


	public function endUserID($data) {
		$this->_emprunteur->setId($data);
	}


	public function startPatronCheckoutInfo() {
		$this->_current_operation = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire();
	}


	public function endPatronCheckoutInfo() {
		$this->_emprunteur->empruntsAdd($this->_current_operation);
	}


	public function startPatronHoldInfo() {
		$this->_current_operation = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire();
		$this->_emprunteur->reservationsAdd($this->_current_operation);
	}


	public function endItemId($data) {
		$this->_current_operation->setCodeBarre($data);
	}


	public function endDueDate($data) {
		$time = strtotime($data);
		$this->_current_operation->setDateRetour(date('d/m/Y', $time));
	}


	public function endQueuePosition($data) {
		$this->_current_operation->setRang($data);
	}


	public function endAvailable($data) {
		$this->_current_operation->setEtat($data == 'true' ? $this->_('Disponible') : $this->_('Réservé'));
	}

	
	public function endHoldKey($data) {
		$this->_current_operation->setId($data);
	}
}
