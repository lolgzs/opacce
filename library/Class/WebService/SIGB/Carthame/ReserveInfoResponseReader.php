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
class Class_WebService_SIGB_Carthame_ReserveInfoResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;

	/** @var Array */
	protected $_sites;
	protected $_current_site_id;

	protected $_codif_site_ifr = array('Institut F' => 1,																	 
																		 'Cluj-Napoc' => 2,
																		 'Iasi' => 3,
																		 'Timisoara' => 4);

	/**
	 * @return Class_WebService_SIGB_Carthame_AccountResponseReader
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $xml
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function readXml($xml) {
		$this->_sites = array();

		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser
			->setElementHandler($this)
			->parse($xml);
		return $this;
	}


	public function getId($code) {
		return $this->_codif_site_ifr[$code];
	}

	public function getSites() {
		return $this->_sites;
	}

	public function isSiteAllowed($code) {
		return array_key_exists($this->getId($code), $this->_sites);
	}


	public function startSite($attributes) {
		$this->_current_site_id = $attributes['ID'];
	}


	public function endSite($data) {
		$this->_sites[$this->_current_site_id] = $data;
	}
}

?>