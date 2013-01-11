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
///////////////////////////////////////////////////////////////////
//
//         PROFILS DE DONNEES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");
require_once("classe_profil_donnees.php");
require_once( "fonctions/objets_saisie.php");
$oProfil = new profil_donnees();

?>

<h1>Profils de données</h1>
 
<?PHP

//---------------------------------------------------------------------------------
// CREER
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="CREER")
{
	$profil=$oProfil->getProfil(0);
	afficherProfil($profil,"block");
	print('</form></body></html>');
	exit;
}
//---------------------------------------------------------------------------------
// VALIDER
//---------------------------------------------------------------------------------

if($_REQUEST["action"]=="VALIDER")
{
	extract($_POST);
	for($i=0;$i<count($td_code); $i++)
	{
		$td["code"]=$td_code[$i];
		$td["label"]=$td_label[$i];
		$td["zone_995"]=$td_zone_995[$i];
		$type_doc[]=$td;
	}
	if($format==0) $champs="";
	$attributs[0]["type_doc"]=$type_doc;
	$attributs[0]["champ_code_barres"]=$champ_code_barres;
	$attributs[0]["champ_cote"]=$champ_cote;
	$attributs[0]["champ_type_doc"]=$champ_type_doc;
	$attributs[0]["champ_genre"]=$champ_genre;
	$attributs[0]["champ_section"]=$champ_section;
	$attributs[0]["champ_emplacement"]=$champ_emplacement;
	$attributs[0]["champ_annexe"]=$champ_annexe;
	$attributs[1]["champs"]=$champs;
	$attributs[2]["champs"]=$champs;
	$attributs[3]["champs"]=$champs;
	$attributs[5]["champs"]=$champs;
	$attributs[4]=array("zone"=>$nouveaute_zone,"champ"=>$nouveaute_champ,"format"=>$nouveaute_format,"jours"=>$nouveaute_jours,"valeurs"=>$nouveaute_valeurs);

	// champs xml
	$attributs[5]["xml_balise_abonne"]=$xml_balise_abonne;
	foreach($_POST as $key=>$valeur)
	{
		if(substr($key,0,11)!="xml_abonne_") continue;
		$champ=substr($key,11);
		$attributs[5]["xml_champs_abonne"][$champ]=$valeur;
	}
	$oProfil->ecrire($id_profil,$libelle,$accents,$rejet_periodiques,$type_fichier,$format,$attributs,$id_article_periodique);
}

//---------------------------------------------------------------------------------
// SUPPRIMER
//---------------------------------------------------------------------------------

if($_REQUEST["action"]=="SUPPRIMER")
{
	$id_profil=$_REQUEST["id_profil"];
	if($id_profil == 1) afficherErreur("Suppression interdite car ce profil sert de modèle");
	$sql->execute("delete from profil_donnees where id_profil =$id_profil");
}

//---------------------------------------------------------------------------------
// LISTE 
//---------------------------------------------------------------------------------
print('<div class="liste">');

$liste=$sql->fetchAll("Select id_profil from profil_donnees order by libelle");
for($p=0; $p< count($liste); $p++)
{
	$profil=$oProfil->getProfil($liste[$p]["id_profil"]);
	$img="plus.gif";
	$display="none";
	if($profil["id_profil"] == $_REQUEST["id_profil"])
		{
			$img="moins.gif";
			$display="block";
		}
		print('<div class="liste_img"><img id="Iprofil'.$profil["id_profil"].'" src="'.URL_IMG.$img.'" onclick="contracter_bloc(\'profil'.$profil["id_profil"].'\')" style="cursor:pointer"></div>');
		print('<div class="liste_titre">'. $profil["libelle"].'</div>');
		afficherProfil($profil,$display);
}
print('</div>');

// Bouton ajouter
$bouton_ajout=rendBouton("Ajouter un profil","config_profil_donnees","action=CREER");
print(BR.BR.$bouton_ajout);

print('</body></html>');
exit;

