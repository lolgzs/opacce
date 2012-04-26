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

	public function xml($params = array()) {
		if (array_key_exists('identifier', $params))
			$this->_identifier = $params['identifier'];

		if (array_key_exists('metadataPrefix', $params))
			$this->_metadataPrefix = $params['metadataPrefix'];

		if (null != $this->_identifier) {
			$parts = explode('/', $this->_identifier);
			if (null !== ($notice = Class_Notice::getLoader()->getNoticeByClefAlpha(end($parts))))
				$this->_notice = $notice;
		}
		return parent::xml();
	}


	public function buildXmlOn($builder) {
		$response = '';
		$requestOptions = array('verb' => 'GetRecord');
		if (null !== $this->_metadataPrefix)
			$requestOptions['metadataPrefix'] = $this->_metadataPrefix;
		if (null !== $this->_identifier)
			$requestOptions['identifier'] = $this->_identifier;

		$response .= $builder->request($requestOptions, $this->_baseUrl);

		if (null == $this->_identifier)
			return $response . $builder->error(array('code' => 'badArgument'), 'Missing identifier');
		
		if (null == $this->_metadataPrefix) 
			return $response . $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');
 
		if (null == $this->_notice)
			return $response . $builder->error(array('code' => 'idDoesNotExist'));

		if ('oai_dc' != $this->_metadataPrefix) 
			return $response . $builder->error(array('code' => 'cannotDisseminateFormat'));

		$visitor = new Class_Notice_DublinCoreVisitor();
		$visitor->visit($this->_notice);
		$recordBuilder = new Class_WebService_OAI_Response_RecordBuilder();

		return $response . $builder->GetRecord($builder->record($recordBuilder->xml($builder, $visitor)));
	}
}
?>