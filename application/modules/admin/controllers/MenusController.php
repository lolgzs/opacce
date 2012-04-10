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
// OPAC3 - Propriétés des modules des menus
//
//////////////////////////////////////////////////////////////////////////////////////////
class Admin_MenusController extends Zend_Controller_Action
{
	private $id_profil;							// Id_profil du menu
	private $id_module;							// N° d'identifiant unique pour le javascript config_menus
	private $type_menu;							// Type de menu

//------------------------------------------------------------------------------------------------------
// Initialisation des parametres et du layout
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Changer le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');

		// Recup des parametres
		$this->id_module=$this->_getParam("id_module");
		$this->type_menu=$this->_getParam("type_menu");
		$this->id_profil=$this->_getParam("id_profil");
		$this->id_bib=$this->_getParam("id_bib");

		// On initalise les proprietes
		$this->view->id_bib=$this->id_bib;
		$this->view->id_profil_maj=$this->_getParam("id_profil");
		$this->view->libelle=$this->_getParam("libelle");
		$this->view->picto=$this->_getParam("picto");
		$this->view->preferences=$this->extractProperties();
		$this->view->url=$this->_request->getRequestUri();
	}

	function preDispatch(){
		Zend_Layout::startMvc();
	}

//------------------------------------------------------------------------------------------------------
// Fonctions sans préférences : uniquement picto et libelle
//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
	}
	
//------------------------------------------------------------------------------------------------------
// Avis
//------------------------------------------------------------------------------------------------------
	function avisAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$nb=(int)($enreg["nb"]);
			if($nb < 1 or $nb > 50) $this->retourErreur("Le nombre d'avis doit être compris entre 1 et 50");
			else $this->updateEtRetour($enreg);
		}
	}

//------------------------------------------------------------------------------------------------------
// NEWS (articles cms)
//------------------------------------------------------------------------------------------------------
	function newsAction()
	{	
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$enreg["nb_aff"] = (int)$enreg["nb_aff"];
			$enreg["nb_analyse"] = (int)$enreg["nb_analyse"];
			$this->updateEtRetour($enreg);
		}
	}
//------------------------------------------------------------------------------------------------------
// LAST_NEWS (derniers articles)
//------------------------------------------------------------------------------------------------------
	function lastnewsAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$nb=(int)($enreg["nb"]);
			if($nb < 1 or $nb > 50) $this->retourErreur("Le nombre d'articles doit être compris entre 1 et 50");
			else $this->updateEtRetour($enreg);
		}
	}

//------------------------------------------------------------------------------------------------------
// RSS 
//------------------------------------------------------------------------------------------------------
	function rssAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$nb=(int)($enreg["nb"]);
			if($nb < 1 or $nb > 50) $this->retourErreur("Le nombre à afficher doit être compris entre 1 et 50");
			else $this->updateEtRetour($enreg);
		}
	}
	
//------------------------------------------------------------------------------------------------------
// SITOTHEQUE 
//------------------------------------------------------------------------------------------------------
	function sitothequeAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$nb=(int)($enreg["nb"]);
			if($nb < 1 or $nb > 50) $this->retourErreur("Le nombre à afficher doit être compris entre 1 et 50");
			else $this->updateEtRetour($enreg);
		}
	}
	
//------------------------------------------------------------------------------------------------------
// Lien vers une site (url libre)
//------------------------------------------------------------------------------------------------------
	function liensiteAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			if(trim($enreg["url"])> "" and substr(strtolower($enreg["url"]),0,4) != "http") $this->retourErreur("L'url saisie n'est pas correcte");
			else $this->updateEtRetour($enreg);
		}
	}

//------------------------------------------------------------------------------------------------------
// Lien vers un profil du portail
//------------------------------------------------------------------------------------------------------
	function lienprofilAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$this->updateEtRetour($enreg);
		}
	}

//------------------------------------------------------------------------------------------------------
// Google map
//------------------------------------------------------------------------------------------------------
	function googlemapAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			$enreg["id_bib"]=$enreg["bib"];
			unset($enreg["bib"]);
			$this->updateEtRetour($enreg);
		}
		$bib=new Class_Bib();
		$this->view->combo_bibs=$bib->getComboBib($this->view->preferences["id_bib"],false,false);
	}

