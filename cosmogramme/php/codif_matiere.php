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
//            GESTION DES MOTS-MATIERES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);

require_once("classe_indexation.php");
$indexation = new indexation();

?>
<script>
	function give_libelle(sIndice,sValeur)
	{
		if(sValeur == "") return;
		oChampRetour=document.getElementById("indice_a_ecrire");
		oChampRetour.value=oChampRetour.value + sIndice + "#" + sValeur + ";";
	}
	function give_suppression()
	{
		oChampRetour=document.getElementById("indice_a_ecrire");
		chaine="";
		for (var i=0;i<document.getElementsByName("selection").length;i++) 
		{
			if (document.getElementsByName("selection")[i].checked==true) 
			{
         	if(chaine > '') chaine=chaine+";";
         	chaine=chaine+document.getElementsByName("selection")[i].id;
    	}
     }
     oChampRetour.value=oChampRetour.value=chaine;
		}
</script>

<h1>Gestion des autorités Matières</h1>
<?PHP

// ----------------------------------------------------------------
// ACCUEIL
// ----------------------------------------------------------------
if($_REQUEST["action"]=="")
{
	$nb=$sql->fetchOne("Select count(*) from codif_matiere");
	print('<span class="orange"><b>La base contient '.number_format($nb, 0, ',', ' ').' fiches matières</b></span>'.BR.BR);
	
	// recherche
	print('<div class="form" style="width:500px;margin-left:30px">');
	$url="codif_matiere.php?action=LANCER";
	print('<form id="nolib" method="post" action="'.$url.'">');
	print('<table class="form" width="60%" cellspacing="0">');
	print('<tr><th class="form" align="left"colspan="2">Recherche</th></tr>');
	print('<tr>');
	print('<td class="form_first" align="right">Vedette commence par</td>');
	print('<td class="form_first"><input type ="text" name="vedette" value="" size="20" align="left"></td>');
	print('</tr>');
	print('<tr>');
	print('<td class="form_milieu" align="right">Contient</td>');
	print('<td class="form_milieu"><input type ="text" name="contient" value="" size="40" align="left"></td>');
	print('</tr>');
	print('<tr><th class ="form" colspan="2" align="center"><input type="submit" class="bouton" value="Lancer la recherche"></td></tr>');
	print('</table></form></div>');
	
	// déconstruction des vedettes
	print(BR.'<div class="form" style="width:500px;margin-left:30px">');
	print('<table class="form" width="60%" cellspacing="0">');
	print('<tr><th class="form" align="left">Analyses</th></tr>');
	$vedette=rendBouton("Déconstruction par vedettes","codif_matiere.php","action=DECONSTRUCTION");
	print('<tr><td class="form_first" align="center">'.$vedette.'</td></tr>');
	print('</table></div>');

	// indexation des renvois
	$url=URL_BASE.'php/util_renvois.php';
	print(BR.'<div class="form" style="width:500px;margin-left:30px">');
	print('<table class="form" width="100%" cellspacing="0">');
	print('<tr><th class="form" align="left">Indexation des renvois</th>');
	print('</tr>');
	print('<tr><td class="form" >Cette fonction cherche les autorités sur le serveur de cache et indexe les notices à tous les renvois.</td></tr>');
	print('<tr><th class="form" align="center" style="padding:10px">'.rendBouton("Lancer l'indexation",$url,"type_autorite=matiere").'</th></tr>');
	print('</table></div>');

	// suppression des autorités non utilisées
	$url=URL_BASE.'php/util_suppression_autorites.php';
	print(BR.'<div class="form" style="width:500px;margin-left:30px">');
	print('<table class="form" width="100%" cellspacing="0">');
	print('<tr><th class="form" align="left"colspan="2">Suppression des autorités non utilisées</th>');
	print('</tr>');
	print('<tr><td class="form">Cette fonction supprime les autorités référencées par aucune notice.</td></tr>');
	print('<tr><td class="form" align="center" style="padding:10px">'.rendBouton("Lancer la suppression",$url,"type_autorite=matiere").'</td></tr>');
	print('</table></div>');

}
// ----------------------------------------------------------------
// LANCER
// ----------------------------------------------------------------
if($_REQUEST["action"] == "LANCER")
{
	extract($_POST);
	if($vedette)
	{
		$rech=$indexation->alphaMaj($vedette);
		$req="from codif_matiere ";
		if($rech) $req.="Where libelle like '".$rech."%' ";
		$req.="order by libelle";
	}
	elseif($contient)
	{
		$mots=$indexation->getMots($contient);
		$recherche="";
		foreach($mots as $mot)
		{
			$mot=$indexation->getExpressionRecherche($mot);
			if($mot) $recherche.=" +".$mot;
		}
		if(!$recherche) quit("Il n'y aucun mot assez significatif pour la recherche.");
		$req="from codif_matiere where MATCH(code_alpha) AGAINST(' ".$recherche." ' IN BOOLEAN MODE) order by libelle";
	}
	else quit("Aucune expression saisie");
	
	$req_comptage="select count(*) ".$req;
	$req="select * ".$req;
	$nombre=$sql->fetchOne($req_comptage);
	print('<span class="orange" style="margin-left:10px"><b>'.$nombre.' autorités trouvées</b></span>'.BR);
	$handle=$sql->prepareListe($req);
	if(!$handle) {print(BR.BR.'<h3>Aucun résultat trouvé</h3>');exit;} 
	print('<div style="width:700px;margin-left:20px"><table width="100%"><tr>');
	print('<th width="1%">&nbsp;</th>');
	print('<th width="1%">id</th>');
	print('<th>Intitulé</th>');
	print('</tr>');
	while($matiere=$sql->fetchNext($handle))
	{
		$url=rendUrlImg("loupe.png", "codif_matiere.php","action=NOTICES&id_matiere=M".$matiere["id_matiere"]);
		print('<tr><td>'.$url.'</td>');
		print('<td>'.$matiere["id_matiere"].'</td>');
		print('<td>'.$matiere["libelle"].'</td></tr>');
	}
	print('</div></table>');
}
// ----------------------------------------------------------------
// LISTE DE NOTICES
// ----------------------------------------------------------------
if($_REQUEST["action"] == "NOTICES")
{
	require_once("classe_liste_notices.php");
	$oListe=new liste_notices();
	$id_matiere=$_REQUEST["id_matiere"];
	$page=$_REQUEST["page"];
	$_SESSION["url_retour"]=URL_BASE."php/codif_matiere.php?action=NOTICES&id_matiere=".$id_matiere."&page=".$page;
	
	$req="Select id_notice from notices where MATCH(facettes) AGAINST('+".$id_matiere."' IN BOOLEAN MODE) order by alpha_titre";
	$liste=$oListe->getListe($req,$page);
	print('<div style="margin-left:30px">');
	if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
	else 
	{
		$args_url="action=NOTICES&id_matiere=".$id_matiere;
		print($oListe->getHtml($liste,$args_url));
	}
}
// ----------------------------------------------------------------
// DECONSTRUCTION DES VEDETTES
// ----------------------------------------------------------------
if($_REQUEST["action"] == "DECONSTRUCTION")
{
	print('<h5>Déconstruction par vedettes</h5>');
	$temps=time();
	$handle=$sql->prepareListe("select * from codif_matiere");
	while($lig=$sql->fetchNext($handle))
	{
		$mots=explode(" : ",$lig["libelle"]);
		foreach($mots as $mot) $liste[strToUpper($mot)]++;
	}
	print('<span class="orange" style="margin-left:10px"><b>Temps de calcul : '.(time()-$temps).' secondes'.'</b></span>'.BR);
	print('<span class="orange" style="margin-left:10px"><b>'.count($liste).' formes trouvées</b></span>'.BR);
	
	// Affichage
	arsort($liste);
	print(getTags());
	print(BR.'<table style="width:700px;margin-left:20px">');
	foreach($liste as $mot => $nombre) print('<tr><td>'.$mot.'</td><td>'.$nombre.'</td</tr>');
	print('</table>');
}
// ----------------------------------------------------------------
// DECONSTRUCTION PAR MOTS
// ----------------------------------------------------------------
if($_REQUEST["action"] == "DECONSTRUCT_MOTS")
{
	print('<h5>Déconstruction par mots</h5>');
	exit;
}

