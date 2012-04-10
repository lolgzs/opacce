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
// OPAC3 - Propriétés des modules gérés par les controllers
//
//////////////////////////////////////////////////////////////////////////////////////////
class Admin_ModulesController extends Zend_Controller_Action
{
	private $id_profil;										// Profil a modifier
	private $path_templates;							// Templates si on vient de l'admin
	private $config;											// Qui a appelé la config : "admin" ou "site"
	private $type_module;									// Identifiant du module à traiter
	private $action;											// Identifiant de l'action du module à traiter
	private $action2;											// Action secondaire du module à traiter

//------------------------------------------------------------------------------------------------------
// Initialisation des parametres et du layout
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Changer le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');

		// Recup des parametres
		$this->id_profil = $this->_request->getParam('id_profil');
		$this->config = $this->_request->getParam("config");
		$this->type_module = $this->_request->getParam("type_module");
		$this->action = $this->_request->getParam("action1");
		$this->action2 = $this->_request->getParam("action2");

		// On initalise les proprietes
		$profil= Class_Profil::getLoader()->find($this->id_profil);
		$cls_module=new Class_Systeme_ModulesAppli();
		$this->path_templates=$profil->getPathTemplates();

		$preferences = $profil->getCfgModulesPreferences($this->type_module, $this->action, $this->action2);

		// Variables de vue
		$this->view->titre_module=$cls_module->getLibelleModule($this->type_module,$this->action);
		$this->view->preferences = $preferences;
		$this->view->url=$this->_request->getRequestUri();
		$this->view->combo_templates = ZendAfi_View_Helper_Accueil_Base::getComboTemplates($preferences["boite"], $this->path_templates);
		$this->view->id_profil=$this->id_profil;
		$this->view->id_bib = $profil->getIdSite();
		$this->view->action = $this->type_module.'_'.$this->action;
	}

	function preDispatch(){
		Zend_Layout::startMvc();
	}

//------------------------------------------------------------------------------------------------------
// Si pas d'action demandee c'est une erreur
//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/index_all.phtml');
	}


	function abonneAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/abonne_all.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Proprietés auth (login)
//------------------------------------------------------------------------------------------------------
	function authAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/auth_'.$this->action.'.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Proprietés cms
//------------------------------------------------------------------------------------------------------
	function cmsAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/cms_all.phtml');
	}


