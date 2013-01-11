<?PHP
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
//////////////////////////////////////////////////////////////////////////////
//   OBJETS DE SAISIE
//////////////////////////////////////////////////////////////////////////////
function getComboSimple($name,$valeur,$liste)
{
	$combo='<select id="'.$name.'" name="'.$name.'">';
	foreach($liste as $clef => $libelle)
	{
		if($valeur==$clef) $selected=" selected"; else $selected="";
		$combo.='<option value="'.$clef.'"'.$selected.'>'.$libelle.'</option>';
	}
	$combo.='</select>';
	return $combo;
}
function getComboCodif($name,$clef,$valeur,$events="",$tous=false)
{
	$data=fetchOne("Select liste from variables where clef='$clef'");
	if($events > "") $events=" ".$events;
	$champ_valeur='<select name="'.$name.'" id="'.$name.'" '.$events.'>';
	if($tous == true) $champ_valeur.='<option value="">tous</option>';
	$v=explode(chr(13).chr(10),$data);
	for($i=0; $i<count($v); $i++)
	{
		$elem=explode(":",$v[$i]);
		if(trim($elem[0])>"")
		{
			if($valeur==$elem[0]) $selected=" selected"; else $selected="";
			$champ_valeur.='<option value="'.$elem[0].'"'.$selected.'>'.stripSlashes($elem[1]).'</option>';
		}
	}
	$champ_valeur.='</select>';
	return $champ_valeur;
}

function getComboTable($name,$table,$clef,$valeur,$condition="",$tous=false)
{
	global $sql;
	$req="Select $clef,libelle from $table";
	$data=$sql->fetchAll($req." ".$condition);
	$combo='<select name="'.$name.'">';
	if($tous == true) $combo.='<option value="">tous</option>';
	for($i=0; $i<count($data); $i++)
	{
		$elem=$data[$i];
		if($valeur==$elem[$clef]) $selected=" selected"; else $selected="";
		$combo.='<option value="'.$elem[$clef].'"'.$selected.'>'.$elem["libelle"].'</option>';
	}
	$combo.='</select>';
	return $combo;
}

function getChamp($id,$valeur,$taille)
{
	$champ='<input type="text" name="'.$id.'" value="'.$valeur .'"';
	if($taille) $champ .=' size="'.$taille.'"';
	$champ.='>';
	return $champ;
}

function getTextarea($id,$valeur,$largeur,$hauteur)
{
	$champ='<textarea name="'.$id.'" cols="'.$largeur .'" rows="'.$hauteur.'">';
	$champ.=$valeur.'</textarea>';
	return $champ;
}
?>