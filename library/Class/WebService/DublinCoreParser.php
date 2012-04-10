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

/* Sait extraire informations du XML retourné par 
 * l'opération OAI ListRecords, format DublinCore
 * Functions:
 * - parse($xml): analyse le xml donné
 * - getRecords: retourne tous les enregistrements sous forme de tableau associatif
 * - getResumptionToken: retourne le token qui permet de rechercher 
 *   les enregistrements suivants
 */
class Class_WebService_DublinCoreParser {
	public function __construct() {
		$this->_records = array();
	}


	protected function _create_parser() {
		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'start_element', 'end_element');
		xml_set_character_data_handler($parser, "on_data"); 
		return $parser;
	}


	protected function _tag_to_method_name($tag) {
		return strtolower(str_replace(':', '_', $tag));
	}


	public function parse($xml) {
		$parser = $this->_create_parser();
		xml_parse($parser, $xml);
		xml_parser_free($parser);

		return $this->_records;
	}


	public function start_element($parser, $tag, $attributes) {
		$this->current_data = null;

		$method_name = 'start_'.$this->_tag_to_method_name($tag);
		if (method_exists($this, $method_name))
			$this->$method_name($parser, $attributes);
	}


	public function end_element($parser, $tag) {	
		$method_name = 'end_'.$this->_tag_to_method_name($tag);
		if (method_exists($this, $method_name))
			$this->$method_name($parser);
	}


	public function on_data($parser, $data) {
		$this->current_data .= trim($data);
	}


	protected function start_record($parser, $attributes) {
		$this->_records []= array();
	}

	protected function start_ns1_record($parser, $attributes) {
		$this->_records []= array();
	}


	protected function end_dc_identifier($parser) {
		$this->assign_data_to('id_oai');
	}


	protected function end_dc_title($parser) {
		$this->assign_data_to('titre');
	}


	protected function end_dc_creator($parser) {
		$this->assign_data_to('auteur');
	}


	protected function end_dc_publisher($parser) {
		$this->assign_data_to('editeur');
	}


	protected function end_dc_date($parser) {
		$this->assign_data_to('date');
	}


	protected function end_dc_relation($parser) {
		$this->assign_data_to('relation');
	}

	protected function end_dc_description($parser) {
		$this->assign_data_to('description');
	}

	protected function assign_data_to($key) {
		$this->_records[count($this->_records)-1][$key] = $this->current_data;
	}

	protected function start_resumptionToken($parser, $attributes) {
		$this->_resumptionToken = new Class_WebService_ResumptionToken();
		$this->_resumptionToken->setListSize($attributes['COMPLETELISTSIZE']);
		$this->_resumptionToken->setCursor($attributes['CURSOR']);
	}

	protected function end_resumptiontoken($parser) {
		$this->_resumptionToken->setToken($this->current_data);
	}


	public function getResumptionToken() {
		return $this->_resumptionToken;
	}


	public function getRecords() {
		return $this->_records;
	}
}
?>