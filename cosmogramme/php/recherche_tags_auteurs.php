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
//////////////////////////////////////////////////////////////////////////////
//  TAGS AUTEURS
//////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);

// Classe indexation
require_once("fonctions/objets_saisie.php");
require_once("classe_bib.php");
require_once("classe_liste_notices.php");
$oListe=new liste_notices();

// Comptage des notices et des exemplaires
print('<h1>Nuages de tags d\'Auteurs</h1>');
$nb_notices=$sql->fetchOne("select count(*) from notices");
$nb_ex=$sql->fetchOne("select count(*) from exemplaires");
print('<span class="orange"><b>La base contient '.number_format($nb_notices,0, ',', ' ').' notices et '.number_format($nb_ex,0, ',', ' ').' exemplaires</b></span>'.BR.BR);

// On met les criteres de recherche dans la session
$page=$_REQUEST["page"];
if($_REQUEST["refresh"]=="oui") $_SESSION["rech_tags"]=$_POST;
else $_POST=$_SESSION["rech_tags"];

// Formulaire de saisie
print('<form method="post" action="recherche_tags_auteurs.php?refresh=oui">');
print('<table class="form" cellspacing="0" style="width:500px;margin-left:20px">');
print('<tr><th class="form" colspan="2">Critères de sélection</td></tr>');
print('<tr><td align="right" class="form_milieu">Type de document </td><td class="form_milieu">'. getCombo(2) .'</td></tr>');
print('<tr><td align="right" class="form_milieu">Bibliothèque </td><td class="form_milieu">'. getCombo(4) .'</td></tr>');
print('<tr><td align="right" class="form_milieu">Nombre de tags à calculer </td><td class="form_milieu">'. getCombo(5) .'</td></tr>');
print('<th class="form" colspan="2" align="center"><input type="submit" value="Lancer" class="bouton"></th>');
print('</table></form>');


// ----------------------------------------------------------------
// Calcul requete
// ----------------------------------------------------------------
$max_tags=$_POST["max_tags"];
if($_POST["type_document"]) $condition=" where type_doc=".$_POST["type_document"];
if($_POST["bibliotheque"]) $against="+".$_POST["bibliotheque"];
if($_REQUEST["tag"]) $against.=" +".$_REQUEST["tag"];
if($against)
{
	if($condition) $condition.=" And "; else $condition = " Where ";
	$condition .= " MATCH(facettes) AGAINST('".$against."' IN BOOLEAN MODE)";
}
// ----------------------------------------------------------------
//Mode liste
// ----------------------------------------------------------------
if($_REQUEST["tag"])
{
	$_SESSION["url_retour"]=URL_BASE."php/recherche_tags_auteurs.php?tag=".$_REQUEST["tag"]."&page=".$page;
	$req="select id_notice from notices" .$condition ." order by alpha_titre";
	$liste=$oListe->getListe($req,$page);
	$args_url="tag=".$_REQUEST["tag"];
	print(BR.$oListe->getHtml($liste,$args_url));
	quit("");
}

// ----------------------------------------------------------------
// Mode Calcul des tags
// ----------------------------------------------------------------
$req="select facettes from notices" .$condition;

$temps=time();
$html=getTags($req);
print(BR.'<span class="orange" style="margin-left:10px"><b>Temps de calcul des tags : '.(time()-$temps).' secondes'.'</b></span>'.BR);
if(!$html) quit("Aucun résultat trouvé");
print(BR.$html.BR.BR);
quit("");

function getTags($req)
{
	global $sql,$max_tags;
	// Nombre max de tags
	if(!$max_tags) $max_tags=100;
	$max=0;
	$min=1000000;
	
	$result=$sql->prepareListe($req);
	while($notice=$sql->fetchNext($result))
	{
		$items=explode(" ",trim($notice["facettes"]));
		foreach($items as $item)
		{ 
			$type=$item[0];
			if($type == "A") $facettes[$item]++;
			$nb++;
			if($nb==10000)
			{
				$nb=0;
				arsort($facettes);
				$facettes=array_slice($facettes,0,10000);
			}
		}
	}
	if($facettes) arsort($facettes);

	// Auteurs
	$nb=0;
	if($facettes)
	{
		foreach($facettes as $clef => $nombre)
		{
			if($nombre > $max) $max=$nombre;
			if($nombre < $min) $min=$nombre;
			$nb++;
			if($nb>$max_tags) break;
			$id_auteur=substr($clef,1);
			$table[$nb]["id"]=$clef;
			$table[$nb]["libelle"]=$sql->fetchOne("select libelle from codif_auteur where id_auteur=$id_auteur");
			$table[$nb]["nombre"]=$nombre;
			$alea[$clef]=$nb;
		}
	}

	// Déterminer les tranches
	if($max < 11) $tranches=array(1,2,3,4,5,6,7,8,9,10);
	else
	{
		$tranche=intVal(($max - $min)/10);
		for($i=0;$i<10; $i++) $tranches[$i]=intval($min + ($i * $tranche));
	}
	// Remettre dans un ordre aleatoire
	if(!$nb) return false;
	ksort($alea);

	// Fabriquer le Html
	$html='<div class="nuage" style="margin-left:20px">';
	foreach($alea as $index)
	{
		$enreg=$table[$index];
		for($niveau=9; $niveau > 0; $niveau--) if($enreg["nombre"]>=$tranches[$niveau]) break;
		$classe="nuage_niveau".($niveau+1);
		$url=URL_BASE.'php/recherche_tags_auteurs.php?tag='.$enreg["id"];
		$html.='<span class="nuage"><a class="'.$classe.'" href="'.$url.'">'.$enreg["libelle"].' </a></span>';
	}
	$html.='</div>';
	return $html;
}

function getCombo($type)
{
	switch($type)
	{
		// Types de documents
		case 2:
			$name = "type_document";
			return getComboCodif($name,"types_docs",$_POST[$name],"",true);
			break;
		// Bibliotheque
		case 4:
			$name = "bibliotheque";
			$oBib=new bibliotheque();
			$valeurs=array(""=>"toutes");
			$bibs=$oBib->getAll();
			foreach($bibs as $bib) 
			{
				$code="B".$bib["id_bib"];
				$valeurs[$code]=stripSlashes($bib["nom_court"]);
			}
			break;
		// Nombre de tags
		case 5:
			$name = "max_tags";
			$valeurs=array("100"=>"100","200"=>"200","300"=>"300","500"=>"500","1000"=>"1000");
			break;
	}
	$selection=$_POST[$name];
	$combo='<select name="'.$name.'">';
	foreach($valeurs as $clef => $valeur)
	{
		if($selection == $clef) $selected=' selected="selected"'; else $selected ="";
		$combo.='<option value="'.$clef.'"'.$selected.'>'.$valeur.'</option>';
	}
	$combo.='</select>';
	return $combo;
}

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 class="erreur" style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

?>
