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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PAGE D'ACCUEIL
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_bib.php");
$cls_bib=new bibliotheque();

print("<h1>Administration</h1>".BR);
//---------------------------------------------------------------------------------
// Numero de version
//---------------------------------------------------------------------------------
print('<div class="analyse">Version Cosmogramme :</div>');
afficherLigne(false,"Version : ".VERSION_COSMOGRAMME);
$niveau_client=getVariable("patch_level");
if($niveau_client < PATCH_LEVEL) afficherLigne(true,"Vous devez éxécuter une <a href='util_patch_sgbd.php'style='color:red;text-decoration:underline;font-size:13px'>mise à niveau de la base de données</a>. Niveau de patch : ".$niveau_client."/".PATCH_LEVEL);

//---------------------------------------------------------------------------------
// Acces
//---------------------------------------------------------------------------------
print('<div class="analyse">Accès :</div>');
switch($_SESSION["passe"])
{
	case "admin_systeme": afficherLigne(false,"Accès : Administrateur système"); break;
	case "admin_portail": afficherLigne(false,"Accès : Administrateur du portail"); break;
	case "catalogueur": afficherLigne(false,"Accès : Catalogueur"); break;
}

//---------------------------------------------------------------------------------
// Statut base
//---------------------------------------------------------------------------------
print(BR.'<div class="analyse">Dernière intégration :</div>');
$date_integration=getVariable("integration_date");
afficherLigne(false,"Dernier traitement d'intégration effectué le : " .rendDate($date_integration,1)); 
$statut=getVariable("clef_traitements");
if($statut=="1") 
{
	afficherLigne(true,"La base est bloquée (Phase de traitement : ".getVariable("traitement_phase").")</h3>");
}

//---------------------------------------------------------------------------------
// Ecart entre traitements
//---------------------------------------------------------------------------------
$ecart=ecartDates(dateDuJour(0),$date_integration);
$frequence = (int)getVariable("integration_frequence");
if($ecart > $frequence And $frequence > 0)
{
	afficherLigne(true,"Les traitements d'intégration n'ont pas été effectués depuis ".$ecart." jours</h3>");
}

//---------------------------------------------------------------------------------
// Nombre de fichiers en attente
//---------------------------------------------------------------------------------
$nombre=$sql->fetchOne("select count(*) from integrations where traite = 'non'");
if($nombre == 0) afficherLigne(false,"Tous les fichiers en attente ont été intégrés.");
else afficherLigne(false,"Il reste " . $nombre . " fichier(s) en attente d'intégration."); 

//---------------------------------------------------------------------------------
// Notices succintes et en attente d'homogeneisation
//---------------------------------------------------------------------------------
$retries=getVariable("Z3950_retry_level");
$qualite=getVariable("homogene_code_qualite");
$nombre=$sql->fetchOne("select count(*) from notices_succintes");
$nombre1=$sql->fetchOne("select count(*) from notices_succintes where z3950_retry <= $retries");
afficherLigne(false,"Il reste " . $nombre . " notices succintes dont ".$nombre1." en attente de traitement (retry_level=".$retries.")."); 

//---------------------------------------------------------------------------------
// Bibs qui ont du retard
//---------------------------------------------------------------------------------
print(BR.'<div class="analyse">Retards d\'envoi de fichiers :</div>');
$bibs=$cls_bib->getListeRetardIntegration();
if($bibs)
{
	foreach($bibs as $bib)
	{
		print('<h3 style="margin-left:20px;margin-bottom:5px">&raquo;&nbsp;'.$bib["nom_court"]." : </h3>");
		print('<table class="blank" style="margin-left:35px;margin-top:0px">');
		print('<tr><td class="blank">dernière date d\'envoi</td><td class="blank">'.$bib["dernier_ajout"].'</td></tr>');
		print('<tr><td class="blank">Ecart maximum prévu</td><td class="blank">'.$bib["ecart_ajouts"].' jour(s)</td></tr>');
		print('<tr><td class="blank">Retard</td><td class="blank">'.$bib["retard"].'</td></tr>');
		print('<tr><td class="blank">Mail envoyé le</td><td class="blank">'.$bib["date_mail"].'</td></tr>');
		print('</table>');
	}
}
else afficherLigne(false,"Toutes les bibliothèques à alerter sont à jour de leurs envois de fichiers");

//---------------------------------------------------------------------------------
// Apuration et comptage des paniers d'integrations
//---------------------------------------------------------------------------------
print(BR.'<div class="analyse">Informations système :</div>');
$path=getVariable("integration_path");
$nb_max=getVariable("integre_max_paniers");
if(!$nb_max) afficherLigne("La variable integre_max_paniers n'est pas définie.");
$nb=$sql->fetchOne("select count(*) from integrations where traite != 'non' and destroy=0");
if($nb > $nb_max)
{
	$liste=$sql->fetchAll("select id,fichier from integrations where traite != 'non' and destroy=0 order by id desc LIMIT $nb_max,10000");
	foreach($liste as $enreg)
	{
		$fic=$path.$enreg["fichier"];
		if(file_exists($fic)) { unlink($fic); $nb_destroy++; }
		$sql->execute("update integrations set destroy=1 where id=".$enreg["id"]);
	}
}
$taille=0;
$liste=$sql->fetchAll("select fichier from integrations where traite != 'non' and destroy=0");
foreach($liste as $enreg)
{
	$fic=$path.$enreg["fichier"];
	if(file_exists($fic)) $taille+=filesize($fic);
}
$taille=$taille / 1024;
$taille=number_format($taille, 0, ',', ' ')." ko";
afficherLigne(false,"Il y a ".($nb - $nb_destroy)." paniers conservés en historique pour une taille de : ".$taille);

//---------------------------------------------------------------------------------
// Taille et nombre de logs
//---------------------------------------------------------------------------------
require_once("classe_log.php");
$log=new Class_log("");
$ret=$log->getTailleLogs();
afficherLigne(false,"Il y a ".$ret["nb_fic"]." logs conservés en historique pour une taille de : ".$ret["taille"]);

//---------------------------------------------------------------------------------
// Fin html
//---------------------------------------------------------------------------------
print('</body></html>');

function afficherLigne($erreur, $texte)
{
	if($erreur == true ) $class=' class="erreur"';
	print('<h3' .$class.' style="margin-left:20px">&raquo;&nbsp;'.$texte.'</h3>');
}

?>