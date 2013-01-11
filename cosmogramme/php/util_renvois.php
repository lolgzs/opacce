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
// REINEXATION DES RENVOIS D'AUTORITES AUTEURS ET MATIERE
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
$colonne_index=$_REQUEST["type_autorite"]."s";
$date_ref=date("Y")-1;
$date_ref.=date("-m-d");

if($_REQUEST["reprise"]=="oui") restaureContext();
else $nb_total = fetchOne("select count(*) from ".$table." where date_creation<'$date_ref'");

// Jauge
print('<h1>Réindexation des renvois d\'autorités : '.$_REQUEST["type_autorite"].'</h1>');
print(BR.'<div class="message_grand">Traitement en cours...</div>').BR;
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
print('<br><div>');
print('<span id="notice"></span>');
print('</div>'.BR);

// compteurs
print('<div><table class="blank">');
print('<tr><td class="blank">Echecs de connexion au service</td><td class="blank" align="right"><span id="c_erreur">&nbsp;</span></td></tr>');
print('<tr><td class="blank">Autorités trouvées et traitées</td><td class="blank" align="right"><span id="c_ok">&nbsp;</span></td></tr>');
print('<tr><td class="blank">Autorités non trouvées sur le serveur</td><td class="blank" align="right"><span id="c_not_found">&nbsp;</span></td></tr>');
print('<tr><td class="blank">Renvois trouvés</td><td class="blank" align="right"><span id="c_renvois">&nbsp;</span></td></tr>');
print('<tr><td class="blank">Notices modifiées</td><td class="blank" align="right"><span id="c_maj_notices">&nbsp;</span></td></tr>');
print('</table></div>');
flush();

// ----------------------------------------------------------------
// Boucle principale 
// ----------------------------------------------------------------
$resultat=$sql->prepareListe("select * from $table where $colonne_clef > $pointeur_reprise and date_creation<'$date_ref' Order by $colonne_clef LIMIT 0,20000");
while($ligne=$sql->fetchNext($resultat)) 
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();

	// appel au serveur de cache
	$args=array("format"=>"renvois");
	if($ligne["id_bnf"]) $args["id_bnf"]=$ligne["id_bnf"];
	else
	{
		if($ligne["formes"]) $args["libelle"]=str_replace("x","+",$ligne["formes"]);
		else $args["libelle"]=str_replace(" ","+",$ligne["code_alpha"]);
	}
	$response=communication::runService(5,$args,"tab");
	if($response["statut"]=="OK")
	{
		// renvois trouvés
		if($response["statut_recherche"]==2)
		{
			$compteur["ok"]++;
			
			// maj de la fiche autorite
			$renvois=$ix->getFulltext($response["formes"]);
			sqlExecute("update $table set mots_renvois='$renvois',id_bnf='".$response["id_bnf"]."' where $colonne_clef=".$ligne[$colonne_clef]);

			// on met a jour les notices
			if($response["clef_alpha"]!=$response["formes"])
			{
				$compteur["renvois"]++;
				$req="select id_notice,$colonne_index from notices where match(facettes) against('+".$clef_facette.$ligne[$colonne_clef]."')";
				$notices=fetchAll($req);
				if($notices)
				{
					$compteur["maj_notices"]+=count($notices);
					foreach($notices as $notice)
					{
						$data=array($notice[$colonne_index],$response["formes"]);
						$renvois=$ix->getFulltext($data);
						sqlExecute("update notices set $colonne_index='$renvois' where id_notice=".$notice["id_notice"]);
					}
				}				
			}
		}
		if($response["statut_recherche"]==1) $compteur["not_found"]++;
		sqlExecute("update $table set date_creation='".date("Y-m-d")."' where $colonne_clef=".$ligne[$colonne_clef]);
	}
	else
	{
		$compteur["erreurs"]++;
	}
	//tracedebug($response,true);

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
	redirection( "util_renvois.php?type_autorite=".$_REQUEST["type_autorite"]."&reprise=oui");
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
	$pct=number_format($pct, 1, '.', ' ');
	if($pct > $avance)
	{
		$avance=$pct;
		print('<script>');
		print("document.getElementById('pct').innerHTML='".$pct."%';");
		print("document.getElementById('jauge').style.width='".(int)$pct."%';");
		print("document.getElementById('c_erreur').innerHTML='".$compteur["erreurs"]."';");
		print("document.getElementById('c_ok').innerHTML='".$compteur["ok"]."';");
		print("document.getElementById('c_not_found').innerHTML='".$compteur["not_found"]."';");
		print("document.getElementById('c_renvois').innerHTML='".$compteur["renvois"]."';");
		print("document.getElementById('c_maj_notices').innerHTML='".$compteur["maj_notices"]."';");
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
