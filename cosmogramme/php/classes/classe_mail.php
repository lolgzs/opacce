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
////////////////////////////////////////////////////////////////////////////////////////
// ENVOI DE MAILS
///////////////////////////////////////////////////////////////////////////////////////

class classe_mail
{
	private $params_ok=false;								// Flag de controle si un mail peut etre envoyé
	private $mail_from;											// Header from
	private $sujet;													// Objet du mail
	private $body;													// Corps du mail
	
//---------------------------------------------------------------------------------
// constructeur : init des parametres
//---------------------------------------------------------------------------------
	function __construct()
	{
		$this->mail_from=trim(getVariable("mail_admin"));
		if($this->mail_from) ini_set('sendmail_from', $this->mail_from);
		$this->sujet=utf8_decode(trim(getVariable("mail_retard_sujet")));
		$this->body=trim(getVariable("mail_retard_body"));	
		
		// Flag de controle
		if($this->mail_from > '' and $this->sujet > '' and $this->body > '') $this->params_ok=true;
	}

//---------------------------------------------------------------------------------
// Envoi de mail
//---------------------------------------------------------------------------------
	public function sendMail($destinataire,$data)
	{
		// Controle des parametres
		if($this->params_ok==false)	return "Les paramètres d'envoi de mails du portail sont incomplets (variables : mail_admin, mail_retard_sujet et mail_retard_body)";
		
		// Fusion
		$body=$this->body;
		foreach($data as $var => $valeur)
		{
			$var="{".$var."}";
			$body=str_replace($var,$valeur,$body);
		}
		$body = wordwrap($body, 60);
		
		// Envoi du mail
		$ret=$this->getHeaders($destinataire);
		if($ret["erreur"]) return $ret["erreur"];
		else $headers=$ret["headers"];
		$statut=mail($destinataire, $this->sujet, $body, $headers);
		if($statut == false) return "Une erreur s'est produite à l'envoi du mail.";
		return "";
	}
	
//---------------------------------------------------------------------------------
// Constitution des headers
//---------------------------------------------------------------------------------
	private function getHeaders($to)
	{
		// Controle de l'adresse du destinataire
		//$masque='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
		//if(!preg_match($masque,$to))
		//{
			//$ret["erreur"]= "L'adresse e-mail du destinataire est incorrecte.";
			//return $ret;
		//}
		
		// Headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'To: '.$to. "\r\n";
    $headers .= 'From: '.$this->mail_from . "\r\n";
    $ret["headers"]=$headers;
    return $ret;
	}

}

?>