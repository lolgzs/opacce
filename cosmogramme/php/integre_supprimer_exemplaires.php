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
///////////////////////////////////////////////////////////////////
//
//         SUPPRESSION DES EXEMPLIARES D'UNE BIBLIOTHEQUE
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

// Includes
require_once("classe_bib.php");
$oBib=new bibliotheque();
require_once("classe_chronometre.php"); 
$chrono = new chronometre();

?>

<h1>Suppression de notices ou d'exemplaires</h1>
 
<?PHP

//---------------------------------------------------------------------------------
// SUPPRESSION
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="SUPPRIMER")
{
	// Suppression de toutes les notices de la base
	if($_REQUEST["id_bib"] == "ALL")
	{
		if($_SESSION["passe"]!= "admin_systeme") afficherErreur("Vous devez être connecté en tant qu'administrateur système pour exécuter cette fonction"); 
		viderTable("notices");
		viderTable("notices_articles");
		viderTable("notices_succintes");
		viderTable("exemplaires");
		viderTable("stats_notices");
		//viderTable("codif_auteur");
		//viderTable("codif_matiere");
		//viderTable("codif_interet");
		viderTable("codif_tags");
		viderTable("integrations");
		viderTable("int_analyse");
		viderTable("prets");
		viderTable("reservations");
		viderRepertoire("Cache","cache_path");
		viderRepertoire("Integration","integration_path");
		viderRepertoire("Logs","log_path");
		
		print(BR.'<h3>Traitement terminé</h3>'.BR.BR.'</body></html>');
		exit;
	}
	
	// Initialisations
	$id_bib=$_REQUEST["id_bib"];
	$nb_notices=0;
	$nb_total=0;
	$avance=-1;
	$pointeur_reprise=0;
	$timeout=intval(ini_get("max_execution_time") * 0.75);
	$timeStart=time();
	$chrono->start();
	
	if($_REQUEST["reprise"]=="oui") restaureContext();
	else 
	{
		$nb_total = $sql->fetchOne("select count(*) from notices");
		// Suppression des exemplaires et des notices succintes
		$entete=BR.'<h4>Suppression des exemplaires de : '.$oBib->getNomCourt($id_bib).'</h4>';
		$nombre=$sql->execute("delete from exemplaires where id_bib=$id_bib");
		$nombre1=$sql->execute("delete from notices_succintes where id_bib=$id_bib");
		$entete.='<span class="vert" style="margin-left:10px">'.$nombre.' exemplaires supprimés</span>'.BR;
		$entete.='<span class="vert" style="margin-left:10px">'.$nombre1.' notices succintes supprimées</span>'.BR;
	}
	
	// Recalcul de la facette bibliotheque
	print($entete);
	print(BR.'<h4>Mise à jour des facettes bibliothèques</h4>');
	
	// Jauge
	print(BR.'<div class="jauge" style="border:none"><div id="pct" class="pct">0 %</div>');
	print('<div class="jauge"><div id="jauge" class="jauge_avance"></div></div>');
	print('</div>');
	print('<br><div>');
	print('<span id="notice"></span>');
	print('</div>');
	flush();
	
	// Boucle de traitement
	$resultat=$sql->prepareListe("select id_notice,facettes from notices where id_notice > $pointeur_reprise Order by id_notice ");
	while($ligne=$sql->fetchNext($resultat)) 
	{
		if($chrono->tempsPasse() > $timeout) sauveContexte();
		$id_notice=$ligne["id_notice"];
		$facette=explode(" ",$ligne["facettes"]);
		$facettes="";
		for($i=0; $i < count($facette); $i++)
		{
			if(!$facette[$i]) continue;
			if(substr($facette[$i],0,1) != "B")$facettes.=" ".$facette[$i];
		}
		// Chercher les exemplaires
		$bibs=$sql->fetchAll("select distinct(id_bib) from exemplaires where id_notice=$id_notice");
		if(count($bibs))
		{
			foreach($bibs as $enreg)
			{
				$bib=" B".$enreg["id_bib"];
				$facettes.= $bib;
			}
		}
		// Ecrire
		$sql->execute("update notices set facettes='$facettes' where id_notice=$id_notice");
		$pointeur_reprise=$ligne["id_notice"];
		$nb_notices++;
		afficherAvance($nb_notices,$nb_total);
	}

	// Fin
	afficherAvance($nb_total,$nb_total);
	$chrono->timeStart=$timeStart;
	print("<h4>Fin du traitement</h4>");
	print('<span class="vert" style="margin-left:10px">Temps de traitement : '.$chrono->end().'</span>'.BR.BR);
	exit;
}

