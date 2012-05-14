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
////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : ENVOI DE MAILS
///////////////////////////////////////////////////////////////////////////////////////

class Class_Mail
{
	private $params_ok=false;								// Flag de controle si un mail peut etre envoyé
	private $mail_from;											// Header from
	private $_translate;
	
//---------------------------------------------------------------------------------
// constructeur : init des parametres
//---------------------------------------------------------------------------------
	public function __construct()	{
		$this->_translate = Zend_Registry::get('translate');
		$this->params_ok = false;

		$this->mail_from = Class_CosmoVar::get('mail_admin');
		
		if ($this->isMailValid($this->mail_from)) {
			ini_set('sendmail_from', $this->mail_from);
			$this->params_ok=true;
		}
	}


	public function _translate($message) {
		return $this->_translate->_($message);
	}


	public function mail($destinataire, $sujet, $body, $headers) {
		$mail = new Zend_Mail('utf8');
		$mail
			->setSubject($sujet)
			->setBodyText($body)
			->setFrom($this->mail_from)
			->addTo($destinataire);

		try {
			$mail->send();
			return true;
		} catch(Exception $e) {
			return $e->getMessage();
		}
	}

//---------------------------------------------------------------------------------
// Envoi de mail
//---------------------------------------------------------------------------------
	public function sendMail($sujet,$body,$destinataire,$data=false)
	{
		$error_message = sprintf('%s <br/> %s',
														 $this->_translate("Les paramètres d'envoi de mails du portail sont incomplets."),
														 $this->_translate("Merci de le signaler aux responsables de la bibliothèque."));

		// Controle des parametres
		if(!trim($destinataire))
			return $this->_translate->_("Adresse du destinataire absente.");

		if($this->params_ok==false or !trim($body))
			return $error_message;

		// Fusion
		if($data)
		{
			foreach($data as $var => $valeur)
			{
				$var="{".$var."}";
				$body=str_replace($var,$valeur,$body);
			}
		}
		$body = wordwrap($body, 60);
		
		// Envoi du mail
		$ret=$this->getHeaders($destinataire);
		if (array_isset("erreur", $ret))
			 return $ret["erreur"];
		else
			$headers=$ret["headers"];

		$statut = $this->mail($destinataire, $sujet, $body, $headers);

		if($statut == false)
			return $error_message;

		return "";
	}


	public function isMailValid($mail) {
		$validator = new Zend_Validate_EmailAddress();
		return $validator->isValid($mail);
	}
	

//---------------------------------------------------------------------------------
// Constitution des headers
//---------------------------------------------------------------------------------
	protected function getHeaders($destinataire)
	{
		$ret = array('headers' => '');
		if (!$this->isMailValid($destinataire)) {
			$ret["erreur"]= "L'adresse e-mail du destinataire est incorrecte.";
			return $ret;
		}
		
		// Headers
		$headers	= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'To: '.$destinataire. "\r\n";
		$headers .= 'From: '.$this->mail_from . "\r\n";
		$ret["headers"]=$headers;
		return $ret;
	}
}

?>