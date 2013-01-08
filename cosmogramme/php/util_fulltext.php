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
// REINEXATION DES CHAMPS FULLTEXT ET PHONETIQUE
/////////////////////////////////////////////////////////////////////////
	
include("_init_frame.php");

// Includes
require_once("classe_chronometre.php"); 
require_once("classe_unimarc.php");
require_once("classe_indexation.php");
require_once("fonctions/objets_saisie.php");

// Instanciations
$chrono = new chronometre(); 
$unimarc = new notice_unimarc();
$ix = new indexation();

// ----------------------------------------------------------------
//  Saisie des parametres
// ----------------------------------------------------------------
if($_REQUEST["action"]=="PARAM")
{
	print('<div class="liste">');
	print('<form method="post" action="'.URL_BASE.'php/util_fulltext.php">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Paramètres</th></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Commencer à la notice n°</td><td class="form_first"><input type="text" name="notice_depart" size="6" value="0"></td></tr>');
	print('<tr><td class="form_first" align="right" width="40%">Refaire les facettes notices</td><td class="form_first">'.getComboSimple("faire_facettes_notices","0",array("0"=>"non","1"=>"oui")).'</td></tr>');

	print('<tr><th class="form" colspan="2" align="center"><input type="submit" class="bouton" value="Lancer"></th></tr>');
	print('</table></form></div></div>');
	exit;
}

// Initialisations
$nb_notices=0;
$nb_total=0;
$avance=-1;
$pointeur_reprise=0;
$notice_depart=$_POST["notice_depart"];
$faire_facettes_notices=$_POST["faire_facettes_notices"];
$timeout=intval(ini_get("max_execution_time") * 0.75);
$timeStart=time();
$chrono->start();

// ----------------------------------------------------------------
// Début du traitement
// ----------------------------------------------------------------
if($_REQUEST["reprise"]=="oui") restaureContext();
else $nb_total = $sql->fetchOne("select count(*) from notices");

// Jauge
print('<h1>Réindexation fulltext et phonétique</h1>');
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
$resultat=$sql->prepareListe("select id_notice,facettes,unimarc from notices where id_notice > $pointeur_reprise Order by id_notice LIMIT 0,20000");
while($ligne=$sql->fetchNext($resultat)) 
{
	if($chrono->tempsPasse() > $timeout) sauveContexte();
	if($nb_notices >= $notice_depart)
	{
		$unimarc->ouvrirNotice($ligne["unimarc"],0);
		$notice=$unimarc->getNoticeIntegration();
		$notice["facettes"]=$ligne["facettes"];
		$data["titres"]=$ix->getfullText($notice["titres"]);
		$data["auteurs"]=$ix->getfullText($notice["auteurs"]);
		$data["editeur"]=$ix->getfullText($notice["editeur"]);
		$data["collection"]=$ix->getfullText($notice["collection"]);
		$data["matieres"]=$ix->getfullText($notice["matieres"]);
		$dewey=$ix->getfullText($notice["dewey"]);
		$pcdm4=$ix->getfullText($notice["pcdm4"]);
		$data["dewey"]=trim($dewey." ".$pcdm4);
		if($faire_facettes_notices=="1") $data["facettes"]=traiteFacettes($notice);
		$sql->update("update notices set @SET@ where id_notice=".$ligne["id_notice"],$data);
	}
	$pointeur_reprise=$ligne["id_notice"];
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
	global $timeStart,$pointeur_reprise,$notice_depart;
	global $nb_notices,$nb_total,$faire_facettes_notices;

	$data=compact("timeStart","pointeur_reprise","nb_notices","nb_total","notice_depart","faire_facettes_notices");
	$_SESSION["reprise"]=$data;
	redirection( "util_fulltext.php?reprise=oui");
}

function restaureContext()
{
	global $timeStart,$pointeur_reprise,$notice_depart;
	global $nb_notices,$nb_total;
	
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
}

