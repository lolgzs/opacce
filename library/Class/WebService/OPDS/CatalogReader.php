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

class Class_WebService_OPDS_CatalogReader {
	protected $_current_entry;
	protected $_entries;
	protected $_xml_parser;

	public static function getEntriesFromXml($xml) {
		$instance = new self();
		return $instance
			->parse($xml)
			->getEntries();
	}


	public function parse($xml) {
		$this->_entries = array();
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser->setElementHandler($this);
		$this->_xml_parser->parse($xml);
		return $this;
	}


	public function getEntries() {
		return $this->_entries;
	}


	public function startEntry() {
		$this->_current_entry = new Class_WebService_OPDS_CatalogEntry();
		$this->_entries[] = $this->_current_entry;
	}


	public function endTitle($data) {
		if (!$this->_xml_parser->inParents('entry'))
			return;

		$this->_current_entry->setTitle($data);
	}


	public function startLink($attributes) {
		if (!$this->_xml_parser->inParents('entry'))
			return;

		if (false === strpos($attributes['TYPE'], 'application/atom+xml'))
			return;

		$this->_current_entry->setLink($attributes['HREF']);
	}
}
?>