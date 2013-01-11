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
print('<h1>Analyse de fichier unimarc</h1>');

// Redirection pour le mode synthèse
if($_POST["test_level"]==3) 
{
	setVariable("test_level",3);
	redirection(URL_BASE."php/integre_analyse_fichier_synthese.php?action=INIT&fichier=".$_POST["fichier"]."&profil=".$_POST["profil_unimarc"]);
}
// Redirection pour le mode : valeurs distinctes
if($_POST["test_level"]==4) 
{
	setVariable("test_level",4);
	if(controleChampsDistinct()==false)
	{
		afficherErreur('Les champs a analyser sont mal définis !',false);
		$_REQUEST["action"]="PARAM";
	}
	else redirection(URL_BASE."php/integre_analyse_fichier_valeurs_distinctes.php?action=INIT&fichier=".$_POST["fichier"]."&profil=".$_POST["profil_unimarc"]."&distinct=".$_POST["distinct"]);
}

// Includes
require_once("classe_buffer.php"); 
require_once("classe_chronometre.php");
require_once("classe_parseur.php");
require_once("classe_notice_integration.php");

// Instanciations
$chrono = new chronometre();
$parseur = new parseur();
$notice = new notice_integration();
$buffer = new buffer();

// ----------------------------------------------------------------
//  Saisie des parametres
// ----------------------------------------------------------------
if($_REQUEST["action"]=="PARAM")
{
	require("fonctions/objets_saisie.php");
	print(BR.'<div class="liste"><p>NB : Les fichiers à analyser doivent être téléchargés dans le sous-dossier "test" du dossier de transfert ftp des notices.</p></div>'.BR);  
	print('<div class="liste">');
	print('<form method="post" action="'.URL_BASE.'php/integre_analyse_fichier_unimarc.php">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Fichier à analyser</th></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Fichier</td><td class="form_first">'.getComboFichiers().'</td></tr>');
	print('<tr><td class="form" align="right" width="40%">Profil unimarc</td><td class="form_first">'.getComboTable("profil_unimarc","profil_donnees","id_profil",1,"where format in(0,6)").'</td></tr>');
	print('<tr><td class="form" align="right" width="40%">Mode d\'affichage</td><td class="form_first">'.getComboCodif("test_level","test_level",getVariable("test_level")).'</td></tr>');
	print('<tr><td class="form" align="right" width="40%">Valeurs distinctes (ex: 995$a;210$b)</td><td class="form_first">'.getChamp("distinct",$_REQUEST["distinct"],"60").'</td></tr>');
	
	print('<tr><th class="form" colspan="2" align="left">Piéger des notices</th></tr>');

	$liste[""]="";
	$tdocs=getCodifsVariable("types_docs");
	foreach($tdocs as $td)$liste[$td["code"]]=$td["libelle"];
	unset($liste["0"]);
	$liste["100"]="article de périodique";
	print('<tr><td class="form_first" align="right" width="40%">Type de document</td><td class="form_first">'.getComboSimple("piege_type_doc","",$liste,"").'</td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">n° de notice</td><td class="form_first">'.getChamp("piege_numero","","5").'</td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Titre</td><td class="form_first">'.getChamp("piege_titre","","50").'</td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Code-barres</td><td class="form_first">'.getChamp("piege_code_barres","","30").'</td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Isbn</td><td class="form_first">'.getChamp("piege_isbn","","30").'</td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Mémoriser l\'unimarc</td><td class="form_first"><input type="checkbox" name="pave_unimarc" value="1"></td></tr></td></tr>');
	print('<tr><th class="form" colspan="2" align="center"><input type="submit" class="bouton" value="Lancer"></th></tr>');
	print('</table></form></div></div>');
	exit;
}