//------------------------------------------------------------------------------------------------------
// Proprietés rss
//------------------------------------------------------------------------------------------------------
	function rssAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/rss_all.phtml');
	}


	function bibAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/bib_all.phtml');
	}

	//------------------------------------------------------------------------------------------------------
	// Proprietés rss
	//------------------------------------------------------------------------------------------------------
	function sitoAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/sito_all.phtml');
	}

	//------------------------------------------------------------------------------------------------------
	// Proprietés blog (avis et critiques)
	//------------------------------------------------------------------------------------------------------
	function blogAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/blog_all.phtml');
	}

	//------------------------------------------------------------------------------------------------------
	// Catalogues
	//------------------------------------------------------------------------------------------------------
	function catalogueAction()
	{
		$this->view->titre="Catalogues";
		if ($this->_request->isPost())
		{
			$ret=$this->getPostListe();
			if($ret != "ok") $this->retourErreur($ret);
			else
			{
				$enreg=$_POST;
				$this->updateEtRetour($enreg);
			}
		}
		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/recherche_resultat.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Proprietés recherche
//------------------------------------------------------------------------------------------------------
	function rechercheAction()
	{
		if($this->action=="viewnotice")
			$this->view->type_doc=$this->action2;
		$this->view->titre="Recherche ".$_SESSION["recherche"]["mode"];

		// Retour du formulaire
		if($this->_request->isPost())
		{
			// Controles de saisie ->resultat
			if($this->action=="resultat")
			{
				$ret=$this->getPostListe();
				if($ret != "ok") $erreur=$ret;
			}
			// Controles de saisie ->viewnotice
			elseif($this->action=="viewnotice") $enreg=$this->getPostNotice();

			if($erreur) $this->retourErreur($erreur);
			else
			{
				if(!$enreg) $enreg=$_POST;
				if($this->action=="resultat") unset($_SESSION["recherche"]["resultat"]);
				$this->updateEtRetour($enreg);
			}
		}

		// viewnotice -> Consolidation des onglets (si de nouveaux ont ete ajoutes)
		if($this->action=="viewnotice") $this->noticeConsolidationOnglets();

		// Redirection sur le bon formulaire
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/recherche_'.$this->action.'.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Controle de saisie pour la liste résultat de recherches et catalogues
//------------------------------------------------------------------------------------------------------
	private function getPostListe()
	{
		extract($_POST);
		$liste_nb_par_page=(int)$liste_nb_par_page;
		$facettes_nombre=(int)$facettes_nombre;
		$tags_nombre=(int)$tags_nombre;

		if($liste_nb_par_page < 3 or $liste_nb_par_page > 50) $erreur="Le nombre de notices par page doit être compris entre 3 et 50";
		elseif(!trim($liste_codes)) $erreur="Indiquez au moins 1 champ à afficher pour la liste";
		elseif($facettes_actif == 1 and $facettes_nombre < 2 or $facettes_nombre > 10) $erreur="Le nombre de facettes doit être compris entre 2 et 10";
		elseif($facettes_actif == 1 and !trim($facettes_codes)) $erreur="Indiquez au moins 1 facette à afficher";
		elseif($tags_actif == 1 and $tags_nombre < 5 or $tags_nombre > 1000) $erreur="Le nombre de tags doit être compris entre 5 et 1000";
		elseif($tags_actif == 1 and !trim($tags_codes)) $erreur="Indiquez au moins 1 type de tag à afficher";

		if($erreur) return $erreur;
		else return "ok";
	}


//------------------------------------------------------------------------------------------------------
// Proprietés notice ajax
//------------------------------------------------------------------------------------------------------
	function noticeajaxAction()	{
		$this->view->type_doc=$this->action2;
		$this->noticeConsolidationOnglets();
		// Retour du formulaire
		if($this->_request->isPost()) {
			$enreg=$this->getPostNotice();
			$this->updateEtRetour($enreg);
		}
		// On utilise le meme formulaire que pour recherche/viewnotice
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/recherche_viewnotice.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Proprietés des notices
//------------------------------------------------------------------------------------------------------
	private function noticeConsolidationOnglets()
	{
		if (!array_key_exists("onglets", $this->view->preferences))
			$this->view->preferences["onglets"] = array();

		$onglets=Class_Codification::getNomOnglet("");
		foreach($onglets as $key => $valeur) {
			if(!array_key_exists($key, $this->view->preferences["onglets"])) {
				$this->view->preferences["onglets"][$key]["titre"]="";
				$this->view->preferences["onglets"][$key]["aff"]=0;
				$this->view->preferences["onglets"][$key]["ordre"]=100;
				$this->view->preferences["onglets"][$key]["largeur"]=0;
			} 
		}

		foreach($this->view->preferences["onglets"] as $key => $valeur) {
			if (!array_key_exists($key, $onglets))
				unset($this->view->preferences["onglets"][$key]);
		}
	}

//------------------------------------------------------------------------------------------------------
// Proprietés des notices
//------------------------------------------------------------------------------------------------------
	private function getPostNotice()
	{
		// Récup et controle des valeurs du formulaire
		foreach($_POST as $clef => $valeur)
		{
			$pos=strPos($clef,"_");
			if($pos === false)  {
				$enreg[$clef] = $valeur;
				continue;
			}
			$type=substr($clef,0,$pos);
			$champ=substr($clef,($pos+1));
			if($champ == "ordre") {$valeur=(int)$valeur; if($valeur < 1 or $valeur > 100) $valeur="1";}
			if($champ == "largeur") {$valeur=(int)$valeur; if($valeur < 5 or $valeur > 50) $valeur="0";}
			if($clef == "champs_codes") $enreg["entete"]=$valeur;
			else $enreg["onglets"][$type][$champ]=$valeur;
		}
		return $enreg;
	}

//------------------------------------------------------------------------------------------------------
// Validation et retour config admin de la page d'accueil
//------------------------------------------------------------------------------------------------------
	private function updateEtRetour($enreg)
	{
		$profil = Class_Profil::getLoader()->find($this->id_profil);
		$cfg_modules = $profil->getCfgModulesAsArray();
		$cfg_modules[$this->type_module][$this->action.$this->action2]=$enreg;

		$profil
			->setCfgModules($cfg_modules)
			->save();
		$this->view->reload="SITE";

		// Execute le script de retour
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('modules/_retour.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Retour au formulaire pour erreurs
//------------------------------------------------------------------------------------------------------
	private function retourErreur($erreur)
	{
		$this->view->erreur=$erreur;
		$this->view->preferences=$_POST;
	}
}