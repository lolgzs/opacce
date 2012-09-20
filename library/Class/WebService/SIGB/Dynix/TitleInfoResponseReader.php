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

class Class_WebService_SIGB_Dynix_TitleInfoResponseReader extends Class_WebService_SIGB_AbstractXMLNoticeReader {
	protected $_current_code_annexe;
	protected $_current_location_id;
	protected $_current_exemplaire;
	protected $_is_reservable = false;


	public function endTitleId($content) {
		$this->_notice = new Class_WebService_SIGB_Notice($content);
	}


	public function endLibraryId($data) {
		$this->_current_code_annexe = $data;
	}


	public function endItemId($content) {
		$this->_current_exemplaire = (new Class_WebService_SIGB_Exemplaire($content))
			->setCodeBarre($content)
			->setReservable($this->_is_reservable);
		
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}


	public function endCallInfo() {
		$this->_current_exemplaire->setCodeAnnexe($this->_current_code_annexe);
	}


	public function endCurrentLocationID($data) {
		if ($data == 'CHECKEDOUT')
			$this->_current_exemplaire->setDisponibiliteEnPret();

		if ($data == 'INTRANSIT')
			$this->_current_exemplaire->setDisponibiliteEnTransit();

		$this->_current_location_id = $data;
	}

	
	public function endHomeLocationId($data) {
		if ($this->_current_location_id == $data)
			$this->_current_exemplaire->setDisponibiliteLibre();
	}


	public function endHoldable($data) {
		$this->_is_reservable = ($data == 'true');
	}
}

?>