// ---------------------------------------------------------------- 
//  Affichage du test
// ----------------------------------------------------------------
if($_REQUEST["action"]=="AFFICHER")
{
	$buffer->afficher($_REQUEST["page"]);
	print('</body></html>');
	exit;
}
// ----------------------------------------------------------------
// Initialisations
// ----------------------------------------------------------------
$timeout=intval(ini_get("max_execution_time") * 0.70);
$fichier=$_POST["fichier"];
$test_level=$_POST["test_level"]; setVariable("test_level",$test_level);
$profil_unimarc=$_POST["profil_unimarc"];
$mode_piege=false;
$piege_numero=trim($_POST["piege_numero"]);
$piege_titre=trim($_POST["piege_titre"]);
$piege_code_barres=trim($_POST["piege_code_barres"]);
$piege_isbn=trim($_POST["piege_isbn"]);
$piege_type_doc=$_POST["piege_type_doc"];
$pave_unimarc=$_POST["pave_unimarc"];
$nb_notices=0;
$nb_warnings=0;
$nb_rejets=0;
$nb_suppr=0;
$nb_ex=0;
$pointeur_reprise=0;
$type_doc=array();
$avance=-1;

// Début du traitement
if($_REQUEST["reprise"]=="oui") restaureContext();
else 
{
	$buffer->open(false);
	$timeStart = time();
	$buffer->ecrire('<h4>Début de l\'analyse</h4>');
	$buffer->ecrire('Fichier : '.$fichier.BR);
	$buffer->ecrire('Profil unimarc : '.$sql->fetchOne("select libelle from profil_donnees where id_profil=$profil_unimarc").BR);
	$buffer->ecrire('Affichage : '.getLibCodifVariable("test_level",$test_level).BR);
	$buffer->ecrire('Heure :  '.date("G:i:s").BR.BR);
}

if( false == $parseur->open( getVariable("ftp_path")."test/".$fichier,0,$pointeur_reprise) )
{
	afficherErreur('Impossible d\'ouvrir le fichier : '. $fichier);
	exit;
}

