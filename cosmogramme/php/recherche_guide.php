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
//  RECHERCHE GUIDEE
//////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);
if(!$_REQUEST["rubrique"]) unset($_SESSION["guide"]);

// Classe indexation
require_once("fonctions/objets_saisie.php");
require_once("classe_bib.php");
require_once("classe_liste_notices.php");
require_once("classe_dewey.php");
require_once("classe_pcdm4.php");
$oListe=new liste_notices();
$oBib=new bibliotheque();

// Comptage des notices et des exemplaires
print('<h1>Recherche guidée</h1>');
$nb_notices=$sql->fetchOne("select count(*) from notices");
$nb_ex=$sql->fetchOne("select count(*) from exemplaires");
print('<span class="orange"><b>La base contient '.number_format($nb_notices,0, ',', ' ').' notices et '.number_format($nb_ex,0, ',', ' ').' exemplaires</b></span>'.BR.BR);

// On met les criteres de recherche dans la session
$page=$_REQUEST["page"];
if(count($_POST) > 1) $_SESSION["guide_selection"]=$_POST; else $_POST=$_SESSION["guide_selection"];

// Formulaire de saisie
if($_REQUEST["mode"]=="INTRO")
{
	print('<form method="post" action="recherche_guide.php">');
	print('<table class="form" cellspacing="0" style="width:500px;margin-left:20px">');
	print('<tr><th class="form" colspan="2">Critères de sélection</td></tr>');
	print('<tr><td align="right" class="form_milieu">Type de document </td><td class="form_milieu">'. getCombo(2) .'</td></tr>');
	print('<tr><td align="right" class="form_milieu">Bibliothèque </td><td class="form_milieu">'. getCombo(4) .'</td></tr>');
	print('<tr><td align="right" class="form_milieu">Mode d\'affichage du résultat </td><td class="form_milieu">'. getCombo(6) .'</td></tr>');
	print('<th class="form" colspan="2" align="center"><input type="submit" value="Lancer" class="bouton"></th>');
	print('</table></form>');
	quit("");
}

// ----------------------------------------------------------------
// Rubriques
// ----------------------------------------------------------------
$html='<div class="guide">';
$html.=getFilAriane();

// Racine
if(!$_REQUEST["rubrique"])
{
	$rubriques=getRubriquesMain();
	$html.=getHtmlGuide($rubriques);
	print($html);
	print('</div>');
	quit("");
}
// Sous niveaux
$rubriques=getRubriques();
$html.=getHtmlGuide($rubriques);
print($html);
print('</div>');

// ----------------------------------------------------------------
// Criteres de selection du formulaire
// ----------------------------------------------------------------
$condition="";
if($_SESSION["guide_selection"]["type_document"]) 
{
	$condition="type_doc=".$_SESSION["guide_selection"]["type_document"]." and ";
	$texte_condition="Type de document = ".getLibCodifVariable("types_docs",$_SESSION["guide_selection"]["type_document"]);
}
if($_SESSION["guide_selection"]["bibliotheque"]) 
{
	$against=" +".$_SESSION["guide_selection"]["bibliotheque"];
	if($texte_condition) $texte_condition.=" - ";
	$texte_condition.="bibliothèque = ".$oBib->getNomCourt(substr($_SESSION["guide_selection"]["bibliotheque"],1));
}

$rubrique=$_REQUEST["rubrique"];
if($rubrique[0]=="X") quit("");
else $rubrique ="+".$rubrique."*";

