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

class Class_Notice_DublinCoreVisitor {
	protected $_xml;
	protected $_builder;
	protected $_identifier;
	protected $_date;


	public function __construct() {
		$this->_builder = new Class_Xml_Oai_DublinCoreBuilder();
	}


	public function visit($notice) {
		$notice->acceptVisitor($this);
	}


	public function xml() {
		return $this->_builder->oai_dc($this->_xml);
	}


	public function visitClefAlpha($clef) {
		$this->_identifier = sprintf('http://%s%s/recherche/notice/%s',
																 $_SERVER['SERVER_NAME'], BASE_URL, $clef);
		$this->_xml .= $this->_builder->identifier($this->_identifier);
	}


	public function visitTitre($titre) {
		$this->_xml .= $this->_builder->title($titre);
	}


	public function visitDateMaj($dateMaj) {
		$this->_date = substr($dateMaj, 0, 10);
		$this->_xml .= $this->_builder->date($this->_date);
	}


	public function getIdentifier() {
		return $this->_identifier;
	}


	public function getDate() {
		return $this->_date;
	}
}

?>