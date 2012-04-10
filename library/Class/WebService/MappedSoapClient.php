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

class Class_WebService_MappedSoapClient extends SoapClient {
	/*
	 * A SoapClient which automatically map existing
	 * PHP classes to WSDL types with the same name
	 */

	protected function __getWSDLStructTypes(){
		$types = array();

		foreach($this->__getTypes() as $type) {
			preg_match("/([a-z0-9_]+)\s+([a-z0-9_]+(\[\])?)(.*)?/si", $type, $matches);
			if ($matches[1] == 'struct') $types[]=$matches[2];
		}
		return $types;		
	}

	protected function __generateClassmap(){
		$classnames = $this->__getWSDLStructTypes();

		foreach($classnames as $classname)
			if (class_exists($classname, false))
				$this->_classmap[$classname] = $classname;
	}


	public function __construct($wsdl, $options = array()) {
		parent::__construct($wsdl, $options);
		$this->__generateClassmap();
	}
}


?>