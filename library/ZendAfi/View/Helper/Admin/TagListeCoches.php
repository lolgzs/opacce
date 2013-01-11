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
// OPAC3 :	Tag liste de cases a cocher
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_TagListeCoches extends ZendAfi_View_Helper_BaseHelper
{

	public function TagListeCoches($rubrique,$name,$valeurs="",$id_bib=0)
	{
		$liste = array();
		$data = array();

		// Lire les libelles en fonction de la rubrique
		switch($rubrique)
		{
			case "type_doc": $liste=Class_Codification::getTypesDocs(); break;
			case "section": 
				$data=fetchAll("select id_section,libelle from codif_section order by libelle"); 
				if($data) foreach($data as $enreg) $liste[$enreg["id_section"]]=$enreg["libelle"];
				break;
			case "genre": 
				$data=fetchAll("select id_genre,libelle from codif_genre order by libelle");
				if($data) foreach($data as $enreg) $liste[$enreg["id_genre"]]=$enreg["libelle"];
				break;
			case "langue": 
				$data=fetchAll("select id_langue,libelle from codif_langue order by libelle");
				if($data) foreach($data as $enreg) $liste[$enreg["id_langue"]]=$enreg["libelle"];
				break;
			case "bibliotheque":
				$data=fetchAll("select ID_SITE,LIBELLE from bib_c_site where VISIBILITE=2 order by LIBELLE");
				if($data) foreach($data as $enreg) $liste[$enreg["ID_SITE"]]=$enreg["LIBELLE"];
				break;
			case "emplacement":
				$data=fetchAll("select id_emplacement,libelle from codif_emplacement order by libelle");
				if($data) foreach($data as $enreg) $liste[$enreg["id_emplacement"]]=$enreg["libelle"];
				break;
			case "annexe":
				$where = $id_bib ? "id_bib=$id_bib and " : '';
				$where.=" invisible=0";
				$data=fetchAll("select code,libelle from codif_annexe where ".$where." order by libelle");
				if($data) foreach($data as $enreg) $liste[$enreg["code"]]=$enreg["libelle"];
				break;
		}
		
		// Texte affichage des libelles
		$selection = '';
		$coche = array();
		if($valeurs)
		{
			$codes=explode(";",$valeurs);
			foreach($codes as $code) {
				if (array_key_exists($code, $liste)) {
					$selection.="&laquo;".$liste[$code]."&raquo; "; 
					$coche[$code]=true;
				}
			}
		}
		
		// Champs code et libelle
		$html='<input id="'.$name.'" type="hidden" name="'.$name.'" value="'.$valeurs.'">';
		$html.='<div class="tag_selection">';
		$onclick="ouvrirFermer(this,'".$name."_saisie');";
		$html.='<img onclick="'.$onclick.'" src="'.URL_ADMIN_IMG.'ico/ouvrir.gif" style="margin-top:1px;margin-right:3px;float:left;cursor:pointer">';
		$html.='<div id="'.$name.'_aff">'.$selection.'</div>';
		$html.='</div>';
		
		// Bloc de saisie
		$html.='<div id="'.$name.'_saisie" class="tag_saisie">';
		
		// Tout cocher/decocher
		$html.='<div style="margin-bottom:5px;">';
		$html.='<a style="color:#D44100;text-decoration:none;" href="javascript:selectAll(\''.$name.'\',true)">&raquo;Tout cocher</a>';
		$html.='&nbsp;&nbsp;&nbsp;<a style="color:#D44100;text-decoration:none;" href="javascript:selectAll(\''.$name.'\',false)">&raquo;Tout décocher</a>';
		$html.='</div>';
		
		// Valeurs
		if($liste)
		{
			foreach($liste as $clef => $libelle)
			{
				if(array_key_exists($clef, $coche) && $coche[$clef]==true) $checked=' checked="checked"';
				else $checked="";
				$html.='<input type="checkbox" style="width: inherit;" value="1" clef="'.$clef.'" onclick="getCoches(\''.$name.'\')"'.$checked.'>'.$libelle.BR;
			}
		}
		$html.='</div>';
		return $html;
	}

}