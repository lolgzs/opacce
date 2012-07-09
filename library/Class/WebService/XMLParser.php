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

class Class_WebService_XMLParser {
	protected $_current_data ;
	protected $_parents ;
	protected $_element_handler ;
	protected $_parsed_xml ;


	/**
	 * @return Class_WebService_XMLParser
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @param string $xml
	 * @return Class_WebService_XMLParser
	 */
	public function parse($xml) {
		$this->_parsed_xml = $xml ;
		$this->_parents = array() ;
		$parser = $this->_createParser() ;
		xml_parse($parser, $xml) ;
		// echo xml_error_string(xml_get_error_code($parser));
		// echo xml_get_current_line_number($parser);
		xml_parser_free($parser) ;
		return $this ;
	}


	/**
	 * @param Object $element_handler
	 * @return Class_WebService_XMLParser
	 */
	public function setElementHandler($element_handler) {
		$this->_element_handler = $element_handler;
		return $this;
	}


	/**
	 * @return PHP Resource
	 */
	protected function _createParser() {
		$parser = xml_parser_create() ;
		xml_set_object($parser, $this) ;
		xml_set_element_handler($parser, 'startElement', 'endElement') ;
		xml_set_character_data_handler($parser, 'onData') ;
		return $parser ;
	}


	/**
	 * @param PHP Resource $parser
	 * @param string $tag
	 * @param array $attributes
	 */
	public function startElement($parser, $tag, $attributes) {
		$parts = split(':', $tag);
		$tag = end($parts);
		$this->_parents[] = strtolower($tag); 

		if ($this->isDataToBeResetOnTag($tag))
			$this->_current_data = null ;

		$method_name = 'start'.$tag ;
		if (method_exists($this->_element_handler, $method_name)) {
			$this->_element_handler->$method_name($attributes) ;
		}
	}


	/**
	 * Permet de "sauter" des tags...
	 * @param string $tag
	 * @return boolean
	 */
	public function isDataToBeResetOnTag($tag) {
		return true;
	}


	/**
	 * @param PHP Resource $parser
	 * @param string $tag
	 */
	public function endElement($parser, $tag) {
		$method_name = 'end'.$tag ;

		if (method_exists($this->_element_handler, $method_name))  {
			$this->_element_handler->$method_name($this->_current_data);
		}
		array_pop($this->_parents) ;
	}


	/**
	 * @param PHP Resource $parser
	 * @param string $data
	 */
	public function onData($parser, $data) {
		$this->_current_data .= $data ;
	}


	/**
	 * @param string $tag
	 * @return bool
	 */
	public function inParents($tag) {
		return in_array(strtolower($tag), $this->_parents) ;
	}
}

?>