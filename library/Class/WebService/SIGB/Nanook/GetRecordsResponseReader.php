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
class Class_WebService_SIGB_Nanook_GetRecordsResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_Notice */
	protected $_notice;

	/** @var Class_WebService_SIGB_Exemplaire */
	protected $_current_item;


	/**
	 * @return Class_WebService_SIGB_Nanook_GetRecordsResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Notice
	 */
	public function getNoticeFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
													->setElementHandler($this)
													->parse($xml);

		return $this->_notice;
	}


	/**
	 * @param string $data
	 */
	public function endBibId($data) {
		$this->_notice = new Class_WebService_SIGB_Notice($data);
	}


	/**
	 * @param array $attributes
	 */
	public function startItem($attributes) {
		$this->_current_item = new Class_WebService_SIGB_Exemplaire(null);
	}


	/**
	 * @param string $data
	 */
	public function endBarcode($data) {
		$this->_current_item->setCodeBarre($data);
	}


	/**
	 * @param string $data
	 */
	public function endItemId($data) {
		$this->_current_item->setId($data);
	}


	/**
	 * @param string $data
	 */
	public function endItem($data) {
		$this->_notice->addExemplaire($this->_current_item);
	}


	/**
	 * @param string $data
	 */
	public function endAvailable($data) {
		if ('1' == $data) {
			$this->_current_item->setDisponibiliteLibre();
		}
	}


	/**
	 * @param string $data
	 */
	public function endDueDate($data) {
		$date = implode('/', array_reverse(explode('-', $data)));
		$this->_current_item->setDateRetour($date);

		if ('' != $date)
			$this->_current_item->setDisponibiliteEnPret();
	}


	/**
	 * @param string $data
	 */
	public function endHoldable($data) {
		if ('1' == $data)
			$this->_current_item->setReservable(true);
	}


	/**
	 * @param string $data
	 */
	public function endLocationLabel($data) {
		$this->_current_item->setBibliotheque($data);
	}


	/**
	 * @param string $data
	 */
	public function endLocationId($data) {
		if ($annexe = Class_CodifAnnexe::getLoader()->findFirstBy(array('code' => $data)))
			$this->_current_item->setCodeAnnexe($annexe->getIdBib());
		else	
			$this->_current_item->setCodeAnnexe((int)$data);
	}


	/** 
	 * @param string $data
	 */
	public function endActivityMessage($data) {
		$this->_current_item->setDisponibiliteLabel($data);
	}
}

?>