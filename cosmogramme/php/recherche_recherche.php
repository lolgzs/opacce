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
//  RECHERCHE
//////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);

// Classe indexation
require_once("fonctions/objets_saisie.php");
require_once("classe_bib.php");
require_once("classe_indexation.php");
require_once("classe_dewey.php");
require_once("classe_pcdm4.php");
require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$ix= new indexation();

// Comptage des notices et des exemplaires
print('<h1>Recherche de notices</h1>');
$nb_notices=$sql->fetchOne("select count(*) from notices");
$nb_ex=$sql->fetchOne("select count(*) from exemplaires");
print('<span class="orange"><b>La base contient '.number_format($nb_notices,0, ',', ' ').' notices et '.number_format($nb_ex,0, ',', ' ').' exemplaires</b></span>'.BR.BR);

// On met les criteres de recherche dans la session
$page=$_REQUEST["page"];
if($_POST["expression_recherche"]) $_SESSION["recherche"]=$_POST;
else $_POST=$_SESSION["recherche"];
$_SESSION["url_retour"]=URL_BASE."php/recherche_recherche.php?lancer=oui&page=".$page;
if($_REQUEST["facette"]) $_SESSION["url_retour"].="&facette=".$_REQUEST["facette"];
if($_REQUEST["type_doc"]) {$_POST["type_document"]=$_REQUEST["type_doc"];$_SESSION["url_retour"].="&type_doc=".$_REQUEST["type_doc"];}

// HTML
print('<form method="post" action="recherche_recherche.php?lancer=oui">');
print('<table class="form" cellspacing="0" style="width:500px;margin-left:20px"><tr>');
print('<th class="form" align="right" style="height:50px">Recherche</th>');
print('<th class="form"><input type="text" name="expression_recherche" size="50" value="'.stripSlashes($_POST["expression_recherche"]).'"></th>');
print('<th class="form"><input type="submit" value="Lancer" class="bouton"></th>');
print('</tr><tr><td colspan="3" class="form" align="center">');
afficherOptions();
print('</td></tr></table></form>');

if(!$_REQUEST["lancer"]) quit("");

/////////////////////////////////////////////////////////////////
// Resultat de la recherche
/////////////////////////////////////////////////////////////////

// Analyse de l'expression
$recherche_saisie=trim(strip_tags($_POST["expression_recherche"]));
if(! $recherche_saisie) quit("Aucune expression saisie");

$mots=$ix->getMots($recherche_saisie);
$recherche="";
foreach($mots as $mot)
{
	$mot=$ix->getExpressionRecherche($mot);
	if($mot)
	{
		if($_POST["pertinence"]=="1") $recherche.=" ".$mot;
		else $recherche.=" +".$mot;
	}
}

$recherche=trim($recherche);
if(!$recherche) quit("Il n'y aucun mot assez significatif pour la recherche.");

// Constitution des requetes
$req_comptage="Select count(*)from notices ";
$req_facettes="select id_notice,type_doc,facettes from notices ";
if($_POST["pertinence"]=="1") $against=" AGAINST('".$recherche."')";
else $against=" AGAINST('".$recherche."' IN BOOLEAN MODE)";

if($_POST["axe_recherche"] > "")
{
	if($_POST["axe_recherche"]=="unimarc") 
	{
		$against=""; $where="Where unimarc like '%$recherche_saisie%'";
		$_POST["tri_resultat"]="alpha_titre";
		}
	else $where="Where MATCH(".$_POST["axe_recherche"].")";
}
if(!$where) $where="Where MATCH(titres,auteurs,editeur,collection,matieres,dewey)";
if($_REQUEST["facette"]) {$facette=str_replace("["," +", $_REQUEST["facette"]);$facette=str_replace("]","",$facette);}
if($_POST["bibliotheque"] > "") $facette.= " +".$_POST["bibliotheque"].$facette;
if($facette) $conditions=" And MATCH(facettes) AGAINST('".$facette."' IN BOOLEAN MODE)";
if($_POST["type_document"] > "") $conditions.=" And type_doc='".$_POST["type_document"]."'";
if($_POST["tri_resultat"] > "")
{
	$req="select id_notice from notices ";
	$order_by=" order by ".$_POST["tri_resultat"];
}
else
{
	$req="select id_notice,MATCH(alpha_titre) ".str_replace("+"," ",$against)." as rel1, ";
	$req.="MATCH(alpha_auteur)".str_replace("+"," ",$against)." as rel2 ";
	$req.="from notices ";
	$order_by=" ORDER BY (rel1)+(rel2) desc";
}