//------------------------------------------------------------------------------------------------------
// Catalogue
//------------------------------------------------------------------------------------------------------
	function catalogueAction()	{

		$selected_panier = null;
		if (array_key_exists('id_panier', $this->view->preferences) &&
				array_key_exists('id_user', $this->view->preferences))
			$selected_panier = Class_PanierNotice::getLoader()->findFirstBy(array( 'id_panier' => $this->view->preferences['id_panier'],
																																						 'id_user' => $this->view->preferences['id_user']));
		if ($selected_panier !== null) $this->view->preferences['id_panier'] = $selected_panier->getId();

		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			if(!$enreg["titre"]) $enreg["titre"]=$enreg["libelle"];
			if($enreg["id_panier"]) {
				$panier = Class_PanierNotice::getLoader()->find($enreg['id_panier']);
				$enreg["id_catalogue"]=0;
				$enreg['id_panier'] = $panier->getIdPanier();
				$enreg["id_user"] = $panier->getIdUser();
				
			}
			else $enreg["id_user"]=0;
			$this->updateEtRetour($enreg);
		}
		
		$this->view->catalogues=Class_Catalogue::getCataloguesForCombo();
		$this->view->paniers=Class_PanierNotice::getPaniersForComboMenu();
	}

//------------------------------------------------------------------------------------------------------
// Etagère
//------------------------------------------------------------------------------------------------------
	function etagereAction()
	{
		if ($this->_request->isPost())
		{
			$enreg=$_POST;
			if(!$enreg["titre"]) $enreg["titre"]=$enreg["libelle"];
			$this->updateEtRetour($enreg);
		}
	}

	//------------------------------------------------------------------------------------------------------
	// bibliotheque numerique
	//------------------------------------------------------------------------------------------------------
	function albumAction()	{
		if ($this->_request->isPost())	{
			$enreg=$_POST;
			if(!$enreg["titre"]) $enreg["titre"]=$enreg["libelle"];
			$this->updateEtRetour($enreg);
			return;
		}

		$this->view->albums = array();
		foreach(Class_Album::getLoader()->findAll() as $album) 
			$this->view->albums[$album->getId()] = $album->getAbsolutePath();
		asort($this->view->albums);
	}

//------------------------------------------------------------------------------------------------------
// Retour au formulaire pour erreurs
//------------------------------------------------------------------------------------------------------
	private function retourErreur($erreur)
	{
		$this->view->erreur=$erreur;
		$this->view->libelle=$_POST["libelle"];
		$this->view->picto=$_POST["picto"];
		$this->view->preferences=$_POST;
	}

//------------------------------------------------------------------------------------------------------
// Compactage des proprietes si on vient la de config admin des menus
//------------------------------------------------------------------------------------------------------
	private function compactProperties($enreg)
	{
		foreach($enreg as $clef => $valeur)
		{
			if($properties) $properties.="|";
			$properties.=$clef."=".$valeur;
		}
		return $properties;
	}
	
//------------------------------------------------------------------------------------------------------
// Décompactage des proprietes si on vient la de config admin des menus
//------------------------------------------------------------------------------------------------------
	private function extractProperties()
	{
		$props = $this->_getParam("preferences");
		if($props)
		{
			$props=explode("|",$props);
			foreach($props as $prop)
			{
				$pos=strpos($prop,"=");
				$clef=substr($prop,0,$pos);
				$valeur=substr($prop,($pos+1));
				$properties[$clef]=$valeur;
			}
		}
		// On prend les valeurs par defaut pour le module
		else
		{
			$cls=new Class_Systeme_ModulesMenu();
			$properties=$cls->getValeursParDefaut($this->type_menu);
		}
		return $properties;
	}
	
//------------------------------------------------------------------------------------------------------
// Validation et retour config admin des menus
//------------------------------------------------------------------------------------------------------
	private function updateEtRetour($data)
	{
		// Filtrage des données
		foreach($data as $clef => $valeur) $enreg[$clef]=addslashes($valeur);
	
		// Variables de vue
		$this->view->libelle=$enreg["libelle"]; unset($enreg["libelle"]);
		$this->view->picto=$enreg["picto"]; unset($enreg["picto"]);
		$this->view->id_module=$this->id_module;
		$this->view->properties=$this->compactProperties($enreg);

		// Execute le script de retour
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->renderScript('menus/_retour.phtml');
	}
	
}