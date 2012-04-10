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
// OPAC3 - Appels Ajax
//
//////////////////////////////////////////////////////////////////////////////////////////
class Admin_AjaxController extends Zend_Controller_Action
{

//------------------------------------------------------------------------------------------------------
// Initialisation des parametres et du layout
//------------------------------------------------------------------------------------------------------
	function init()
	{
		// Désactiver le view renderer
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}

//------------------------------------------------------------------------------------------------------
// Initialisation des parametres et du layout
//------------------------------------------------------------------------------------------------------
	function listesuggestionAction()
	{
		// Parametres
		$autorite=$this->_getParam('type_autorite');
		$id_champ=$this->_getParam('id_champ');
		$mode_recherche=$this->_getParam('mode');
		$recherche=$this->_getParam('valeur');
		$limite_resultat=100;
		
		// Lancer la recherche en fonction du type d'autorite
		switch($autorite)
		{
			case "auteur" : $cls=new Class_Auteur(); break;
			case "matiere" : $cls=new Class_Matiere(); break;
			case "interet" : $cls=new Class_CentreInteret(); break;
			case "dewey" : $cls=new Class_Dewey(); break;
			case "pcdm4" : $cls=new Class_Pcdm4(); break;
			case "tag" : $cls=new Class_TagNotice(); break;
			default: print("mauvais code rubrique"); exit;
		}
		$liste=$cls->getListeSuggestion($recherche,$mode_recherche,$limite_resultat);
		
		// Renvoi de la liste
		if(!$liste) {print(""); exit;}
		foreach($liste as $item)
		{
			print('<div class="tag_liste" clef="'.$item[0].'" onclick="selectSuggest(\''.$id_champ.'\',this)">'.$item[1].'</div>');
		}
		exit;
	}
	
}