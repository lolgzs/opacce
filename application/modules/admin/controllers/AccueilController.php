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
class Admin_AccueilController extends Zend_Controller_Action {
	private $id_profil;										// Profil a modifier
	private $path_templates;							// Templates si on vient de l'admin
	private $config;											// Qui a appelé la config : "admin" ou "accueil"
	private $id_module;										// Identifiant unique du module a traiter
	private $type_module;									// Identifiant du module à traiter

	/** @var Class_Systeme_ModulesAccueil */
	private $_systemeModulesAccueil;


	public function init() {
		// Changer le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
	 	$viewRenderer->setLayoutScript('subModal.phtml');

		// Recup des parametres
		$this->id_module = $this->_request->getParam("id_module");
		$this->id_profil = $this->_request->getParam("id_profil");
		$this->config = $this->_request->getParam("config");
		$this->type_module = $this->_request->getParam("type_module");

		// On initalise les proprietes
		if (!$this->profil = Class_Profil::getLoader()->find($this->id_profil))
			$this->profil = Class_Profil::getCurrentProfil();

		$user = Class_Users::getIdentity();

		if (($user->getRoleLevel() < ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB) 
				|| ($user->isAdminBib() && ($user->getIdSite() !== $this->profil->getIdSite()))) {
			 $this->_redirect('admin/index');
			 return;
		}



		if ($this->config == "admin")
			$this->preferences = $this->_extractProperties();

		if ($this->config == "accueil")
			$this->preferences = $this->profil->getOrCreateConfigAccueil($this->id_module,
																																	 $this->type_module);

		$boite = isset($this->preferences["boite"]) ? $this->preferences["boite"] : '';
		$this->view->preferences = $this->preferences;
		$this->view->url = $this->_request->getRequestUri();
		
		$this->view->combo_templates = ZendAfi_View_Helper_Accueil_Base::getComboTemplates($boite,
																																											 $this->profil->getPathTemplates());
		$this->view->id_profil = $this->profil->getId();
		$this->view->id_bib = $this->profil->getIdSite();

		$this->_systemeModulesAccueil = new Class_Systeme_ModulesAccueil();
	}

	public function preDispatch(){
		Zend_Layout::startMvc(array());
	}


	public function calendrierAction()	{
		$this->_simpleAction();
	}


	public function menuverticalAction()	{
		$this->_simpleAction();
	}


	public function rechguideeAction() {
		$this->_simpleAction();
	}


	public function rechsimpleAction() {
		$this->_simpleAction();
	}


	public function cartezonesAction() {
		$this->_simpleAction();
	}


	public function sitothequeAction() {
		$this->_simpleAction();
	}


	public function newsAction() {
		if (1 == $this->_getParam('styles_reload')) {
			$this->view->preferences = $this->_request->getPost();
		} else {
			$this->_simpleAction();			
		}
	}


	public function rssAction() {
		$this->_simpleAction();
	}


	public function langueAction() {
		$this->_simpleAction();
	}


	public function loginAction() {
		$this->_simpleAction();
	}


	public function compteursAction() {
		$this->_simpleAction();
	}

	public function pretsAction() {
		$this->_simpleAction();
	}

	public function bibliothequeNumeriqueAction() {
		if (1 == $this->_getParam('styles_reload')) {
			$this->view->preferences = $this->_request->getPost();
		} else {
			$this->_simpleAction();			
		}

		$this->view->categories = Class_AlbumCategorie::getLoader()->getCollections();
		$moduleBibNumerique = $this->_systemeModulesAccueil->getModuleByCode('BIB_NUMERIQUE');
		$this->view->displayModes = $moduleBibNumerique->getDisplayModes();
		$this->view->orderModes = $moduleBibNumerique->getOrderModes();

		$this->view->albums = Class_AlbumCategorie::getLoader()->findAlbumsRecursively();
	}


