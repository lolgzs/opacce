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
// OPAC3 :	Tag liste avec champ suggestion
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_TagListeSuggestion extends ZendAfi_View_Helper_BaseHelper
{
	//----------------------------------------------------------------------------------
	// Main routine
	//----------------------------------------------------------------------------------
	public function TagListeSuggestion($rubrique,$name,$valeurs="") {
		Class_ScriptLoader::getInstance()->addAdminScripts(array('tag_selection', 'controle_maj'));

		$selection = $html = '';
		// Lire les libelles en fonction de la rubrique
		$min_cars_recherche=1;
		$options=array(1 => $this->translate()->_("commence par"), 
									 2 => $this->translate()->_("contient"));
		$champ_libelle="libelle";
		switch($rubrique)
		{
			case "auteur":
				$options=array(1 => $this->translate()->_("commence par"));
				$table="codif_auteur";
				$champ_id="id_auteur";
				break;
			case "matiere": $table="codif_matiere"; $champ_id="id_matiere"; break;
			case "interet": $table="codif_interet"; $champ_id="id_interet"; break;
			case "dewey": 
				$table="codif_dewey"; 
				$champ_id="id_dewey"; 
				$options=array(1 => $this->translate()->_("indice commence par"), 
											 2 => $this->translate()->_("libellé commence par"),
											 3 => $this->translate()->_("libellé contient"));
				break;
			case "pcdm4": 
				$table="codif_pcdm4"; 
				$champ_id="id_pcdm4";
				$options=array(1 => $this->translate()->_("indice commence par"), 
											 2 => $this->translate()->_("libellé commence par"),
											 3 => $this->translate()->_("libellé contient"));
				break;
			case "tag": $table="codif_tags";$champ_id="id_tag"; break;
		}
		
		// Texte affichage des libelles
		if($valeurs)
		{
			$codes=explode(";",$valeurs);
			foreach($codes as $code) $selection.="&laquo;".fetchOne("select ".$champ_libelle." from ".$table." where ".$champ_id."='".$code."'")."&raquo; ";
		}
		
		// Champs code et libelle
		$html.='<input id="'.$name.'" type="hidden" name="'.$name.'" value="'.$valeurs.'">';
		$html.='<div class="tag_selection">';
		$onclick="ouvrirFermer(this,'".$name."_saisie');";
		$html.='<img onclick="'.$onclick.'" src="'.URL_ADMIN_IMG.'ico/ouvrir.gif" style="margin-top:1px;margin-right:3px;float:left;cursor:pointer">';
		$html.='<div id="'.$name.'_aff">'.$selection.'</div>';
		$html.='</div>';
		
		// Bloc de saisie
		$html.='<div id="'.$name.'_saisie" class="tag_saisie">';
		
		// Champs de sélection
		$html.='<div style="margin-bottom:5px;padding-top:5px">';
		$event="'".$rubrique."','".$name."',getId('".$name."_champ').value".",min_cars=".$min_cars_recherche;
		$html.='<select id="mode_'.$name.'" onchange="getSuggest('.$event.')">';
		foreach($options as $value => $libelle)	$html.='<option value="'.$value.'">'.$libelle.'</option>';
		$html.='</select>';
		$html.=str_repeat("&nbsp;",3).'Rechercher ';
		$event="'".$rubrique."','".$name."',this.value".",min_cars=".$min_cars_recherche;
		$html.='<input id="'.$name.'_champ" type="text" size="28" max-length="30" onkeyUp="getSuggest('.$event.')" >';
		$html.=str_repeat("&nbsp;",3).'<a style="color:#D44100;text-decoration:none;" href="javascript:suggestClear(\''.$name.'\',true)">&raquo;'.$this->translate()->_('Tout effacer').'</a>';
		
		// Liste
		$html.='<div id="'.$name.'_liste" style="margin-top:10px;padding-top:5px;border-top:1px solid #C8C8C8"></div>';
		$html.='</div>';
		
		$html.='</div>';
		
		return $html;
	}

}