// ----------------------------------------------------------------
// Affichage mode liste de Notices
// ----------------------------------------------------------------
if($_SESSION["guide_selection"]["affichage"]==1)
{
	$req = " from notices where ".$condition."MATCH(facettes) AGAINST('".$rubrique.$against." 		' IN BOOLEAN MODE)";
	$req_comptage="select count(*)".$req;
	$req_liste="select id_notice".$req;
	print(BR."<B>Requete : </b>".$req_liste.BR);
	if($texte_condition) print(BR.'<span class="violet" style="margin-left:10px"><b>'.$texte_condition.'</b></span>');
	$temps=time();
	$nombre=$sql->fetchOne($req_comptage);
	print(BR.'<span class="orange" style="margin-left:10px"><b>Temps requête de comptage : '.(time()-$temps).' secondes'.'</b></span>'.BR);
	if(!$nombre) quit("Aucun résultat trouvé");
	$temps=time();
	$liste=$oListe->getListe($req_liste,$page);
	print('<span class="orange" style="margin-left:10px"><b>Temps requête résultat : '.(time()-$temps).' secondes'.'</b></span>'.BR);
	print('<span class="orange" style="margin-left:10px"><b>Résultats trouvés : '.$nombre.'</b></span>'.BR);
	$args_url="rubrique=".$_REQUEST["rubrique"];
	print(BR.$oListe->getHtml($liste,$args_url));
}

// ----------------------------------------------------------------
// Affichage mode Tags auteurs
// ----------------------------------------------------------------
if($_SESSION["guide_selection"]["affichage"]==3)
{
	$req = "select facettes from notices where ".$condition." MATCH(facettes) AGAINST('".$rubrique.$against."' IN BOOLEAN MODE)";
	print(BR."<B>Requete : </b>".$req.BR);
	if($texte_condition) print(BR.'<span class="violet" style="margin-left:10px"><b>'.$texte_condition.'</b></span>');
	$temps=time();
	$html=getTags($req);
	print(BR.'<span class="orange" style="margin-left:10px"><b>Temps de calcul des tags : '.(time()-$temps).' secondes'.'</b></span>'.BR);
	if(!$html) quit("Aucun résultat trouvé");
	print(BR.$html.BR.BR);
}

// ----------------------------------------------------------------
// Affichage mode Facettes
// ----------------------------------------------------------------
if($_SESSION["guide_selection"]["affichage"]==2)
{
	$req = "select facettes from notices where ".$condition." MATCH(facettes) AGAINST('".$rubrique.$against."' IN BOOLEAN MODE)";
	print(BR."<B>Requete : </b>".$req.BR);
	if($texte_condition) print(BR.'<span class="violet" style="margin-left:10px"><b>'.$texte_condition.'</b></span>');
	$temps=time();
	$html=getFacettes($req);
	print(BR.'<span class="orange" style="margin-left:10px"><b>Temps de calcul des facettes : '.(time()-$temps).' secondes'.'</b></span>'.BR);
	if(!$html) quit("Aucun résultat trouvé");
	print(BR.$html.BR.BR);
}

quit("");

// ----------------------------------------------------------------
// Fonctions
// ----------------------------------------------------------------
function getFilAriane()
{
	$puce="&nbsp;&raquo;";
	if($_SESSION["guide"]["fil_ariane"]) $elems=explode(";",$_SESSION["guide"]["fil_ariane"]);
	$elems[]=$_REQUEST["rubrique"];
	foreach($elems as $elem)
	{
		if(!$elem) continue;
		$fil_ariane.=";".$elem;
		$url="recherche_guide.php?fil_ariane=".$fil_ariane."&rubrique=".$elem;
		$html.='<a class="guide_ariane" href="'.$url.'">'.$puce.getCodif($elem).'</a>';
		if($elem == $_REQUEST["rubrique"]) break;
	}
		
	// Html
	$_SESSION["guide"]["fil_ariane"]=$fil_ariane;
	$html='<div class="guide_ariane">'.'<a class="guide_ariane" href="recherche_guide.php">'.$puce.'Accueil</a>'.$html.'</div>';
	return $html;
}

function getCodif($code)
{
	global $sql;
	$type=$code[0];
	$id=substr($code,1);
	if($type == "X") { $rubrique=array(" ","Dewey","Pcdm4","Matières"); return $rubrique[intval($id)]; }
	if($type == "D") return dewey::getLibelle($id);
	if($type == "P") return pcdm4::getLibelle($id);
}

