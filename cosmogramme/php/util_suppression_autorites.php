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
/////////////////////////////////////////////////////////////////////////
// SUPPRESSION D'AUTORITES NON UTILISEES (AUTEURS ET MATIERE)
/////////////////////////////////////////////////////////////////////////
	
include("_init_frame.php");

// Includes
require_once("classe_chronometre.php"); 
require_once("classe_indexation.php");
require_once("classe_communication.php");
require_once("fonctions/objets_saisie.php");

// Instanciations
$chrono = new chronometre(); 
$ix = new indexation();

// Initialisations
$type_autorite=$_REQUEST["type_autorite"];
$nb_notices=0;
$nb_total=0;
$compteur=array();
$avance=-1;
$pointeur_reprise=0;
$timeout=intval(ini_get("max_execution_time") * 0.70);
$timeStart=time();
$chrono->start();

// ----------------------------------------------------------------
// Début du traitement
// ----------------------------------------------------------------
$table="codif_".$_REQUEST["type_autorite"];
$colonne_clef="id_".$_REQUEST["type_autorite"];
$clef_facette=strtoupper($_REQUEST["type_autorite"][0]);

if($_REQUEST["reprise"]=="oui") restaureContext();
else $nb_total = fetchOne("select count(*) from ".$table);

// Jauge
print('<h1>Suppression d\'autorités non utilisées : '.$_REQUEST["type_autorite"].'</h1>');
print(BR.'<div class="message_grand">Traitement en cours...</div>').BR;
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
print('<br><div>');
print('<span id="notice"></span>');
print('</div>'.BR);

// compteurs
print('<div><table class="blank">');
print('<tr><td class="blank">Autorités utilisées</td><td class="blank" align="right"><span id="c_ok">&nbsp;</span></td></tr>');
print('<tr><td class="blank">Autorités supprimées</td><td class="blank" align="right"><span id="c_supprime">&nbsp;</span></td></tr>');
print('</table></div>');
flush();

// ----------------------------------------------------------------
// Boucle principale 
// ----------------------------------------------------------------
$resultat=$sql->prepareListe("select * from $table where $colonne_clef > $pointeur_reprise Order by $colonne_clef LIMIT 0,20000");
while($ligne=$sql->fetchNext($resultat)) 
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();

	// controle utilisation
	$req="select count(*) from notices where match(facettes) against('+".$clef_facette.$ligne[$colonne_clef]."')";
	$controle=fetchOne($req);
	if($controle>0) $compteur["ok"]++;
	else
	{
		$compteur["supprime"]++;
		sqlExecute("delete from $table where $colonne_clef=".$ligne[$colonne_clef]);
	}

	// pointeurs
	$pointeur_reprise=$ligne[$colonne_clef];
	$nb_notices++;
	afficherAvance($nb_notices,$nb_total);
}
if($nb_notices<$nb_total) sauveContexte();

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
	global $timeStart,$pointeur_reprise,$type_autorite;
	global $nb_notices,$nb_total,$compteur;

	$data=compact("timeStart","pointeur_reprise","nb_notices","nb_total","notice_depart","type_autorite","compteur");
	$_SESSION["reprise"]=$data;
	redirection( "util_suppression_autorites.php?type_autorite=".$_REQUEST["type_autorite"]."&reprise=oui");
}

function restaureContext()
{
	global $timeStart,$pointeur_reprise,$type_autorite;
	global $nb_notices,$nb_total,$compteur;
	
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
}

function afficherAvance($pointeur,$nb_total)
{
	global $avance,$compteur;
	$pct=(($pointeur / $nb_total) * 100);
	$pct=number_format($pct, 0, '.', ' ');
	if($pct > $avance)
	{
		$avance=$pct;
		print('<script>');
		print("document.getElementById('pct').innerHTML='".$pct."%';");
		print("document.getElementById('jauge').style.width='".(int)$pct."%';");
		print("document.getElementById('c_ok').innerHTML='".$compteur["ok"]."';");
		print("document.getElementById('c_supprime').innerHTML='".$compteur["supprime"]."';");
		print('</script>');
	}
	if($pointeur % 10 == 0 or $pct==100)
	{
		print('<script>');
		print("document.getElementById('notice').innerHTML='".$pointeur." / ".$nb_total."';");
		print('</script>');
	}
	flush();
}
?>
