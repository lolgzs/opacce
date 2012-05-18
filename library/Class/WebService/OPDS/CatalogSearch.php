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
class Class_WebService_OPDS_CatalogSearch {
	const TERM_TOKEN = '{searchTerms}';
	const PAGE_TOKEN = '{startPage?}';

	protected $_url;
	protected $_web_client;
	protected $_xml_parser;
	protected $_template;

	public function __construct($url) {
		$this->_url = $url;
	}


	public function entryForTerm($term) {
		$this->_load();
		if (!$this->hasTemplate())
			return;
		return str_replace(array(self::TERM_TOKEN, self::PAGE_TOKEN), 
											 array(urlencode($term), 1), 
											 $this->_template);
	}


	public function hasTemplate() {
		$this->_load();
		return null != $this->_template;
	}


	public function startUrl($attributes) {
		if (!array_key_exists('TYPE', $attributes)
				|| !array_key_exists('TEMPLATE', $attributes))
			return;

		if ('application/atom+xml' != $attributes['TYPE']) 
			return;

		$this->_template = $attributes['TEMPLATE'];
	}


	public function setWebClient($client) {
		$this->_web_client = $client;
		return $this;
	}


	public function getWebClient() {
		if (null != $this->_web_client)
			return $this->_web_client;
		return new Class_WebService_SimpleWebClient();
	}


	protected function _load() {
		$this->_template = null;
		$xml = $this->getWebClient()->open_url($this->_url);
		$this->_xml_parser = Class_WebService_XMLParser::newInstance();
		$this->_xml_parser->setElementHandler($this);
		$this->_xml_parser->parse($xml);
	}
}