$req.=$where.$against.$conditions.$order_by;
$req_comptage.=$where.$against.$conditions;
$req_facettes.=$where.$against.$conditions;

print(BR."<B>Requete : </b>".$req.BR);

// Lancer les requetes
$temps=time();
$nombre=$sql->fetchOne($req_comptage);
print(BR.'<span class="orange" style="margin-left:10px"><b>Temps d\'éxécution requête de comptage : '.(time()-$temps).' secondes'.'</b></span>'.BR);
if(!$nombre) quit("Aucun résultat trouvé");

$temps=time();
$liste=$oListe->getListe($req,$page);
print('<span class="orange" style="margin-left:10px"><b>Temps d\'éxécution requête de recherche : '.(time()-$temps).' secondes</b></span>'.BR);
$temps=time();
$html_facettes=getFacettes($req_facettes);
print('<span class="orange" style="margin-left:10px"><b>Temps de calcul des facettes : '.(time()-$temps).' secondes</b></span>'.BR);
print('<span class="orange" style="margin-left:10px"><b>Résultats : '.$nombre.'</b></span>'.BR);
flush();
print(BR.$html_facettes);

// Résultat
$args_url="lancer=oui";
if($_REQUEST["facette"]) $args_url.="&facette=".$_REQUEST["facette"];
print(BR.$oListe->getHtml($liste,$args_url));
quit("");

// options
function afficherOptions()
{
	print('<table cellspacing="0" width="100%">');
	print('<tr><td align="right" class="form_milieu" width="50%">Axe de recherche </td><td class="form_milieu">'. getCombo(1) .'</td></tr>');
	print('<tr><td align="right" class="form_milieu">Type de document </td><td class="form_milieu">'. getCombo(2) .'</td></tr>');
	print('<tr><td align="right" class="form_milieu">Bibliothèque </td><td class="form_milieu">'. getCombo(4) .'</td></tr>');
	print('<tr><td align="right" class="form_milieu">Tri du résultat </td><td class="form_milieu">'. getCombo(3) .'</td></tr>');
	if($_POST["pertinence"] == "1") $coche=" checked"; else $coche="";
	print('<tr><td align="right" class="form_milieu">Elargir la recherche (pertinence) </td><td class="form_milieu"><input type="checkbox" name="pertinence" value="1"'.$coche.'>'.'</td></tr>');
	if($_POST["expression_exacte"] == "1") $coche=" checked"; else $coche="";
	print('</table>');
}

