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

class Class_FRBR_Link extends Storm_Model_Abstract {
	use Trait_Translator;
	
	protected $_table_name = 'frbr_link';

	protected $_belongs_to = ['type' => ['model' => 'Class_FRBR_LinkType',
	                                     'referenced_in' => 'type_id']];


	/**
	 * @return string
	 */
	public function getLinkCompleteLabel() {
		if (!$type = $this->getType())
			return '';

		return $type->getCompleteLabel();
	}


	public function getLibelle() {
		return '';
	}


	public function validate() {
		$this
			->validateAttribute('source', 'Zend_Validate_NotEmpty', $this->_('Un libellé objet A est requis'))
			->validateAttribute('target', 'Zend_Validate_NotEmpty', $this->_('Un libellé objet B est requis'));
	}


	public function beforeSave() {
		parent::beforeSave();
		if (false === strpos($this->getSource(), '/clef/'))
			return;

		$parts = explode('/', $this->getSource());
		$this->setSource($parts[array_search('clef', $parts) + 1]);
	}
}

?>