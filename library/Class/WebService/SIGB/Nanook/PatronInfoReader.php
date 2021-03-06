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
class Class_WebService_SIGB_Nanook_PatronInfoReader extends Class_WebService_SIGB_AbstractILSDIPatronInfoReader{
	/**
	 * @return Class_WebService_SIGB_Nanook_PatronInfoReader
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $data
	 */
	public function endPatronId($data) {
		$this->getEmprunteur()->setId($data);
	}


	/**
	 * @param $data string
	 */
	public function endEndDate($data) {
		if ($data && 'null' != $data)
			$this->getEmprunteur()->setEndDate($data);
	}


	/**
	 * @param $data string
	 */
	public function endMail($data) {
		if ($data && 'null' != $data)
			$this->getEmprunteur()->setEmail($data);
	}

		
	/**
	 * @param string $data
	 */
	public function endDueDate($data) {
		if ($this->_xml_parser->inParents('loan')) {
			$date = implode('/', array_reverse(explode('-', $data)));
			$this->_currentLoan->getExemplaire()->setDateRetour($date);
		}
	}

	/**
	 * @param string $data
	 */
	public function endItemId($data) {
		$this->_getCurrentOperation()->setId($data);
		$this->_getCurrentOperation()->getExemplaire()->setId($data);
	}


	/**
	 * @param string $data
	 */
	public function endBibId($data) {
		$this->_getCurrentOperation()->getExemplaire()->setNoNotice($data);
	}


	/**
	 * @param string $data
	 */
	public function endLocationLabel($data) {
		$this->_getCurrentOperation()->getExemplaire()->setBibliotheque($data);
	}


	/** 
	 * @param string $data 
	 */
	public function endAvailable($data) {
		if (1 == (int)$data)
			$this->_currentHold->setEtat('Disponible');
	}


	/**
	 * @paran string $data
	 */
	public function endAvailabilityDate($data) {
		if ('' != $data)
			$this->_currentHold->setEtat('Pas disponible avant le ' . $data);
	}

}
?>