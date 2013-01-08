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
//////////////////////////////////////////////////
// JOURNAL DES INTEGRATIONS
//////////////////////////////////////////////////
include("_init_frame.php");

// Instanciations
require_once("classe_bib.php");
$oBib = new bibliotheque();
require_once("classe_parseur.php");
$parseur=new parseur();
require_once("classe_unimarc.php");
$unimarc=new notice_unimarc();
require_once("classe_notice_ascii.php");
$ascii=new notice_ascii();
require_once("classe_isbn.php");

// Titre page
if($_REQUEST["date"]) $aff_date=" du ".rendDate($_REQUEST["date"],3);
print("<h1>Journal des intégrations".$aff_date."</h1>");
print('<div align="center">');
flush();

// ----------------------------------------------------------------
// Entete pour les details
// ----------------------------------------------------------------
if( $_REQUEST["id_integration"])
{
	// Lire enreg integration
	$enreg=$sql->fetchEnreg("select * from integrations where id=".$_REQUEST["id_integration"]);
	$format=$sql->fetchOne("select format from profil_donnees where id_profil=".$enreg["profil"]);
	$erreurs=stripslashes($enreg["erreurs"]);
	$erreurs=unserialize($erreurs);
	if(!$enreg["nb_erreurs"]) $enreg["nb_erreurs"]="aucune";
	$warnings=stripslashes($enreg["warnings"]);
	$warnings=unserialize($warnings);
	if(!$enreg["nb_warnings"]) $enreg["nb_warnings"]="aucune";
	
	// Ouvrir le fichier
	$fichier=getVariable("integration_path").$enreg["fichier"];
	if(!file_exists($fichier)) quit("Le fichier d'intégration a été effacé");
	$parseur->open($fichier,$format,0);
	
	// Entete
	print('<span class="violet">Bibliothèque : </span><span class="vert">'.$oBib->getNomCourt($enreg["id_bib"]).'</span>'.BR);
	print('<span class="violet">Fichier : </span><span class="vert">'.$enreg["fichier"].'</span>'.BR);
	print('<span class="violet">Intégré le : </span><span class="vert">'.rendDate($enreg["traite"],2).'</span>'.BR);
	print('<span class="violet">Type d\'opération : </span><span class="vert">'.getLibCodifVariable("import_type_operation",$enreg["type_operation"]).'</span>'.BR);
	print('<span class="violet">Profil d\'import : </span><span class="vert">'.$sql->fetchOne("select libelle from profil_donnees where id_profil=".$enreg["profil"]).'</span>'.BR);
	print('<span class="violet">Format de fichier : </span><span class="vert">'.getLibCodifVariable("import_format",$format).'</span>'.BR);
}
	
// ----------------------------------------------------------------
// Liste des notices
// ----------------------------------------------------------------
if( $_REQUEST["mode"] == "DETAIL")
{
	$type=$_REQUEST["type"];
	$rubrique=urldecode($_REQUEST["rubrique"]);
	print(BR.'<div class="analyse">'.$type);
	if($rubrique) print(' : '.$rubrique); 
	print('</div>');
	
	// Liste
	if($type == "Erreurs")
	{
		foreach($erreurs as $libelle => $liste)
		{
			if($rubrique and $libelle != $rubrique) continue;
			if($format == 0) printRubriqueUnimarc($fichier,"erreurs", $libelle,$enreg["profil"], $liste);
			else printRubriqueAscii($fichier,"erreurs", $libelle,$enreg["profil"], $liste);
		}
	}
	if($type == "Anomalies")
	{
		foreach($warnings as $libelle => $liste)
		{
			if($rubrique and $libelle != $rubrique) continue;
			if($format == 0) printRubriqueUnimarc($fichier,"warnings", $libelle,$enreg["profil"], $liste);
			else printRubriqueAscii($fichier,"warnings", $libelle,$enreg["profil"], $liste);
		}
	}
	
	// Fini
	$bouton_retour='<input type="button" class="bouton" value="Retour" onclick="document.location.replace(\'integre_journal_integrations.php?mode=SYNTHESE&id_integration='.$_REQUEST["id_integration"]."&date=".$_REQUEST["date"].'\')" style="margin-left:20px">';
	print(BR.$bouton_retour.BR.BR);
	quit("");
}

