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
	protected $_notices = array();

	public function buildXmlOn($builder) {
		$response = $builder->request(array('verb' => 'ListIdentifiers'), 
																	$this->_baseUrl);
			
		if ($errors = $this->buildErrorsOn($builder))
			return $response . $errors;

		return $response	. $this->listIdentifiers($builder);
	}


	public function buildErrorsOn($builder) {
		if (!isset($this->_params['metadataPrefix'])) 
			return $builder->error(array('code' => 'badArgument'), 'Missing metadataPrefix');

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


	public function setNotices($notices) {
		$this->_notices = $notices;
	}
}

?>