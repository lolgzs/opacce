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
//          CONFIGURATION DES VARIABLES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

?>

<h1>Configuration des variables</h1>
 
 <?PHP
////////////////////////////////////////////////////////////////////////////////////
// MODIFICATION DES PROPRIETES
////////////////////////////////////////////////////////////////////////////////////

$groupe_deploy=$_REQUEST["groupe"];

if($_REQUEST["action"]=="PROPERTY")
{
	// Lire l'enreg
	$clef=$_REQUEST["variable"];
	$req="select * from variables where clef='$clef'";
	$enreg=$sql->fetchEnreg($req);
	$type_champ[$enreg["type_champ"]]="selected";
  
 	// Formulaire
 	print('<br><h3>Mise à jour des propriétés</h3>');
  	print('<br><p>* nb : Pour les listes de valeurs : Entrez une option par ligne, sous la forme [CODE]:[LIBELLE]<br>exemple : m:Multimedia</p><br>');
	print('<div class="form" style="width:650px">');
	print('<form method="post" action="config_variables.php?action=VALIDER_PROP&groupe='.$groupe_deploy.'&variable='.$clef.'">');
	print('<table class="form" border="0" width="100%">');
	print('<tr><th class="form" colspan="2" height="30px" align="left"><b>Variable : '.$clef.'</b></th></tr>');
	print('<tr><td colspan="2" class="form">&nbsp;</td></tr>');
	print('<tr><td class="form" align="right">Type de champ</td><td class="form"><select name="var_type"><option value="0" '.$type_champ[0].'>Champ simple</option><option value="1" '.$type_champ[1].'>Champ multilignes</option><option value="2" '.$type_champ[2].'>Liste</option></select></td></tr>');
	print('<tr><td class="form" valign="top" align="right">Liste de valeurs</td><td class="form"><textarea name="var_liste" cols="50" rows="5">'.stripslashes($enreg["liste"]).'</textarea></td></tr>');
	print('<tr><td class="form" valign="top" align="right">Commentaire</td><td class="form"><textarea name="var_commentaire" cols="50" rows="5">'.stripslashes($enreg["commentaire"]).'</textarea></td></tr>');
	print('<tr><td class="form" align="right">Ordre d\'affichage</td><td class="form"><input type="text" name="var_ordre" size="3" value="'.$enreg["ordre"].'"></td></tr>');
	print('<tr><td class="form" align="right">Verrouillé</td><td class="form"><input type="checkbox" name="var_verrou" '.$enreg["verrou"].'></td></tr>');
	print('<tr><td colspan="2" class="form">&nbsp;</td></tr>');
  	print('</table></div>');
  
	print('<br><input type="submit" class="bouton" value="Valider les modifications">');
  	print('</form>');
  	print('</body></html>');
	exit;
}

////////////////////////////////////////////////////////////////////////////////////
// ENREGISTRER LES PROPRIETES
////////////////////////////////////////////////////////////////////////////////////

if($_REQUEST["action"]=="VALIDER_PROP")
{
	$clef=$_REQUEST["variable"];
	if($_POST["var_verrou"] == "on") $verrou="checked"; else $verrou="";
	$data=array("type_champ"=>$_POST["var_type"],"liste"=>$_POST["var_liste"],"commentaire"=>$_POST["var_commentaire"],"ordre"=>$_POST["var_ordre"],"verrou"=>$verrou);
	$sql->update("update variables set @SET@ Where clef='$clef'",$data);
}

////////////////////////////////////////////////////////////////////////////////////
// MODIFICATION DE LA VALEUR
////////////////////////////////////////////////////////////////////////////////////

if($_REQUEST["action"]=="VALIDER")
{
	$clef=$_REQUEST["variable"];
	$data=array("valeur"=>$_REQUEST["valeur_variable"]);
	$sql->update("update variables set @SET@ Where clef='$clef'",$data);
}

