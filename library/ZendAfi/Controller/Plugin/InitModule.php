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
// OPAC3 :	Initialisation du module courant
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Plugin_InitModule extends Zend_Controller_Plugin_Abstract
{

	function preDispatch(Zend_Controller_Request_Abstract $request) {
		$module=$request->getModuleName();
		$controller=$request->getParam("controller");
		$action=$request->getParam("action");
		$action2="";
		
		// Cas particuliers
		if($controller=="noticeajax") 
			$action2=$request->getParam("type_doc");

		if($controller=="recherche") {
			if($request->getParam("statut") != "saisie" and $request->getParam("statut") != "reset") { 
				$action2=$action; 
				$action="resultat";
			}
		}

		// Proprietes du module courant
		$current_module["module"]=$module;
		$current_module["controller"]=$controller;
		$current_module["action"]=$action;
		$current_module["action2"]=$action2;
		$current_module["preferences"] = Class_Profil::getCurrentProfil()->getCfgModulesPreferences($controller, $action, $action2);

		// On le passe au controller
		$request->setParam("current_module",$current_module);
	} 

}