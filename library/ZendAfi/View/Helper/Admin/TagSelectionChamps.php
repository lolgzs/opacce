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
// OPAC3 :	Tag de selection de champs 
// 					fonctionne avec les scripts drag_and_drop.js et selection_champs.js
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_TagSelectionChamps extends ZendAfi_View_Helper_BaseHelper
{

	public function TagSelectionChamps($rubrique,$valeurs="")
	{
		if($rubrique == "libelles") return $this->getInitLibelles();
		if($rubrique == "liste") $champs="TANECR";
		if($rubrique == "facettes") $champs="AFMDPLGSBY";
		if($rubrique == "tags") $champs="AFMDPZ";
		if($rubrique == "champs") $champs="TAMDPLGSBECNR";
		if($rubrique == "type_tags") $champs="AMDPZ";
		if($rubrique == "champs_tags") $champs="AMDPZ";
		
		// Champs code et libelle
		$ret["codes_dispo"]='<script>codes_champ["'.$rubrique.'"]="'.$champs.'";</script>';
		$ret["champ_code"]='<input id="'.$rubrique.'_codes" type="hidden" name="'.$rubrique.'_codes" value="'.$valeurs.'">';
		$libelles = '';
		for($i=0; $i < strlen($valeurs); $i++)
		{
			if($libelles) $libelles.=", ";
			$libelles.= Class_Codification::getNomChamp($valeurs[$i]);
		}
		$ret["champ_libelle"]='<div id="'.$rubrique.'_libelle" class="drag_libelle" style="min-height:15px" onclick="afficherRubrique(\''.$rubrique.'\');">'.$libelles.'</div>';		
		
		// Bloc de sélection
		$bloc='<div id="'.$rubrique.'_mainDiv" class="drag_container">';
		$hauteur=(strlen($champs) * 26)."px";
		// Items selectionnés
		$bloc.='<table><tr><td><div id="'.$rubrique.'_leftColumn"  class="titre_colonne">'.$this->translate()->_('Champs sélectionnés');
		$bloc.='<div id="'.$rubrique.'_dropContent" class="drop_box" style="height:'.$hauteur.'">';
		for($i=0; $i < strlen($valeurs); $i++)
		{
			$bloc.='<div class="drag_item" id="'.$rubrique.'_box_'.$valeurs[$i].'" code="'.$valeurs[$i].'">'.Class_Codification::getNomChamp($valeurs[$i]).'</div>';
		}
		$bloc.='</div></td>';
		// Items non selectionnes
		$bloc.='<td><div id="'.$rubrique.'_rightColumn" class="titre_colonne">'.$this->translate()->_('Champs disponibles');
		$bloc.='<div id="'.$rubrique.'_dropBox" class="drop_box" style="height:'.$hauteur.'">';
		for($i=0; $i < strlen($champs); $i++)
		{
			if(strPos($valeurs,$champs[$i]) !== false) continue;
			$bloc.='<div class="drag_item" id="'.$rubrique.'_box_'.$champs[$i].'" code="'.$champs[$i].'">'.Class_Codification::getNomChamp($champs[$i]).'</div>';
		}
		$bloc.='</div></div></td>';
		// fin
		$bloc.='</tr></table></div>';
		$ret["bloc_saisie"]=$bloc;
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Envoie le javascript pour l'initialisation des libellés
//------------------------------------------------------------------------------------------------------
	private function getInitLibelles()
	{
		$js='<script>';
		$libs=Class_Codification::getNomChamp("tous");
		foreach($libs as $key => $libelle) $js.='libelle_champ["'.$key.'"]="'.$libelle.'";';
		$js.='</script>';
		return $js;
	}

}