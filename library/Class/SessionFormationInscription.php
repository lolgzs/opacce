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

class Class_SessionFormationInscription extends Storm_Model_Abstract {
	protected $_table_name = 'session_formation_inscriptions';
	protected $_belongs_to = array(
																 'session_formation' => array('model' => 'Class_SessionFormation'),
																 'stagiaire' => array('model' => 'Class_Users'),
																 'formation' => array('through' => 'session_formation'));

	protected $_default_attribute_values = array('presence' => false);


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function bePresent() {
		return $this->setPresence(true);
	}


	public function beAbsent() {
		return $this->setPresence(false);
	}


	public function isPresent() {
		return $this->getPresence();
	}
}

?>