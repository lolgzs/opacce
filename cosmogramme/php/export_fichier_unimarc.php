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
// EXPORT D'UN FICHIER UNIMARC
/////////////////////////////////////////////////////////////////////////
include("_init_frame.php");
print('<h1>Export de fichier unimarc</h1>');

// Includes
require_once("classe_chronometre.php");
require_once("classe_bib.php");
require_once("classe_notice_integration.php");

// Instanciations
$cls_bib = new bibliotheque();
$chrono = new chronometre();
$cls_notice = new iso2709_record();

// ----------------------------------------------------------------
//  Saisie des parametres
// ----------------------------------------------------------------
if($_REQUEST["action"]=="PARAM")
{
	require("fonctions/objets_saisie.php");
	print(BR.'<p>Le fichier sera enregistré dans le dossier de transfert ftp des notices / sous-répertoire : export</p></div>'.BR);
	print('<div class="liste">');
	print('<form method="post" action="'.URL_BASE.'php/export_fichier_unimarc.php">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	
	print('<tr><th class="form" colspan="2" align="left">Format d\'export</th></tr>');
	print('<tr><td class="form" align="right" width="40%">Type d\'accents</td><td class="form_first">'.getComboCodif("type_accents","transco_accents","2").'</td></tr>');
	print('<tr><td class="form" align="right">Nom exportateur (801$b)</td><td class="form">'.getChamp("libelle_801",getVariable("portail_nom_affichage"),50).'</td></tr>');

	print('<tr><th class="form" colspan="2" align="left">Destination</th></tr>');
	print('<tr><td class="form" align="right">Nom du fichier exporté</td><td class="form">'.getChamp("fichier_export",getVariable("export_fichier"),50).'</td></tr>');
	print('<tr><th class="form" colspan="2" align="center"><input type="submit" class="bouton" value="Lancer"></th></tr>');
	print('</table></form></div></div>');
	exit;
}

// ----------------------------------------------------------------
// Initialisations
// ----------------------------------------------------------------
$timeout=intval(ini_get("max_execution_time") * 0.70);
if(!$timeout) $timeout=25;
$fichier_export=$_POST["fichier_export"];
$libelle801=$_POST["libelle_801"];
$type_accents=$_POST["type_accents"];
$nb_notices=0;
$nb_total=0;
$pointeur_reprise=0;
$avance=-1;

// equivalences pour les types de documents
$table_type_doc=getCodifsVariable("export_type_doc_995",true);

// bibs qui n'exportent pas
$condition_bibs="";
$data=fetchAll("select id_bib from int_bib where pas_exporter=1");
if($data)
{
	foreach($data as $bib)
	{
		if($condition_bibs) $condition_bibs.=",";
		$condition_bibs.=$bib["id_bib"];
	}
}
if($condition_bibs) $condition_bibs=" and id_bib not in(".$condition_bibs.")";

// Début du traitement
if($_REQUEST["reprise"]=="oui")
{
	restaureContext();
	$mode_fic_export="a";
}
else 
{
	$timeStart = time();
	$mode_fic_export="w";
	setVariable("portail_nom_affichage",$libelle801);
	setVariable("export_fichier",$fichier_export);
}

print('Fichier de sortie : '.$fichier_export.BR);
print('Types d\'accents : '.getLibCodifVariable("transco_accents",$type_accents).BR);
print('Heure de départ :  '.date("G:i:s",$timeStart).BR.BR);
$fic_out=@fopen( getVariable("ftp_path")."export/".$fichier_export,$mode_fic_export);
if(!$fic_out)
{
	afficherErreur('Impossible d\'ouvrir le fichier : '.getVariable("ftp_path")."export/".$fichier_export." en écriture.");
	exit;
}

// Jauge
print(BR.'<div class="message_grand">Export des notices en cours...</div>').BR;
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
flush();

// ----------------------------------------------------------------
// Boucle de traitement
// ----------------------------------------------------------------
$chrono->start();
$nb_total=fetchOne("select count(*) from notices");

$resultat=$sql->prepareListe("select id_notice,type_doc,unimarc from notices where id_notice > $pointeur_reprise order by id_notice limit 10000");
if($resultat)
{
	while($notice=$sql->fetchNext($resultat))
	{
		if($chrono->tempsPasse() > $timeout) sauveContexte();
		if($notice["type_doc"]==8 or $notice["type_doc"]==9 or $notice["type_doc"]==10 or $notice["type_doc"]>99)  continue; // ne pas exporter cms, rss et sito
		$id_notice=$notice["id_notice"];
		$cls_notice->setNotice($notice["unimarc"]);

		// inserer les exemplaires
		$exemplaires=fetchAll("select * from exemplaires where id_notice=".$id_notice.$condition_bibs);
		if($exemplaires)
		{
			// nom en 801$b
			$cls_notice->delete_field("801");
			$cls_notice->add_field("801","  ",array(array("a","FR"),array("b",$libelle801)));
			
			// permalien vers la notice
			$url=getVariable("url_site")."/recherche/viewnotice/id/".$id_notice."/type_doc/".$notice["type_doc"];
			$cls_notice->delete_field("856");
			$cls_notice->add_field("856","  ","a".$url);

			// exemplaires en 995
			foreach($exemplaires as $exemplaire)
			{
				$champ995=array();
				$champ995[]=array("a",trim(fetchOne("select LIBELLE from bib_c_site where ID_SITE=".$exemplaire["id_bib"])));
				if($exemplaire["code_barres"]) $champ995[]=array("f",$exemplaire["code_barres"]);
				if($exemplaire["section"]) $champ995[]=array("j",$exemplaire["section"]);
				if($exemplaire["cote"]) $champ995[]=array("k",$exemplaire["cote"]);
				if($notice["type_doc"]==2) $champ995[]=array("p","p");
				
				// type de doc
				$td=$table_type_doc[$notice["type_doc"]];
				if($td) $champ995[]=array("r",$td);
				else $champ995[]=array("r",$cls_notice->getInnerGuide("dt").$cls_notice->getInnerGuide("bl"));

				// ajout du champ
				$cls_notice->add_field("995","  ",$champ995);
				unset($champ995);
			}
		
			// ecrire
			$unimarc=$cls_notice->update($type_accents);
			fwrite($fic_out, $unimarc);
		}

		// pointeurs
		$pointeur_reprise=$id_notice;
		$nb_notices++;
		$pct=($nb_notices/$nb_total)*100;
		afficherAvance(number_format($pct,1));
	}
	sauveContexte();
}
fclose($fic_out);

// ----------------------------------------------------------------
// Fin
// ----------------------------------------------------------------
afficherAvance(number_format(100,1));
$chrono->timeStart=$timeStart;
print("<h4>Export terminé.</h4>");
print("Heure :  ".date("G:i:s").BR);
print("temps de traitement ".$chrono->end().BR.BR);

if($nb_notices == 0 ) print('<span class="rouge">Aucune notice n\'a été exportée</span><br>');
else
{
	print($nb_notices.' notices on été exportées.'.BR);
	print(BR.'<a href="'.URL_BASE.getVariable("ftp_path").'export/'.$fichier_export.'">&raquo;&nbsp;Télécharger le fichier (click droit puis enregistrer sous)</a>'.BR);
}
print('</body></html>');

// ----------------------------------------------------------------
// Fonctions
// ----------------------------------------------------------------
function sauveContexte()
{
	global $timeStart,$pointeur_reprise,$nb_notices,$type_accents;
	global $fichier_export,$libelle801;
	$data=compact("timeStart","pointeur_reprise","nb_notices","type_accents","fichier_export","libelle801");
	$_SESSION["reprise"]=$data;
	redirection( "export_fichier_unimarc.php?reprise=oui");
}
function restaureContext()
{
	global $timeStart,$pointeur_reprise,$nb_notices,$type_accents;
	global $fichier_export,$libelle801;
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
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
?>
