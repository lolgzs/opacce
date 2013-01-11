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
// OPAC3 :	Fonctions admin en fonction du role dans interface du site
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_FonctionsAdmin extends ZendAfi_View_Helper_BaseHelper
{
	private $id_profil;											// Profil en cours d'utilisation
	
//------------------------------------------------------------------------------------------------------
// Main routine
//------------------------------------------------------------------------------------------------------  
	public function fonctionsAdmin($contexte,$type_doc=false,$type_module=false) {
		if (!$user = Class_Users::getLoader()->getIdentity())
			return false;
		if (!$user->isAdmin())
			return false;

		$this->id_profil = Class_Profil::getCurrentProfil()->getId();
		
		// Fonctions en accord avec le contexte
		switch($contexte)
		{
			case"module_accueil": $fonction=$this->getModuleAccueil($type_module,$type_doc); break;					// Module de la page d'accueil
			case"module_standard": $fonction=$this->getModuleStandard(); break;															// Module géré par un controller
		}
		
		// Html
		if(!$fonction) return false;
		$html='<div class="configuration_module" style="text-align:right">';
		$onclick="showPopWin('".htmlspecialchars(BASE_URL.$fonction["url"])."',".$fonction["popup_width"].",".$fonction["popup_height"].",null)";
		$html.=sprintf('<img src="'.URL_ADMIN_IMG.'ico/fonctions_admin.png" onclick="'.$onclick.'" alt="%s" title="%s" style="cursor:pointer" />',
									 $this->translate()->_('Propriétés du module'),
									 $this->translate()->_('Propriétés du module'));
		$html.='</div>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Modules des controllers
//------------------------------------------------------------------------------------------------------ 
	private function getModuleStandard()	{
		// Parametres
		extract($this->view->current_module);
		
		$cls_module=new Class_Systeme_ModulesAppli();
		$props=$cls_module->getModule($controller,$action);
		if(!$props) return false;
		
		$ret["url"]="/admin/modules/".$controller."?config=site&type_module=".$controller."&id_profil=".$this->id_profil."&action1=".$action."&action2=".$action2;
		$ret["popup_width"]=$props["popup_width"];
		$ret["popup_height"]=$props["popup_height"];
		return $ret;
	}

//------------------------------------------------------------------------------------------------------
// Modules de la page d'accueil
//------------------------------------------------------------------------------------------------------ 
	private function getModuleAccueil($type_module,$id_module){
		$module = Class_Systeme_ModulesAccueil::moduleByCode($type_module);

		$ret["url"] = sprintf('/admin/accueil/%s?config=accueil&id_profil=%d&id_module=%d&type_module=%s',
													$module->getAction(),
													$this->id_profil,
													$id_module,
													$type_module);

		$ret["popup_height"] = $module->getPopupHeight();
		$ret["popup_width"] = $module->getPopupWidth();
		
		return $ret;
	}
}