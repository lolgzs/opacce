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
//////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : IDENTIFICATION
//////////////////////////////////////////////////////////////////////////////////////

class Admin_AuthController extends Zend_Controller_Action
{

	//----------------------------------------------------------------------------------
	// On utilise le layout : sansMenuGauche.phtml
	//----------------------------------------------------------------------------------
	function init()
	{
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('sansMenuGauche.phtml');
	}

	//----------------------------------------------------------------------------------
	// Retour � l'accueil apres authentification
	//----------------------------------------------------------------------------------
	function indexAction()
	{
		$this->_redirect('admin/');
	}

	//----------------------------------------------------------------------------------
	// Formulaire d'identification
	//----------------------------------------------------------------------------------
	function loginAction() {
		$this->view->message = '';
		if (!$this->_request->isPost())
			return;

		// Champs de saisie
		$f = new Zend_Filter_StripTags();
		$username = $f->filter($this->_request->getPost('username'));
		$password = $f->filter($this->_request->getPost('password'));

		if (empty($username))  {
			$this->view->message = "Entrez votre nom d'utilisateur puis validez S.V.P.";
			return;
		}

		$auth = ZendAfi_Auth::getInstance();
		if ($auth->authenticateLoginPassword($username, $password, [$auth->newAuthDb()]))
			$this->_redirect('admin/');
	}

	//----------------------------------------------------------------------------------
	// Deconnexion de l'utilisateur
	//----------------------------------------------------------------------------------
	function logoutAction()
	{
		ZendAfi_Auth::getInstance()->clearIdentity();
		$this->_redirect('admin/');
	}

}
