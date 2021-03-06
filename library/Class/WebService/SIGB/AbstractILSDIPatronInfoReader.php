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


class Speedy_XMLParser extends Class_WebService_XMLParser {
	public function tagWithoutNamespace($tag) {return $tag;}

	public function startElement($parser, $tag, $attributes) {
		$this->_parents[] = strtolower($tag); 

		$this->_current_data = null;

		$method_name = 'start'.$tag ;
		if (method_exists($this->_element_handler, $method_name)) {
			$this->_element_handler->$method_name($attributes) ;
		}
	}


	public function endElement($parser, $tag) {
		$method_name = 'end'.$tag;

		if (method_exists($this->_element_handler, $method_name))  {
			$this->_element_handler->$method_name($this->_current_data);
		}
		array_pop($this->_parents) ;
	}
}




abstract class Class_WebService_SIGB_AbstractILSDIPatronInfoReader {
	/** @var Class_WebService_SIGB_Emprunteur */
	protected $_emprunteur;

	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Class_WebService_SIGB_ExemplaireOperation */
	protected $_currentHold;

	/** @var Class_WebService_SIGB_ExemplaireOperation */
	protected $_currentLoan;

	protected $_current_operation;

	/** @var array */
	protected $_loans = [];



	/**
	 * @param Class_WebService_SIGB_Emprunteur $emprunteur
	 * @return Class_WebService_SIGB_*_PatronInfoReader
	 */
	public function setEmprunteur($emprunteur) {
		$this->_emprunteur = $emprunteur;
		return $this;
	}


	/**
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteur() {
		return $this->_emprunteur;
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_*_PatronInfoReader
	 */
	public function parseXML($xml) {
		$this->_xml_parser = Speedy_XMLParser::newInstance()
														->setElementHandler($this);

		$this->_xml_parser->parse($xml);

		return $this;
	}


	/**
	 * @param string $data
	 */
	public function endLastName($data) {
		$this->getEmprunteur()->setNom($data);
	}


	/**
	 * @param string $data
	 */
	public function endFirstName($data) {
		$this->getEmprunteur()->setPrenom($data);
	}


	/**
	 * @param array $attributes
	 */
	public function startLoan($attributes) {
		$this->_current_operation = $this->_currentLoan = Class_WebService_SIGB_Emprunt::newInstanceWithEmptyExemplaire();
	}


	/**
	 * @param string $data
	 */
	public function endLoan($data) {
		$this->_loans []= $this->_currentLoan;
	}


	/**
	 * @param string $data
	 */
	public function endLoans($data) {
		$this->getEmprunteur()->empruntsAddAll($this->_loans);
	}


	/**
	 * @param array $attributes
	 */
	public function startHold($attributes) {
		$this->_current_operation = $this->_currentHold = Class_WebService_SIGB_Reservation::newInstanceWithEmptyExemplaire();
	}


	/**
	 * @param string $data
	 */
	public function endHold($data) {
		$this->getEmprunteur()->reservationsAdd($this->_currentHold);
	}


	public function endTitle($titre) {
		if ($this->_xml_parser->inParents('loan') or $this->_xml_parser->inParents('hold'))
			$this->_current_operation->getExemplaire()->setTitre($titre);
	}


	public function endAuthor($author) {
		$this->_current_operation->getExemplaire()->setAuteur($author);
	}


	/**
	 * @param string $data
	 */
	public function endPriority($data) {
		$this->_current_operation->setRang($data);
	}


	public function _getCurrentOperation() {
		return $this->_current_operation;
	}
}

?>