function afficherProfil($profil,$display)
{
	print('<div class="form" id="profil'.$profil["id_profil"].'" style="width:600px;margin-left:20px;display:'.$display.'">');
	print('<form method="post" action="'.URL_BASE.'php/config_profil_donnees.php?action=VALIDER">');
	print('<input type="hidden" name="id_profil" value="'.$profil["id_profil"].'">');
	print('<table class="form" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Général</th></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Libellé</td><td class="form_first">'.getChamp("libelle",$profil["libelle"],40).'</td></tr>');
	print('<tr><td class="form" align="right" width="40%">Caractères accentués</td><td class="form">'.getComboCodif("accents","transco_accents",$profil["accents"]).'</td></tr>');
	$event='onChange="activerFormat(\''.$profil["id_profil"].'\')"';
	print('<tr><td class="form" align="right" width="40%">Type de fichier</td><td class="form">'.getComboCodif("type_fichier","type_fichier",$profil["type_fichier"],$event).'</td></tr>');
	print('<tr><td class="form" align="right" width="40%">Format de fichier</td><td class="form">'.getComboCodif("format","import_format",$profil["format"],$event).'</td></tr>');
	
	// Selon format
	print('<tr><td class="form" colspan="2">');
	paveUnimarcNotice($profil);
	paveUnimarcFichier($profil);
	paveAscii($profil);
	paveXml($profil);
	print('</td></tr>');

	// Boutons maj
	print('<tr><th class="form" colspan="2" align="center">');
	$bouton_valider='<input type="submit" class="bouton" value="Valider">';
	$bouton_supprimer=rendBouton("Supprimer","config_profil_donnees","action=SUPPRIMER&id_profil=".$profil["id_profil"]);
	print($bouton_valider.str_repeat("&nbsp;",5).$bouton_supprimer);
	print('</th></tr></table></form></div>');
}

