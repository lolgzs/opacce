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
// TEST D'UN FICHIER UNIMARC
/////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

// Includes
require_once("classe_chronometre.php");
require_once("classe_parseur.php");
require_once("classe_notice_integration.php");

// Instanciations
$chrono = new chronometre();
$parseur = new parseur();
$notice = new notice_integration();

// ----------------------------------------------------------------
//  Initialisations
// ----------------------------------------------------------------
print('<h1>Analyse de fichier unimarc (mode synthèse)</h1>');
$fichier=$_REQUEST["fichier"];
$profil_unimarc=$_REQUEST["profil"];
$timeStart = time();
$pointeur_reprise=0;
$compteur=array();
$timeout=intval(ini_get("max_execution_time") * 0.75);
if(!$timeout) $timeout=25;
$avance=-1;

if($_REQUEST["action"]=="INIT")
{
	unset($_SESSION["reprise"]);
	$sql->execute("delete from int_analyse where date_analyse < '".dateDuJour(0)."'");
	$id=$_REQUEST["PHPSESSID"];
	$sql->execute("delete from int_analyse where id='$id'");
}
else extract($_SESSION["reprise"]);

// ----------------------------------------------------------------
// Si c'est fini on affiche la synthese
// ----------------------------------------------------------------
if($_REQUEST["action"] == "SYNTHESE")
{
	print('<div class="analyse"><b>Rapport de l\'analyse</b></div>');
	print('<div style="margin-left:20px">');
	print('<font color="purple">Fichier : </font>'.$fichier.BR);
	$chrono->timeStart=$timeStart;
	print('<font color="purple">Traitement : </font>'.$chrono->end().BR);
	print('<font color="purple">Moyenne : </font>'.$chrono->moyenne($compteur["statut"]["nb_notices"],"notices").BR);
	print('</div>');
	print(BR.$texte_statut.BR);
	
	// Nombres
	$nombre_total=$compteur["statut"]["nb_notices"];
	print('<div class="analyse">Notices</div><table class="blank" style="margin-left:20px;width:400px" cellspacing="0">');
	afficheLigneSynthese("Notices analysées", $compteur["statut"]["nb_notices"]);
	afficheLigneSynthese("Notices inexploitables", $compteur["statut"]["rejet"]);
	afficheLigneSynthese("Notices exploitables", $compteur["statut"]["nb_notices"] - $compteur["statut"]["rejet"]);
	afficheLigneSynthese("Notices à supprimer", $compteur["statut"]["suppr"]);
	afficheLigneSynthese("Notices sans anomalies", $compteur["statut"]["ok"]);
	afficheLigneSynthese("Anomalies non bloquantes", $compteur["statut"]["warning"]);
	afficheLigneSynthese("Nombre d'exemplaires", $compteur["statut"]["nb_ex"]);
	print('</table>');

	// les autres tableaux
	$nombre_total=$nombre_total - $compteur["statut"]["rejet"] - $compteur["statut"]["suppr"];
	afficherTableauSynthese("Notices inexploitables",$compteur["rejet"],"rejet");
	afficherTableauSynthese("Anomalies non bloquantes",$compteur["warnings"],"warnings");
	afficherTableauSynthese("Types de documents",$compteur["type_doc"],"type_doc");
	afficherTableauSynthese("Identifiants",$compteur["identifiant"],"identifiant");
	afficherTableauSynthese("Sections",$compteur["section"],"section");
	afficherTableauSynthese("Genres",$compteur["genre"],"genre");
	afficherTableauSynthese("Emplacements",$compteur["emplacement"],"emplacement");
	afficherTableauSynthese("Zones forcées",$compteur["zones_forcees"],"zones_forcees");
	afficherTableauSynthese("Langues",$compteur["langues"],"langues");
	
	print(BR.BR.'</body></html>');
	exit;
}
// ----------------------------------------------------------------
//  Afficher les notices
// ----------------------------------------------------------------
if($_REQUEST["action"] == "NOTICES")
{
	print('<div class="analyse">'.$_REQUEST["titre"].'</div>');
	print('<div class="liste">');
	print('<font color="purple">Type : </font>'.$_REQUEST["code"].BR);
	// Classe unimarc
	require_once("classe_unimarc.php");
	$notice= new notice_unimarc();
	
	// Pager
	$page=$_REQUEST["page"];
	if(!$page) $page=1;
	$deb=($page-1) * 30;
	$fin=$deb+30;
	
	// Lire les adresses
	$id=$_REQUEST["PHPSESSID"];
	$type=$_REQUEST["type"];
	$code=$_REQUEST["code"];
	$data=$sql->fetchEnreg("select * from int_analyse where id='$id' and type='$type' and code ='$code'");
	
	// Afficher les notices
	$data=explode(";", $data["data"]);
	print('<font color="purple">Nombre : </font>'.(count($data)-1).BR.BR);
	for($i=$deb; $i < $fin; $i++)
	{
		$adresse=$data[$i];
		if($adresse == '') break;
		$numero=$i+1;
		$parseur->open( getVariable("ftp_path")."test/".$fichier,0,$adresse);
		$ret=$parseur->nextEnreg();
		$notice->ouvrirNotice($ret["data"],$profil_unimarc);
		$url_notice='<a class="notice" href="'.URL_BASE."php/analyse_afficher_notice.php?fichier=".$fichier."&adresse=".$adresse .'&profil_unimarc='.$profil_unimarc.'">';
  	print('<b>'.$url_notice.$numero.' - '.$notice->getTitrePrincipal().'</a></b>'.BR);
	}
	// Pager
	$url="integre_analyse_fichier_synthese.php";
	$retour=rendBouton("Retour à la synthèse",$url,"action=SYNTHESE").str_repeat("&nbsp;",5);
	$args="action=NOTICES&type=".$type."&code=".$code."&titre=".$_REQUEST["titre"]."&page=";
	if($page>1) $precedent=rendBouton("Page précédente",$url,$args.($page-1)).str_repeat("&nbsp;",5);
	if($fin < count($data)) $suivant=rendBouton("Page suivante",$url,$args.($page+1));
	print(BR.'<div class="liste">'.$retour.$precedent.$suivant.'</div>'.BR.BR);
	
	print('</div></body></html>');
	exit;
}
// ----------------------------------------------------------------
// Affichage entête
// ----------------------------------------------------------------
print(BR.'<div class="message_grand">Analyse du fichier en cours...</div>').BR;
print('Fichier : '.$fichier.BR);
print('Profil unimarc : '.$sql->fetchOne("select libelle from profil_donnees where id_profil='$profil_unimarc'").BR);
print('Heure de départ :  '.date("G:i:s",$timeStart).BR.BR);
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
flush();