////////////////////////////////////////////////////////////////////////////////////
// EXPORT DU REFERENTIEL
////////////////////////////////////////////////////////////////////////////////////
if($_REQUEST["action"]=="EXPORT")
{
	print('<h3>Export du référentiel</h3>');
	print('<div class="info">Toutes les définitions de variables de ce site sont exportées sans les valeurs associées.<br><br>Ceci permet de mettre à jour le référentiel d\'un autre site Cosmogramme sans affecter les valeurs déjà renseignées.<br><br>Pour importer, vous devez copier le contenu du champ ci-dessous dans le presse-papiers, vous connecter au Cosmogramme du site à mettre à jour et l\'importer.</div>');
	print(BR.'<h3>Référentiel</h3>');
	$data=$sql->fetchAll("select * from variables");
	foreach($data as $enreg)
	{
		foreach($enreg as $colonne => $valeur) $bloc.=$colonne."|".$valeur.chr(9);
		$bloc.="§";
	}
	print('<textarea cols="80" rows="20" style="margin-left:20px">'.$bloc.'</textarea>');
	exit;
}

////////////////////////////////////////////////////////////////////////////////////
// IMPORT DU REFERENTIEL
////////////////////////////////////////////////////////////////////////////////////
if($_REQUEST["action"]=="IMPORT")
{
	print('<h3>Import de référentiel</h3>');
	if($_SESSION["passe"]!= "admin_systeme") afficherErreur("Vous devez être connecté en tant qu'administrateur système pour exécuter cette fonction"); 
	if($_REQUEST["mode"] == "LANCER")
	{
		$bloc=trim($_POST["data"]);
		if(!$bloc) afficherErreur("Il n'y a aucune donnée à traiter !");
		$data=explode("§",$bloc);
		foreach($data as $ligne)
		{
			// decoupage par enregs
			$champs=explode(chr(9),$ligne);
			$enreg=array();
			// decoupage colonne|valeur
			foreach($champs as $item)
			{
				$champ=explode("|",$item);
				$colonne=$champ[0];
				$valeur=$champ[1];
				if(!$colonne or $colonne=="valeur") continue;
				if($colonne == "clef")
				{
					print('<span class="violet" style="margin-left:10px"><b>&raquo;&nbsp;Variable : '.$valeur.'</b></span>'.BR); 
					$controle=$sql->fetchOne("select count(*) from variables where clef='$valeur'");
					if(!$controle) $mode="insert";
					else {$req="update variables set @SET@ where clef='$valeur'"; continue;}
				}
				$enreg[$colonne]=$valeur;
			}
			//ecriture
			if($mode == "insert") $sql->insert("variables",$enreg);
			else $sql->update($req,$enreg);
		}
		print(BR.'<h3>Traitement terminé</h3>'.BR.BR.'</body></html>');
		exit;
	}
	print('<div class="info">Pour importer, vous devez avoir exporté au préalable le référentiel d\'un autre site Cosmogramme dans le presse-papier.<br>Seules les définitions seront importées, les valeurs des variables ne seront pas affectées.<br>Pour procéder : collez le contenu du presse-papier dans le champ ci-dessous puis, cliquez le bouton : Lancer l\'import.</div>');
	print(BR.'<h3>Référentiel à importer</h3>');
	print('<form method="post" action="config_variables.php?action=IMPORT&mode=LANCER">');
	print('<textarea id="data" name="data" cols="80" rows="20" style="margin-left:20px"></textarea>');
	print(BR.BR.'<div class="liste"><center><input type="submit" class="bouton" value="Lancer l\'import"></div>');
	print('</form');
	exit;
}

////////////////////////////////////////////////////////////////////////////////////
// LISTE GENERALE
////////////////////////////////////////////////////////////////////////////////////

// Groupes
$lib_groupe[1]="Accès au moteur d'intégration";
$lib_groupe[2]="Codifications de base";
$lib_groupe[3]="Intégration : dates et fréquences";
$lib_groupe[4]="Intégration : paramétrage";
$lib_groupe[5]="Zones unimarc et champs";
$lib_groupe[6]="Pointeurs";
$lib_groupe[7]="Chemins et variables système";

