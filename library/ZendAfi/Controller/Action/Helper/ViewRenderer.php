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
////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : MECANISME DE BASE POUR LE RENDU DU SITE
////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_ViewRenderer
{
	protected $_layoutScript = 'module.phtml';

//-------------------------------------------------------------------------------
// Constructeur
//-------------------------------------------------------------------------------
	public function __construct()
	{
		$options['viewSuffix'] = 'phtml';
		$view=new ZendAfi_Controller_Action_Helper_View();
		parent::__construct($view, $options);
	}


	public function getRouteName() {
		return Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
	}


	public function isEmbedded() {
		return ('embed' === $this->getRouteName());
	}

//-------------------------------------------------------------------------------
// Initialisation du view : Rajout des chemins sur helpers et skin courant
//-------------------------------------------------------------------------------
	public function preDispatch() {
		$module=$this->getmodule();
		if ($module == "admin") {
			$this->view->addHelperPath('ZendAfi/View/Helper/Admin/', 'ZendAfi_View_Helper_Admin');
		} else {
			if ($module=="opac") {
				$this->view->addHelperPath('ZendAfi/View/Helper/Java', 'ZendAfi_View_Helper_Java');
				$this->view->addHelperPath('ZendAfi/View/Helper/Accueil', 'ZendAfi_View_Helper_Accueil');
			}

			if ($module=="telephone") {
				if (isTelephone() or $this->isEmbedded())
					$this->setLayoutScript("main.phtml");
				else
					$this->setLayoutScript("iphone.phtml");

				$this->view->addHelperPath('ZendAfi/View/Helper/Telephone', 'ZendAfi_View_Helper_Telephone');
				$this->view->addHelperPath('ZendAfi/View/Helper/Telephone/Tags', 'ZendAfi_View_Helper_Telephone_Tags');
			}
			$this->view->addScriptPath("..".URL_HTML);
		}

		// user connecté
		$user = Zend_Auth::getInstance()->getIdentity();
		if ($user) {
			$this->view->authUser = $user;
		}
		$this->view->session = Zend_Registry::get('session');  // CA NE SERT PLUS BEAUCOUP -> A VIRER

		// Initialisation du profil
		$this->view->profil = Class_Profil::getCurrentProfil();

		// Initialisation du module courant
		$this->view->current_module=$this->getRequest()->getParam("current_module");
		$this->view->initBoite($this->view->current_module["preferences"]["boite"]);
	}


//-------------------------------------------------------------------------------
// Setter et getter du html container du site (header, contenu vide et footer)
//-------------------------------------------------------------------------------
	public function setLayoutScript($script)
	{
		$this->_layoutScript = $script;
	}

	public function getLayoutScript()
	{
		return $this->_layoutScript;
	}


//-------------------------------------------------------------------------------
// Rendu du contenu html du site (layout + script courant)
//-------------------------------------------------------------------------------
	public function renderScript($script, $name = null)
	{
		$this->view->actionScript = $script;
		$layoutScript = $this->getLayoutScript();
		$layoutContent = $this->view->render($layoutScript);
		$this->getResponse()->appendBody($layoutContent, $this->getResponseSegment());
		$this->setNoRender();
	}
}

?>