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

class Class_Ouverture extends Storm_Model_Abstract {
	protected $_table_name = 'ouvertures';
	protected $_default_attribute_values = array('debut_matin' => '10:00',
																							 'fin_matin' => '12:00',
																							 'debut_apres_midi' => '12:00',
																							 'fin_apres_midi' => '18:00');

	public function getLibelle() {
		return '';
	}


	public function getDebutMatin() {
		return $this->getHourAttributeNamed('debut_matin');
	}

	
	public function getFinMatin() {
		return $this->getHourAttributeNamed('fin_matin');
	}


	public function getDebutApresMidi() {
		return $this->getHourAttributeNamed('debut_apres_midi');
	}

	
	public function getFinApresMidi() {
		return $this->getHourAttributeNamed('fin_apres_midi');
	}


	public function getHourAttributeNamed($name) {
		return substr($this->_get($name), 0, 5);
	}
}

?>