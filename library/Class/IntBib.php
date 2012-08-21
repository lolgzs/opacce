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
	const COM_PERGAME = 1;
	const COM_OPSYS = 2;
	const COM_VSMART = 4;
	const COM_KOHA = 5;
	const COM_CARTHAME = 6;
	const COM_NANOOK = 7;
	const COM_ORPHEE = 8;
	const COM_MICROBIB = 9;
	const COM_BIBLIXNET = 10;



	private $COM_CLASSES = array(self::COM_PERGAME => 'Class_WebService_SIGB_Pergame',
															 self::COM_OPSYS => 'Class_WebService_SIGB_Opsys',
															 self::COM_VSMART => 'Class_WebService_SIGB_VSmart',
															 self::COM_KOHA => 'Class_WebService_SIGB_Koha',
															 self::COM_CARTHAME => 'Class_WebService_SIGB_Carthame',
															 self::COM_NANOOK => 'Class_WebService_SIGB_Nanook',
															 self::COM_ORPHEE => 'Class_WebService_SIGB_Orphee',
															 self::COM_MICROBIB => 'Class_WebService_SIGB_Microbib',
															 self::COM_BIBLIXNET => 'Class_WebService_SIGB_BiblixNet');

	protected $_table_name = 'int_bib';
	protected $_table_primary = 'id_bib';

	protected $_belongs_to = ['bib' => ['model' => 'Class_IntBib',
																			'role' => 'int_bib',
																			'referenced_in' => 'id_bib']];


	public function setCommParams($string_or_array) {
		if (is_array($string_or_array))
			$cfg = ZendAfi_Filters_Serialize::serialize($string_or_array);
		else
			$cfg = $string_or_array;
		return $this->_set('comm_params', $cfg);
	}


	public function getCommParamsAsArray() {
		$a = ZendAfi_Filters_Serialize::unserialize($this->getCommParams());
		if (!is_array($a))
			return [];
		return $a;
	}


	public function getModeComm() {
		return array_merge($this->getCommParamsAsArray(),
											 ['id_bib' => $this->getId(),
												'type' => $this->getCommSigb()]);
	}


	public function getSIGBComm() {
		$type_comm = $this->getCommSigb();
		if (!isset($this->COM_CLASSES[$type_comm]))
			return null;

		return call_user_func([$this->COM_CLASSES[$type_comm], 'getService'], 
													$this->getModeComm());

	}


	public function getSigbExemplaire($id_origine, $code_barres) {
		return $this->getSIGBComm()->getExemplaire($id_origine, $code_barres);
	}
}

?>