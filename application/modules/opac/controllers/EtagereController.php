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
// OPAC3 - Controleur étagères
//////////////////////////////////////////////////////////////////////////////////////////
class EtagereController extends Zend_Controller_Action
{
	private $etagere;															// Instance de la classe etagere
	private $liste;																// Instance de la classe de liste de notices
	private $preferences;													// Préférences pour la liste du résultat
	
//------------------------------------------------------------------------------------------------------
// Initialisation du controler
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Instanciations
		$this->etagere = new Class_Etagere();
		
		// Reset session
		if($_REQUEST["reset"] == "true") 
		{
			unset($_REQUEST["reset"]);
			unset($_SESSION["recherche"]);
		}

		// Facettes
		if($_REQUEST["facette"]) 
		{
			$facette=$_REQUEST["facette"].";";
			$facettes=$_REQUEST["facettes"];
			if(strpos($facettes,$facette) === false) $facettes.=" ".$facette;
			$_REQUEST["facettes"]=$facettes;
			unset($_REQUEST["page"]);
			unset($_REQUEST["facette"]);
			unset($_SESSION["recherche"]["resultat"]);
		}

		// Url de retour
		$this->view->url_retour=BASE_URL."/etagere/".$this->_getParam("action")."?";
		foreach($_REQUEST as $clef => $valeur) $this->view->url_retour.="&".$clef."=".$valeur;

		// Préférences
		$current_module=$this->_getParam("current_module");
		$this->preferences=$current_module["preferences"];
		$this->liste=new Class_ListeNotices($this->preferences["liste_nb_par_page"],$this->preferences["liste_codes"]);
	}
	

//------------------------------------------------------------------------------------------------------
// Appel par une ligne de menu
//------------------------------------------------------------------------------------------------------  
	function appelmenuAction()
	{
		// Sous rubriques d'une etagere
		$id_etagere=$_REQUEST["id_etagere"];
		$id_kiosque=$_REQUEST["id_kiosque"];
		if(!$id_etagere) $id_etagere="1";
		$this->etagere->ecrireSettings($id_etagere);

		// Url retour pour les notices
		$_SESSION["recherche"]["retour_liste"]=BASE_URL."/etagere/appelmenu?id_etagere=".$id_etagere."&id_kiosque=".$id_kiosque;

		// Kiosque
		if($id_kiosque)
		{
			$enreg=$this->etagere->getEtagere($id_kiosque,"");
			$ret=$this->etagere->getNotices($enreg["requete"]);
			$this->etagere->getKiosque($ret,$id_kiosque);
			$this->view->id_kiosque=$id_kiosque;
			$this->view->titre_kiosque=$enreg["libelle"];
			$this->view->description_kiosque=$enreg["description"];
		}

		// Variables de vue
		$this->view->id_etagere=$id_etagere;	
	}

//------------------------------------------------------------------------------------------------------
// Appel par une ligne de menu
//------------------------------------------------------------------------------------------------------
	function kiosqueAction()
	{
		$id_etagere=$_REQUEST["id_etagere"];
		$enreg=$this->etagere->getEtagere($id_etagere,"");
		$ret=$this->etagere->getNotices($enreg["requete"]);
	}

//------------------------------------------------------------------------------------------------------
// Texte de sélection pour les facettes
//------------------------------------------------------------------------------------------------------
	private function getTexteSelection()
	{
		// facettes
		if($_REQUEST["facettes"])
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
		return $this->view->_("Facettes : %s", $facette);
	}
}