	public function tagsAction() {
		// Retour du formulaire
		if ($this->_request->isPost()) {
			$enreg = $this->_request->getPost();
			$enreg['nombre'] = (int)$this->_getParam('nombre', 10);
			$enreg['limite'] = (int)$this->_getParam('limite', 1000);
			$enreg['type_tags'] = $enreg['type_tags_codes'];
			$this->_updateEtRetour($enreg);
		} else {
			$this->view->catalogues = Class_Catalogue::getCataloguesForCombo();
		}
	}


	public function critiquesAction() {
		if ($this->_request->isPost()) 	{
			$enreg = $this->_request->getPost();
			if ($enreg["id_panier"]) {
				$user = ZendAfi_Auth::getInstance()->getIdentity();
				$enreg["id_catalogue"] = 0;
				$enreg["id_user"] = $user->ID_USER;

			} else {
				$enreg["id_user"] = 0;
			}

			$this->_updateEtRetour($enreg);
		}

		$this->view->catalogues = Class_Catalogue::getCataloguesForCombo();
		$this->view->paniers = Class_PanierNotice::getPaniersForCombo();
	}


	public function catalogueAction() {
		if ($this->_request->isPost()) {
			extract($_POST);
			$nb_requete = intval($nb_requete);
			if (!$nb_requete)
				$nb_requete = 200;

			$nb_aff = intval($nb_aff);
			if (!$nb_aff)
				$nb_aff = 10;

			if ($ordre == "1")
				$nb_requete = $nb_aff; // si ordre strict on ne lit pas plus de notices qu'on en affiche

			$enreg["message"] = $message;
			$enreg["notices"] = $notices;
			$enreg["format"] = $format;
			$enreg["ordre"] = $ordre;
			$enreg["type_doc"] = $type_doc;
			$enreg["section"] = $section;
			$enreg["genre"] = $genre;
			$enreg["dewey"] = $dewey;
			$enreg["pcdm4"] = $pcdm4;
			$enreg["matiere"] = $matiere;
			$enreg["nb_requete"] = $nb_requete;
			$enreg["nb_aff"] = $nb_aff;

			$this->_updateEtRetour($enreg);
		}
	}


	public function kiosqueAction() {
		if ($this->_request->isPost()) {
			if (1 == $this->_getParam('styles_reload')) {
				$this->view->preferences = $this->_request->getPost();

			} else {
				$enreg = $this->_request->getPost();
				if (array_key_exists('styles_reload', $enreg)) {
					unset($enreg["styles_reload"]);
				}

				$enreg["nb_notices"] = (1 < (int)$enreg["nb_notices"]) ?
					(int)$enreg["nb_notices"]
					: 1;

				$enreg["nb_analyse"] = (int)$enreg["nb_analyse"];
				if ($enreg["nb_analyse"] < $enreg["nb_notices"])
					$enreg["nb_analyse"] = $enreg["nb_notices"] + 10;

				if ($enreg["id_panier"]) {
					$user = ZendAfi_Auth::getInstance()->getIdentity();
					$enreg["id_catalogue"] = 0;
					$enreg["id_user"] = $user->ID_USER;

				}	else {
					$enreg["id_user"] = 0;
				}

				$this->_updateEtRetour($enreg);
			}
		}

		$this->view->styles_liste = $this->_getStylesListes();
		$this->view->catalogues = Class_Catalogue::getCataloguesForCombo();
		$this->view->paniers = Class_PanierNotice::getPaniersForCombo();
	}


	public function conteneur2colonnesAction()	{
		if ($this->_request->isPost()) {
			$enreg = $this->_request->getPost();

			// On crée les modules si première définition ou changement de type
			foreach (array('gauche', 'droite') as $colonne)
				$this->_conteneur2colonnes_createModules($enreg, $colonne);

			$this->_updateEtRetour($enreg);
			return;
		}

		$modules_accueil = Class_Systeme_ModulesAccueil::getModules();
		$modules = array();
		foreach ($modules_accueil as $key => $module)
			$modules[$key] = $module->getLibelle();

		$this->view->modules = $modules;
	}


	private function _simpleAction()
	{
		// pour combo des annexes
		$this->view->ya_annexes=fetchAll("select count(*) from codif_annexe where invisible=0 order by libelle");
		if ($this->_request->isPost()) $this->_updateEtRetour($this->_request->getPost());
	}


