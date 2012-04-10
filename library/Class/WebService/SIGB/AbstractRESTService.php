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

abstract class Class_WebService_SIGB_AbstractRESTService extends Class_WebService_SIGB_AbstractService {
	protected $_server_root;
	protected $_web_client;

	/**
	 * @param string $server_root
	 * @return Class_WebService_SIGB_AbstractRESTService
	 */
	public function setServerRoot($server_root) {
		$this->_server_root = 'http://'.str_replace('http://', '', $server_root);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getServerRoot() {
		return $this->_server_root;

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

	/**
	 * @param array $options
	 * @return string
	 */
	public function buildQueryURL($options) {
		return sprintf('%s?%s', $this->getServerRoot(), http_build_query($options));
	}

	/**
	 * @param array $options
	 * @return string
	 */
	public function httpGet($options) {
		$url = $this->buildQueryURL($options);
		return $this->getWebClient()->open_url($url);
	}

	/**
	 * @param string $xml
	 * @param string $tag
	 * @return string
	 */
	protected function _getTagData($xml, $tag) {
		$matches = array();
		if (preg_match(sprintf('/%s>([^<]*)<\/%s/', $tag, $tag),
									 $xml,
									 $matches))
			return $matches[1];
		return '';
	}

}

?>