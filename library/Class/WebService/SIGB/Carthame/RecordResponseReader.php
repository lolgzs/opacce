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
class Class_WebService_SIGB_Carthame_RecordResponseReader {
	const STATUT_DISPO = 1;
	const STATUT_RESERVE = 3;
	const STATUT_PRETE = 4;
	const STATUT_EXCLU = 6;

	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Notice */
	protected $_notice;

	/** @var Class_WebService_SIGB_Exemplaire */
	protected $_current_exemplaire;

	/**
	 * @return Class_WebService_SIGB_Carthame_RecordResponseReader
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $xml
	 * @return type
	 */
	public function getNoticeFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser
			->setElementHandler($this)
			->parse($xml);

		return $this->_notice;
	}

	public function endNoticeid($data) {
		$this->_notice = new Class_WebService_SIGB_Notice((string)$data);
	}

	public function endSFh($data) {
		$this->_current_exemplaire = new Class_WebService_SIGB_Exemplaire((string)$data);
		$this->_current_exemplaire->setNotice($this->_notice);
	}

	public function endSFa($data) {
		$this->_current_exemplaire->setCodeBarre((string)$data);
	}

	public function endSFo($data) {
		$data = (int)$data;
		$this->_current_exemplaire->setReservable(in_array($data, array(self::STATUT_RESERVE, 
																																		self::STATUT_PRETE)));
		
		if (self::STATUT_DISPO === $data) {
			$this->_current_exemplaire->setDisponibiliteLibre();
		} else {
			$this->_current_exemplaire->setDisponibiliteIndisponible();
		}
	}

	public function endSFq($data) {
		$data = (string)$data;
		$date = substr($data, strlen($data)-2) . '/' . substr($data, 4, 2). '/' . substr($data, 0, 4);

		$this->_current_exemplaire->setDateRetour($date);
	}


	public function endF941($data) {
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}

}
?>