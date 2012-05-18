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
	protected $_metadatas;
	protected $_xml_parser;

	public static function fromXML($xml) {
			$instance = new self();
			return $instance->parse($xml);
	}


	public function parse($xml) {
		$this->_entries = array();
		$this->_metadatas = array();
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser->setElementHandler($this);
		$this->_xml_parser->parse($xml);
		return $this;
	}


	public function getEntries() {
		return $this->_entries;
	}


	public function getMetadatas() {
		return $this->_metadatas;
	}


	public function startEntry() {
		$this->_current_entry = new Class_WebService_OPDS_CatalogEntry();
		$this->_entries[] = $this->_current_entry;
	}


	public function endTitle($data) {
		if (!$this->_xml_parser->inParents('entry')) {
			$this->_metadatas['Titre'] = $data;
			return;
		}

		$this->_current_entry->setTitle($data);
	}


	public function startLink($attributes) {
		if (!$this->_xml_parser->inParents('entry'))
			return;

		if (array_key_exists('REL', $attributes)
				&& array_key_exists('TYPE', $attributes)
				&& 'http://opds-spec.org/acquisition' == $attributes['REL']
				&& in_array($attributes['TYPE'], array('application/epub+zip', 'application/pdf'))) {
			$this->_current_entry->addFile($attributes['HREF'], $attributes['TYPE']);
			return;
		}

		if (false === strpos($attributes['TYPE'], 'application/atom+xml'))
			return;

		$this->_current_entry->setLink($attributes['HREF']);
	}


	public function endName($data) {
		if (!$this->_xml_parser->inParents('entry')
				&& $this->_xml_parser->inParents('author')) {
			$this->_concatMetadata('Auteur', $data);
			return;
		}
		
		if (!$this->_xml_parser->inParents('author'))
			return;

		$this->_current_entry->setAuthor($data);
	}


	public function endUri($data) {
		if (!$this->_xml_parser->inParents('entry')
				&& $this->_xml_parser->inParents('author')) {
			$this->_concatMetadata('Auteur', $data);
		}
	}


	public function endEmail($data) {
		if (!$this->_xml_parser->inParents('entry')
				&& $this->_xml_parser->inParents('author')) {
			$this->_concatMetadata('Auteur', $data);
		}
	}


	public function endId($data) {
		if (!$this->_xml_parser->inParents('entry'))
			return;

		$this->_current_entry->setId($data);
	}


	public function endUpdated($data) {
		if ($this->_xml_parser->inParents('entry'))
			return;

		$this->_metadatas['Dernière mise à jour'] = Class_Date::humanDate($data);
	}


	protected function _concatMetadata($name, $value, $separator = ' - ') {
		$this->_metadatas[$name] = (isset($this->_metadatas[$name])) 
				? $this->_metadatas[$name] . $separator . $value
				: $value;
	}
}
?>