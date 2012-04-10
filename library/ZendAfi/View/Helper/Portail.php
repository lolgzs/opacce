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
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Page d'accueil
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Portail extends ZendAfi_View_Helper_BaseHelper
{

	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	public function portail($cfg_accueil,$division_demandee=false)
	{
		$style_boite = array("",
												 "boite_de_la_division_gauche",
												 "boite_de_la_division_du_milieu",
												 "boite_de_la_division_droite");

		// initialisation pour les tests en mode strict
		$ret = array('','','','','');
		if ($division_demandee)
			$ret[$division_demandee] = '';

		// Config page d'accueil
		foreach ($cfg_accueil["modules"] as $id_module => $module) {
			if ($id_module === null) continue;
			
			$helper = ZendAfi_View_Helper_Accueil_Base::getModuleHelperFromParams($id_module, $module);
			if (!$helper) continue;

			$division = $helper->getDivision();
			if ($division == 0) continue; // sous-modules de la boîte 2 colonnes n'ont pas de division
			if($division_demandee and $division != $division_demandee) continue;


			// Modules particuliers
			$helper->setView($this->view);
			$ret[$division].=$helper->getBoite();
		}
		return $ret;
	}

	

}