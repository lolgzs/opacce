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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Liste des langues dans cosmogramme
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_CodifLangue extends Storm_Model_Abstract {
  protected $_table_name = 'codif_langue';
  protected $_table_primary = 'id_langue';

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	/**
	 * @param Array langues
	 * @return array
	 */
	public static function toIdLibelleArray($langues) {
		$id_libelle_array = array();
		foreach($langues as $langue)
			$id_libelle_array[$langue->getId()] = $langue->getLibelle();
		return $id_libelle_array;
	}


	/**
	 * @param Array langues
	 * @return array
	 */
	public static function allByIdLibelle() {
		return self::toIdLibelleArray(self::getLoader()->findAllBy(array('order' => 'libelle')));
	}
}

?>
