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
///////////////////////////////////////////////////////////////////
//
//        CODIFS : ANNEXES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once( "fonctions/objets_saisie.php");
require_once( "classe_bib.php");
$cls_bib=new bibliotheque();

?>

<h1>Codification des annexes</h1>
 
<?php

//---------------------------------------------------------------------------------
// CREER
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="CREER")
{
	$action=array("libelle" => "** nouvelle annexe **");
	print(BR.BR.BR);
	afficherAnnexe($action,"block");
	print('</form></body></html>');
	exit;
}
//---------------------------------------------------------------------------------
// VALIDER
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="VALIDER")
{	
	// ecriture
	if(!isset($_POST["invisible"])) $_POST["invisible"]="0";
	if(!isset($_POST["no_pickup"])) $_POST["no_pickup"]="0";
	$id_annexe=$_POST["id_annexe"];
	if(!$id_annexe) $sql->insert("codif_annexe",$_POST);
	else
	{
		unset($_POST["id_annexe"]);
		$sql->update("update codif_annexe set @SET@ where id_annexe=$id_annexe",$_POST);
	}
}

//---------------------------------------------------------------------------------
// SUPPRIMER
//---------------------------------------------------------------------------------

if($_REQUEST["action"]=="SUPPRIMER")
{
	$id_annexe=$_REQUEST["id_annexe"];
	if($id_annexe)	$sql->execute("delete from codif_annexe where id_annexe =$id_annexe");
}

//---------------------------------------------------------------------------------
// LISTE 
//---------------------------------------------------------------------------------
print('<div class="liste">');

$liste=$sql->fetchAll("Select * from codif_annexe order by libelle");
for($p=0; $p< count($liste); $p++)
{
	$annexe=$liste[$p];
	$img="plus.gif";
	$display="none";
	if($annexe["id_annexe"] == $_REQUEST["id_annexe"])
		{
			$img="moins.gif";
			$display="block";
		}
		print('<div class="liste_img"><img id="Iannexe'.$annexe["id_annexe"].'" src="'.URL_IMG.$img.'" onclick="contracter_bloc(\'annexe'.$annexe["id_annexe"].'\')" style="cursor:pointer"></div>');
		print('<div class="liste_titre">'. $annexe["libelle"].'</div>');
		afficherAnnexe($annexe,$display);
}
print('</div>');
// Bouton ajouter
$bouton_ajout=rendBouton("Ajouter une annexe","codif_annexe","action=CREER");
print(BR.BR.$bouton_ajout);

print('</body></html>');
exit;

function afficherAnnexe($annexe,$display)
{
	global $cls_bib;
	print('<div class="form" id="annexe'.$annexe["id_annexe"].'" style="width:600px;margin-left:20px;display:'.$display.'">');
	print('<form method="post" action="'.URL_BASE.'php/codif_annexe.php?action=VALIDER">');
	print('<input type="hidden" name="id_annexe" value="'.$annexe["id_annexe"].'">');
	print('<table class="form" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Création d\'annexe</th></tr>');
	print('<tr><td class="form_first" align="right" width="35%">Bibliothèque</td><td class="form_first">'.$cls_bib->getComboNoms($annexe["id_bib"]).'</td></tr>');
	print('<tr><td class="form_first" align="right" width="35%">Code</td><td class="form_first">'.getChamp("code",$annexe["code"],50).'</td></tr>');
	print('<tr><td class="form_first" align="right" width="35%">Libellé</td><td class="form_first">'.getChamp("libelle",$annexe["libelle"],43).'</td></tr>');

	$checked = (1 == $annexe["invisible"]) ? "checked" : "";
	$inputId = 'invisible_' . $annexe['id_annexe'];
	print('<tr><td class="form_first" align="right" width="35%"><label for="' . $inputId . '">Rejeter les exemplaires</label></td>'
				.'<td class="form_first"><input id="' . $inputId . '" type="checkbox" value="1" '.$checked.' name="invisible"></td></tr>');

	$checked = (1 == $annexe["no_pickup"]) ? "checked" : "";
	$inputId = 'no_pickup_' . $annexe['id_annexe'];
	print('<tr><td class="form_first" align="right" width="35%"><label for="' . $inputId . '">Exclu du PEB </label></td>'
				.'<td class="form_first"><input id="' . $inputId . '" type="checkbox" value="1" '.$checked.' name="no_pickup"></td></tr>');
	
	// Boutons maj
	print('<tr><th class="form" colspan="2" align="center">');
	$bouton_valider='<input type="submit" class="bouton" value="Valider">';
	$bouton_supprimer=rendBouton("Supprimer","codif_annexe","action=SUPPRIMER&id_annexe=".$annexe["id_annexe"]);
	print($bouton_valider.str_repeat("&nbsp;",5).$bouton_supprimer);
	print('</th></tr></table></form></div>');
}

function erreurFormat($erreur)
{
	print('<span class = "rouge">'.$erreur.'</span>');
	$action=$_POST;
	print(BR.BR.BR);
	afficherAnnexe($action,"block");
	print('</form></body></html>');
	exit;
}
?>