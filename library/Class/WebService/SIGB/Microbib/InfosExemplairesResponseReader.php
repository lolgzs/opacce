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

class Class_WebService_SIGB_Microbib_InfosExemplairesResponseReader extends Class_WebService_SIGB_AbstractXMLNoticeReader {
	protected $_current_exemplaire;

	public static function noticeFromXML($xml) {
		$instance = new self();
		return $instance->getNoticeFromXML($xml);
	}


	public function startExemplaires() {
		$this->_notice = new Class_WebService_SIGB_Notice(0);
	}


	public function startExemplaire() {
		$this->_current_exemplaire = new Class_WebService_SIGB_Exemplaire(0);
		$this->_notice->addExemplaire($this->_current_exemplaire);
	}


	public function endCode_Barre($data) {
		$this->_current_exemplaire
			->setId($data)
			->setCodeBarre($data);
	}


	public function endDisponible($data) {
		if ($data == 'true')
			$this->_current_exemplaire->setDisponibiliteLibre();
	}


	public function endReservable($data) {
		$this->_current_exemplaire->setReservable($data);
	}


	public function endDate_Retour($data) {
		if (!$data)
			return;

		$this->_current_exemplaire->setDateRetour(implode('/', array_reverse(explode('-', $data))));
	}
}


?>