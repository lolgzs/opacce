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

class ZendAfi_Form_FRBR_Link extends ZendAfi_Form {
	use Trait_Translator;

	public function init() {
		parent::init();
		$this
			->setAttrib('id', 'frbr_link')
			->setAttrib('class', 'zend_form')
			
			->addElement('frbrType', 'type_id', ['label' => $this->_('Type').' *'])
			->addElement('text', 'source', ['label' => $this->_('URL Objet A') . ' *', 'size' => 80])
			->addElement('text', 'target', ['label' => $this->_('URL Objet B') . ' *', 'size' => 80])

			->addDisplayGroup(['type_id', 'source', 'target'], 'link', ['legend' => '']);
	}
}

?>