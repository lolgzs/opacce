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

	const GROUP_MENU_NAVIGATION = 'MENU_NAV';
	const GROUP_MENU_INFORMATIONS = 'MENU_INFO';
	const GROUP_MENU_RECHERCHES = 'MENU_RECH';
	const GROUP_MENU_CATALOGUES = 'MENU_CATALOG';
	const GROUP_MENU_ABONNES = 'MENU_ABONNE';

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
			"ACCUEIL" => new Class_Systeme_ModulesMenu_Accueil(),
			"CONNECT" => new Class_Systeme_ModulesMenu_Connect(),
			"DISCONNECT" => new Class_Systeme_ModulesMenu_Disconnect(),
			"GOOGLEMAP" => new Class_Systeme_ModulesMenu_GoogleMap(),
			"AVIS" => new Class_Systeme_ModulesMenu_Avis(),
			"LAST_NEWS" => new Class_Systeme_ModulesMenu_LastNews(),
			"NEWS" => new Class_Systeme_ModulesMenu_News(),
			"SITO" => new Class_Systeme_ModulesMenu_Sitotheque(),
			"RSS" => new Class_Systeme_ModulesMenu_Rss(),
			"URL" => new Class_Systeme_ModulesMenu_Url(),
			"PROFIL" => new Class_Systeme_ModulesMenu_Profil(),
			"BIBNUM" => new Class_Systeme_ModulesMenu_BibliothequeNumerique(),
			"RECH_SIMPLE" => new Class_Systeme_ModulesMenu_RechercheSimple(),
			"RECH_AVANCEE" => new Class_Systeme_ModulesMenu_RechercheAvancee(),
			"RECH_GUIDEE" => new Class_Systeme_ModulesMenu_RechercheGuidee(),
			"RECH_GEO" => new Class_Systeme_ModulesMenu_RechercheGeographique(),
			"RECH_OAI" => new Class_Systeme_ModulesMenu_RechercheOai(),
			"CATALOGUE" => new Class_Systeme_ModulesMenu_Catalogue(),
			"ETAGERE" => new Class_Systeme_ModulesMenu_Etagere(),
			"TAGS" => new Class_Systeme_ModulesMenu_Tags(),
			"PANIER" => new Class_Systeme_ModulesMenu_Paniers(),
			"ABON_AVIS" => new Class_Systeme_ModulesMenu_Avis(),
			"ABON_FICHE" => new Class_Systeme_ModulesMenu_AbonneFiche(),
			"ABON_MODIF_FICHE" => new Class_Systeme_ModulesMenu_AbonneModificationFiche(),
			"ABON_PRETS" => new Class_Systeme_ModulesMenu_AbonnePrets(),
			"ABON_RESAS" => new Class_Systeme_ModulesMenu_AbonneReservations(),
			"ABON_FORMATIONS" => new Class_Systeme_ModulesMenu_AbonneFormations(),
			"FORM_CONTACT" => new Class_Systeme_ModulesMenu_FormulaireContact(), 
			"VODECLIC" => new Class_Systeme_ModulesMenu_Vodeclic(),
			"RESERVER_POSTE" => new Class_Systeme_ModulesMenu_ReserverPoste(),
			'SUGGESTION_ACHAT' => new Class_Systeme_ModulesMenu_SuggestionAchat()
	 ];

		$modules_accueil = Class_Systeme_ModulesAccueil::getModules();
		
		//		$this->fonctions = array_merge($this->fonctions,Class_Systeme_ModulesAccueil::getModules());
	
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