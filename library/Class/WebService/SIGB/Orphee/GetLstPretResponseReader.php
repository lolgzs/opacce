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

class Class_WebService_SIGB_Orphee_GetLstPretResponseReader extends Class_WebService_SIGB_AbstractXMLNoticeReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var array */
	protected $_emprunts = array();

	/** Class_WebService_SIGB_Emprunt */
	protected $_current_emprunt;


	/**
	 * @return Class_WebService_SIGB_Orphee_GetLstPretResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @param string $xml
	 * @return array
	 */
	public function getEmpruntsFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
			->setElementHandler($this)
			->parse($xml);

		return $this->_emprunts;
	}


	public function startDocument() {
		$this->_current_emprunt = new Class_WebService_SIGB_Emprunt(0, new Class_WebService_SIGB_Exemplaire(0));
		$this->_emprunts []= $this->_current_emprunt;
	}

	
	public function endTit($data) {
		if ($data = trim($data))
			$this->_current_emprunt->getExemplaire()->setTitre($data);
	}


	public function endNtc($data) {
		$this->_current_emprunt->getExemplaire()->setNoNotice('974767802');
	}


	public function endDate_Ret($data) {
		$this->_current_emprunt->setDateRetour(trim($data));
	}


	public function endAut($data) {
		if ($data = trim($data))
			$this->_current_emprunt->getExemplaire()->setAuteur($data);
	}


	public function endCb($data) {
		$this->_current_emprunt->getExemplaire()->setCodeBarre(trim($data));

		if ($exemplaire_opac = Class_Exemplaire::getLoader()->findFirstBy(array('code_barres' => trim($data)))) {
			$notice = $exemplaire_opac->getNotice();

			$this->_current_emprunt->getExemplaire()->setNoNotice($notice->getId());
			$this->_current_emprunt->setTitre($notice->getTitrePrincipal());
			$this->_current_emprunt->getExemplaire()->setBibliotheque($exemplaire_opac->getBib()->getLibelle());
			$this->_current_emprunt->setAuteur($notice->getAuteurPrincipal());
		}
	}


	public function endNo($data) {
		$this->_current_emprunt->setId(trim($data));
		$this->_current_emprunt->getExemplaire()->setId(trim($data));
	}
}

?>