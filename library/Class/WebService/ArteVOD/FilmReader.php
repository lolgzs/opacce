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

class Class_WebService_ArteVOD_FilmReader {
	protected $_xml_parser;
	protected $_film;

	public function parseContentOn($xml, $film) {
		$this->_film = $film;
		
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser->setElementHandler($this);
		$this->_xml_parser->parse($xml);

		return $this;
	}


	public function endExternalUri($data) {
		$this->_film->setExternalUri($data);
	}


	public function endTitle($data) {
		$this->_film->setTitle($data);
	}


	public function endBody($data) {
		$this->_film->setDescription($data);
	}


	public function endProduction_Year($data) {
		$this->_film->setYear($data);
	}


	public function endFull_Name($data) {
		if ($this->_xml_parser->inParents('authors')) 
			$this->_film->addAuthor($data);
	}


	public function startMedia($attributes) {
		if (!array_key_exists('SRC', $attributes))
			return;

		if ($this->_xml_parser->inParents('posters'))
			$this->_film->addPoster($attributes['SRC']);

		if ($this->_xml_parser->inParents('trailers'))
			$this->_film->addTrailer($attributes['SRC']);

		if ($this->_xml_parser->inParents('photos'))
			$this->_film->addPhoto($attributes['SRC']);
	}
}