// ----------------------------------------------------------------
// Ouvrir le fichier
// ----------------------------------------------------------------
if( false == $parseur->open( getVariable("ftp_path")."test/".$fichier,0,$pointeur_reprise) )
{
	afficherErreur('Impossible d\'ouvrir le fichier : '. $fichier);
	exit;
}

// ----------------------------------------------------------------
// Boucle de traitement
// ----------------------------------------------------------------
$chrono->start();
$notice->setParamsIntegration(0,0,$profil_unimarc);

while(true)
{
  	if($chrono->tempsPasse() > $timeout) sauveContexte();
  	$ret=$parseur->nextEnreg();
  	$pointeur_reprise=$ret["pointeur_reprise"];
  	$adresse=$ret["adresse"];
  	afficherAvance($ret["pct"]);
  	if($ret["statut"]=="ok")
  	{
  		$ret_test=$notice->syntheseNotice($ret["data"]);
  		// Compteurs
  		$compteur["statut"]["nb_notices"]++;
  		$compteur["statut"]["nb_ex"]+=$ret_test["nb_ex"];
  		$compteur["statut"][$ret_test["statut"]]++;
  		if($ret_test["rejet"]) {$compteur["rejet"][$ret_test["rejet"]]++; $adresses["rejet"][$ret_test["rejet"]].=$adresse.";";}
  		if($ret_test["warnings"]) foreach($ret_test["warnings"] as $type) {$compteur["warnings"][$type]++; $adresses["warnings"][$type].=$adresse.";";}
  		$type_doc=$ret_test["type_doc"]; if($type_doc) {$compteur["type_doc"][$type_doc]++; $adresses["type_doc"][$type_doc].=$adresse.";";}
  		$id=$ret_test["identifiant"]; if($id) {$compteur["identifiant"][$id]++; $adresses["identifiant"][$id].=$adresse.";";}

			if($ret_test["sections"]) foreach($ret_test["sections"] as $section) {$compteur["section"][$section]++; $adresses["section"][$section].=$adresse.";";}
			if($ret_test["genres"]) foreach($ret_test["genres"] as $genre) {$compteur["genre"][$genre]++; $adresses["genre"][$genre].=$adresse.";";}
			if($ret_test["emplacements"]) foreach($ret_test["emplacements"] as $emplacement) {$compteur["emplacement"][$emplacement]++; $adresses["emplacement"][$section].=$adresse.";";}
			if($ret_test["zones_forcees"]) foreach($ret_test["zones_forcees"] as $zone) {$compteur["zones_forcees"][$zone]++; $adresses["zones_forcees"][$zone].=$adresse.";";}
  		if($ret_test["langues"]) foreach($ret_test["langues"] as $langue) {$compteur["langues"][$langue]++; $adresses["langues"][$langue].=$adresse.";";}
   }
   else break;
}
// ----------------------------------------------------------------
// Fin
// ----------------------------------------------------------------
$nb_notices=$compteur["statut"]["nb_notices"];
if($ret["statut"]=="erreur")
{
	$texte_statut='<span class="rouge">Le fichier ne respecte pas la norme unimarc</span><br>';
	if($nb_notices > 0)	$texte_statut .= $nb_notices . " notices ont pu être analysées.".BR;
	else 	$texte_statut .= "aucune notice n'a pu être analysée.".BR;
}
else
{
	if($nb_notices == 0 ) $texte_statut = '<span class="rouge">Le fichier ne contenait aucune notice</span><br>';
	else $texte_statut = '<b><span class="vert">Le fichier respecte la norme unimarc</span></b>'.BR;
}	
sauveContexte(true);

