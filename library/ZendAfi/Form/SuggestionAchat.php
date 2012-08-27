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

class ZendAfi_Form_SuggestionAchat extends ZendAfi_Form {
	use Trait_Translator;

	public function init() {
		parent::init();
		$this
			->setAttrib('id', 'suggestion')
			->setAttrib('class', 'zend_form')
			->addElement('text', 'titre', ['label' => $this->_('Titre').' *',
																		 'placeholder' => $this->_('Harry Potter à l\'école des sorciers'),
																		 'size' => 80])

			->addElement('text', 'auteur', ['label' => $this->_('Auteur').' *',
																			'placeholder' => 'Joanne Kathleen Rowling',
																			'size' => 80])

			->addElement('url', 'description_url', ['label' => $this->_('Lien internet vers une description'),
																							'placeholder' => 'http://fr.wikipedia.org/wiki/Harry_Potter_à_l\'école_des_sorciers',
																							'size' => 80])

			->addElement('text', 'isbn', ['label' => $this->_('Code-barres / ISBN'),
																		'placeholder' => '2-07-054127-4',
																		'size' => 17])

			->addElement('textarea', 'commentaire', ['label' => '',
																							 'cols' => 80,
																							 'rows' => 10])

			->addDisplayGroup(['titre', 'auteur', 'description_url', 'isbn'],
												'suggestion',
												['legend' => $this->_('Informations sur le document')])

			->addDisplayGroup(['commentaire'],
												'commentaires',
												['legend' => $this->_('Pourquoi suggérez-vous ce document ?')])

			->addElement('submit', 'submit', ['label' => $this->_('Envoyer')]);
	}


	public function removeSubmitButton() {
		$this->removeElement('submit');
		return $this;
	}
}

?>