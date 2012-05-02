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
class Class_WebService_OAI_Response_ListIdentifiers extends Class_WebService_OAI_Response_Null {
	protected $_metadataPrefix;
	protected $_catalogue;
	protected $_set;
	protected $_from;
	protected $_until;
	protected $_resumptionToken;
	protected $_notices;


	public function xml($params = array()) {
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

		return parent::xml();
	}


	public function buildXmlOn($builder) {
		$requestOptions = array('verb' => 'ListIdentifiers');
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

		$response = $builder->request($requestOptions, $this->_baseUrl);
			
		if ($errors = $this->buildErrorsOn($builder))
			return $response . $errors;

		return $response . $this->listIdentifiers($builder);
	}


	public function buildErrorsOn($builder) {
		if (!$this->_metadataPrefix) 
			return $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');

		if ('oai_dc' != $this->_metadataPrefix) 
			return $builder->error(array('code' => 'cannotDisseminateFormat'));

		if ($this->_set && !$this->_catalogue)
			return $builder->error(array('code' => 'badArgument'), 'Set not found');

		if ($this->_resumptionToken 
				&& !($token = Class_WebService_OAI_ResumptionToken::find($this->_resumptionToken)))
			return $builder->error(array('code' => 'badResumptionToken'));

		$this->_notices = Class_Notice::getLoader()->findAllByCatalogue($this->_catalogue);
		if (0 == count($this->_notices))
			return $builder->error(array('code' => 'noRecordsMatch'));
	}


	public function listIdentifiers($builder) {
		return $builder->ListIdentifiers($this->headers($builder));
	}


	public function headers($builder) {
		$visitor = new Class_Notice_DublinCoreVisitor();
		$recordBuilder = new Class_WebService_OAI_Response_RecordHeadersBuilder();
		$headers = '';
		foreach ($this->_notices as $notice) {
			$visitor->visit($notice);
			$headers .= $recordBuilder->xml($builder, $visitor);
		}

		return $headers;
	}


	public function getCatalogueFromSetSpec($setSpec) {
		if (null == $setSpec) 
			return Class_Catalogue::newCatalogueForAll();
		return current(Class_Catalogue::getLoader()->findAllBy(array('oai_spec' => $setSpec)));
	}
}

?>