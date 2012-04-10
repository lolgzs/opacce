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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	Notice OAI
//////////////////////////////////////////////////////////////////////////////////////////

class TableEntrepotOAI extends Zend_Db_Table_Abstract {
    protected $_name = 'oai_entrepots';
}


class Class_EntrepotOAI extends Storm_Model_Abstract {
	protected static $_table;

	protected $_table_name = 'oai_entrepots';
	protected $_has_many = array('notice' => array('model' => 'Class_NoticeOAI'));

	// class side

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	public static function newFromRow($row) {
		if (!is_array($row))
			$row = $row->toArray();

		$entrepot = new self;
		return $entrepot
			->setId($row['id'])
			->setHandler($row['handler'])
			->setLibelle($row['libelle']);
	}

	public static function setTableEntrepotOAI($tbl) {
		self::$_table = $tbl;
	}

	public static function getTableEntrepotOAI() {
		if (!isset(self::$_table))
			self::$_table = new TableEntrepotOAI();
		return self::$_table;
	}

	// Retourne tous les entrepots de la base
	public static function findAll() {
		$rows = self::getTableEntrepotOAI()->fetchAll();
		$entrepots = array();

		foreach($rows as $row)
					$entrepots []= self::newFromRow($row);

		return $entrepots;
	}

	public static function findAllAsArray() {
		$entrepots = self::findAll();
		$ent_array = array();
     foreach ($entrepots as $entrepot)
			 $ent_array[$entrepot->getId()] = $entrepot->getLibelle();
		 return $ent_array;
	}

	// Retourne l'entrepot avec l'id donné
	public static function findById($id) {
		$row = self::getTableEntrepotOAI()->fetchRow('id='.$id);
		if (!$row) return null;
		return self::newFromRow($row);		
	}


	// instance side

	public function setHandler($handler) {
		$this->_handler = $handler;
		return $this;
	}

	public function setId($id) {
		$this->_id = $id;
		return $this;
	}

	public function setLibelle($libelle) {
		$this->_libelle = $libelle;
		return $this;
	}

	public function getHandler() {
		return $this->_handler;
	}

	public function getId() {
		return $this->_id;
	}

	public function getLibelle() {
		return $this->_libelle;
	}

	public function isGallica() {
		return (strpos($this->_get('handler'), 'oai.bnf.fr') !== false);
  }
}

?>