// Jauge
print(BR.'<div class="message_grand">Analyse du fichier en cours...</div>').BR;
if($piege_numero) {print('<span class="violet">Recherche de la notice n° '.$piege_numero.'</span>'.BR); $mode_piege=true;}
if($piege_titre) {print('<span class="violet">Recherche du titre : '.$piege_titre.'</span>'.BR); $mode_piege=true;}
if($piege_code_barres) {print('<span class="violet">Recherche du code-barres : '.$piege_code_barres.'</span>'.BR); $mode_piege=true;}
if($piege_isbn) {print('<span class="violet">Recherche de l\'isbn : '.$piege_isbn.'</span>'.BR); $mode_piege=true;}
if($piege_type_doc) {print('<span class="violet">Recherche du type de document : '.$piege_type_doc.'</span>'.BR); $mode_piege=true;}
print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
print('</div>');
flush();

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
  		if($test_level==1 and $nb_notices == 100) break;
  		$nb_notices++;
  		$ret_test=$notice->testNotice($ret["data"],$piege_numero,$piege_titre,$piege_code_barres,$piege_isbn,$piege_type_doc);
  		$nb_ex+=$ret_test["nb_ex"];
  		
			// Mode piege
  		if($mode_piege == true)
  		{
  			if($nb_notices == $piege_numero or $ret_test["statut"]==1)
  			{
  				$url_notice='<a class="notice" href="'.URL_BASE."php/analyse_afficher_notice.php?fichier=".$fichier."&adresse=".$adresse .'&profil_unimarc='.$profil_unimarc.'">';
  				$buffer->ecrire('<b>'.$url_notice.'Notice n° '.$nb_notices.' : '.$ret_test["titre"].'</a></b>'.BR);
  				if($pave_unimarc > 0)
  				{
  					if($pave_unimarc == 1) $mf="w"; else $mf="a";
  					$pave_unimarc++;
  					$ficmemo="test/analyse_unimarc.txt";
  					$handle=fopen(getVariable("ftp_path").$ficmemo,$mf);
  					fwrite($handle,$ret["data"]);
  					fclose($handle);
  					$buffer->ecrire("La notice a été écrite dans le fichier : ".$ficmemo.BR);
  				}
  				// Si le piege est sur le n° on quitte
  				if($piege_numero) break;
  			}
  			continue;
  		}
  		// Mode analyse
  		$type_doc[$ret_test["type_doc"]]++;
  		if($test_level > 0 or $ret_test["statut"]>0)
  		{
  			if($ret_test["statut"]==1) $nb_warnings++;
  			if($ret_test["statut"]==2) $nb_rejets++;
  			if($ret_test["statut"]==3) $nb_suppr++;
  			$url_notice='<a class="notice" href="'.URL_BASE."php/analyse_afficher_notice.php?fichier=".$fichier."&adresse=".$adresse .'&profil_unimarc='.$profil_unimarc.'">';
  			$buffer->ecrire('<b>'.$url_notice.'Notice n° '.$nb_notices.' : '.$ret_test["titre"].'</a></b>'.BR);
  			$buffer->ecrire('<table class="blank" cellspacing="0" cellpadding="5px" style="margin-left:15px;margin-bottom:10px;">');
  			foreach($ret_test["lig"] as $item)
  			{
  				$item[1]=str_replace(" ","&nbsp;",$item[1]);
  				if(! $item[2]) $item[2]="&nbsp;";
  				if(! $item[3]) $item[3]="&nbsp;";
  				$buffer->ecrire('<tr><td class="blank"><span class="vert">'.$item[1].'</span></td>');
  				if($item[0] > 0 and $item[0] < 3) $buffer->ecrire('<td class="blank"><span class="rouge">'.$item[2].'</span></td>');
  				else $buffer->ecrire('<td class="blank">'.$item[2].'</td>');
  				$buffer->ecrire('<td class="blank">'.$item[3].'</td>');
  				$buffer->ecrire('</tr>');
  			}
  			$buffer->ecrire('</table>');
  		}
   }
   else break;
}
// ----------------------------------------------------------------
// Fin
// ----------------------------------------------------------------
if($mode_piege == true)
{
	$page=$buffer->close();
	redirection("integre_analyse_fichier_unimarc.php?action=AFFICHER&page=1");
}

$chrono->timeStart=$timeStart;
$buffer->ecrire("<h4>Fin de l'analyse</h4>",true);
$buffer->ecrire("Heure :  ".date("G:i:s").BR);
$buffer->ecrire("temps de traitement ".$chrono->end().BR.BR);