// Requete
$data=$sql->fetchAll("select * from variables order by groupe,ordre");
foreach($data as $ligne)
{
	// Changement de groupe
	if($ligne["groupe"] != $oldGroupe)	
	{
		if($oldGroupe) print('</table></div><br>');
		$oldGroupe=$ligne["groupe"];
		$idGroupe="groupe".$oldGroupe;
		$img="plus.gif";
		$display="none";
		if($oldGroupe == $groupe_deploy)
		{
			$img="moins.gif";
			$display="block";
		}
		print('<table cellspacing="0" style="width:700px">');
		print('<tr><th align="center">');
		print('<img id="I'.$idGroupe.'" src="'.URL_IMG.$img.'" onclick="contracter_bloc(\''.$idGroupe.'\')" style="cursor:pointer">');
  	print('</th><th align="left" width="100%">'.$oldGroupe. ' - ' . $lib_groupe[$oldGroupe].'</th></tr></table>');
  	print('<div id="'.$idGroupe.'" style="width:700px;display:'.$display.'"><table cellspacing="0">');
	}
	
	// Type de champ
	switch($ligne["type_champ"])
	{
		case 1: $champ_valeur='<textarea name="valeur_variable" id="valeur_variable" cols="50" rows="3">'.$ligne["valeur"].'</textarea>'; break;
		case 2: 
			$champ_valeur='<select name="valeur_variable" id="valeur_variable">';
			$v=explode(chr(13).chr(10),$ligne["liste"]);
			if(! $v) // si pas de liste : champ normal
			{
				$champ_valeur='<input type="text" name="valeur_variable" id="valeur_variable" size="57" value="'.$ligne["valeur"].'">'; 
				break;
			}
			for($i=0; $i<count($v); $i++)
			{
				$elem=explode(":",$v[$i]);
				if(trim($elem[0])>"")
				{ 
					if($ligne["valeur"]==$elem[0]) $selected=" selected"; else $selected="";
					$champ_valeur.='<option value="'.$elem[0].'"'.$selected.'>'.stripSlashes($elem[1]).'</option>';
				}
			}
			$champ_valeur.='</select>';
			break;
		default : $champ_valeur='<input type="text" name="valeur_variable" id="valeur_variable" size="57" value="'.$ligne["valeur"].'">'; break;
	}
	if($oldGroupe == 2) $valider = "&nbsp;";
	else $valider='<span style="margin-left:10px";"><input type="submit" class="bouton" value="Valider"></span>';

	// Controle variables verrouillees
	$url="config_variables.php?action=PROPERTY&variable=".$ligne["clef"]."&groupe=".$oldGroupe;
	if($ligne["verrou"]=="checked" )	
	{
		$img="verrou.gif";
		if($_SESSION["passe"]!= "admin_systeme") {$valider = ""; $url='#" onclick="alert(\'Cette variable est verrouillée !\');"';}
	} 
	else $img="modif_fiche.gif";
	// Afficher item
	$modifier='<span style="margin-top:10px;"><a href="'.$url.'" align="center"><img src="'.URL_IMG.$img.'" border="0" title="Modifier les propriétés de cette variable">&nbsp;</a></span>';
	
	print('<tr>');
	print('<td width="1%" class="sans_ligne">&nbsp;</td>');
	print('<td width="15%" class="haut" style="vertical-align:middle;"><b>'.$ligne["clef"].'</b></td>');
	print('<form id="'.$ligne["clef"].'" method="post" action="config_variables.php?action=VALIDER&variable='.$ligne["clef"].'&groupe='.$oldGroupe.'">');
	print('<td width="80%" colspan="3" class="haut" style="vertical-align:middle;">'.$champ_valeur.$valider);
	print('</form></td></tr>');
	print('<tr><td>&nbsp;</td><td colspan="2" class="milieu" width="96%"><div class="commentaire">'.$ligne["commentaire"].'&nbsp;</div></td>');
	print('<td width="4%">'.$modifier.'</td>');
	print('</tr>');
}
print('</table></div>');

// Boutons export / import COMMENTE CAR TROP DANGEREUX
//print(BR.BR.'<div class="liste"><center><a href="config_variables.php?action=EXPORT">&raquo;&nbsp;Exporter le référentiel');
//print('<a href="config_variables.php?action=IMPORT" style="margin-left:20px">&raquo;&nbsp;Importer un référentiel</a></div>');
print('</body></html>');
	
?>