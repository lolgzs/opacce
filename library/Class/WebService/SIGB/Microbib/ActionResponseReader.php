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
class Class_WebService_SIGB_Microbib_ActionResponseReader {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;
	protected $_response;

	/**
	 * @return Class_WebService_SIGB_Microbib_ActionResponseReader
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @return string
	 */
	public static function responseFromXML($xml) {
		return self::newInstance()->getResponseFromXML($xml);
	}


	/**
	 * @param string $xml
	 * @return string
	 */
	public function getResponseFromXML($xml) {
		$this->_xml_parser = Class_WebService_XMLParser::newInstance()
			->setElementHandler($this)
			->parse($xml);
		return $this->_response;
	}


	public function endReponse($data) {
		$this->_response = $data;
	}
}
?>