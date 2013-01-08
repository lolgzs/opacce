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
////////////////////////////////////////////////////////////////////////////////////
// REINDEXATION DES CLEFS NOTICES DANS LES PANIERS ET AVIS
////////////////////////////////////////////////////////////////////////////////////
	
include("_init_frame.php");

// Includes
require_once("classe_chronometre.php"); 
require_once("classe_unimarc.php");
require_once("classe_isbn.php");

// Instanciations
$chrono = new chronometre(); 

// Initialisations
$nb_notices=0;
$nb_total=0;
$avance=-1;
$pointeur_reprise=0;
$timeout=intval(ini_get("max_execution_time") * 0.75);
$timeStart=time();
$chrono->start();

// ----------------------------------------------------------------
// Début du traitement
// ----------------------------------------------------------------
if($_REQUEST["reprise"]=="oui") restaureContext();
else
{
	$nb_total = $sql->fetchOne("select count(*) from notices_paniers");
	$nb_total += $sql->fetchOne("select count(*) from notices_avis");
}

// Jauge
print('<h1>Réindexation des paniers de notices et des avis sur les notices</h1>');
print(BR.'<div class="message_grand">Traitement en cours...</div>').BR;
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
print('<br><div>');
print('<span id="notice"></span>');
print('</div>');
flush();

// ----------------------------------------------------------------
// Paniers
// ----------------------------------------------------------------
$resultat=$sql->prepareListe("select ID_PANIER,ID_USER,NOTICES from notices_paniers where id_panier > $pointeur_reprise Order by id_panier");
while($ligne=$sql->fetchNext($resultat)) 
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();
	if(!$ligne["NOTICES"]) continue;
	$clefs="";
	$notices=explode(";",$ligne["NOTICES"]);
	foreach($notices as $id_notice)
	{
		if(!trim($id_notice)) continue;
		if(strlen($id_notice) < 8)	$clef_alpha=$sql->fetchOne("select clef_alpha from notices where id_notice=$id_notice");
		else $clef_alpha=$id_notice;
		if($clef_alpha) $clefs.=";".$clef_alpha.";";
	}
	$sql->execute("update notices_paniers set NOTICES='$clefs' where ID_USER=".$ligne["ID_USER"]." and ID_PANIER=".$ligne["ID_PANIER"]);
	$pointeur_reprise=$ligne["id_panier"];
	$nb_notices++;
	afficherAvance($nb_notices,$nb_total);
}

// ----------------------------------------------------------------
// Avis
// ----------------------------------------------------------------
$pointeur_reprise=0;
$resultat=$sql->prepareListe("select ID_USER,ID_NOTICE from notices_avis where id_user >= $pointeur_reprise Order by id_user");
while($ligne=$sql->fetchNext($resultat))
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();
	$id_user=$ligne["ID_USER"];
	$id_notice=$ligne["ID_NOTICE"];
	$clef_oeuvre=$sql->fetchOne("select clef_oeuvre from notices where id_notice=$id_notice");
	$sql->execute("update notices_avis set CLEF_OEUVRE='$clef_oeuvre' where ID_USER=$id_user and ID_NOTICE=$id_notice");
	$pointeur_reprise=$id_user;
	$nb_notices++;
	afficherAvance($nb_notices,$nb_total);
}

// ----------------------------------------------------------------
// Fin
// ----------------------------------------------------------------
afficherAvance($nb_total,$nb_total);
$chrono->timeStart=$timeStart;
print("<h4>Traitement terminé.</h4>");
print('Temps de traitement : '.$chrono->end().BR);

print('</body></html>');
exit;

// ----------------------------------------------------------------
// Gestion du contexte pour les timeout
// ----------------------------------------------------------------
function sauveContexte()
{
	global $timeStart,$pointeur_reprise;
	global $nb_notices,$nb_total;

	$data=compact("timeStart","pointeur_reprise","nb_notices","nb_total");
	$_SESSION["reprise"]=$data;
	redirection( "util_indexation.php?reprise=oui");
}

function restaureContext()
{
	global $timeStart,$pointeur_reprise;
	global $nb_notices,$nb_total;
	
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
}

function afficherAvance($pointeur,$nb_total)
{
	global $avance;
	$pct=(int)(($pointeur / $nb_total) * 100);
	if($pct > $avance)
	{
		$avance=$pct;
		print('<script>');
		print("document.getElementById('pct').innerHTML='".$pct."%';");
		$jauge="document.getElementById('jauge').style.width='".$pct."%';";
		print($jauge);		
		print('</script>');
	}
	if($pointeur % 100 == 0 or $pct==100)
	{
		print('<script>');
		print("document.getElementById('notice').innerHTML='".$pointeur." / ".$nb_total."';");
		print('</script>');
	}
	flush();
}
?>