function paveUnimarcNotice($profil)
{	
	$id_profil=$profil["id_profil"];
	if($profil["type_fichier"]==0 and ($profil["format"] == 0 or $profil["format"] == 6)) $display="block"; else $display="none";
	print('<div id="unimarc_0" style="display:'.$display.'">');
	print('<table class="form" cellspacing="0" cellpadding="5">');

	// Types de docs
	print('<tr><th class="form" align="left" colspan="2">Types de documents</th></tr>');
	print('<tr><td class="form" align="left" colspan="2">');
	
	print('<div class="form" style="width:450px;margin-left:30px;margin-top:10px;margin-bottom:10px">');
	print('<table class="form" cellspacing="0" cellpadding="5">');
	print('<tr>');
	print('<td class="form" width="40%" align="left">&nbsp;</td>');
	print('<td class="form_first" width="29%" align="left">Label</td>');
	print('<td class="form_first" width="29%" align="left">Zone 995$r</td>');
	print('</tr>');

	$num=0;
	foreach($profil["attributs"][0]["type_doc"] as $typeDoc)
	{
		if(count($typeDoc["label"]) > 1) $label=implode(";",$typeDoc["label"]); else $label=$typeDoc["label"][0];
		if(count($typeDoc["zone_995"]) > 1) $zone_995=implode(";",$typeDoc["zone_995"]); else $zone_995=$typeDoc["zone_995"][0];
		print('<tr>');
		print('<td class="form" align="right"><input type="hidden" name="td_code['.$num.']'.'" value="'.$typeDoc["code"].'">'.$typeDoc["libelle"].'</td>');
		print('<td class="form">'.getChamp("td_label[".$num."]",$label,20).'</td>');
		print('<td class="form">'.getChamp("td_zone_995[".$num."]",$zone_995,20).'</td>');
		print('</tr>');
		$num++;
	}
	print('</table></div>');
	print('</td></tr>');

	// Autres données
	print('<tr><th class="form" align="left" colspan="2">Périodiques</th></tr>');
	print('<tr><td class="form" align="right">Rejeter les périodiques</td><td class="form">'.getComboCodif("rejet_periodiques","oui_non",$profil["rejet_periodiques"]).'</td></tr>');
	print('<tr><td class="form" align="right">Identification des articles de périodiques</td><td class="form">'.getComboCodif("id_article_periodique","id_article_periodique",$profil["id_article_periodique"]).'</td></tr>');

	print('<tr><th class="form" align="left" colspan="2">Champs exemplaires</th></tr>');
	print('<tr><td class="form_first" align="right" width="50%">Prendre le code-barres en</td><td class="form_first">'.getComboCodif("champ_code_barres","champ_code_barres",$profil["attributs"][0]["champ_code_barres"]).'</td></tr>');
	print('<tr><td class="form_first" align="right" width="50%">Prendre la cote en</td><td class="form">'.getComboCodif("champ_cote","champ_cote",$profil["attributs"][0]["champ_cote"]).'</td></tr>');
	print('<tr><td class="form_first" align="right" width="50%">Type de document</td><td class="form">'.getComboChampsExemplaires("champ_type_doc",$profil["attributs"][0]["champ_type_doc"]).'</td></tr>');
	print('<tr><td class="form" align="right">Section</td><td class="form">'.getComboChampsExemplaires("champ_section",$profil["attributs"][0]["champ_section"]).'</td></tr>');
	print('<tr><td class="form" align="right">Genre</td><td class="form">'.getComboChampsExemplaires("champ_genre",$profil["attributs"][0]["champ_genre"]).'</td></tr>');
	print('<tr><td class="form" align="right">Emplacement</td><td class="form">'.getComboChampsExemplaires("champ_emplacement",$profil["attributs"][0]["champ_emplacement"]).'</td></tr>');
	print('<tr><td class="form" align="right">Annexe</td><td class="form">'.getComboChampsExemplaires("champ_annexe",$profil["attributs"][0]["champ_annexe"]).'</td></tr>');
	
	print('<tr><th class="form" align="left" colspan="2">Date de nouveauté</th></tr>');
	print('<tr><td class="form" align="right">Zone / champ</td><td class="form">'.getChamp("nouveaute_zone",$profil["attributs"][4]["zone"],3).'&nbsp;$&nbsp;'.getChamp("nouveaute_champ",$profil["attributs"][4]["champ"],1).'</td></tr>');
	$fmt=array(""=>"","1"=>"AAAA-MM-JJ","2"=>"AAAAMMJJ","4"=>"JJ-MM-AAAA","5"=>"J/M/AAAA","3"=>"Valeur(s)");
	print('<tr><td class="form" align="right">Format</td><td class="form">'.getComboSimple("nouveaute_format",$profil["attributs"][4]["format"],$fmt).'</td></tr>');
	print('<tr><td class="form" align="right">Valeurs séparées par des ;</td><td class="form">'.getChamp("nouveaute_valeurs",$profil["attributs"][4]["valeurs"],20).'</td></tr>');
	print('<tr><td class="form" align="right">Ajouter</td><td class="form">'.getChamp("nouveaute_jours",$profil["attributs"][4]["jours"],3).' jours</td></tr>');

	print('</table></div>');
}

function paveUnimarcFichier($profil)
{
	if($profil["type_fichier"] =="1" and $profil["format"]==0) $display="block"; else $display="none";
	print('<div id="unimarc_1" style="display:'.$display.';">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Champs</th></tr>');
	print('<tr><td class="form_first" colspan="2" align="center">Le format unimarc n\'est pas supporté pour ce type de fichier</td></tr>');
	print('</table></div>');
}

