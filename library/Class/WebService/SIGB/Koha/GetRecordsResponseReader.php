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
class Class_WebService_SIGB_Koha_GetRecordsResponseReader {
	protected $_xml_parser;
	protected $_notice;
	protected $_not_for_loan_status = array(
																					0 => Class_WebService_SIGB_Exemplaire::DISPO_LIBRE,
																					1 => "Exclu du prêt",
																					2 => "En traitement",
																					3 => "Consultation sur place",
																					4 => "En réserve",
																					5 => "En réparation",
																					6 => "En reliure",
																					7 => "Exclu du prêt temporairement");

	/**
	 * @var Class_WebService_SIGB_Koha_Exemplaire
	 */
	protected $_current_exemplaire;

	public static function newInstance() {
		return new self();
	}


	public function getNoticeFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser
			->setElementHandler($this)
			->parse($xml);

		return $this->_notice;
	}


	public function allowAvailableDocumentReservation() {
		return true;
	}


	public function endBiblioItemNumber($data) {
		if (!$this->_xml_parser->inParents('items'))
			$this->_notice = new Class_WebService_SIGB_Notice($data);
	}


	public function startItem($attributes) {
		$this->_current_exemplaire = new Class_WebService_SIGB_Koha_Exemplaire(null);
	}


	public function endItem($data) {
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}


	public function endBarCode($data) {
		if ($this->_xml_parser->inParents('item'))
			$this->_current_exemplaire->setCodeBarre($data);
	}


	public function endDate_Due($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;

		$date = implode('/', array_reverse(explode('-', $data)));
		$this->_current_exemplaire->setDateRetour($date);

		if (('' != $date)  || ($this->allowAvailableDocumentReservation())) {
			$this->_current_exemplaire->setReservable(true);
		}
	}


	public function endWthdrawn($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;

		if (0 != $data) {
			$this->_current_exemplaire->setRetire(true);
			$this->_current_exemplaire->setDisponibilitePilonne();
		}
	}


	public function endItemlost($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;

		if (0 != $data) {
			$this->_current_exemplaire->setPerdu(true);
			$this->_current_exemplaire->setDisponibilitePerdu();

		}
	}


	public function endDamaged($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;

		if (0 != $data)
			$this->_current_exemplaire->setEndommage(true);
	}


	public function endNotForLoan($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;


		if (array_key_exists($data, $this->_not_for_loan_status) and !$this->_current_exemplaire->isPiege()) {
			$this->_current_exemplaire->setDisponibilite($this->_not_for_loan_status[$data]);
			if (!in_array($data, array('0', '4'))) 
				$this->_current_exemplaire->notForLoan();
		}
	}


	public function endItemNumber($data) {
		if (!$this->_xml_parser->inParents('item'))
			return;

		$this->_current_exemplaire->setId($data);
	}
}

?>