// ----------------------------------------------------------------
// Detail pour un fichier
// ----------------------------------------------------------------
if($_REQUEST["mode"] == "SYNTHESE")
{
	// Notices
	print(BR.'<div class="analyse">Notices</div><table class="blank" style="margin-left:20px;width:300px" cellspacing="0">');
	printLigneTableau($enreg["id"],0,"Notices traitées",$enreg["pointeur_reprise"]);
	printLigneTableau($enreg["id"],0,"Erreurs (notices rejetées)",$enreg["nb_erreurs"]);
	printLigneTableau($enreg["id"],0,"Anomalies",$enreg["nb_warnings"]);
	print('</table>');
	
	// Erreurs
	if($erreurs)
	{
		print(BR.'<div class="analyse">Erreurs (notices rejetées)</div><table class="blank" style="margin-left:20px;width:300px" cellspacing="0">');
		foreach($erreurs as $libelle => $liste)	printLigneTableau($enreg["id"],"Erreurs",$libelle,count($liste));
	}
	print('</table>');
	
	// Warnings
	if($warnings)
	{
		print(BR.'<div class="analyse">Anomalies</div><table class="blank" style="margin-left:20px;width:300px" cellspacing="0">');
		foreach($warnings as $libelle => $liste) printLigneTableau($enreg["id"],"Anomalies",$libelle,count($liste));
	}
	print('</table>');
	
	// Fini
	print('</table></div>');
	$bouton_retour='<input type="button" class="bouton" value="Retour" onclick="document.location.replace(\'integre_journal_integrations.php'."?date=".$_REQUEST["date"].'\')" style="margin-left:20px">';
	print(BR.$bouton_retour.BR.BR);
	quit("");
}

