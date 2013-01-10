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


class Class_Systeme_ModulesMenu extends Class_Systeme_ModulesAbstract {
	const PREFERENCES_KEY	= 'preferences';
	const MENUS_KEY				= 'menus';
	const SUBMENUS_KEY		= 'sous_menus';

	const GROUP_MENU_NAVIGATION = 'NAV';
	const GROUP_MENU_INFORMATIONS = 'INFO';
	const GROUP_MENU_RECHERCHES = 'RECH';
	const GROUP_MENU_CATALOGUES = 'CATALOG';
	const GROUP_MENU_ABONNES = 'ABON';

	protected $_groupes = [
		self::GROUP_MENU_NAVIGATION => "Navigation"  ,
		 self::GROUP_MENU_INFORMATIONS => "Informations",
		 self::GROUP_MENU_RECHERCHES =>"Recherches" ,
		self::GROUP_MENU_CATALOGUES => "Catalogues",
		self::GROUP_MENU_ABONNES	=> "Abonnés",
		Class_Systeme_ModulesAccueil::GROUP_INFO => "Modules informations",
		Class_Systeme_ModulesAccueil::GROUP_RECH => "Modules Recherches",
		Class_Systeme_ModulesAccueil::GROUP_SITE => "Modules Site",
		Class_Systeme_ModulesAccueil::GROUP_ABONNE => "Modules Abonnés"

	];

	private $fonctions;

	public function __construct() {
		$this->fonctions = [
			"vide" => new Class_Systeme_ModulesMenu_Null(),
			"MENUACCUEIL" => new Class_Systeme_ModulesMenu_Accueil(),
			"MENUCONNECT" => new Class_Systeme_ModulesMenu_Connect(),
			"MENUDISCONNECT" => new Class_Systeme_ModulesMenu_Disconnect(),
			"MENUGOOGLEMAP" => new Class_Systeme_ModulesMenu_GoogleMap(),
			"MENUAVIS" => new Class_Systeme_ModulesMenu_Avis(),
			"MENULAST_NEWS" => new Class_Systeme_ModulesMenu_LastNews(),
			"MENUNEWS" => new Class_Systeme_ModulesMenu_News(),
			"MENUSITO" => new Class_Systeme_ModulesMenu_Sitotheque(),
			"MENURSS" => new Class_Systeme_ModulesMenu_Rss(),
			"MENUURL" => new Class_Systeme_ModulesMenu_Url(),
			"MENUPROFIL" => new Class_Systeme_ModulesMenu_Profil(),
			"MENUBIBNUM" => new Class_Systeme_ModulesMenu_BibliothequeNumerique(),
			"MENURECH_SIMPLE" => new Class_Systeme_ModulesMenu_RechercheSimple(),
			"MENURECH_AVANCEE" => new Class_Systeme_ModulesMenu_RechercheAvancee(),
			"MENURECH_GUIDEE" => new Class_Systeme_ModulesMenu_RechercheGuidee(),
			"MENURECH_GEO" => new Class_Systeme_ModulesMenu_RechercheGeographique(),
			"MENURECH_OAI" => new Class_Systeme_ModulesMenu_RechercheOai(),
			"MENUCATALOGUE" => new Class_Systeme_ModulesMenu_Catalogue(),
			"MENUETAGERE" => new Class_Systeme_ModulesMenu_Etagere(),
			"MENUTAGS" => new Class_Systeme_ModulesMenu_Tags(),
			"MENUPANIER" => new Class_Systeme_ModulesMenu_Paniers(),
			"MENUABON_AVIS" => new Class_Systeme_ModulesMenu_Avis(),
			"MENUABON_FICHE" => new Class_Systeme_ModulesMenu_AbonneFiche(),
			"MENUABON_MODIF_FICHE" => new Class_Systeme_ModulesMenu_AbonneModificationFiche(),
			"MENUABON_PRETS" => new Class_Systeme_ModulesMenu_AbonnePrets(),
			"MENUABON_RESAS" => new Class_Systeme_ModulesMenu_AbonneReservations(),
			"MENUABON_FORMATIONS" => new Class_Systeme_ModulesMenu_AbonneFormations(),
			"MENUFORM_CONTACT" => new Class_Systeme_ModulesMenu_FormulaireContact(), 
			"MENUVODECLIC" => new Class_Systeme_ModulesMenu_Vodeclic(),
			"MENURESERVER_POSTE" => new Class_Systeme_ModulesMenu_ReserverPoste(),
			'MENUSUGGESTION_ACHAT' => new Class_Systeme_ModulesMenu_SuggestionAchat()
	 ];
		$this->fonctions = array_merge($this->fonctions,Class_Systeme_ModulesAccueil::getModules());
	
		foreach ($this->fonctions as $key => $module) {
			if (!$module->isVisibleForProfil(null)) 
				unset($this->fonctions[$key]);
				
		}
	}


