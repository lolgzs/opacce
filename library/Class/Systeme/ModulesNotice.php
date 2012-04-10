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
//   OPAC3 : Gestion des modules lies aux notices documents et autorites
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_Systeme_ModulesNotice
{
	private $groupes=array
		(
			"notice" => "Notices",
			"auteur" => "Auteurs",
		);
	private $modules=array
		(
			"bandeAnnonce" => array("libelle" => "Bande annonce cinéma","groupe" => "notice","popup_width" => 550, "popup_height" => 412),
			"exemplaires" => array("libelle" => "Exemplaires","groupe" => "notice","popup_width" => 550, "popup_height" => 480),
		);

//------------------------------------------------------------------------------------------------------
// Rend la définition de tous les modules
//------------------------------------------------------------------------------------------------------
	public function getModules()
	{
		return $this->modules;
	}

//------------------------------------------------------------------------------------------------------
// Rend la définition d'un module
//------------------------------------------------------------------------------------------------------		
	public function getModule($type)
	{
		$module=$this->modules[$type];
		if(!$module) return false;
		return $module;
	}

//------------------------------------------------------------------------------------------------------
// Valeurs par défaut pour une fonction
//------------------------------------------------------------------------------------------------------		
	public function getValeursParDefaut($type)
	{
		switch($type)
		{
			case "bandeAnnonce": return $this->getDefautBandeAnnonce();
			case "exemplaires": return $this->getDefautExemplaires();
			default: return array();
		}
	}

//------------------------------------------------------------------------------------------------------
// Valeurs par defaut module bande-annonce
//------------------------------------------------------------------------------------------------------	
	private function getDefautBandeAnnonce()
	{
		$ret["target"]="1";								// Ouvrir dans un nouvel onglet ou pas
		$ret["url"]="http://google.fr";   // Test de proprietes
		return $ret;
	}

//------------------------------------------------------------------------------------------------------
// Valeurs par defaut module : Exemplaires
//------------------------------------------------------------------------------------------------------
	private function getDefautExemplaires()
	{
		$ret["grouper"]="0";							// Groupage
		$ret["bib"]="1";									// Afficher la bibliotheque
		$ret["section"]="0";							// Afficher la section
		$ret["emplacement"]="0";					// Afficher l'emplacement
		$ret["localisation"]="1";					// Afficher lien vers la localisation sur le plan
		$ret["plan"]="1";									// Afficher lien vers google maps
		$ret["resa"]="1";									// Afficher le lien de réservation
		$ret["dispo"]="1";								// Afficher la disponibilité

		return $ret;
	}
}