// ----------------------------------------------------------------
// AFFICHAGE DE LA LISTE 
// ----------------------------------------------------------------
else
{
  // Entête
	print('<table style="width:800px;margin-left:20px">');
	print('<tr>
 	<th width="15%" colspan="2" align="left">Date</th>
 	<th width="30%" align="left">Bibliothèque</th>
	<th width="20%" align="left">Type de fichier</th>
 	<th width="20%" align="left">Type de transaction</th>
	<th width="20%" align="left">Fichier</th>
 	<th width="9%" align="left">Fiches traitées</th>
 	<th width="9%" align="left">Erreurs</th>
 	<th width="9%" align="left">Anomalies</th>
	</tr>');
  
  // Lire les integrations
  if($_REQUEST["date"]) $cond = " and traite='".$_REQUEST["date"]."' ";
  $req="select * from integrations where traite != 'non' ".$cond."order by id DESC";
	$liste=$sql->fetchAll($req);

  foreach ($liste as $ligne) 
	{ 
   	$sql_date=rendDate($ligne["traite"],1);
   	$nb_notices=number_format($ligne["pointeur_reprise"], 0, ',', ' ');
    $sql_type=getLibCodifVariable("import_type_operation",$ligne["type_operation"]);
    $nom_bib=$oBib->getNomCourt($ligne["id_bib"]);
		$type_fichier=getLibCodifVariable("type_fichier",$sql->fetchOne("select type_fichier from profil_donnees where id_profil=".$ligne["profil"]));
    $loupe=rendUrlImg("loupe.png", "integre_journal_integrations.php","mode=SYNTHESE&id_integration=".$ligne["id"]."&date=".$_REQUEST["date"],"Afficher le détail");
			
		print ("<tr>");
		print ('<td align="center">'.$loupe.'</td>');
		print ('<td align="center">'.$sql_date.'</td>');
		print ("<td>(".$ligne["id_bib"].")&nbsp;$nom_bib</td>");
		print ("<td>$type_fichier</td>");
		print ("<td>$sql_type</td>");
		print ("<td>".$ligne["fichier"]."</td>");
		print ('<td align="right">'.$nb_notices.'</td>');
		print ('<td align="right">'.$ligne["nb_erreurs"].'</td>');
		print ('<td align="right">'.$ligne["nb_warnings"].'</td>');
		print("</tr>");
	}

	// Fini
	print('</table></div>');
	$bouton_retour='<input type="button" class="bouton" value="Retour" onclick="document.location.replace(\'integre_log.php\')" style="margin-left:20px">';
	print(BR.$bouton_retour.BR.BR);
	quit("");

}

// ----------------------------------------------------------------
// FONCTIONS D'AFFICHAGE
// ----------------------------------------------------------------
function printLigneTableau($id_integration,$type,$libelle,$nombre)
{
	if($nombre > 0) $nombre=number_format($nombre, 0, ",", " ");
	else $nombre="aucune";
	$loupe=rendUrlImg("loupe.png", "integre_journal_integrations.php","id_integration=".$id_integration."&mode=DETAIL&type=".$type."&rubrique=".urlencode($libelle)."&date=".$_REQUEST["date"],"Afficher le détail");
	print('<tr>');
	if($type) print('<td class="blank" align="center">'.$loupe.'</td>');
	print('<td class="blank">'.$libelle.'</td><td class="blank" align="right">'.$nombre.'</td>');
	print('</tr>');
}

function printRubriqueUnimarc($fichier, $type, $rubrique, $id_profil, $notices)
{
	if(!$notices) return;
	global $parseur,$unimarc;
	
	// Entete
	print('<table class="blank" style="margin-left:20px;width:800px" cellspacing="0">');
	print('<tr><th class="blank" colspan="2">no</th>');
	print('<th class="blank">Titre principal</th>');
	print('<th class="blank">Codes-barres</th>');
	if($type == "warnings") print('<th class="blank">Anomalie</th>');
	print('</tr>');
	
	// Rubrique
	$num=0;
	foreach($notices as $enreg)
	{
		$num++;
		$data=explode(chr(9), $enreg);
		
		// Lire notice
		$ret=$parseur->getEnreg($data[0]);
		$unimarc->ouvrirNotice($ret["data"],$id_profil); 
		$notice=$unimarc->getNoticeIntegration();
				
		// Recup données a afficher
		$titre=$notice["titre_princ"];
		if(!$titre) $titre = "** pas de titre principal **";
		$cab="";
		for($i=0; $i< count($notice["exemplaires"]); $i++)
		{
			if($notice["exemplaires"][$i]["code_barres"]) $cab.="[".$notice["exemplaires"][$i]["code_barres"]."] ";
		}
		if(!$cab) $cab="aucun exemplaire";
		
		// Afficher
		$loupe=rendUrlImg("loupe.png", "analyse_afficher_notice.php","mode=INTEGRATION&fichier=".urlencode($fichier)."&adresse=".$data[0]."&date=".$_REQUEST["date"],"Afficher la notice d'origine");
		print('<tr>');
		print('<th class="blank" align="center">'.$loupe.'</th>');
		print('<th class="blank" align="center">'.$num.'</th>');
		print('<td class="blank">'.$titre.'</td>');
		print('<td class="blank">'.$cab.'</td>');
		if($type == "warnings") print('<td class="blank">'.$rubrique.' : '.$data[1].'</td>');
		print('</tr>');
	}
	print('</table>');
}

function printRubriqueAscii($fichier, $type, $rubrique, $id_profil, $notices)
{
	if(!$notices) return;
	global $parseur,$ascii;
	
	// Entete
	print('<table class="blank" style="margin-left:20px;width:800px" cellspacing="0">');
	
	// Rubrique
	$num=0;
	foreach($notices as $enreg)
	{
		$num++;
		$data=explode(chr(9), $enreg);
		
		// Lire notice
		$ret=$parseur->getEnreg($data[0]);
		$ascii->ouvrirNotice($ret["data"],$id_profil); 
		$notice=$ascii->getEnreg();
				
		// Afficher entete
		if($num == 1)
		{
			print('<tr><th class="blank">no</th>');
			foreach($notice as $champ => $valeur)
			{
				if($champ == "data") continue;
				print('<th class="blank">'.$champ.'</th>');
			}
		if($type == "warnings") print('<th class="blank">Anomalie</th>');
		print('</tr>');
		}
		
		// Afficher ligne
		print('<tr>');
		print('<th class="blank">'.$num.'</th>');
		foreach($notice as $champ => $valeur)
		{
			if($champ == "data") continue;
			if(!$valeur) $valeur="&nbsp;";
			print('<td class="blank">'.$valeur.'</td>');
		}
		if($type == "warnings") print('<td class="blank">'.$rubrique.' : '.$data[1].'</td>');
		print('</tr>');
	}
	print('</table>');
}

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}
?>