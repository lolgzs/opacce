<?PHP
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

class Class_EntrepotOAI extends Storm_Model_Abstract {
	protected $_table_name = 'oai_entrepots';
	protected $_has_many = array('notice' => array('model' => 'Class_NoticeOAI'));

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	// Retourne tous les entrepots de la base
	public static function findAll() {
		return self::getLoader()->findAllBy(array('order' => 'libelle'));
	}


	public static function findAllAsArray() {
		$entrepots = self::findAll();
		$ent_array = array();
     foreach ($entrepots as $entrepot)
			 $ent_array[$entrepot->getId()] = $entrepot->getLibelle();
		 return $ent_array;
	}


	public function isGallica() {
		return (strpos($this->_get('handler'), 'oai.bnf.fr') !== false);
  }
}

?>