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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Propriétés des modules des notices
//////////////////////////////////////////////////////////////////////////////////////////
class Admin_ModulesnoticeController extends Zend_Controller_Action
{
	private $id_profil;										// Profil a modifier
	private $path_templates;							// Templates si on vient de l'admin
	private $type_module;									// Identifiant du module à traiter

//------------------------------------------------------------------------------------------------------
// Initialisation des parametres et du layout
//------------------------------------------------------------------------------------------------------
	function init()	{
		// Changer le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');
		
		// Recup des parametres
		$this->type_module=$this->_getParam('action');
		$this->id_profil = $this->_getParam("id_profil");
		
		// Lire la definition du module
		$module=new Class_Systeme_ModulesNotice();
		$def_module=$module->getModule($this->type_module);

		// On initalise les proprietes
		$profil = Class_Profil::getLoader()->find($this->id_profil);

		$cfg = $profil->getCfgNoticeAsArray();
		$preferences=$cfg[$this->type_module];
		if(!$preferences) $preferences=$module->getValeursParDefaut($this->type_module);
		
		// Variables de vue
		$this->view->titre_module=$def_module["libelle"];
		$this->view->preferences=$preferences;
		$this->view->url=$this->_request->getRequestUri();
		$this->view->id_profil=$this->id_profil;
	}

	function preDispatch(){
		Zend_Layout::startMvc();
	}

//------------------------------------------------------------------------------------------------------
// Si pas d'action demandee c'est une erreur
//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		// A FAIRE le PHTML //////////////////////////////////////////////////////////
	}
	
//------------------------------------------------------------------------------------------------------
// Proprietés : BANDE-ANNONCE
//------------------------------------------------------------------------------------------------------
	function bandeannonceAction()
	{
		// Retour du formulaire
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
	}

//------------------------------------------------------------------------------------------------------
// Proprietés : EXEMPLAIRES
//------------------------------------------------------------------------------------------------------
	function exemplairesAction()
	{
		// Retour du formulaire
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			if($enreg["grouper"]==0)
			{
				$enreg["section"]=0;
				$enreg["emplacement"]=0;
				$enreg["annexe"]=0;
			}
			$this->updateEtRetour($enreg);
		}

		if (!array_isset("en_pret", $this->view->preferences) || !trim($this->view->preferences["en_pret"]))
			$this->view->preferences["en_pret"]="emprunté";
	}
	
//------------------------------------------------------------------------------------------------------
// Validation et retour config admin de la page d'accueil
//------------------------------------------------------------------------------------------------------
	private function updateEtRetour($data) {
		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$enreg = $profil->getCfgNoticeAsArray();
		$enreg[$this->type_module]=$data;
		$profil->setCfgNotice($enreg)->save();


		// Execute le script de retour
		$this->view->reload="SITE";
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modulesnotice/_retour.phtml');
	}
	
}