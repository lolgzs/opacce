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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  OPAC3: AUTHENTIFICATION ABONNE
//
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class AuthController extends Zend_Controller_Action
{
	function init()
	{
		$this->view->locale = Zend_Registry::get('locale');


	}

	function indexAction()	{
		$this->_redirect('opac/');
	}



	protected function _authenticate() {
		// collect the data from the user
		$f = new Zend_Filter_StripTags();
		$username = $f->filter($this->_request->getPost('username'));
		$password = $f->filter($this->_request->getPost('password'));

		if (empty($username))
			return $this->view->_('Entrez votre identifiant S.V.P.');

		if (empty($password))
			return $this->view->_('Entrez votre mot de passe S.V.P.');

		// do the authentication
		if (!ZendAfi_Auth::getInstance()->authenticateLoginPassword($username, $password))
			return $this->view->_('Identifiant ou mot de passe incorrect.');
	}

//------------------------------------------------------------------------------------------------------
// Login normal
//------------------------------------------------------------------------------------------------------
	function loginAction() {
		$this->view->titreAdd("Se connecter");
		$error = (int)$this->_getParam('error');
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('module.phtml');

		if($this->_request->isPost()) {
			$error = $this->_authenticate();
			if (!$error) {
				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				if (isset($_SESSION["abonne_redirect"]))
					$this->getResponse()->setBody("<script>window.location.replace('" .$_SESSION["abonne_redirect"] . "');</script>");
				else
					$this->_redirect('opac');
			}
		}

		$this->view->message = $error;

	}


//------------------------------------------------------------------------------------------------------
// Login dans popup
//------------------------------------------------------------------------------------------------------
	function ajaxloginAction()
	{
		$ss_var = Zend_Registry::get('session');

		if ($this->_request->isPost())	{
			$error = $this->_authenticate();

			if (!$error) {
				$data = ZendAfi_Auth::getInstance()->getIdentity();
				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.abonne_ok(".$data->ID_USER.",'". $data->LOGIN ."', '')</script>");
			}
		}

		// Affichage du formulaire
		$this->view->message = isset($error) ? $error : null;
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Login (dans boite accueil)
//------------------------------------------------------------------------------------------------------
	function boiteloginAction() {
		$id_module = $this->_getParam('id_module');

		$this->view->preferences = Class_Profil::getCurrentProfil()->getModuleAccueilPreferences($id_module);

		if ($this->_request->isPost()) {
			if (!$error = $this->_authenticate())
				$this->_redirect($this->_request->getServer('HTTP_REFERER'));
		}

		$this->view->boite_login_message = $error;
		$this->view->id_module = $id_module;

	}

//------------------------------------------------------------------------------------------------------
// Mot de passe perdu (AJAX)
//------------------------------------------------------------------------------------------------------
	function ajaxlostpassAction()
	{
		if($_POST)
		{
			$user=ZendAfi_Filters_Post::filterStatic($this->_request->getPost('username'));
			$classe_user = new Class_Users();
			$ret=$classe_user->lostpass($user);
			$this->view->message=$this->messages[$ret["error"]];
			$this->view->message_mail=$ret["message_mail"];
		}
		$this->view->username=$user;
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Logout
//------------------------------------------------------------------------------------------------------
	function logoutAction()	{
		ZendAfi_Auth::getInstance()->clearIdentity();
		$this->_redirect('/');
	}

//------------------------------------------------------------------------------------------------------
// Mot de passe perdu
//------------------------------------------------------------------------------------------------------
	function lostpassAction()
	{
		if($_POST)
		{
			$user=ZendAfi_Filters_Post::filterStatic($this->_request->getPost('username'));
			$classe_user = new Class_Users();
			$ret=$classe_user->lostpass($user);
			$this->view->message=$this->messages[$ret["error"]];
			$this->view->message_mail=$ret["message_mail"];
		}
		$this->view->username=$user;
	}

//------------------------------------------------------------------------------------------------------
// Nouvelle inscription
//------------------------------------------------------------------------------------------------------
	function registerAction()	{
		if (Class_AdminVar::get('INTERDIRE_ENREG_UTIL'))
			$this->_redirect('/');

		if ($this->_request->isPost())
		{
			// recup _post
			$data=ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
			$class_user = new Class_Users();
			$ret=$class_user->registerUser($data);

			// Affichage des erreurs
			if($ret["error"])
			{
				$this->view->cle = $this->getCleActivation();
				$this->view->login = $data["login"];
				$this->view->email = $data["mail"];
				$this->view->error = '<div align="center" class="error">'.$ret["error"].'</div>';
			}
			$this->view->message_mail=$ret["message_mail"];
		}

		// Opération terminée
		if($this->view->message_mail)
		{
			$this->view->titre=$this->view->_("Votre demande d'inscription");
			$viewRenderer = $this->getHelper('ViewRenderer');
			$viewRenderer->renderScript('auth/message.phtml');
		}

		// Formulaire de saisie
		$this->view->img_captcha = '<img src="'.BASE_URL.'/auth/generateCaptcha" />';
		$this->view->cle = $this->getCleActivation();
	}

//------------------------------------------------------------------------------------------------------
// Activation d'une nouvelle inscription
//------------------------------------------------------------------------------------------------------
	function activeuserAction()
	{
		$class_user = new Class_Users();
		$cle = $this->_request->getParam('c');
		$update_user = $class_user->activerRegistration($cle);

		if($update_user =="")
		{
			$active = str_replace('%0D%0A',"<br />",getVar('USER_VALIDATED'));
			$this->view->info = urldecode($active);
		}
		else
		{
			$activate = str_replace('%0D%0A',"<br />",getVar('USER_NON_VALIDATED'));
			$this->view->info = urldecode($activate);
		}
	}

//------------------------------------------------------------------------------------------------------
// Captcha
//------------------------------------------------------------------------------------------------------
	function generatecaptchaAction()
	{
		$md5_hash = md5(rand(0,999));
		$security_code = substr($md5_hash, 15, 5);

		$_SESSION['captcha_code'] = $security_code;
		$image = ImageCreate(100, 20);                          // largeur, hauteur
		$fond = ImageColorAllocate($image, 0, 0, 0);
		$white = ImageColorAllocate($image, 255, 255, 255);    // bg image
		ImageString($image, 3, 30, 3, $security_code, $white);

		header("Content-Type: image/jpeg");
		ImageJpeg($image);
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}

//------------------------------------------------------------------------------------------------------
// genere une cle aleatoire pour l'activation de compte par URL
//------------------------------------------------------------------------------------------------------
	function getCleActivation() {
		$cle = '';
		for ($i=0; $i< 10; $i++)
		{
			$nb_ascii = rand(1,26) + 64 ;
			$cle.=chr($nb_ascii).rand(0,9);
		}
		return($cle);
	}
}