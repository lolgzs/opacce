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
class Class_SuggestionAchat extends Storm_Model_Abstract {
	protected $_table_name = 'suggestion_achat';

	protected $_belongs_to = array('user' => array('model' => 'Class_Users',
																								 'referenced_in' => 'user_id'));

	protected $_default_attribute_values = array('date_creation' => '');

	public function setIsbn($isbn) {
		return parent::_set('isbn', preg_replace('/[^(0-9)]/', '', $isbn));
	}


	public function beforeSave() {
		if (!$this->hasDateCreation())
			$this->setDateCreation(date('Y-m-d'));
	}
}

?>