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
	use Trait_Translator;

	protected $_table_name = 'suggestion_achat';

	protected $_belongs_to = ['user' => ['model' => 'Class_Users',
																			 'referenced_in' => 'user_id']];

	protected $_default_attribute_values = ['date_creation' => '',
																					'titre' => '',
																					'auteur' => ''];

	public function setIsbn($isbn) {
		return parent::_set('isbn', preg_replace('/[\s\.\-\_]/', '', (string)$isbn));
	}


	public function beforeSave() {
		if (!$this->hasDateCreation())
			$this->setDateCreation(date('Y-m-d'));
	}


	public function getLibelle() {
		return '';
	}


	public function validate() {
		$this
			->validateTitleOrComment()
			->validateAttribute('description_url', 'ZendAfi_Validate_Url')
			->validateAttribute('isbn', 'ZendAfi_Validate_Isbn');
	}


	public function validateTitleOrComment() {
		$message = $this->_('Titre ou commentaire requis');
		$validator = new Zend_Validate_NotEmpty();

		if ((!$validator->isValid($this->getTitre()))
			&& (!$validator->isValid($this->getCommentaire()))) {
			$this->checkAttribute('titre', false, $message);
			$this->checkAttribute('commentaire', false, $message);
		}

		return $this;
	}


	public function sendMail($from) {
		$body_text = '';

		$infos = [$this->_('Titre') => $this->getTitre(), 
							$this->_('Auteur') => $this->getAuteur(),
							$this->_('ISBN') => $this->getISBN(),
							$this->_('Lien') => $this->getDescriptionUrl(),
							$this->_('Demandeur') => $this->getUser()->getNomComplet(),
							$this->_('N° carte abonné') => $this->getUser()->getIdabon(),
							$this->_('Commentaire') => $this->getCommentaire()];
		foreach($infos as $label => $value)
			$body_text .= sprintf("%s: %s\n", $label, $value);

		$mail = new Zend_Mail('utf8');
		$mail
			->setFrom($from)
			->setSubject($this->_('Suggestion d\'achat: ').$this->getTitre())
			->setBodyText($body_text);

		if ($mail_user = $this->getUser()->getMail())
			$mail->addTo($mail_user);

		if ($mail_profil = Class_Profil::getCurrentProfil()->getMailSuggestionAchatOrPortail())
			$mail->addTo($mail_profil);

		$mail->send();
	}


	public function getIdabon() {
		if ($this->hasUser() &&  $this->getUser()->isAbonne())
			return $this->getUser()->getIdabon();
		return '';
	}


	public function getCompte() {
		if ($this->hasUser())
			return $this->getUser()->getNomComplet();
		return '';
	}
}
?>