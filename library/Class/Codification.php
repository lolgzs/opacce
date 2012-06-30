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
// OPAC3 : Codifications
//////////////////////////////////////////////////////////////////////////////////////////

class Class_Codification
{

//------------------------------------------------------------------------------------------------------
// Liste des types de documents 
//------------------------------------------------------------------------------------------------------
	static function getTypesDocs()	{
		return Class_TypeDoc::allByIdLabel();
	}

//------------------------------------------------------------------------------------------------------
// Combo types de docs
//------------------------------------------------------------------------------------------------------
	static function getComboTypesDocs($valeur,$events="",$tous=false)
	{
		$data=fetchOne("select liste from variables where clef='types_docs'");
		if($events > "") $events=" ".$events;
		$combo='<select name="type_doc" '.$events.'>';
		if($tous == true) $combo.='<option value="">tous</option>';
		$v=explode(chr(13).chr(10),$data);
		for($i=0; $i<count($v); $i++)
		{
			$elem=explode(":",$v[$i]);
			if(trim($elem[0])>"")
			{
				if($valeur==$elem[0]) $selected=" selected"; else $selected="";
				$combo.='<option value="'.$elem[0].'"'.$selected.'>'.stripSlashes($elem[1]).'</option>';
			}
		}
		$combo.='</select>';
		return $combo;
	}
	
//------------------------------------------------------------------------------------------------------
// Retourne libelle correspondant à un code facette
//------------------------------------------------------------------------------------------------------
	static function getLibelleFacette($rubrique)
	{
		$type=$rubrique[0];
		$id=substr($rubrique,1);
		switch($type)
		{
			case "X": 
				$lib=array(" ","Dewey","Pcdm4");
				$l=getVar("PCDM4_LIB"); if(trim($l)) $lib[2]=$l;
				$l=getVar("DEWEY_LIB"); if(trim($l)) $lib[1]=$l;
				return $lib[intval($id)]; 
			case "D": return Class_Dewey::getLibelle($id);
			case "P": return Class_Pcdm4::getLibelle($id);
			case "A": return fetchOne("select libelle from codif_auteur where id_auteur=".(int)$id);
		  case "M": return fetchOne("select libelle from codif_matiere where id_matiere=".(int)$id);
			case "F": return fetchOne("select libelle from codif_interet where id_interet=".(int)$id);
			case "G": return fetchOne("select libelle from codif_genre where id_genre=".(int)$id);
			case "L": return fetchOne("select libelle from codif_langue where id_langue='$id'");
			case "S": return fetchOne("select libelle from codif_section where id_section=".(int)$id);
			case "E": return fetchOne("select libelle from codif_emplacement where id_emplacement=$id");
		  case "B": return fetchOne("select nom_court from int_bib where id_bib=".(int)$id);
		  case "Y": return fetchOne("select libelle from codif_annexe where code='".addslashes($id)."'");
			case "Z": return fetchOne("select libelle from codif_tags where id_tag=$id");
			case "T":
			case "t":	{
				if ($type_doc = Class_TypeDoc::getLoader()->find((int)$id))
					return $type_doc->getLabel();
				return '';
			}
		}
	}
//------------------------------------------------------------------------------------------------------
// Retourne un nom de champ a partir d'1 type ou d'1 facette
//------------------------------------------------------------------------------------------------------
	static function getNomChamp($code,$pluriel=0)
	{
		$translate = Zend_Registry::get('translate');
		$type=$code[0];
		$libs=array(
								"A" => array($translate->_("Auteur"),				$translate->_( "Auteur(s)")),
								"B" => array($translate->_("Bibliothèque"),	$translate->_("Bibliothèque(s)")),
								"C" => array($translate->_("Collection"),		$translate->_("Collection(s)")),
								"D" => array($translate->_("Dewey"),				$translate->_("Dewey")),
								"E" => array($translate->_("Editeur"),			$translate->_("Editeur(s)")),
								"F" => array($translate->_("Centre d'intérêt"),$translate->_("Centre(s) d'intérêt")),
								"G" => array($translate->_("Genre"),				$translate->_("Genre")),
								"I" => array($translate->_("Identifiant"),	$translate->_("Identifiant")),
								"K" => array($translate->_("Collation"),		$translate->_("Collation")),
								"L" => array($translate->_("Langue"),				$translate->_("Langue(s)")),
								"M" => array($translate->_("Sujet"),				$translate->_("Sujet(s)")),
								"N" => array($translate->_("Année"),				$translate->_("Année")),
								"O" => array($translate->_("Notes"),				$translate->_("Notes")),
								"P" => array($translate->_("Pcdm4"),				$translate->_("Pcdm4")),
								"R" => array($translate->_("Résumé"),				$translate->_("Résumé")),
								"S" => array($translate->_("Section"),			$translate->_("Section")),
								"T" => array($translate->_("Titre"),				$translate->_("Titre(s)")),
								"t" => array($translate->_("Type de document"),				$translate->_("Types de documents")),
								"Y" => array($translate->_("Site"),					$translate->_("Site")),
								"Z" => array($translate->_("Tag"),					$translate->_("Tag(s)")),
								"8" => array($translate->_("Lien internet"),$translate->_("Liens internet")));
		$l=getVar("PCDM4_LIB"); if(trim($l)) {$libs["P"][0]=$l; $libs["P"][1]=$l; }
		$l=getVar("DEWEY_LIB"); if(trim($l)) {$libs["D"][0]=$l; $libs["D"][1]=$l; }
		if($code=="tous")
		{ 
			foreach($libs as $key => $valeur) $lib[$key]=$valeur[0];
			return $lib;
		}
		else return $libs[$type][$pluriel];	
	}
//------------------------------------------------------------------------------------------------------
// Retourne un nom d'onglet pour les notices
//------------------------------------------------------------------------------------------------------
	static function getNomOnglet($onglet)	{
			$translate = Zend_Registry::get('translate');
			$libs=array(
								"detail" => $translate->_("Description du document"),
								"avis" => $translate->_("Critiques"),
								"exemplaires" => $translate->_("Exemplaires"),
								"resume" => $translate->_("Résumés"),
								"tags" => $translate->_("Rebondir dans le catalogue"),
								"biographie" => $translate->_("Biographie de l'auteur"),
								"similaires" => $translate->_("Documents similaires"),
								"bibliographie"	=> $translate->_("Discographie"),
								"morceaux" => $translate->_("Morceaux"),
								"bandeAnnonce"	=> $translate->_("Bande-annonce"),
								"photos" => $translate->_("Photos"),
								"videos" => $translate->_("Archives vidéo"),
								"resnumeriques" => $translate->_("Ressources numériques"),
								"babeltheque" => $translate->_('Babelthèque'));
		if($onglet) return $libs[$onglet];
		else return $libs;
	}
}