	public function getValeursParDefaut($type) {
		return $this->getFonction($type)->getDefaultValues();

	}


	/**
	 * @param string $type
	 * @return array
	 */
	public function getFonction($type) {
		if (isset($this->fonctions[$type]))
				return $this->fonctions[$type];
		return new Class_Systeme_ModulesMenu_Null();
	}


	/**
	 * @param string $type
	 * @param array $preferences
	 * @return string
	 */
	public function getUrl($type, $preferences) {
		$module = $this->getFonction($type);

		$preferences = array_merge($module->getDefaultValues(),$preferences);

		return ['url' => $module->getUrl($preferences),
						'target' => $module->shouldOpenInNewWindow($preferences) ? '_blank' : ''];
	}



	/**
	 *
	 * @param string $item_selected
	 * @param string $is_sous_menu
	 * @param string $browser
	 * @return string
	 */
	public function getComboFonctions($item_selected, $is_sous_menu=false, $browser) {
		$combo = '<select name="type_menu" onchange="setTypeMenu(this)">';

		if (!$is_sous_menu)
			$combo.='<option value="MENU">Menu</option>';

		foreach ($this->getGroupes() as $id_groupe => $libelle) {
			$combo.='<optgroup style="font-style: normal; color: #FF6600;" label="' . $libelle . '">';

			foreach ($this->fonctions as $id_fonction => $module) {
				if ($module->getGroup() != $id_groupe)
					continue;

				if ($browser == "telephone" && !$module->isPhone())
					continue;

				if ($item_selected == $id_fonction)
					$selected = " selected"; else
					$selected="";

				$combo.='<option style="color:#575757" value="' . $id_fonction . '"' . $selected . '>' . stripSlashes($module->getLibelle()) . '</option>';

			}

			$combo.='</optgroup>';

		}

		$combo.='</select>';

		return $combo;
	}

	/**
	 * @return string
	 */
	public function getStructureJavaScript() {
		$js = "function initModules(){" . NL;
		$js.="sModules['vide']=new Array();" . NL;

		//		foreach ($this->fonction_vide as $id => $valeur)
		//			$js.="sModules['vide']['" . $id . "']='" . $valeur . "';" . NL;

		foreach ($this->fonctions as $clef => $module) {
			$js.="sModules['" . $clef . "']=new Array();" . NL;

			$properties = $module->getProperties();

			foreach ($properties as $id => $valeur)
				$js.="sModules['" . $clef . "']['" . $id . "']=\"" . $valeur . "\";" . NL;

		}

		$js.="}";

		return $js;

	}


	public function acceptVisitor($visitor) {
		foreach ($this->_data as $item) {
			$visitor->visitModule($item);

			if (!isset($item[self::MENUS_KEY]))
				continue;

			if (!is_array($item[self::MENUS_KEY]))
				continue;

			foreach ($item[self::MENUS_KEY] as $menu) {
				$visitor->visitMenu($menu);

				if (!isset($menu[self::SUBMENUS_KEY]))
					continue;

				if (!is_array($menu[self::SUBMENUS_KEY]))
					continue;

				foreach ($menu[self::SUBMENUS_KEY] as $subMenu) {
					$visitor->visitSubmenu($subMenu);

					if (!isset($subMenu[self::PREFERENCES_KEY]))
						continue;

					if (!is_array($subMenu[self::PREFERENCES_KEY]))
						continue;

					$visitor->visitSubmenuPref($subMenu[self::PREFERENCES_KEY]);

				}

			}

		}
	}

}