	/**
	 * @param array $datas
	 * @return string
	 */
	private function _compactProperties($datas) {
		$properties = array();
		foreach ($datas as $k => $v) {
			$properties[] = $k . '=' . $v;
		}

		return implode('/', $properties);
	}


	/** @return array */
	private function _extractProperties() {

		if (null != ($props = $this->_getParam("proprietes"))) {
			$props = explode('/', $props);
			foreach ($props as $prop) {
				$pos = strpos($prop, '=');
				$clef = substr($prop, 0, $pos);
				$valeur = substr($prop, ($pos + 1));
				$properties[$clef] = $valeur;
			}
		}	else {
			$cls = new Class_Systeme_ModulesAccueil();
			$properties = $cls->getValeursParDefaut($this->type_module);
		}

		return $properties;
	}


	/**
	 * @param array $data
	 */
	private function _updateEtRetour($data) {
		$enreg = array();

		// Filtrage des données
		foreach ($data as $clef => $valeur)
			$enreg[$clef] = addslashes($valeur);

		// Si on venait de la config admin
		if ($this->config == "admin") {
			$this->view->id_module = $this->id_module;
			$this->view->properties = $this->_compactProperties($enreg);
		}

		// Si on venait de l'interface du site
		else	{
			$module_config = $this->profil->getModuleAccueilConfig($this->id_module);
			$module_config['preferences'] = $enreg;
			$this->profil
				->updateModuleConfigAccueil($this->id_module, $module_config)
				->save();

			$this->view->reload = 'SITE';
		}

		// Execute le script de retour
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('accueil/_retour.phtml');
	}


	/** @return array */
	private function _getStylesListes() {
		// Styles java
		$styles["java"]["slide_show"] = "Barre d'images horizontale";
		$styles["java"]["protoflow"] = "Cover flow";
		$styles["java"]["cube"] = "Cube";
		$styles["java"]["diaporama"] = "Diaporama";
		$styles["java"]["jcarousel"] = "Barre horizontale animée";
		$styles["java"]["mycarousel_horizontal"] = "Barre horizontale pleine largeur";
		$styles["java"]["mycarousel_vertical"] = "Barre verticale";

		// Styles flash
		$styles["flash"]["coverflow"] = "Cover flow";
		$styles["flash"]["carrousel_horizontal"] = "Carrousel horizontal";
		$styles["flash"]["carrousel_vertical"] = "Carrousel vertical";
		$styles["flash"]["dockmenu_horizontal"] = "Barre d'images horizontale";
		$styles["flash"]["dockmenu_vertical"] = "Barre d'images verticale";
		$styles["flash"]["pyramid_gallery"] = "Mur d'images";

		return $styles;
	}


	private function _conteneur2colonnes_createModules(&$enreg, $colonne) {
		$id_key = 'col_' . $colonne . '_module_id';
		$type_key = 'col_' . $colonne . '_type';
		$type_module = $enreg[$type_key];

		$id_module = null;
		if (array_key_exists($id_key, $this->preferences))
				$id_module = $this->preferences[$id_key];

		$enreg[$id_key] = $id_module; // L'id module n'est pas dans le post.

		if ($this->profil->getModuleAccueilConfig($id_module) == null)
			$id_module = null;

		// Si le module existe et du même type, pas besoin de le créer
		if ($id_module and ($type_module == $this->preferences[$type_key]))
			return;

		// Si c'est la première fois qu'on définie un module pour cette boîte 2 colonnes,
		// on créé son id, sinon on réutilise l'existant
		if (!$id_module) {
			$id_module = $this->profil->createNewModuleAccueilId(1000);
			$enreg[$id_key] = $id_module;
		}

		$modules_accueil = new Class_Systeme_ModulesAccueil();
		$preferences = $modules_accueil->getValeursParDefaut($type_module);

		$preferences['conteneur_deux_colonnes_id'] = $this->id_module;
		$config = array("preferences" => $preferences,
										"type_module" => $type_module);

		$this->profil->updateModuleConfigAccueil($id_module, $config);
	}
}