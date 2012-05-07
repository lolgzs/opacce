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
class Class_WebService_OAI_Request_ListIdentifiers {
	const IDENTIFIERS_BY_PAGE = 100;
	protected $_verb = 'ListIdentifiers';
	protected $_baseUrl;
	protected $_params;
	protected $_metadataPrefix;
	protected $_catalogue;
	protected $_set;
	protected $_from;
	protected $_until;
	protected $_resumptionToken;
	protected $_notices;
	protected $_token;

	
	public function __construct($params, $baseUrl) {
		$this->_baseUrl = $baseUrl;
		$this->_params = array_merge(array('metadataPrefix' => null,
																			 'set' => null,
																			 'from' => null,
																			 'until' => null,
																			 'resumptionToken' => null),
																 $params);

		$this->_metadataPrefix = $this->_params['metadataPrefix'];
		$this->_set = $this->_params['set'];
		$this->_from = $this->_params['from'];
		$this->_until = $this->_params['until'];
		$this->_resumptionToken = $this->_params['resumptionToken'];

		$this->_catalogue = $this->getCatalogueFromSetSpec($this->_set);
	}


	public function getNotices() {
		return $this->_notices;
	}


	public function getErrorOn($builder) {
		if (!$this->_metadataPrefix) 
			return $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');

		if ('oai_dc' != $this->_metadataPrefix) 
			return $builder->error(array('code' => 'cannotDisseminateFormat'));

		if ($this->_set && !$this->_catalogue)
			return $builder->error(array('code' => 'badArgument'), 'Set not found');

		if (0 == ($count = $this->_catalogue->getNoticesCount()))
			return $builder->error(array('code' => 'noRecordsMatch'));

		$token = null;
		if ($this->_resumptionToken 
				&& !($token = Class_WebService_OAI_ResumptionToken::find($this->_resumptionToken)))
			return $builder->error(array('code' => 'badResumptionToken'));

		$page_number = 1;
		if (null != $token) {
			$this->_token = $token->next(self::IDENTIFIERS_BY_PAGE);
			$page_number = $this->_token->getPageNumber();
		} elseif (self::IDENTIFIERS_BY_PAGE < $count) {
			$this->_token = Class_WebService_OAI_ResumptionToken::newWithParamsAndListSize($this->_params, $count);
		}

		if ($this->_token)
			$this->_token->save();
		
		$this->_notices = $this->_catalogue->getNotices($page_number, self::IDENTIFIERS_BY_PAGE);
	}


	public function getToken() {
		return $this->_token;
	}


	public function renderOn($builder) {
		$requestOptions = array('verb' => $this->_verb);
		if (null !== $this->_set)
			$requestOptions['set'] = $this->_set;
		if (null !== $this->_from)
			$requestOptions['from'] = $this->_from;
		if (null !== $this->_until) 
			$requestOptions['until'] = $this->_until;
		if (null !== $this->_resumptionToken)
			$requestOptions['resumptionToken'] = $this->_resumptionToken;
		if (null !== $this->_metadataPrefix) 
			$requestOptions['metadataPrefix'] = $this->_metadataPrefix;

		return $builder->request($requestOptions, $this->_baseUrl);
	}


	public function getCatalogueFromSetSpec($setSpec) {
		if (null == $setSpec) 
			return Class_Catalogue::newCatalogueForAll();
		return current(Class_Catalogue::getLoader()->findAllBy(array('oai_spec' => $setSpec)));
	}
}
?>