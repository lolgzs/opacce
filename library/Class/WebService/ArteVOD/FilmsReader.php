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

class Class_WebService_ArteVOD_FilmsReader {
	protected $_xml_parser;
	protected $_total_count;
	protected $_page_number;
	protected $_page_size;
	protected $_films;

	public function parse($xml) {
		$this->_total_count = $this->_page_number = 0;
		$this->_page_size = 1;
		$this->_films = array();

		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser->setElementHandler($this);
		$this->_xml_parser->parse($xml);

		return $this;
	}


	public function startWsObjectListQuery($attributes) {
		if (isset($attributes['TOTAL_COUNT'])) 
			$this->_total_count = (int)$attributes['TOTAL_COUNT'];

		if (isset($attributes['PAGE_NB'])) 
			$this->_page_number = (int)$attributes['PAGE_NB'];

		if (isset($attributes['PAGE_SIZE'])) 
			$this->_page_size = (int)$attributes['PAGE_SIZE'];
	}


	public function startFilm() {
		$this->_current_film = new Class_WebService_ArteVOD_Film();
	}


	public function endFilm() {
		$this->_films[] = $this->_current_film;
	}


	public function endPk($data) {
		$this->_current_film->setId($data);
	}


	public function getTotalCount() {
		return $this->_total_count;
	}


	public function getPageNumber() {
		return $this->_page_number;
	}


	public function getPageCount() {
		return ceil($this->_total_count / $this->_page_size);
	}


	public function getFilms() {
		return $this->_films;
	}
}
