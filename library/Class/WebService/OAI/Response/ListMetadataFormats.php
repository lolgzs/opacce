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
class Class_WebService_OAI_Response_ListMetadataFormats extends Class_WebService_OAI_Response_Null {
	const OAI_DC_SCHEMA = 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd';
	const OAI_DC_NAMESPACE = 'http://www.openarchives.org/OAI/2.0/oai_dc/';

	public function buildXmlOn($builder) {
		return 
			$builder->request(array('verb' => 'ListMetadataFormats'), 
												$this->_baseUrl)
			. $this->listMetadataFormats($builder);
	}

		
	public function listMetadataFormats($builder) {
		return $builder->ListMetadataFormats(
							 $builder->metadataFormat($builder->metadataPrefix('oai_dc')
																				. $builder->schema(self::OAI_DC_SCHEMA)
																				. $builder->metadataNamespace(self::OAI_DC_NAMESPACE)));
	}
}
?>