//---------------------------------------------------------------------------------
// LISTE DES BIBLIOTHEQUES
//---------------------------------------------------------------------------------
print(BR.'<div style="margin-left:140px">');
print(rendBouton("Supprimer toutes les notices de la base","integre_supprimer_exemplaires.php","action=SUPPRIMER&id_bib=ALL","Etes vous sûr de vouloir supprimer toutes les notices de la base ?"));
print('</div>');

print(BR.'<span class="violet"><b>Attention : cette fonction va supprimer tous les exemplaires de la bibliothèque que vous allez choisir</b></span>'.BR.BR);
print('<div class="liste" style="margin-left:100px">');
print('<table>');
$liste=$oBib->getAll();
foreach( $liste as $id_bib => $bib)
{
	$suppr=rendBouton("Supprimer","integre_supprimer_exemplaires.php","action=SUPPRIMER&id_bib=".$bib["id_bib"]);
	print('<tr><td style="padding:7px"><b>'. $bib["id_bib"].'</b></td>');
	print('<td style="padding:7px"><b>'. $bib["nom_court"].'</b></td>');
	print('<td>'.$suppr.'</td</tr>');
}
print('</table></div>'.BR.BR);
print('</body></html>');

// ----------------------------------------------------------------
// Gestion du contexte pour les timeout
// ----------------------------------------------------------------
function sauveContexte()
{
	global $timeStart,$pointeur_reprise,$entete;
	global $nb_notices,$nb_total;

	$data=compact("timeStart","pointeur_reprise","nb_notices","nb_total","entete");
	$_SESSION["reprise"]=$data;
	redirection( "integre_supprimer_exemplaires.php?reprise=oui&action=SUPPRIMER");
}

function restaureContext()
{
	global $timeStart,$pointeur_reprise,$entete;
	global $nb_notices,$nb_total;
	
	extract($_SESSION["reprise"]);
	unset($_SESSION["reprise"]);
}

function afficherAvance($pointeur,$nb_total)
{
	global $avance;
	$pct=(int)(($pointeur / $nb_total) * 100);
	if($pct != $avance)
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

function viderTable($table)
{
	global $sql;
	$nb=$sql->fetchOne("select count(*) from ".$table);
	print('<span class="violet" style="margin-left:10px"><b>&raquo;&nbsp;Table : '.$table.'</b></span>'); 
	$sql->execute("TRUNCATE TABLE ".$table);
	if(!$nb) $msg="aucun enregistrement supprimé";
	elseif($nb == 1) $msg="1 enregistrement supprimé";
	else $msg=$nb." enregistrements supprimés";
	print('<span class="vert" style="margin-left:10px">-&nbsp;'.$msg.'</span>'.BR);
}

function viderRepertoire($titre,$type)
{
	print('<span class="violet" style="margin-left:10px"><b>&raquo;&nbsp;Dossier : '.$titre.'</b></span>'); 
	$path=getVariable($type);
	
	@$dir = opendir($path) or AfficherErreur("Impossible d'ouvrir le dossier : " .$path);
	while (($file = readdir($dir)) !== false)
	{
		$fichier=$path.$file;
		if(is_dir($fichier)) continue;
		$nb++;
		unlink($fichier);
	}
	closedir( $dir);
	// affichage du nombre
	if(!$nb) $msg="aucun fichier supprimé";
	elseif($nb == 1) $msg="1 fichier supprimé";
	else $msg=$nb." fichiers supprimés";
	print('<span class="vert" style="margin-left:10px">-&nbsp;'.$msg.'</span>'.BR);
}
?>