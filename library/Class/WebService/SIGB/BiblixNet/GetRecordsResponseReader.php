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

class Class_WebService_SIGB_BiblixNet_GetRecordsResponseReader extends Class_WebService_SIGB_AbstractMarcXMLNoticeReader {
	protected $_current_exemplaire;


	public static function newInstance() {
		return new self();
	}


	public function startBibliographic($attributes) {
		$this->_notice = new Class_WebService_SIGB_Notice($attributes['ID']);
	}


	public function startDatafield_995() {
		$this->_current_exemplaire = new Class_WebService_SIGB_Exemplaire(null);
		$this->_current_exemplaire->beReservable();
	}


	public function endSubfield_995_f($data) {
		$this->_current_exemplaire->setCodeBarre($data)->setId($data);
	}


	public function endSubfield_995_O($data) {
		$this->_current_exemplaire->setDisponibilite($data);
	}


	public function endDatafield_995() {
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}
}

?>