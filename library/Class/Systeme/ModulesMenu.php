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

	protected $_groupes = [
		"NAV" => "Navigation",
		"INFO" => "Informations",
		"RECH" => "Recherches",
		"CATALOG" => "Catalogues",
		"ABON" => "Abonnés"
	];

	private $fonctions = [
		"ACCUEIL" => ["libelle" => "Retour à l'accueil", "groupe" => "NAV", "phone" => true],
		"CONNECT" => ["libelle" => "Se connecter", "groupe" => "NAV", "phone" => false],
		"DISCONNECT" => ["libelle" => "Se déconnecter", "groupe" => "NAV", "phone" => true],
		"GOOGLEMAP" => ["libelle" => "Plan d'accès google", "groupe" => "NAV", "action" => "googlemap", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"AVIS" => ["libelle" => "Dernières critiques", "groupe" => "INFO", "action" => "avis", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"LAST_NEWS" => ["libelle" => "Derniers articles", "groupe" => "INFO", "action" => "lastnews", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"NEWS" => ["libelle" => "Articles cms", "groupe" => "INFO", "action" => "news", "popup_width" => 800, "popup_height" => 600, "phone" => false],
		"SITO" => ["libelle" => "Sitothèque", "groupe" => "INFO", "action" => "sitotheque", "popup_width" => 800, "popup_height" => 550, "phone" => false],
		"RSS" => ["libelle" => "Fils Rss", "groupe" => "INFO", "action" => "rss", "popup_width" => 800, "popup_height" => 550, "phone" => false],
		"URL" => ["libelle" => "Lien vers un site", "groupe" => "INFO", "action" => "liensite", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"PROFIL" => ["libelle" => "Lien vers un profil du portail", "groupe" => "INFO", "action" => "lienprofil", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"BIBNUM" => ["libelle" => "Lien vers un album", "groupe" => "INFO", "action" => "album", "popup_width" => 550, "popup_height" => 290, "phone" => false],
		"RECH_SIMPLE" => ["libelle" => "Recherche simple", "groupe" => "RECH", "phone" => true],
		"RECH_AVANCEE" => ["libelle" => "Recherche avancée", "groupe" => "RECH", "phone" => false],
		"RECH_GUIDEE" => ["libelle" => "Recherche guidée", "groupe" => "RECH", "phone" => false],
		"RECH_GEO" => ["libelle" => "Recherche géographique", "groupe" => "RECH", "phone" => false],
		"RECH_OAI" => ["libelle" => "Recherche OAI", "groupe" => "RECH", "phone" => false],
		"CATALOGUE" => ["libelle" => "Catalogue", "groupe" => "CATALOG", "action" => "catalogue", "popup_width" => 550, "popup_height" => 470, "phone" => false],
		"ETAGERE" => ["libelle" => "Etagères", "groupe" => "CATALOG", "action" => "etagere", "popup_width" => 550, "popup_height" => 200, "phone" => false],
		"TAGS" => ["libelle" => "Nuage de tags", "groupe" => "CATALOG", "phone" => false],
		"PANIER" => ["libelle" => "Paniers de notices", "groupe" => "ABON", "phone" => false],
		"ABON_AVIS" => ["libelle" => "Derniers avis", "groupe" => "ABON", "phone" => false],
		"ABON_FICHE" => ["libelle" => "Fiche abonné", "groupe" => "ABON", "phone" => false],
		"ABON_MODIF_FICHE" => ["libelle" => "Modifier données abonné", "groupe" => "ABON", "phone" => false],
		"ABON_PRETS" => ["libelle" => "Prêts en cours", "groupe" => "ABON", "phone" => false],
		"ABON_RESAS" => ["libelle" => "Réservations en cours", "groupe" => "ABON", "phone" => false],
		"ABON_FORMATIONS" => ["libelle" => "Formations", "groupe" => "ABON", "phone" => false],
		"FORM_CONTACT" => ["libelle" => "Formulaire de contact", "groupe" => "ABON", "phone" => false], 
		"VODECLIC" => ["libelle" => "Lien vers Vodeclic", "groupe" => "ABON", "phone" => false],
		"RESERVER_POSTE" => ["libelle" => "Réserver un poste multimédia", "groupe" => "ABON", "phone" => false],
		'SUGGESTION_ACHAT' => ['libelle' => 'Suggestion d\'achat', 'groupe' => 'ABON', 'phone' => false]
	 ];

	private $fonction_vide = ["action" => "index", "popup_width" => 550, "popup_height" => 215, "phone" => true];


	public function __construct() {
		if (!Class_AdminVar::isFormationEnabled())
			unset($this->fonctions['ABON_FORMATIONS']);

		if (!Class_AdminVar::isBibNumEnabled())
			unset($this->fonctions['BIBNUM']);

		if (!Class_AdminVar::isVodeclicEnabled())
			unset($this->fonctions['VODECLIC']);

		if (!Class_AdminVar::isMultimediaEnabled())
			unset($this->fonctions['RESERVER_POSTE']);
	}


	/**
	 * @param string $type
	 * @return array
	 */
	public function getFonction($type) {
		if (isset($this->fonctions[$type]))
				return $this->fonctions[$type];
		return $this->fonction_vide;
	}

	/**
	 * @param string $type
	 * @return array
	 */
	public function getValeursParDefaut($type) {
		switch ($type) {
			case "URL": return $this->getDefautUrl();
			case "PROFIL": return $this->getDefautProfil();
			case "GOOGLEMAP": return $this->getDefautGoogleMap();
			case "AVIS": return $this->getDefautAvis();
			case "NEWS": return $this->getDefautNews();
			case "LAST_NEWS": return $this->getDefautLastNews();
			case "SITO": return $this->getDefautSitotheque();
			case "RSS": return $this->getDefautRss();
			case "CATALOGUE": return $this->getDefautCatalogue();
			case "ETAGERE": return $this->getDefautEtagere();
			case "ALBUM": return $this->getDefautAlbum();
			default: return array();
		}
	}

	/**
	 * @param string $type
	 * @param array $preferences
	 * @return string
	 */
	public function getUrl($type, $preferences) {
		$target = '';

		if (!$preferences)
			$preferences = $this->getValeursParDefaut($type);

		$url = '';
		switch ($type) {
			case "MENU": $url = "#";
				break;
			case "CONNECT": $url = BASE_URL . "/auth/login/";
				break;
			case "DISCONNECT": $url = BASE_URL . "/auth/logout/";
				break;
			case "URL":
				$url = $preferences["url"];
				if ($preferences["target"] == 0)
					$target = "_blank";
				break;
			case "PROFIL":$url = BASE_URL . "/opac?id_profil=" . $preferences["clef_profil"];
				break;
			case "GOOGLEMAP":$url = BASE_URL . "/opac/bib/mapview?id_bib=" . $preferences["id_bib"] . "&retour=" . $_SERVER["HTTP_REFERER"];
				break;
			case "RECH_SIMPLE": $url = BASE_URL . "/recherche/simple?statut=reset";
				break;
			case "RECH_AVANCEE": $url = BASE_URL . "/recherche/avancee?statut=reset";
				break;
			case "RECH_GUIDEE": $url = BASE_URL . "/recherche/guidee?statut=reset";
				break;
			case "RECH_GEO": $url = BASE_URL . "/bib";
				break;
			case "RECH_OAI": $url = BASE_URL . "/rechercheoai?statut=saisie";
				break;

			case "PANIER":
				$url = BASE_URL . "/panier";
				$retour = null;
				if (array_isset("recherche", $_SESSION))
					$retour = $_SESSION["recherche"]["retour_liste"];

				if (!$retour or strpos($retour, "viewnotice") > 0)
					$_SESSION["recherche"]["retour_liste"] = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER["HTTP_REFERER"] : '';
				break;

			case "AVIS": $url = BASE_URL . "/blog/lastcritique/nb/" . $preferences["nb"];
				break;
			case "CATALOGUE":
				$url = BASE_URL . "/catalogue/appelmenu" . $this->getArgsPreferences($preferences) . "&reset=true";
				break;
			case "ETAGERE":
				$url = BASE_URL . "/etagere/appelmenu" . $this->getArgsPreferences($preferences) . "&reset=true";
				break;
			case "BIBNUM":
				$url = BASE_URL . "/bib-numerique/booklet/id/" . $preferences['album_id'];
				break;
			case "LAST_NEWS": $url = BASE_URL . "/cms/articleviewrecent/nb/" . $preferences["nb"];
				break;
			case "NEWS": // Pour l'instant 1 seul article et 1 seule categorie
				$url = BASE_URL . '/cms/articleviewpreferences?' . http_build_query($preferences);
				break;
			case "SITO": // Pour l'instant 1 seul site et pas de categorie
				if ($preferences["id_items"]) {
					$items = explode("-", $preferences["id_items"]);
					$url = BASE_URL . "/sito/sitoview/id/" . $items[0];
				}
				break;
			case "RSS": // Pour l'instant 1 seul flux et pas de categorie
				if ($preferences["id_items"]) {
					$items = explode("-", $preferences["id_items"]);
					$url = BASE_URL . "/rss/main/id_flux/" . $items[0];
				}
				break;
			case "ABON_AVIS": $url = BASE_URL . "/abonne/viewavis";
				break;
			case "ABON_FICHE": $url = BASE_URL . "/abonne/fiche";
				break;
			case "ABON_MODIF_FICHE": $url = BASE_URL . "/abonne/edit";
				break;
			case "ABON_PRETS": $url = BASE_URL . "/abonne/prets";
				break;
			case "ABON_RESAS": $url = BASE_URL . "/abonne/reservations";
				break;
			case "ABON_FORMATIONS": $url = BASE_URL . "/abonne/formations";
				break;
			case "FORM_CONTACT": $url = BASE_URL . "/index/formulairecontact";
				break;
			case "VODECLIC": 
				$url = BASE_URL . '/auth/login';
				$target = 0;
				if ($user = Class_Users::getLoader()->getIdentity()) {
					$url = $this->getVodeclicUrlForUser($user);
					$target = 1;
				}
				break;
			case "RESERVER_POSTE": 
				$url = BASE_URL . '/abonne/multimedia-hold-location';
				break;
		  case 'SUGGESTION_ACHAT':
			  $url = BASE_URL . '/abonne/suggestion-achat';
				break;
			default: $url = BASE_URL;
				break;
		}

		return array("url" => $url, "target" => $target);
	}


	public function getVodeclicUrlForUser($user) {
		if ($user->isAbonne() && $user->isAbonnementValid()) 
			return Class_VodeclicLink::forUser($user)->url();
	
		return 'javascript:alert(\\\'Votre abonnement est terminé\\\')';
	}


	/**
	 * @return array
	 */
	private function getDefautUrl() {
		return array(
			'target' => 1, // Ouvrir dans un nouvel onglet ou pas
			'url' => 'http://google.fr',
		);
	}


	/**
	 * @return array
	 */
	private function getDefautProfil() {
		return array('clef_profil' => '1');// Par defaut profil portail
	}

	/**
	 * @return array
	 */
	private function getDefautGoogleMap() {
		return array('id_bib' => '1'); // Par defaut bibliothèque n°1
	}

	/**
	 * @return array
	 */
	private function getDefautAvis() {
		return array('nb' => '10');// Nombres d'avis à afficher
	}

	/**
	 * @return array
	 */
	private function getDefautNews() {
		return array(
			'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
			'id_items' => '', // Liste d'id_news séparés par des tirets
			'nb_aff' => '5', // Nombres d'articles à afficher
			'nb_analyse' => '10', // Nombres d'articles à analyser
			'display_order' => 'Selection',
		);
	}

	/**
	 * @return array
	 */
	private function getDefautLastNews() {
		return array('nb' => '5'); // Nombres d'articles à afficher
	}

	/**
	 * @return array
	 */
	private function getDefautRss() {
		return array(
			'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
			'id_items' => '', // Liste d'id_rss séparés par des tirets
			'nb' => '10', // Nombres de flux à afficher
		);
	}

	/**
	 * @return array
	 */
	private function getDefautSitotheque() {
		return array(
			'id_categorie' => '', // Liste d'id_categorie séparés par des tirets
			'id_items' => '', // Liste d'id_sito séparés par des tirets
			'nb' => '10', // Nombres de flux à afficher
		);
	}

	/**
	 * @return array
	 */
	private function getDefautCatalogue() {
		return array(
			'titre' => 'Catalogue', // Titre de la boite
			'nb_notices' => 20, // Nombre de notices a afficher
			'aleatoire' => 1,	// 1=tirage aleatoire
			'tri' => 1, // 0=alpha,1=par date de creation,2=les plus consultées
			'nb_analyse' => 50, // nbre a analyser pour le mode aleatoire
		);
	}

	/**
	 * @return array
	 */
	private function getDefautEtagere() {
		return array('titre' => 'Etagère'); // Titre de la boite
	}

	/**
	 * @return array
	 */
	private function getDefautAlbum() {
		return array('titre' => 'Album photos'); // Titre de la boite
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

			foreach ($this->fonctions as $id_fonction => $fonction) {
				if ($fonction["groupe"] != $id_groupe)
					continue;

				if ($browser == "telephone" and $fonction["phone"] == false)
					continue;

				if ($item_selected == $id_fonction)
					$selected = " selected"; else
					$selected="";

				$combo.='<option style="color:#575757" value="' . $id_fonction . '"' . $selected . '>' . stripSlashes($fonction["libelle"]) . '</option>';

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

		foreach ($this->fonction_vide as $id => $valeur)
			$js.="sModules['vide']['" . $id . "']='" . $valeur . "';" . NL;

		foreach ($this->fonctions as $clef => $fonction) {
			$js.="sModules['" . $clef . "']=new Array();" . NL;

			foreach ($fonction as $id => $valeur)
				$js.="sModules['" . $clef . "']['" . $id . "']=\"" . $valeur . "\";" . NL;

		}

		$js.="}";

		return $js;

	}

	/**
	 * @param array $preferences
	 * @return string
	 */
	private function getArgsPreferences($preferences) {
		$args = "";
		if (!$preferences)
			return false;
		foreach ($preferences as $clef => $valeur) {
			if ($args)
				$args.="&";
			$args.=$clef . "=" . urlencode($valeur);
		}
		return "?" . $args;
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