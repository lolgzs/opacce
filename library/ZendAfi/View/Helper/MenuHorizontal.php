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
// OPAC3 - Menu horizontal javascript
//
// Ressources :	- Javascript : menu.js
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_MenuHorizontal extends ZendAfi_View_Helper_BaseHelper {
	private $cls_menu;
	private $profil;
	
	function __construct() {
		$this->cls_menu = new Class_Systeme_ModulesMenu();
		$this->profil = Class_Profil::getCurrentProfil();
	}
	
	function get_menus(){
		$menus=$this->profil->getCfgMenusAsArray();
		return $menus["H"]["menus"];
	}
	
	function anchor_for_menu($menu){
		if (!array_isset('preferences', $menu))
			$menu['preferences'] = array();

		$params = $this->cls_menu->getUrl(	$menu["type_menu"],
																				$menu["preferences"]);
		$url = $params['url'];
		if ($params['target'])
			$target = "target='".$params['target']."'";
		else
			$target = '';

		$anchor='<a href="'.htmlspecialchars($url).'" '.$target.'>';
		if($menu["picto"] > '' and $menu["picto"] != "vide.gif") {
			$anchor.=sprintf('<img  alt="%s" src="'.URL_IMG.'menus/'.$menu["picto"].'" />',
											 $this->translate()->_('pictogramme pour %s', $menu["libelle"]));
		}

		$anchor.=$menu["libelle"].'</a>';
		return $anchor;
	}

//---------------------------------------------------------------------
// Fabrique le html (nombre d'entrees principales limite a 10)
//---------------------------------------------------------------------  
	public function menuHorizontal() {
		$menus=$this->get_menus();
		if(!count($menus)) {
			return $this->retourErreur($this->translate()->_("Ce menu ne contient aucune entrée."));
		}

		$html='<div id="menu_horizontal"><ul>';
		foreach($menus as $entree) {
			$html.='<li class="menu" 
				        onmouseover="menu_horizontal_mouse_over(this)"
								onmouseout="menu_horizontal_mouse_out(this)">';
			$html.=$this->anchor_for_menu($entree);

			if(array_isset("sous_menus", $entree)) {
				$html.='<ul class="sous_menu">';
				foreach($entree["sous_menus"] as $sous_menu) {
					$html.='<li>';
					$html.=$this->anchor_for_menu($sous_menu);
					$html.='</li>';
				}
				$html.='</ul>';
			}
			$html.='</li>';
		}
		$html.='</ul></div>';
		return $html;
	}

//---------------------------------------------------------------------
// Retour en erreur
//---------------------------------------------------------------------
	protected function retourErreur($erreur)
	{
		return '<br/><span style=background-color:red;color:#FFFFFF;padding:3px;font-size:12px;font-weight:bold;width:auto">'.$erreur.'</span><br/>';
	}
}