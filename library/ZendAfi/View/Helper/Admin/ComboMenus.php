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
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Combo des menu paramétrés
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Admin_ComboMenus extends ZendAfi_View_Helper_BaseHelper
{
	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	function comboMenus($id_profil,$menu_selected)	{
		// Lire les menus du profil
		$menus = Class_Profil::getLoader()->find($id_profil)->getCfgMenusAsArray();
		
		// Si yen a pas on dégage
		if(!$menus) 
			return sprintf('<p class="erreur">%s</p>',
										 $this->translate()->_("Aucun menu n'est paramétré pour ce profil"));
		
		// Constitution du html
		$combo='<select name="menu">';
		foreach($menus as $id_menu => $menu)
		{	
			if($menu_selected==$id_menu) $selected=" selected"; else $selected="";
			$combo.='<option style="color:#575757" value="'.$id_menu .'"'.$selected.'>'.stripSlashes($menu["libelle"]).'</option>';
		}
		$combo.='</select>';
		return $combo;
	}
}