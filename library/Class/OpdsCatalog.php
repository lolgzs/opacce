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

class Class_OpdsCatalog extends Storm_Model_Abstract {
	protected $_table_name = 'opds_catalogs';
	protected $_web_client;

	public static function getLoader() {
		return parent::getLoaderFor(__CLASS__);
	}


	public function getEntries() {
		return $this->getCatalogueReader()->getEntries();
	}


	public function getCatalogueReader() {
		if (isset($this->_catalog_reader))
			return $this->_catalog_reader;

		$xml = $this->getWebClient()->open_url($this->getUrl());
		return $this->_catalog_reader = Class_WebService_OPDS_CatalogReader::fromXML($xml);
	}


	public function getMetadatas() {
		return $this->getCatalogueReader()->getMetadatas();
	}


	public function getEntry($id) {
		$entries = $this->getEntries();
		foreach($entries as $entry) {
			if ($id == $entry->getId())
				return $entry;
		}
		return null;
	}


	public function newForEntry($url) {
		$url = $this->_normalizeUrl($url);
		return Class_OpdsCatalog::getLoader()->newInstance()
			->setLibelle($this->getLibelle())
			->setWebClient($this->getWebClient())
			->setUrl($url);
	}


	/**
	 * @return Class_WebService_SimpleWebClient
	 */
	public function getWebClient() {
		if (!isset($this->_web_client))
			$this->_web_client = new Class_WebService_SimpleWebClient();
		return $this->_web_client;
	}


	/**
	 * @param Class_WebService_SimpleWebClient $web_client
	 * @return Class_WebService_SIGB_AbstractRESTService
	 */
	public function setWebClient($web_client) {
		$this->_web_client = $web_client;
		return $this;
	}


	public function getSearchForm() {
		$form = new Zend_Form(array('method' => 'post'));
		$form
			->addElement('text', 'search', array('required' => true,
																					 'allowEmpty' => false))
			->addElement('submit', 'Rechercher');
		return $form;
	}


	public function getSearch() {
		return $this->getCatalogueReader()->getSearch()->setWebClient($this->_web_client);
	}


	protected function _normalizeUrl($url) {
		if (!$this->getUrl()) 
			return $url;

		if ('http' == substr($url, 0, 4)) 
			return $url;

		$urlInfos = parse_url($this->getUrl());
		$normalized = $urlInfos['scheme'] . '://' . $urlInfos['host'];
		if ('/' == substr($url, 0, 1)) 
			return $normalized . $url;
		return $normalized . dirname($urlInfos['path']) . $url;
	}
}
?>