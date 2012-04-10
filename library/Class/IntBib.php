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

/**
 *  Configuration bib dans Cosmogramme
 */

class Class_IntBib extends Storm_Model_Abstract {
	protected $_table_name = 'int_bib';
	protected $_table_primary = 'id_bib';


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function setCommParams($string_or_array) {
		if (is_array($string_or_array))
			$cfg = ZendAfi_Filters_Serialize::serialize($string_or_array);
		else
			$cfg = $string_or_array;
		return $this->_set('comm_params', $cfg);
	}


	public function getCommParamsAsArray() {
		return ZendAfi_Filters_Serialize::unserialize($this->getCommParams());
	}
}

?>