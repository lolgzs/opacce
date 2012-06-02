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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Menu vertical javascript
//
// Ressources :	- Javascript : imageMenu.js
//							- css :imageMenu.css
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Java_MenuImage {
		
//---------------------------------------------------------------------
// Fabrique le html (nombre d'entrees principales limite a 10)
//---------------------------------------------------------------------  
	public function MenuImage()	{
		$cls_menu=new Class_Systeme_ModulesMenu();
		$profil= Class_profil::getCurrentProfil();

		// Lire la config du menu
		$menus = $profil->getCfgMenusAsArray();
		$config=$menus["H"];
		if(!count($config["menus"])) 
			return $this->retourErreur("Ce menu ne contient aucune entrée.");
		
		// Construction du html
		$html='<div id="imageMenu"><ul>';
		
		$num_menu=0;
		$contenu='<div class="menuGauche"><ul class="menuGauche">';
		foreach($config["menus"] as $entree)	{
			$id_menu="vmenu".$num_menu;
			$html.='<li class="'.$id_menu.'">';
			$html.='<a href="#">';
			$html.='<div id="'.$id_menu.'" style="display:none;">';
			$html.='<div class="titre">'.$entree["libelle"].'</div>';

			if($entree["sous_menus"])	{
				$num_sous_menu=0;
				$html.='<table cellspacing="2" cellpadding="2">';
				foreach($entree["sous_menus"] as $sous_menu)	{ 
					$id_sous_menu=$id_menu."_".$num_sous_menu;
					$preferences = isset($sous_menu["preferences"]) ? $sous_menu["preferences"] : array();
					$param_url=$cls_menu->getUrl($sous_menu["type_menu"],	$preferences);
					$html.='<tr><td onclick="window.parent.location=\''.$param_url["url"].'\';">&raquo&nbsp;'.$sous_menu["libelle"].'</td></tr>';
					$num_sous_menu++;
				}
				$html.='</table>';
			}
			$num_menu++;
			$html.='</div></a></li>';
		}
		$html.='</ul></div>';

		// Retour
		return $html;
	}
	
//---------------------------------------------------------------------
// Retour en erreur
//--------------------------------------------------------------------- 
	protected function retourErreur($erreur)
	{
		return '<br><span style="background-color:red;color:#FFFFFF;padding:3px;font-size:12px;font-weight:bold;width:auto">'.$erreur.'</span><br>';
	}
}