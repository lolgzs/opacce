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
// REINEXATION DES CLEFS D'ACCES (ISBN, EAN,ID_COMMERCIALE,CLEF_ALPHA et TOME_ALPHA)
////////////////////////////////////////////////////////////////////////////////////
	
include("_init_frame.php");

// Includes
require_once("classe_chronometre.php"); 
require_once("classe_unimarc.php");
require_once("classe_isbn.php");

// Instanciations
$chrono = new chronometre(); 
$unimarc = new notice_unimarc();

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
else $nb_total = $sql->fetchOne("select count(*) from notices");

// Jauge
print('<h1>Réindexation des identifiants (isbn,ean,no commercial, clef alpha, clef oeuvre, clef chapeau, no de partie)</h1>');
print(BR.'<div class="message_grand">Traitement en cours...</div>').BR;
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
print('<br><div>');
print('<span id="notice"></span>');
print('</div>');
flush();

// ----------------------------------------------------------------
// Boucle principale 
// ----------------------------------------------------------------
$resultat=$sql->prepareListe("select id_notice,unimarc from notices where id_notice > $pointeur_reprise Order by id_notice LIMIT 0,20000");
while($ligne=$sql->fetchNext($resultat)) 
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();
	$unimarc->ouvrirNotice($ligne["unimarc"],0);
	$notice=$unimarc->getNoticeIntegration();
	$data["isbn"]=$notice["isbn"];
	$data["ean"]=$notice["ean"];
	$data["id_commerciale"]=$notice["id_commerciale"];
	$data["clef_alpha"]=$notice["clef_alpha"];
	$data["clef_oeuvre"]=$notice["clef_oeuvre"];
	$data["clef_chapeau"]=$notice["clef_chapeau"];
	$data["tome_alpha"]=$notice["tome_alpha"];
	$sql->update("update notices set @SET@ where id_notice=".$ligne["id_notice"],$data);
	$pointeur_reprise=$ligne["id_notice"];
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
