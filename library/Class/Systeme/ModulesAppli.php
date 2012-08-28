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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC 3 : Liste des modules gérés par les controllers
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Class_Systeme_ModulesAppli extends Class_Systeme_ModulesAbstract {
	const ONGLETS_KEY			= 'onglets';

	/**
	 * @var array
	 */
	protected $_groupes = array (
		"INFO" => "Modules d'informations",
		"RECH" => "Modules de recherches",
		"SITE" => "Modules niveau site"
	);

	/**
	 * @var array
	 */
	private $modules = array(
		"index" => array(
			"formulairecontact" => array("libelle" => "Contact", "popup_width" => 500, "popup_height" => 300)
  	 ),

		"abonne" => array(
			"*" => array("libelle" => "Abonne", "popup_width" => 500, "popup_height" => 300),
			"formations" => array("libelle" => "Contact", "popup_width" => 500, "popup_height" => 300)
  	 ),

		"auth" => array(
			"*" => array("libelle" => "Connexion", "popup_width" => 500, "popup_height" => 300),
			"login" => array("libelle" => "Connexion utilisateur", "popup_width" => 500, "popup_height" => 300),
			"register" => array("libelle" => "Demande d'inscription", "popup_width" => 710, "popup_height" => 290),
			"lostpass" => array("libelle" => "Mot de passe oublié", "popup_width" => 710, "popup_height" => 290)
		),

		"recherche" => array(
			"*" => array("libelle" => "Recherche", "popup_width" => 500, "popup_height" => 300),
			"simple" => array("libelle" => "Recherche simple", "popup_width" => 710, "popup_height" => 340),
			"avancee" => array("libelle" => "Recherche avancee", "popup_width" => 710, "popup_height" => 290),
			"guidee" => array("libelle" => "Recherche guidée", "popup_width" => 710, "popup_height" => 290),
			"resultat" => array("libelle" => "Résultat", "popup_width" => 710, "popup_height" => 620),
			"viewnotice" => array("libelle" => "Notice", "popup_width" => 700, "popup_height" => 720)),

		"noticeajax" => array(
			"*" => array("libelle" => "Notice", "popup_width" => 500, "popup_height" => 300),
			"notice" => array("libelle" => "Notice", "popup_width" => 600, "popup_height" => 620)
		),

		"cms" => array(
			"*" => array("libelle" => "Articles", "popup_width" => 500, "popup_height" => 200)
		),

		"rss" => array(
			"*" => array("libelle" => "Fils Rss", "popup_width" => 500, "popup_height" => 200)
		),

		"sito" => array(
			"*" => array("libelle" => "Sitothèque", "popup_width" => 500, "popup_height" => 200)
		),

		"blog" => array(
			"*" => array("libelle" => "Avis", "popup_width" => 500, "popup_height" => 200)
		),

		"bib" => array(
			"*" => array("libelle" => "Recherche géographique", "popup_width" => 500, "popup_height" => 260),
			"zoneview" => array("libelle" => "Zone", "popup_width" => 500, "popup_height" => 260),
			"mapzoneview" => array("libelle" => "Réseau", "popup_width" => 500, "popup_height" => 260),
			"bibview" => array("libelle" => "Bibliothèque", "popup_width" => 500, "popup_height" => 260)
		),

		"catalogue" => array(
			"*" => array("libelle" => "Catalogues", "popup_width" => 710, "popup_height" => 620)
		)
	);

	/**
	 * @param string $type_module
	 * @param string $action
	 * @return array | false
	 */
	public function getModule($type_module=false, $action=false) {
		if (false !== $type_module) {
			$type_module = (string)$type_module;

			if (!array_key_exists($type_module, $this->modules))
				return false;

			if (
				(false === $action)
				|| (!array_key_exists((string)$action, $this->modules[$type_module]))
			) {
				$action = '*';
			}

			if (!isset($this->modules[$type_module][$action]))
				return [];
			return $this->modules[$type_module][$action];
		}

		return $this->modules;

	}

	/**
	 * @param string $type_module
	 * @param string $action
	 * @return string
	 */
	public function getLibelleModule($type_module, $action) {
		if (array_isset($action, $this->modules[$type_module]))
			return $this->modules[$type_module][$action]["libelle"];

		return $this->modules[$type_module]["*"]["libelle"];

	}

	/**
	 * @param string $type
	 * @param string $action
	 * @return array
	 */
	public function getValeursParDefaut($type, $action) {
		switch ((string)$type) {
			case "auth": $valeurs = $this->getDefautAuth($action);
				break;
			case "recherche": $valeurs = $this->getDefautRecherche($action);
				break;
			case "noticeajax": $valeurs = $this->getDefautRecherche("viewnotice");
				break;
			case "cms": $valeurs = $this->getDefautCms($action);
				break;
			case "rss": $valeurs = $this->getDefautRss($action);
				break;
			case "sito": $valeurs = $this->getDefautSito($action);
				break;
			case "blog": $valeurs = $this->getDefautBlog($action);
				break;
			case "catalogue": $valeurs = $this->getDefautRecherche("resultat");
				break;
			case "bib": $valeurs = $this->getDefautBib($action);
				break;
			default : $valeurs = array();
		}

		if (!array_key_exists('boite', $valeurs))
			$valeurs['boite'] = null;

		if (!array_key_exists('barre_nav', $valeurs))
			$valeurs['barre_nav'] = '';

		return $valeurs;

	}

	/**
	 * @param type $action
	 * @return int
	 */
	private function getDefautAuth($action) {
		$ret = array();

		switch ((string)$action) {
			case "login":
				$ret["barre_nav"] = "Connexion";						// Barre de nav
				$ret["titre"] = "Entrez votre identité S.V.P.";		 // Titre de la boite
				$ret["largeur_form"] = 400;							// Largeur du formulaire de saisie
				break;
			case "register":
				$ret["barre_nav"] = "S'inscrire";						// Barre de nav
				$ret["titre"] = "Demande d'inscription";				// Titre de la boite
				$ret["largeur_form"] = 400;							// Largeur du formulaire de saisie
				break;
			case "lostpass":
				$ret["barre_nav"] = "demande de mot de passe";		 // Barre de nav
				$ret["titre"] = "Mot de passe oublié";				// Titre de la boite
				$ret["largeur_form"] = 400;							// Largeur du formulaire de saisie
				break;
		}

		return $ret;

	}

	/**
	 * @param type string
	 * @return array
	 */
	private function getDefautRecherche($action) {
		$ret = array();

		switch ((string)$action) {
			case "simple":
				$ret["barre_nav"] = "Recherche simple";							 // Barre de nav
				$ret["titre"] = "Rechercher un livre, un disque, une vidéo";	 // Titre de la boite
				$ret["select_bib"] = 1;											// Afficher le lien de sélection des bibliothèques
				$ret["message"] = "Dans les bibliothèques du Haut-Rhin";		 // Message au dessus du champ de saisie
				$ret["exemple"] = "Exemple:chanson française";					// Message exemple sous le champ de saisie
				break;
			case "avancee":
				$ret["barre_nav"] = "Recherche avancée";					// Barre de nav
				$ret["titre"] = "Recherche avancée";					 // Titre de la boite
				$ret["select_bib"] = 1;									// Afficher le lien de sélection des bibliothèques
				$ret["liste_nb_par_page"] = 10;													 // Nombre de notices par page
				$ret["liste_codes"] = 'TAE';															// Champs à afficher
				break;
			case "guidee":
				$ret["barre_nav"] = "Recherche guidée";					 // Barre de nav
				$ret["titre"] = "Recherche guidée";						// Titre de la boite
				$ret["select_bib"] = 1;									// Afficher le lien de sélection des bibliothèques
				break;
			case "resultat":
				$ret["barre_nav"] = "Résultat";							 // Barre de nav
				$ret["liste_nb_par_page"] = 10;							 // Nombre de notices par page
				$ret["liste_format"] = 1;									// Format de liste (1=liste 2=accordéon 3=vignettes 4=bookflip)
				$ret["liste_codes"] = "TAN";								// Champs a afficher dans la liste (TANECR)
				$ret["facettes_actif"] = 1;									 // Afficher les facettes
				$ret["facettes_codes"] = "ADPML";							// Types de facettes à afficher
				$ret["facettes_nombre"] = 3;								 // Nombre de facettes à afficher pour 1 rubrique
				$ret["facettes_message"] = "Affiner le résultat...";		 // Message au dessus de la boite
				$ret["tags_actif"] = 1;									 // Afficher les tags de liste
				$ret["tags_codes"] = "AMDPZ";								// Types de tags a afficher
				$ret["tags_nombre"] = 30;									// Nombre de tags a afficher
				$ret["tags_calcul"] = 3;									// Méthode de calcul pour les tranches (0=répartition 1=ecart/moyenne 2=ecart/moyenne pondÃ©rÃ©)
				$ret["tags_position"] = 2;								 // Position 2=sous les facettes 1=sous la liste
				$ret["tags_message"] = "Elargir la recherche...";			// Message au dessus de la boite
				break;
			case "viewnotice":
				$ret["barre_nav"] = "Notice";						// Barre de nav
				$ret["entete"] = "ECN";							 // Champs a afficher dans l'entete
				$onglets = Class_Codification::getNomOnglet("");	// On prend tous les onglets dispo
				$ordre = 0;
				foreach ($onglets as $key => $valeur) {
					$ret["onglets"][$key]["titre"] = "";			 // Prend le nom de l'onglet par défaut
					$ret["onglets"][$key]["aff"] = 2;				 // Mode d'affichage 0=aucun 1=bloc déplié 2=bloc fermé 3=dans 1 onglet
					$ret["onglets"][$key]["ordre"] = ++$ordre;		// Ordre d'affichage
					$ret["onglets"][$key]["largeur"] = 0;			 // Largeur de l'onglet 0=répartition auto en pourcentage
				}
				break;
		}

		return $ret;

	}

	/**
	 * @param string $action
	 * @return array
	 */
	private function getDefautCms($action) {
		$ret = array();

		switch ((string)$action) {
			case "articleviewbydate" : $ret["barre_nav"] = "Calendrier";
				break;
			default: $ret["barre_nav"] = "Article";
				break;
		}

		return $ret;

	}

	/**
	 * @param string $action
	 * @return array
	 */
	private function getDefautRss($action) {
		return array('barre_nav' => 'Fil rss');
	}

	/**
	 * @param string $action
	 * @return array
	 */
	private function getDefautSito($action) {
		return array('barre_nav' => 'Sitothèque');
	}

	/**
	 * @param type $action
	 * @return array
	 */
	private function getDefautBlog($action) {
		return array('barre_nav' => 'Critique');
	}

	/**
	 * @param string $action
	 * @return array
	 */
	private function getDefautBib($action) {
		$ret = array();
		$ret["hide_news"] = 0;

		switch ((string)$action) {
		case 'zoneview': 
			$ret["barre_nav"] = "Zone";
			break;
		case 'bibview': 
			$ret["barre_nav"] = "Bibliothèque";
			break;
		default: 
			$ret["barre_nav"] = "Recherche géographique";
			break;
		}

		return $ret;

	}

	public function acceptVisitor($visitor) {
		foreach ($this->_data as $actions) {
			foreach ($actions as $params) {
				$visitor->visitAction($params);
				foreach ($params as $k => $v) {
					if ((self::ONGLETS_KEY == $k) && is_array($v)) {
						foreach ($v as $onglet) {
							$visitor->visitOnglet($onglet);
						}
					}
				}
			}
		}


	}


}