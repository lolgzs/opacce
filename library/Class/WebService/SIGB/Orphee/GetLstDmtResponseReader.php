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

class Class_WebService_SIGB_Orphee_GetLstDmtResponseReader extends Class_WebService_SIGB_AbstractXMLNoticeReader {
	const CODE_SITUTATION_SORTI = 2;
	const CODE_SITUTATION_RESERVE = 3;
	const CODE_SITUTATION_ARCHIVAGE = 14;
	const CODE_SITUTATION_CATALOGAGE = 17;
	
	/**
	 * @return Class_WebService_SIGB_Orphee_GetLstDmtResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	public function startDocuments() {
		$this->_notice = new Class_WebService_SIGB_Notice(0);
	}


	public function startDocument() {
		$this->_current_exemplaire = new Class_WebService_SIGB_Exemplaire(0);
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}


	public function endNtc($data) {
		$this->_notice->setId($data);
	}


	public function endNo($data) {
		$this->_current_exemplaire->setId(trim($data));
	}


	public function endCarte($data) {
		$this->_current_exemplaire->setCodeBarre(trim($data));
	}


	public function endLib_Sit($data) {
		$this->_current_exemplaire->setDisponibilite(trim($data));
	}

	
	public function endSit($data) {
		$reservable = ($data==self::CODE_SITUTATION_SORTI || $data==self::CODE_SITUTATION_RESERVE);
		$this->_current_exemplaire->setReservable($reservable);


		$visible = ($data!=self::CODE_SITUTATION_ARCHIVAGE && $data!=self::CODE_SITUTATION_CATALOGAGE);
		$this->_current_exemplaire->setVisibleOPAC($visible);
	}


	public function endDate_Last_Retour($data) {
		$this->_current_exemplaire->setDateRetour(trim($data));
	}
}

?>