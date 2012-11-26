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
class Class_WebService_OAI_Request_GetRecord {
	protected $_baseUrl;
	protected $_params;
	protected $_metadataPrefix;
	protected $_notice;


	public function __construct($params, $baseUrl) {
		$this->_baseUrl = $baseUrl;
		$this->_params = array_merge(array('metadataPrefix' => null,
																			 'identifier' => null),
																 $params);

		$this->_metadataPrefix = $this->_params['metadataPrefix'];
		$this->_identifier = $this->_params['identifier'];
	}


	public function getErrorOn($builder) {
		if (!$this->_metadataPrefix) 
			return $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');

		if ('oai_dc' != $this->_metadataPrefix) 
			return $builder->error(array('code' => 'cannotDisseminateFormat'));

		if (!$this->_identifier)
			return $builder->error(array('code' => 'badArgument'), 'Missing identifier');
		
		$parts = explode(':', $this->_identifier);
		$this->_notice = Class_Notice::getLoader()->getNoticeByClefAlpha(end($parts));

		if (!$this->_notice) 
			return $builder->error(array('code' => 'idDoesNotExist'));
	}


	public function renderOn($builder) {
		$attributes = array('verb' => 'GetRecord');
		if ($this->_metadataPrefix)
			$attributes['metadataPrefix'] = $this->_metadataPrefix;

		if ($this->_identifier)
			$attributes['identifier'] = $this->_identifier;

		return $builder->request($attributes, $this->_baseUrl);
	}


	public function getNotice() {
		return $this->_notice;
	}
}
?>