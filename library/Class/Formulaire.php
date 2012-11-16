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

class Class_Formulaire extends Storm_Model_Abstract {
	use Trait_TimeSource;

	protected	$_table_name='formulaires';
	protected $_belongs_to =  ['user' => ['model' => 'Class_Users',
																				'referenced_in' => 'id_user'],

														 'article' => ['model' => 'Class_Article',
																					 'referenced_in' => 'id_article']];

	protected $_default_attribute_values = ['data' => 'a:0:{}'];

	protected $_datas;
	public static function mergeDataNames($formulaires) {
		$names = [];
		foreach($formulaires as $formulaire) {
			$names=array_merge($names,$formulaire->getDataNames());
		}
		
		return array_unique($names);
	}

	public function getDataNames() {
		return array_keys($this->getDatas());
		
	}


	public function getDatas() {
		return isset($_datas) 
		? $this->_datas 
		: $this->_datas = unserialize(parent::_get('data'));
	}


	public function _get($attribute) {
		try {
			return parent::_get($attribute);
		}
		catch (Exception $e) {
			$datas = array_change_key_case($this->getDatas());
			$attribute=strtolower($attribute);
			return isset($datas[$attribute])?$datas[$attribute]:'' ;
		}
	}


	public function beforeSave() {
		$this->setDateCreation(date('Y-m-d H:i:s', self::getTimeSource()->time()));
	}

}
