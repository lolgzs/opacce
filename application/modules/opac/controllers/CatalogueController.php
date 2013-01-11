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
// OPAC3 - Controleur catalogues
//////////////////////////////////////////////////////////////////////////////////////////
class CatalogueController extends Zend_Controller_Action
{
	private $catalogue;														// Instance de la classe catalogue
	private $liste;																// Instance de la classe de liste de notices
	private $preferences;													// Préférences pour la liste du résultat
	
//------------------------------------------------------------------------------------------------------
// Initialisation du controler
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Instanciations
		$this->catalogue = new Class_Catalogue();
		
		// Reset session
		if (isset($_REQUEST["reset"]) && ($_REQUEST["reset"] == "true"))
		{
			unset($_REQUEST["reset"]);
			unset($_SESSION["recherche"]);
		}

		// Facettes
		if(array_isset("facette", $_REQUEST)) 
		{
			$facette = $_REQUEST["facette"].";";
			$facettes = isset($_REQUEST["facettes"]) ? $_REQUEST["facettes"] : '';
			if(strpos($facettes,$facette) === false) $facettes.=" ".$facette;
			$_REQUEST["facettes"]=$facettes;
			unset($_REQUEST["page"]);
			unset($_REQUEST["facette"]);
			unset($_SESSION["recherche"]["resultat"]);
		}

		// Url de retour
		$this->view->url_retour=BASE_URL."/catalogue/".$this->_getParam("action")."?";
		foreach($_REQUEST as $clef => $valeur) $this->view->url_retour.="&".$clef."=".$valeur;

		// Préférences
		$current_module=$this->_getParam("current_module");
		$this->preferences=$current_module["preferences"];
		$this->liste=new Class_ListeNotices($this->preferences["liste_nb_par_page"],$this->preferences["liste_codes"]);
	}
	
//------------------------------------------------------------------------------------------------------
// Index : liste des catalogues dispo
//------------------------------------------------------------------------------------------------------  
	function indexAction()
	{
		//@TODO : liste des catalogues (etageres)
	}

//------------------------------------------------------------------------------------------------------
// Appel par une ligne de menu
//------------------------------------------------------------------------------------------------------  
	function appelmenuAction()
	{
		// Moteur de recherche
		$moteur=new Class_MoteurRecherche();
		if (!array_isset("recherche", $_SESSION)) $_SESSION["recherche"] = array();

		// Get requetes
		if (!array_isset("resultat", $_SESSION["recherche"]))		{
			$ret=$this->catalogue->getRequetes($_REQUEST,false);
			
			if (array_isset("req_comptage", $ret)) {
				$ret["nombre"]=fetchOne($ret["req_comptage"]);
				if($_REQUEST["nb_notices"]) if($ret["nombre"] > $_REQUEST["nb_notices"]) $ret["nombre"]=$_REQUEST["nb_notices"];
				$facettes = $moteur->getFacettes($ret["req_facettes"],$this->preferences);
				$_SESSION["recherche"]["resultat"]=array_merge($facettes,$ret);
			}
			else $_SESSION["recherche"] = array("resultat" => array("req_liste" => '',"nombre" => 0));
		}

		// Variables viewer
		$_SESSION["recherche"]["retour_liste"]=$this->view->url_retour;
		$page = $this->_getParam('page', 1);
		$this->view->titre=$_REQUEST["titre"];
		$this->view->liste=$this->liste->getListe($_SESSION["recherche"]["resultat"]["req_liste"], $page);
		$this->view->resultat=$_SESSION["recherche"]["resultat"];
		$this->view->resultat["page_cours"]=$page;
		$this->view->url_facette=$this->view->url_retour;
		$this->view->texte_selection=$this->getTexteSelection();
	}

//------------------------------------------------------------------------------------------------------
// Texte de sélection pour les facettes
//------------------------------------------------------------------------------------------------------
	private function getTexteSelection()
	{
		$facette = '';
		// facettes
		if(array_isset("facettes", $_REQUEST))
		{
			$items=explode(";",$_REQUEST["facettes"]);
			foreach($items as $item)
			{
				$item=trim($item);
				if(!$item) continue;
				if($facette) $facette.=", ";
				$facette.=Class_Codification::getNomChamp($item)."=";
				$facette.= Class_Codification::getLibelleFacette($item);
			}
		}
		else $facette=$this->view->_("aucune");
		return $this->view->_("Facettes: %s", $facette);
	}
}