function afficherAvance($pointeur,$nb_total)
{
	global $avance;
	$pct=(($pointeur / $nb_total) * 100);
	$pct=number_format($pct, 1, '.', ' ');
	if($pct > $avance)
	{
		$avance=$pct;
		print('<script>');
		print("document.getElementById('pct').innerHTML='".$pct."%';");
		$jauge="document.getElementById('jauge').style.width='".(int)$pct."%';";
		print($jauge);		
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

// --------------------------------------------------------------------------------
// Traitement des facettes
// --------------------------------------------------------------------------------
	function traiteFacettes($notice)
	{
		global $ix;
		$facettes_a_garder=array("Z","B","G","E","S","Y","T");

		// Virer les facettes sauf les tags et les facettes exemplaires
		$controle=explode(" ",$notice["facettes"]);
		$notice["facettes"]="";
		for($i=0; $i < count($controle); $i++)
		{
			$tp=substr($controle[$i],0,1);
			if (in_array ($tp, $facettes_a_garder)) $notice["facettes"].=" ".$controle[$i];
		}

		// Dewey
		if($notice["dewey"])
		{
			foreach($notice["dewey"] as $indice)
			{
				$enreg=fetchEnreg("Select * from codif_dewey where id_dewey='$indice'");
				if(!$enreg["id_dewey"]) sqlInsert("codif_dewey",array("id_dewey"=>$indice));
				else $notice["full_dewey"].=$enreg["libelle"]." ";
				$facettes[]="D".$indice;
			}
		}
		// Pcdm4
		if($notice["pcdm4"])
		{
			$indice=$notice["pcdm4"];
			$enreg=fetchEnreg("Select * from codif_pcdm4 where id_pcdm4='$indice'");
			if(!$enreg["id_pcdm4"]) sqlInsert("codif_pcdm4",array("id_pcdm4"=>$indice));
			else $notice["full_dewey"].=$enreg["libelle"]." ";
			$facettes[]="P".$indice;
		}

		// Auteurs
		if($notice["auteurs"])
		{
			foreach($notice["auteurs"] as $auteur)
			{
				$code_alpha=$ix->alphaMaj($auteur);
				$code_alpha=str_replace(" ","x",$code_alpha);
				if(!$code_alpha) continue;
				$enreg=fetchEnreg("Select * from codif_auteur where MATCH(formes) AGAINST('\"".$code_alpha."\"' IN BOOLEAN MODE) ");
				if(!$enreg["id_auteur"])
				{
					$pos=strscan($auteur,"|");
					$nom_prenom = trim(substr($auteur,($pos+1))." ".substr($auteur,0,$pos));
					$id_auteur=sqlInsert("codif_auteur",array("libelle" => $nom_prenom,"formes" => $code_alpha));
				}
				else $id_auteur=$enreg["id_auteur"];
				$facettes[]="A".$id_auteur;
			}
		}

		// Matieres
		if($notice["matieres"])
		{
			foreach($notice["matieres"] as $matiere)
			{
				$code_alpha=$ix->alphaMaj($matiere);
				if(!$code_alpha) continue;
				$enreg=fetchEnreg("Select * from codif_matiere where code_alpha='$code_alpha'");
				if(!$enreg["id_matiere"]) $id_matiere=sqlInsert("codif_matiere",array("libelle" => $matiere,"code_alpha" => $code_alpha));
				else $id_matiere=$enreg["id_matiere"];
				$facettes[]="M".$id_matiere;
			}
		}
		// Centres d'interet
		if($notice["interet"])
		{
			foreach($notice["interet"] as $interet)
			{
				$code_alpha=$ix->alphaMaj($interet);
				if(!$code_alpha) continue;
				$enreg=fetchEnreg("Select * from codif_interet where code_alpha='$code_alpha'");
				if(!$enreg["id_interet"]) $id_interet=sqlInsert("codif_interet",array("libelle" => $interet,"code_alpha" => $code_alpha));
				else $id_interet=$enreg["id_interet"];
				$facettes[]="F".$id_interet;
				$this->notice["full_dewey"].=$interet." ";
			}
		}
		// Langues
		if($notice["langues"])
		{
			foreach($notice["langues"] as $langue)
			{
				$controle=fetchOne("Select count(*) from codif_langue where id_langue='$langue'");
				if($controle) $facettes[]="L".$langue;
			}
		}

		// Maj enreg facette
		if(!$facettes) return "";
		foreach($facettes as $facette)
		{
			if(strpos($notice["facettes"],$facette) === false) $notice["facettes"].=" ".$facette;
		}
		return $notice["facettes"];
	}
?>
