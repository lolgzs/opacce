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

class Class_WebService_SIGB_Koha_PatronInfoReader extends Class_WebService_SIGB_AbstractILSDIPatronInfoReader {
	protected $_current_exemplaire_operation;

	public static function newInstance() {
		return new self();
	}

	public function endBorrowerNumber($id) {
		$this->getEmprunteur()->setId($id);
	}


	public function endDate_Due($data) {
		$date = implode('/', array_reverse(explode('-', $data)));
		$this->_current_operation->getExemplaire()->setDateRetour($date);
	}


	public function endBarcode($code) {
		$this->_current_operation->getExemplaire()->setCodeBarre($code);
	}


	public function endItemNumber($id) {
		if ($this->_xml_parser->inParents('loan'))
			$this->_current_operation->setId($id);
		$this->_current_operation->getExemplaire()->setId($id);
	}


	public function endBiblioNumber($id) {
		if ($this->_xml_parser->inParents('hold'))
			$this->_current_operation->setId($id);
	}
}

?>