function getRubriquesMain()
{
	return array(array("id"=>"X1"),array("id"=>"X2"));
}

function getRubriques()
{
	global $sql;
	$tag=$_REQUEST["rubrique"];
	$rubrique=$tag[0];
	$id=substr($tag,1);
	
	// Rubriques racine
	if($rubrique == "X")
	{
		switch(intval($id))
		{
			case 1: 	// dewey
				$liste=dewey::getIndices("root");
				foreach($liste as $indice) $items[]="D".$indice["id_dewey"];
				break;
			case 2: 	// pcdm4
				$liste=pcdm4::getIndices("root");
				foreach($liste as $indice) $items[]="P".$indice["id_pcdm4"];
				break;
		}
	}
	// Dewey
	if($rubrique == "D")
	{
		$liste=dewey::getIndices($id);
		if(!$liste) return false;
		foreach($liste as $indice) $items[]="D".$indice["id_dewey"];
	}
	// Pcdm4
	if($rubrique == "P")
	{
		$liste=pcdm4::getIndices($id);
		if(!$liste) return false;
		foreach($liste as $indice) $items[]="P".$indice["id_pcdm4"];
	}
	// Compter les items
	for($i=0; $i < count($items); $i++)
	{
		//$req="select count(*) from notices where MATCH(facettes) AGAINST('+".$items[$i]."' IN BOOLEAN MODE)";
		//$nombre=$sql->fetchOne($req);
		//if(!$nombre) continue;
		$ret[]=array("id"=>$items[$i],"nombre"=>$nombre);
	}
	return $ret;
}

function getHtmlGuide($rubriques)
{
	$puce="&raquo&nbsp;";
	$html='<div class="guide_contenu">';
	if(!$rubriques) $html.='<h3>Il n\'y a plus de sous-niveaux</h3>'; 
	else
	{
		foreach($rubriques as $rubrique)
		{
			$url='recherche_guide.php?rubrique='.$rubrique["id"];
			$html.=$puce.'<a class="guide_contenu" href="'.$url.'">'.getCodif($rubrique["id"]).'</a>'.BR;
		}
	}
	$html.='</div>';
	return $html;
}

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
		//$url=URL_BASE.'php/recherche_tags_auteurs.php?tag='.$enreg["id"];
		$url="#";
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
			$valeurs=array(""=>"toutes");
			global $oBib;
			$bibs=$oBib->getAll();
			foreach($bibs as $bib) 
			{
				$code="B".$bib["id_bib"];
				$valeurs[$code]=stripSlashes($bib["nom_court"]);
			}
			break;
		// Mode d'affichage du résultat
		case 6:
			$name = "affichage";
			$valeurs=array("1"=>"Liste de notices","2"=>"Facettes","3"=>"Nuages de tags Auteurs");
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
	// Fabriquer le Html
	$html='<div style="width:500px;margin-left:20px"><table class="form">';
	foreach($table as $type => $valeurs)
	{
		$html.='<tr><th class="form" align="left" colspan="2">'.$valeurs["titre"].'</th></tr>';
		for($i=1; $i< count($valeurs); $i++)
		{
			//$url='<a href="'.URL_BASE.'php/recherche_recherche.php?lancer=oui';
			//if($type=="type_doc") $url.="&type_doc=".substr($valeurs[$i]["id"],1).'">';
			//else 
			//{
			//	if($_REQUEST["type_doc"]) $url.="&type_doc=".$_REQUEST["type_doc"];
			//	$facette='['.$valeurs[$i]["id"]."]";
			//	if(strpos($_REQUEST["facette"],$facette) !== false) $facette="";
			//	$url.='&facette='.$_REQUEST["facette"].$facette.'">';
			//}
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