function paveAscii($profil)
{	
	$id_profil=$profil["id_profil"];
	$format=$profil["format"];
	if( $format > 0 and $format !=4 and $format !=6) $display="block"; else $display="none";
	print('<div id="ascii" style="display:'.$display.';">');
	
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Champs</th></tr>');
	print('<tr><td class="form_first" colspan="2">');
	$id_champ="champs_".$profil["id_profil"];
	$event='onChange="selectChamp(\''.$id_champ.'\',this.value)"';
	print('<div style="margin-right:5px;float:left">Sélectionnez un champ</div>');

	// Combos de champs
	if($profil["type_fichier"]<"1") $display="block"; else $display="none";
	print('<div id="combo_type_0" style="float:left;display:'.$display.'">'.getComboCodif("","champs_ascii",0,$event)).'</div>';

	if($profil["type_fichier"]=="1" and $format !=4) $display="block"; else $display="none";
	print('<div id="combo_type_1" style="float:left;display:'.$display.'">'.getComboCodif("","champs_abonne",0,$event)).'</div>';

	if($profil["type_fichier"]=="2" and $format !=4) $display="block"; else $display="none";
	print('<div id="combo_type_2" style="float:left;display:'.$display.'">'.getComboCodif("","champs_pret",0,$event)).'</div>';

	if($profil["type_fichier"]=="3" and $format !=4) $display="block"; else $display="none";
	print('<div id="combo_type_3" style="float:left;display:'.$display.'">'.getComboCodif("","champs_reservation",0,$event)).'</div>';

	print('&nbsp;&nbsp;<input type="button" class="bouton" value="Effacer" onclick="selectChamp(\''.$id_champ.'\',\'\')">');
	print('</td></tr>');
	print('<tr>');
	print('<td class="form" width="7%">Champs</td>');
	print('<td class="form"><div id="A'.$id_champ.'" style="background-color:#ffffff;font-weight:bold;padding:2px 5px 3px 5px;min-height:10px">'.$profil["attributs"][$format]["champs"].'&nbsp;</div>');
	print('<input type="hidden" name="champs" id="'.$id_champ.'" value="'.$profil["attributs"][$format]["champs"].'">');
	print('</td>');
	print('</tr>');
	
	print('</table></div>');
}

function paveXml($profil)
{
	$id_profil=$profil["id_profil"];
	$format=$profil["format"];
	$type_fichier=$profil["type_fichier"];

	// Bloc
	if( $format==4 ) $display="block"; else $display="none";
	print('<div id="fmt_xml" style="display:'.$display.';">');

	// ecran format pas supporté
	if($type_fichier != 1) $display="block"; else $display="none";
	print('<div id="xml_pas_supporte" style="display:'.$display.';padding:50px"><b>Le format Xml n\'est pas supporté pour ce type de fichier</b></div>');
	
	// Abonnés
	print('<table class="form" id="xml_abonne" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Description des champs</th></tr>');
	print('<tr><td class="form_first" align="right" width="50%">Balise abonné (sans les crochets)</td><td class="form">'.getChamp("xml_balise_abonne",$profil["attributs"][5]["xml_balise_abonne"],20).'</td></tr>');
	print('<tr><td class="form" colspan="2"><table width="500px" align="center">');
	print('<tr><td class="form" align="right"><b>Champ</b></td><td class="form"><b>Balise</b></td></tr>');
	foreach($profil["attributs"][5]["xml_champs_abonne"] as $champ=>$valeur)
	{
		print('<tr><td class="form" align="right">'.$champ.'</td><td class="form">'.getChamp("xml_abonne_".$champ,$valeur,20).'</td></tr>');
	}
	print('</table></td></tr>');
	print('</table></div>');
}

function getComboChampsExemplaires($name,$valeur_select)
{
	$combo='<select name="'.$name.'">';
	$combo.='<option value=""></option>';
	for($i=0; $i<10; $i++)
	{
		if($i==0) $code="#"; else $code=$i;
		if($valeur_select==$code) $selected=" selected"; else $selected="";
		$combo.='<option value="'.$code.'"'.$selected.'>995$'.$i.'</option>';
	}
	for($i=65; $i<91; $i++)
	{
		$clef=strtolower(chr($i));
		if($valeur_select==$clef) $selected=" selected"; else $selected="";
		$combo.='<option value="'.$clef.'"'.$selected.'>995$'.$clef.'</option>';
	}
	$combo.='</select>';
	return $combo;
}
?>