// ----------------------------------------------------------------
// Fonctions
// ----------------------------------------------------------------
function sauveContexte($fin=false)
{
	global $sql;
	global $fichier,$timeStart,$pointeur_reprise,$profil_unimarc,$texte_statut;
	global $compteur,$adresses;
	$data=compact("fichier","timeStart","pointeur_reprise","profil_unimarc","texte_statut","compteur");
	$_SESSION["reprise"]=$data;
	// Ecrire dans int_analyse
	$id=$_REQUEST["PHPSESSID"];
	foreach($adresses as $type => $valeurs)
	{
		foreach($valeurs as $code => $data)
		{
			$code=str_replace("'","''",$code);
			$controle=$sql->fetchOne("select count(*) from int_analyse where id='$id' and type='$type' and code ='$code'");
			if(!$controle) $sql->execute("insert into int_analyse(id,date_analyse,type,code) Values('$id','".dateDuJour(0)."','$type','$code')");
			$sql->execute("update int_analyse set data=CONCAT(data,'$data') where id='$id' and type='$type' and code='$code'");
		}
	}
	// Si fin on redirige sur la synthese
	if($fin == true) redirection( "integre_analyse_fichier_synthese.php?action=SYNTHESE");
	else redirection( "integre_analyse_fichier_synthese.php");
}

function afficherAvance($pct)
{
	global $avance;
	if($pct > $avance)
	{
		$avance=$pct;
		print('<script>');
		print("document.getElementById('pct').innerHTML='".$pct."%';");
		$jauge="document.getElementById('jauge').style.width='".$pct."%';";
		print($jauge);		
		print('</script>');
		flush();
	}
}
// ----------------------------------------------------------------
// Tableau de synthèse
// ----------------------------------------------------------------
function afficherTableauSynthese($titre, $table,$type)
{
	if(!$table) return false;
	print(BR.'<div class="analyse">'.$titre.'</div><table class="blank" style="margin-left:20px;width:400px" cellspacing="0">');
	foreach($table as $libelle => $nombre)
	{
		$args="action=NOTICES&type=".$type."&code=".$libelle."&titre=".$titre;
		afficheLigneSynthese($libelle, $nombre,$args);
	}
	print('</table>');
	flush();
}

function afficheLigneSynthese($libelle,$nombre,$args_url="")
{
	global $nombre_total;
	
	if(!$nombre) {$nombre="aucune"; $pct="&nbsp"; }
	else 
	{
		if(!$nombre_total) $pct="0 %";
		else $pct = number_format(($nombre / $nombre_total) * 100, 2, ',', ' ')." %";
		$nombre = number_format($nombre, 0, ',', ' '); 
	}
	if($args_url) $url=rendUrlImg("loupe.png", "integre_analyse_fichier_synthese.php",$args_url);
	
	print('<tr>');
	if($url) print('<td class="blank" width="1%">'.$url.'</td>');
	print('<td class="blank" width="55%">'.$libelle.'</td>');
	print('<td class="blank" width="22%" align="right">'.$nombre.'</td>');
	print('<td class="blank" width="22%" align="right">'.$pct.'</td>');
	print('</tr>');
}

?>
