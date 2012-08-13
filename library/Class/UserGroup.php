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

class Class_UserGroup extends Storm_Model_Abstract {
	protected $_table_name = 'user_groups';
	protected $_has_many = [ 'user_group_memberships' => [ 'model' => 'Class_UserGroupMembership',
																												 'role' => 'user_group',
																												 'dependent' => 'delete' ],
													 'users' => [ 'through' => 'user_group_memberships',
																				'unique' => true ] ];

	// Les droits doivent être une puissance de 2 (ce sont des masques)
	const RIGHT_SUIVRE_FORMATION = 1;
	const RIGHT_DIRIGER_FORMATION = 2;

	// Type de groupe
	const TYPE_MANUAL = 0;
	const TYPE_DYNAMIC = 1;

	protected static $_rights_definition = [ self::RIGHT_SUIVRE_FORMATION => 'Suivre une formation',
																					 self::RIGHT_DIRIGER_FORMATION => 'Diriger une formation' ];


	protected $_default_attribute_values = ['rights_token' => 0, 
																					'group_type' => 0];

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public static function getRightDefinitionList() {
		return self::$_rights_definition;
	}


	public function getUsers() {
		if ($this->isManual())
			return parent::_get('users');
		return Class_Users::findAllBy(['role_level' => $this->getRoleLevel(),
																	 'limit' => 50]);
	}

	
	/**
	 * Liste des droits, cf. setRights.
	 * Note: les droits sont persistés sous forme d'un entier (rights_token) dans la base,
	 *       chaque bit correspondant à l'activation d'un droit
	 * @return array
	 */
	public function getRights() {
		$rights = array();
		$token = $this->getRightsToken();

		foreach(self::$_rights_definition as $right => $label) {
			if (($right & $token) === $right)
				$rights []= $right;
		}

		return $rights;
	}


	/**
	 * Ex:
	 *	group->setRights(array(Class_UserGroup::RIGHT_DIRIGER_FORMATION,
	 *												Class_UserGroup::RIGHT_SUIVRE_FORMATION));
	 * @param  array $rights
	 */
	public function setRights($rights) {
		$token = 0;
		foreach(self::$_rights_definition as $right => $label) {
			if (in_array($right, $rights))
				$token = $token + $right;
		}
		return $this->setRightsToken($token);
	}


	/**
	 * Ajoute les droits pour le Zend_Form::populate
	 * @return array
	 */
	public function toArray() {
		$attributes = parent::toArray();
		$attributes['rights'] = $this->getRights();
		return $attributes;
	}


	/**
	 * @return bool
	 */
	public function hasRightDirigerFormation() {
		return in_array(self::RIGHT_DIRIGER_FORMATION, $this->getRights());
	}


	/** @return Class_UserGroup */
	public function addRightDirigerFormation() {
		return $this->addRight(self::RIGHT_DIRIGER_FORMATION);
	}


	/** @return Class_UserGroup */
	public function addRightSuivreFormation() {
		return $this->addRight(self::RIGHT_SUIVRE_FORMATION);
	}


	/** 
	 * @param int right
	 * @return Class_UserGroup 
	 */
	public function addRight($right) {
		return $this->setRightsToken($this->getRightsToken() | $right);
	}


	/**
	 * @return Class_UserGroup 
	 */
	public function clearRights() {
		return $this->setRightsToken(0);
	}


	/**
	 * Retourne les libellés des droits courants
	 *
	 * @return array
	 */
	public function getRightsLibelles() {
		$rights = $this->getRights();
		$libelles = array();
		foreach($rights as $right)
			$libelles []= self::$_rights_definition[$right];
		return $libelles;
	}


	/**
	 * @return bool
	 */
	public function isManual() {
		return $this->getGroupType() == self::TYPE_MANUAL;
	}


	/**
	 * @return bool
	 */
	public function isDynamic() {
		return $this->getGroupType() == self::TYPE_DYNAMIC;
	}


	/**
	 * @return Class_UserGroup
	 */
	public function beDynamic() {
		return $this->setGroupType(self::TYPE_DYNAMIC);
	}
}


?>