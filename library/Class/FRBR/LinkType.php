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

class Class_FRBR_LinkTypeLoader extends Storm_Model_Loader {
	/**
	 * @return array
	 */
	public function getComboList() {
		$list = [];
		foreach ($this->findAllBy(['order' => 'libelle']) as $model)
			$list[$model->getId()] = $model->getLibelle();
		
		return $list;
	}
}


class Class_FRBR_LinkType extends Storm_Model_Abstract {
	use Trait_Translator;
	
	protected $_table_name = 'frbr_linktype';
	protected $_loader_class = 'Class_FRBR_LinkTypeLoader';
	protected $_has_many = ['links' => ['model' => 'Class_FRBR_Link',
			                                'role' => 'type']];


	/**
	 * @return string
	 */
	public function getCompleteLabel() {
		return '    ' . $this->getFromSource() . ' -&gt;<br>&lt;- ' . $this->getFromTarget();
	}

	
	public function validate() {
		$this
			->validateAttribute('libelle', 'Zend_Validate_NotEmpty', $this->_('Un libellé est requis'))
			->validateAttribute('from_source', 'Zend_Validate_NotEmpty', $this->_('Un libellé de l\'objet A vers l\'objet B est requis'))
			->validateAttribute('from_target', 'Zend_Validate_NotEmpty', $this->_('Un libellé de l\'objet B vers l\'objet A est requis'));
	}
}

?>