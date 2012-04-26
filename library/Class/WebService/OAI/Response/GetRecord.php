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
class Class_WebService_OAI_Response_GetRecord extends Class_WebService_OAI_Response_Null {
	protected $_notice;
	protected $_identifier;
	protected $_metadataPrefix;
	protected $_params;

	public function xml($params = array()) {
		$this->_params = array_merge(array('identifier' => null,
																			 'metadataPrefix' => null),
																 $params);

		$this->_identifier = $this->_params['identifier'];
		$this->_metadataPrefix = $this->_params['metadataPrefix'];
		$this->_notice = $this->getNoticeFromIdentifier($this->_identifier);
		
		return parent::xml();
	}


	public function requestTagOn($builder) {
		$requestOptions = array('verb' => 'GetRecord');
		if (null !== $this->_metadataPrefix)
			$requestOptions['metadataPrefix'] = $this->_metadataPrefix;
		if (null !== $this->_identifier)
			$requestOptions['identifier'] = $this->_identifier;

		return $builder->request($requestOptions, $this->_baseUrl);
	}


	public function buildErrorsOn($builder) {
		if (null == $this->_identifier)
			return $builder->error(array('code' => 'badArgument'), 'Missing identifier');
		
		if (null == $this->_metadataPrefix) 
			return $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');
 
		if (null == $this->_notice)
			return $builder->error(array('code' => 'idDoesNotExist'));

		if ('oai_dc' != $this->_metadataPrefix) 
			return $builder->error(array('code' => 'cannotDisseminateFormat'));

		return '';
	}


	public function getNoticeFromIdentifier($identifier) {
		if (null != $this->_identifier) {
			$parts = explode('/', $this->_identifier);
			return Class_Notice::getLoader()->getNoticeByClefAlpha(end($parts));
		}
		return null;
	}


	public function buildXmlOn($builder) {
		$response = $this->requestTagOn($builder);

		if ($errors = $this->buildErrorsOn($builder))
			return $response . $errors;

		$visitor = new Class_Notice_DublinCoreVisitor();
		$visitor->visit($this->_notice);
		$recordBuilder = new Class_WebService_OAI_Response_RecordBuilder();

		return $response . $builder->GetRecord($builder->record($recordBuilder->xml($builder, $visitor)));
	}
}
?>