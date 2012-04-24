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


class Class_WebService_OAI_Response_Identify extends Class_WebService_OAI_Response_Null {
	const GRANULARITY = 'YYYY-MM-DD';
	const DELETED_RECORD = 'no';

	protected $_earliestDatestamp;
	protected $_adminEmail;

	public function buildXmlOn($builder) {
		return 
			$builder->request(array('verb' => 'Identify'), 
												$this->_baseUrl)
			. $this->identify($builder);
	}


	public function identify($builder) {
		return $builder->Identify($builder->repositoryName('Afi OPAC 3 Oai repository')
															. $builder->baseURL($this->_baseUrl)
															. $builder->protocolVersion($this->_protocolVersion)
															. $builder->earliestDatestamp($this->_earliestDatestamp)
															. $builder->granularity(self::GRANULARITY)
															. $builder->deletedRecord(self::DELETED_RECORD)
															. $builder->adminEmail($this->_adminEmail));
	}


	public function setEarliestDatestamp($datestamp) {
		$this->_earliestDatestamp = $datestamp;
		return $this;
	}


	public function setAdminEmail($mail) {
		$this->_adminEmail = $mail;
		return $this;
	}
}


?>