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

class Class_Formation extends Storm_Model_Abstract {
	protected $_table_name = 'formations';
	protected $_has_many = array('sessions' => array(
																									 'model' => 'Class_SessionFormation',
																									 'role' => 'formation',
																									 'dependents' => 'delete',
																									 'order' => 'date_debut desc'),

															 'session_formation_inscriptions' => array('through' => 'sessions'),

															 'stagiaires' => array('through' => 'session_formation_inscriptions'));

	protected $_default_attribute_values = array('description' => '');

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public static function indexByYear($formations) {
		$formations_by_year = array();
		foreach($formations as $formation) {
			if (!array_key_exists($formation->getAnnee(), $formations_by_year))
				$formations_by_year[$formation->getAnnee()] = array();
			if ($formation->hasSessions())
				$formations_by_year[$formation->getAnnee()][] = $formation;
		}
		ksort($formations_by_year);

		return $formations_by_year;
	}


	/** @return String */
	public function getAnnee() {
		if ($this->hasSessions())
			return array_first($this->getSessions())->getAnnee();
		return date('Y');
	}

}

?>