if($ret["statut"]=="erreur")
{
	$buffer->ecrire('<span class="rouge">Le fichier ne respecte pas la norme unimarc</span><br>');
	if($nb_notices > 0)
	{ 
		$buffer->ecrire($nb_notices . " notices ont pu être analysées.".BR);
	} 
	else 
	{
		$buffer->ecrire("aucune notice n'a pu être analysée.".BR);
	}
}
else
{
	if($nb_notices == 0 ) $buffer->ecrire('<span class="rouge">Le fichier ne contenait aucune notice</span><br>');
	else
	{
		$buffer->ecrire('<b><span class="vert">Le fichier respecte la norme unimarc</span></b>'.BR);
		$buffer->ecrire($nb_notices.' notices on été analysées.'.BR);
	}
}	
if($nb_notices > 0 )
{
	if(!$nb_suppr > 0) $nb_suppr="aucune";
	$buffer->ecrire("Notices à supprimer : " . $nb_suppr.BR);
	$buffer->ecrire("Notices à mettre à jour : " . ($nb_notices - ($nb_rejets + $nb_suppr)).BR);
	$buffer->ecrire("Exemplaires à insérer : " . $nb_ex.BR);
	$buffer->ecrire('Anomalies non bloquantes : '. $nb_warnings.BR);
	if($nb_rejets>0) $buffer->ecrire('<span class="rouge">Notices non exploitables : '. $nb_rejets.'</span>'.BR);
	else $buffer->ecrire('Notices non exploitables : aucune'.BR);
	$buffer->ecrire("Moyenne : ".$chrono->moyenne($nb_notices,"notices").BR);
	
	// Types de docs
	$buffer->ecrire(BR."<b>Types de documents :</b>".BR);
	$buffer->ecrire('<table class="blank" cellpadding="5px" style="margin-left:15px;margin-top:5px;">');
	$td=$sql->fetchOne("select liste from variables where clef='types_docs'");
	$td=explode(chr(13).chr(10),$td);
	$td[]="100:articles de périodiques";
	for($i=0; $i<count($td); $i++)
	{
		$elem=explode(":",$td[$i]);
		if(!$type_doc[$elem[0]]) $type_doc[$elem[0]]=0;
		$buffer->ecrire('<tr>');
		$buffer->ecrire('<td class="blank">'.$elem[1].'</td>');
		$buffer->ecrire('<td class="blank" align="right">'.$type_doc[$elem[0]].'</td>');
		$buffer->ecrire('</tr>');
	}
	$buffer->ecrire('</table>'.BR);
}
// Réafficher le debut si + d'1 page en buffer
$page=$buffer->close();
redirection("integre_analyse_fichier_unimarc.php?action=AFFICHER&page=1");
print('</body></html>');

// ----------------------------------------------------------------
// Fonctions
// ----------------------------------------------------------------
function sauveContexte()
{
	global $buffer;
	global $fichier,$timeStart,$pointeur_reprise,$nb_notices,$nb_ex,$nb_warnings,$nb_rejets,$nb_suppr,$type_doc,$test_level,$profil_unimarc;
	global $mode_piege,$piege_numero,$piege_titre,$piege_code_barres,$piege_isbn,$piege_type_doc,$pave_unimarc;
	$page=$buffer->close();
	$data=compact("page","fichier","timeStart","pointeur_reprise","nb_notices","nb_ex","nb_warnings","nb_rejets"
			,"type_doc","test_level","profil_unimarc","mode_piege","piege_numero","piege_titre","piege_code_barres","piege_type_doc","piege_isbn","pave_unimarc");
	$_SESSION["reprise"]=$data;
	redirection( "integre_analyse_fichier_unimarc.php?reprise=oui");
}
function restaureContext()
{
	global $buffer;
	global $fichier,$timeStart,$pointeur_reprise,$nb_notices,$nb_ex,$nb_warnings,$nb_rejets,$nb_suppr,$type_doc,$fichier,$test_level,$profil_unimarc;
	global $mode_piege,$piege_numero,$piege_titre,$piege_code_barres,$piege_isbn,$piege_type_doc,$pave_unimarc;
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
	$buffer->open($page);
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
function getComboFichiers()
{
	$dossier=getVariable("ftp_path")."test/";
	@$dir = opendir( $dossier) or afficherErreur("Impossible d'ouvrir le dossier : ".$dossier);	
	$combo='<select name="fichier">';
	while(($file = readdir($dir)) !== false) 
	{
		if(is_file($dossier.$file))
		{
			$combo.='<option value="'.$file.'">'.$file.'</option>';
		}
	}
	$combo.='</select>';
	closedir( $dir);
	return $combo;
}

function controleChampsDistinct()
{
	$champs=trim($_POST["distinct"]);
	if(!$champs) return false;
	$champs=explode(";",$champs);
	foreach($champs as $champ)
	{
		if(strlen($champ) !=5) return false;
		if(!intVal(substr($champ,0,3))) return false;
		if(substr($champ,3,1) != "\$") return false;
		$car=substr($champ,-1);
		if($car >="0" and $car <="9") continue;
		if($car >="a" and $car <="z") continue;
		return false;
	}
	return true;
}

?>