function getCombo($type)
{
	switch($type)
	{
		// Axes de recherche
		case 1:
			$name = "axe_recherche";
			$valeurs=array(""=>"tous","titres"=>"Titres","auteurs"=>"Auteurs","editeur"=>"Editeurs","collection"=>"collections","matieres"=>"matieres /sujets","dewey"=>"Dewey / pcdm4","unimarc"=>"Full unimarc" );
			break;
		// Types de documents
		case 2:
			$name = "type_document";
			return getComboCodif($name,"types_docs",$_POST[$name],"",true);
			break;
		// Tris du résultat
		case 3:
			$name = "tri_resultat";
			$valeurs=array(""=>"aucun","alpha_titre"=>"Par titres","alpha_auteur"=>"Par auteurs","annee desc"=>"Par années");
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
// Calcul des facettes
function getFacettes($req)
{
	global $sql;
	$result=$sql->prepareListe($req);
	while($notice=$sql->fetchNext($result))
	{
		$type="T".$notice["type_doc"];
		$facettes["type_doc"][$type]++;
		$items=explode(" ",trim($notice["facettes"]));
		foreach($items as $item)
		{ 
			$type=substr($item,0,1);
			$facettes[$type][$item]++;
		}
	}
	arsort($facettes["type_doc"]);
	arsort($facettes["B"]); 
	if($facettes["A"]) arsort($facettes["A"]);
	if($facettes["D"]) arsort($facettes["D"]); 
	if($facettes["P"]) arsort($facettes["P"]);
	if($facettes["M"]) arsort($facettes["M"]);
	if($facettes["L"]) arsort($facettes["L"]);
	if($facettes["F"]) arsort($facettes["F"]);
	
	// Types de docs
	$nb=0;
	$table["type_doc"]["titre"]="Type de document";
	foreach($facettes["type_doc"] as $clef => $nombre)
	{
		$nb++;
		if($nb>5) break;
		$id_type_doc=substr($clef,1);
		$table["type_doc"][$nb]["id"]=$clef;
		$table["type_doc"][$nb]["libelle"]=getLibCodifVariable("types_docs",$id_type_doc);
		$table["type_doc"][$nb]["nombre"]=$nombre;
	}
	// Bibliotheques
	$nb=0;
	$table["B"]["titre"]="Bibliothèque";
	foreach($facettes["B"] as $clef => $nombre)
	{
		$nb++;
		if($nb>5) break;
		$id_bib=substr($clef,1);
		$table["B"][$nb]["id"]=$clef;
		$table["B"][$nb]["libelle"]=$sql->fetchOne("select nom_court from int_bib where id_bib=$id_bib");
		$table["B"][$nb]["nombre"]=$nombre;
	}
	// Auteurs
	$nb=0;
	if($facettes["A"])
	{
		$table["A"]["titre"]="Auteur";
		foreach($facettes["A"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_auteur=substr($clef,1);
			$table["A"][$nb]["id"]=$clef;
			$table["A"][$nb]["libelle"]=$sql->fetchOne("select libelle from codif_auteur where id_auteur=$id_auteur");
			$table["A"][$nb]["nombre"]=$nombre;
		}
	}
	// Dewey
	$nb=0;
	if($facettes["D"])
	{
		$table["D"]["titre"]="Dewey";
		foreach($facettes["D"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_dewey=substr($clef,1);
			$table["D"][$nb]["id"]=$clef;
			$table["D"][$nb]["libelle"]=dewey::getLibelle($id_dewey);
			$table["D"][$nb]["nombre"]=$nombre;
		}
	}
	// Pcdm4
	$nb=0;
	if($facettes["P"])
	{
		$table["P"]["titre"]="Pcdm4";
		foreach($facettes["P"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_pcdm4=substr($clef,1);
			$table["P"][$nb]["id"]=$clef;
			$table["P"][$nb]["libelle"]=pcdm4::getLibelle($id_pcdm4);
			$table["P"][$nb]["nombre"]=$nombre;
		}
	}
	// Matières
	$nb=0;
	if($facettes["M"])
	{
		$table["M"]["titre"]="Mot-matière";
		foreach($facettes["M"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_matiere=substr($clef,1);
			$table["M"][$nb]["id"]=$clef;
			$table["M"][$nb]["libelle"]=$sql->fetchOne("select libelle from codif_matiere where id_matiere=$id_matiere");
			$table["M"][$nb]["nombre"]=$nombre;
		}
	}
	// Langues
	$nb=0;
	if($facettes["L"])
	{
		$table["L"]["titre"]="Langue";
		foreach($facettes["L"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_langue=substr($clef,1);
			$table["L"][$nb]["id"]=$clef;
			$table["L"][$nb]["libelle"]=$sql->fetchOne("select libelle from codif_langue where id_langue='$id_langue'");
			$table["L"][$nb]["nombre"]=$nombre;
		}
	}
	// Centres d'interet
	$nb=0;
	if($facettes["F"])
	{
		$table["F"]["titre"]="Centre d'intérêt";
		foreach($facettes["F"] as $clef => $nombre)
		{
			$nb++;
			if($nb>5) break;
			$id_interet=substr($clef,1);
			$table["F"][$nb]["id"]=$clef;
			$table["F"][$nb]["libelle"]=$sql->fetchOne("select libelle from codif_interet where id_interet='$id_interet'");
			$table["F"][$nb]["nombre"]=$nombre;
		}
	}
	// Fabriquer le Html
	$html='<div style="width:500px;margin-left:20px"><table class="form">';
	foreach($table as $type => $valeurs)
	{
		$html.='<tr><th class="form" align="left" colspan="2">'.$valeurs["titre"].'</th></tr>';
		for($i=1; $i< count($valeurs); $i++)
		{
			$url='<a href="'.URL_BASE.'php/recherche_recherche.php?lancer=oui';
			if($type=="type_doc") $url.="&type_doc=".substr($valeurs[$i]["id"],1).'">';
			else 
			{
				if($_REQUEST["type_doc"]) $url.="&type_doc=".$_REQUEST["type_doc"];
				$facette='['.$valeurs[$i]["id"]."]";
				if(strpos($_REQUEST["facette"],$facette) !== false) $facette="";
				$url.='&facette='.$_REQUEST["facette"].$facette.'">';
			}
			$html.='<tr><td style="width:15px" class="form"></td>';
			$html.='<td class="form">'.$url.$valeurs[$i]["libelle"]." : ".$valeurs[$i]["nombre"].'</a></td></tr>';
		}
	}
	$html.='</table></div>';
	return $html;
}

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 class="erreur" style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

?>
