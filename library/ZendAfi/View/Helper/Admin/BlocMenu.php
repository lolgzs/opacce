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
class ZendAfi_View_Helper_Admin_BlocMenu extends ZendAfi_View_Helper_BaseHelper {
	public function blocMenu($path_ico, $id_menu, $menu, $is_sous_menu = false, $browser) {
		// Combo des fonctions
		$cls_menu = new Class_Systeme_ModulesMenu();
		$combo = $cls_menu->getComboFonctions($menu['type_menu'], $is_sous_menu, $browser);
	
		// Url pour les proprietes
		$url_property = "majProprietes(this,'" . BASE_URL . "/admin/menus/');";
		$preferences = '';
		if (array_isset('preferences', $menu))	{
			foreach($menu['preferences'] as $clef => $valeur) 
				$preferences .= $clef . '=' . $valeur . '|';
		}
	
		// Html
		$display = ($id_menu == 'menu_vide' or $id_menu == 'sous_menu_vide')
				         ? 'none' : 'block';
		$onclick = "if(confirm('Etes-vous sûr de vouloir supprimer cette entrée de menu ?')) deleteMenu(this)";

		$html = '<div class="formTable" id="' . $id_menu . '" type_menu="' . $menu['type_menu'] . '" style="display:' . $display . '" libelle="' . $menu['libelle'] . '" picto="' . $menu['picto'] . '" preferences="' . $preferences . '">';
		$html.= '<table widh="100%"><tr>';
		$html.= '<td width="15px"><img src="' . URL_ADMIN_IMG . 'picto/fleche_verte.gif" alt=""/></td>';
		$html.= '<td width="20px"><img id="picto" src="'. $path_ico . $menu['picto'] . '" alt ="picto"/></td>';
		$html.= '<td align="left"><span id="libelle" class="entree_menu">'.$menu['libelle'].'</span></td>';
		$html.= '<td align="right">' . $combo . '</td>';
		$html.= '<td width="20px" align="center"><img src="'.URL_ADMIN_IMG.'ico/edit.gif" onclick="' . $url_property . '" style="cursor:pointer" title="Propriétés" alt="éditer"/></td>';
		$html.= '<td width="20px" align="center"><img src="'.URL_ADMIN_IMG.'ico/del.gif" onclick="' . $onclick . '" style="cursor:pointer" title="Supprimer" alt="Supprimer" /></td>';
		$html.= '<td width="20px" align="center"><img src="'.URL_ADMIN_IMG.'ico/up.gif" onclick="monterMenu(this)" style="cursor:pointer" title="Monter" alt="Monter" /></td>';
		$html.= '<td width="20px" align="center"><img src="'.URL_ADMIN_IMG.'ico/down.gif" onclick="descendreMenu(this)" style="cursor:pointer" title="Descendre" alt="Descendre" /></td>';
		$html.= '</tr></table>';
	
		$this->_writeChildrenOfMenuOnHtml($menu, $html);

		$html .= '</div>';
		
		return $html;
	}


	protected function _writeChildrenOfMenuOnHtml($menu, $html) {
		// Ajout bloc des sous-menus 
		$display = ($menu['type_menu'] == 'MENU') ? 'block' : 'none';

		$html .= '<div id="sous_menu" class="formTable" style="margin-left:50px;border:1px solid #C8C8C8;padding:5px;margin-bottom:5px;margin-right:0px;border-right:none;display:' . $display . '">';
		$html .= '<div class="fonction_menu" onclick="addSousMenu(this);">&raquo;&nbsp;Ajouter un sous-menu</div>';

		if (!array_isset('sous_menus', $menu)) {
			$html .= '</div>';
			return;
		}

		foreach ($menu['sous_menus'] as $sous_menu )
			$html .= $this->blocMenu($path_ico, 'module', $sous_menu, true, $browser);

		$html .= '</div>';
	}
}
?>