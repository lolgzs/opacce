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
require_once('trait_singleton.php');

abstract class codif_abstract {
	use trait_singleton;

	protected $_codif_cache;
 	protected $_table_name;
	protected $_primary_key;

	public function setCodif($codif) {
		$this->_codif_cache = $codif;
	}


	public function getLibelle($codif_id) {
		return $this->getCodif($codif_id)['libelle'];
	}



	public function getCodif($codif_id) {
		global $sql;
		if (!isset($this->_codif_cache[$codif_id]))
			$this->_codif_cache[$codif_id] = $sql->fetchEnreg('select * from '.$this->_table_name.' where '.$this->_primary_key.'='.(int)$codif_id);
		return $this->_codif_cache[$codif_id];
	}


	public function isCodifExists($codif_id) {
		return false != $this->getCodif($codif_id);
	}
}

?>