// Fin
print('</body></html>');
exit;

function getTags()
{
	global $liste;
	$max_tags=100;
	
	// Calcul des tranches par ecart a la moyenne
	$tranche=calcultranches(array_slice($liste,0,$max_tags),$max_tags);
	
	foreach($liste as $clef => $nombre)
	{
		$nb++;
		if($nb>$max_tags) break;
		$table[$nb]["libelle"]=$clef;
		$table[$nb]["nombre"]=$nombre;
		$alea[$clef]=$nb;
		// Déterminer le niveau
		for($niveau=9; $niveau > 0; $niveau--) 
		{
			if($nombre > $tranche[$niveau]) break;
		}
		$table[$nb]["niveau"]=$niveau;
	}
	
	// Remettre dans un ordre aleatoire
	if(!$nb) return false;
	ksort($alea);

	// Fabriquer le Html
	$html='<div class="nuage" style="margin-left:20px">';
	foreach($alea as $index)
	{
		$enreg=$table[$index];
		$classe="nuage_niveau".($enreg["niveau"]);
		$url=URL_BASE.'php/recherche_tags_auteurs.php?tag='.$enreg["id"];
		$html.='<span class="nuage"><a class="'.$classe.'" href="'.$url.'">'.$enreg["libelle"].' </a></span>';
	}
	$html.='</div>';
	return($html);
}

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 class="erreur" style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

// Calcul des trances (Ecart a la moyenne)
function calcultranches($tableau,$nb_elements)
{
	$data=array_values($tableau);
	$min=1000000;
	
	for ($i = 0; $i < $nb_elements; $i++)
	{
		$sumX += $data[$i];
		$sumX2 += $data[$i] * $data[$i];
		if($data[$i] < $min) $min = $data[$i];
		if($data[$i] > $max) $max = $data[$i];
	}
	$mean = $sumX / $nb_elements;
	$stdDev = sqrt($sumX2 - $mean * $mean * $nb_elements) / $nb_elements; 
	$fBreakVal = $mean - ($stdDev * 3);
	for( $i = 0; $i < 10; $i++)
	{
		if($fBreakVal >= $min and $fBreakVal <= $max) $tranche[]= intval($fBreakVal);
		$fBreakVal = $fBreakVal + $stdDev;
	}
	//tracedebug(1,$tranche);
	$tranche[9]=intval(($max - $tranche[8]) /2);
	return $tranche;
}
?>



