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
//         AFFICHAGE d'une Notice complète
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_unimarc.php");
require_once("classe_bib.php");
$unimarc=new notice_unimarc();
$bib=new bibliotheque();

// Lire la notice
print('<h1>Notice complète</h1>');
$id_notice=$_REQUEST["id_notice"];
$notice_interne=$sql->fetchEnreg("select * from notices where id_notice=$id_notice");
$unimarc->ouvrirNotice($notice_interne["unimarc"],0);
$titre=$unimarc->getTitrePrincipal();

// Titre et barre de boutons
print('<div class="notice_titre">'.$titre.'</div>');
print('<div class="liste">');

$tout_fermer="document.getElementById('notice_interne').style.display='none';";
$tout_fermer.="document.getElementById('exemplaires').style.display='none';";
$tout_fermer.="document.getElementById('notice_unimarc').style.display='none';";
$tout_fermer.="document.getElementById('unimarc_natif').style.display='none';";
$b1='<input type="button" class="bouton" value="Notice interne" onclick="'.$tout_fermer.'document.getElementById(\'notice_interne\').style.display=\'block\';">';
$b2='<input type="button" class="bouton" value="Exemplaires" onclick="'.$tout_fermer.'document.getElementById(\'exemplaires\').style.display=\'block\';">';
$b3='<input type="button" class="bouton" value="Notice unimarc" onclick="'.$tout_fermer.'document.getElementById(\'notice_unimarc\').style.display=\'block\';">';
$b4='<input type="button" class="bouton" value="Unimarc natif" onclick="'.$tout_fermer.'document.getElementById(\'unimarc_natif\').style.display=\'block\';">';
if($_SESSION["url_retour"]){$url_retour="document.location='".$_SESSION["url_retour"]."'"; unset($_SESSION["url_retour"]);}
else $url_retour="history.back()";
$retour='<input type="button" class="bouton" value="Retour" onclick="'.$url_retour.'">';
print(BR.$b1."&nbsp;&nbsp;".$b2."&nbsp;&nbsp;".$b3."&nbsp;&nbsp;".$b4."&nbsp;&nbsp;".$retour.BR.BR);

$sep='<div class="separateur">:</div>';

// Notice interne
print('<div class="notice" id="notice_interne" style="display:block">');
print('<div class="notice_entete">Notice interne</div>');
print('<div class="notice_label">id notice</div>'.$sep.'<div class="notice_valeur">'.$id_notice.'</div>');
$lib=getLibCodifVariable("types_docs",$notice_interne["type_doc"]);

// articles de périodiques
if($notice_interne["type_doc"]==2)
{
	$clef_chapeau=$notice_interne["clef_chapeau"];
	$clef_numero=$notice_interne["tome_alpha"];
	$nb_articles=$sql->fetchOne("select count(*) from notices_articles where clef_chapeau='$clef_chapeau' and clef_numero='$clef_numero'");
	if($nb_articles) $article='<a href="'.URL_BASE.'php/stat_types_documents.php?type_doc=100&clef_chapeau='.$clef_chapeau.'&clef_numero='.$clef_numero.'">liste des articles liés('.$nb_articles.')</a>';
	else $article="aucun article lié";
	$article='<span style="margin-left:50px">--- '.$article.' ---</span>';
}
print('<div class="notice_label">Type de document</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["type_doc"]." - ".$lib.$article.'</div>');
print('<div class="notice_label">Isbn</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["isbn"].'</div>');
print('<div class="notice_label">Ean</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["ean"].'</div>');
print('<div class="notice_label">Clef commerciale</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["id_commerciale"].'</div>');
print('<div class="notice_label">Clef alphabétique</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["clef_alpha"].'</div>');

// chapeau et lien vers liste des tomes
if($notice_interne["clef_chapeau"])
{
	$clef_chapeau=$notice_interne["clef_chapeau"];
	$nb_tomes=$sql->fetchOne("select count(*) from notices where clef_chapeau='$clef_chapeau'");
	if($nb_tomes) $tomes='<a href="'.URL_BASE.'php/recherche_liste_tomes.php?clef_chapeau='.$clef_chapeau.'">liste des tomes liés('.$nb_tomes.')</a>';
	else $tomes="aucun tome lié";
	$tomes='<span style="margin-left:50px">--- '.$tomes.' ---</span>';
	print('<div class="notice_label">Clef chapeau</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["clef_chapeau"].$tomes.'</div>');
}
print('<div class="notice_label">N° de partie</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["tome_alpha"].'</div>');
print('<div class="notice_label">Année</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["annee"].'</div>');
$lib=getLibCodifVariable("code_qualite",$notice_interne["qualite"]);
print('<div class="notice_label">Qualité</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["qualite"]. " - ".$lib.'</div>');
$lib=getLibCodifVariable("oui_non",$notice_interne["exportable"]);
print('<div class="notice_label">Libre de droits</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["exportable"]. " - ".$lib.'</div>');
print('<div class="notice_label">Date de création</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["date_creation"].'</div>');
print('<div class="notice_label">Date de mise à jour</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["date_maj"].'</div>');
print('<div class="notice_zone">Clefs de tri</div>');
print('<div class="notice_label">Titre principal</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["alpha_titre"].'</div>');
print('<div class="notice_label">Auteur principal</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["alpha_auteur"].'</div>');
print('<div class="notice_zone">Champs de recherche</div>');
print('<div class="notice_label">Titres</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["titres"].'</div>');
print('<div class="notice_label">Auteurs</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["auteurs"].'</div>');
print('<div class="notice_label">Editeurs</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["editeur"].'</div>');
print('<div class="notice_label">Collections</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["collection"].'</div>');
print('<div class="notice_label">Matières</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["matieres"].'</div>');
print('<div class="notice_label">Dewey et pcdm4</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["dewey"].'</div>');
print('<div class="notice_label">Facettes</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["facettes"].'</div>');
$facettes=explode(" ",$notice_interne["facettes"]);
if($facettes)
{
	foreach($facettes as $facette)
	{
		if($facette[0] == "S") $section=$sql->fetchOne("select libelle from codif_section where id_section=".substr($facette,1));
		if($facette[0] == "G") $genre=$sql->fetchOne("select libelle from codif_genre where id_genre=".substr($facette,1));
	}
}
if(!$section) $section = "non identifiée";
if(!$genre) $genre = "non identifié";
print('<div class="notice_label">Section</div>'.$sep.'<div class="notice_valeur">'.$section.'</div>');
print('<div class="notice_label">Genre</div>'.$sep.'<div class="notice_valeur">'.$genre.'</div>');
print('<div class="notice_zone">Statistiques</div>');
print('<div class="notice_label">Visualisations</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["nb_visu"].'</div>');
print('<div class="notice_label">Réservations</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["nb_resa"].'</div>');

print('<div class="notice_zone">Répartition des exemplaires</div>');
$bibs=$sql->fetchAll("select id_bib,count(*) from exemplaires where id_notice=$id_notice group by 1",true);
if($bibs)
{
	foreach($bibs as $origine)
	{
		print('<div class="notice_label" style="width:35%"><b>'.$bib->getNomCourt($origine[0]).'</b></div>'.$sep);
		print('<div class="notice_valeur">'.$origine[1].' ex.</div>');
	}
}
print('</div>');

// Exemplaires
$exemplaires=$sql->fetchAll("select * from exemplaires where id_notice=$id_notice order by id_bib,code_barres" );
print('<div class="notice" id="exemplaires" style="display:none;width:1100px">');
print('<div class="notice_entete" style="width:99%">Exemplaires</div>');
print('<table class="blank" width="100%" cellspacing="0">');
print('<tr><td>n°</td><td>Bibliothèque</td><td>Code-barres</td>');
print('<td>Cote</td>');
print('<td>Section</td>');
print('<td>Genre</td>');
print('<td>Annexe</td>');
print('<td>Emplacement</td>');
print('<td>Activité</td>');
print('<td>Id origine</td>');
print('<td>Date nouveauté</td>');
print('<td>Zone 995</td>');
print('</tr>');
$num=0;
foreach($exemplaires as $ex)
{
	$id_bib=$ex["id_bib"];
	$num++;
	foreach($ex as $key => $valeur) if(!$valeur) $ex[$key]="&nbsp;";
	print('<tr>');
	print('<td class="blank"><b>'.$num.'</b></td>');
	print('<td class="blank">'.$bib->getNomCourt($id_bib).'</td>');
	print('<td class="blank">'.$ex["code_barres"].'</td>');
	print('<td class="blank">'.$ex["cote"].'</td>');
	print('<td class="blank">'.$ex["section"].BR.fetchOne("select libelle from codif_section where id_section='".$ex["section"]."'").'</td>');
	print('<td class="blank">'.$ex["genre"].BR.fetchOne("select libelle from codif_genre where id_genre='".$ex["genre"]."'").'</td>');
	print('<td class="blank">'.$ex["annexe"].BR.fetchOne("select libelle from codif_annexe where id_annexe='".$ex["annexe"]."'").'</td>');
	print('<td class="blank">'.$ex["emplacement"].BR.fetchOne("select libelle from codif_emplacement where id_emplacement='".$ex["emplacement"]."'").'</td>');
	print('<td class="blank">'.$ex["activite"].'</td>');
	print('<td class="blank">'.$ex["id_origine"].'</td>');
	print('<td class="blank">'.$ex["date_nouveaute"].'</td>');
	print('<td class="blank">'.str_replace(" ",BR,$ex["zone995"]).'</td>');
	print('</tr>');
}
print('</table></div>');

// Notice unimarc
$notice=$unimarc->getAll();
print('<div class="notice" id="notice_unimarc" style="display:none">');
print('<div class="notice_entete">Notice unimarc</div>');
foreach($notice["label"] as $lig)
{
	print('<div class="notice_label">'.$lig[0] .'</div>'.$sep.'<div class="notice_valeur">'.$lig[1].'</div>');
}
foreach($notice["zones"] as $zone)
{
	print('<div class="notice_zone">&raquo;&nbsp;'.$zone["zone"].'</div>');
	if(trim($zone["indicateur1"])) print('<div class="notice_indicateur">indicateur 1</div>'.$sep.'<div class="notice_valeur">'.$zone["indicateur1"].'</div>');
	if(trim($zone["indicateur1=2"])) print('<div class="notice_indicateur">indicateur 2</div>'.$sep.'<div class="notice_valeur">'.$zone["indicateur2"].'</div>');
	for($i=0; $i < count($zone["champs"]); $i++)
	{
		$champ=$zone["champs"][$i];
		print('<div class="notice_champ">'.$champ["code"]. '</div>'.$sep.'<div class="notice_valeur">'.$champ["valeur"].'</div>'); 
	}
}
print('</div>');

// Unimarc natif
$natif=$unimarc->getUnimarcNatif();
print('<div class="notice" id="unimarc_natif" style="display:none">');
print('<div class="notice_entete">Unimarc natif</div>');
print('<div class="notice_zone">Bloc de label</div>');
print('<div class="notice_valeur">'.$natif["label"].'</div>');
// zones
print('<div class="notice_zone">Bloc des zones</div>');
print('<table class="blank">');
print('<tr><td class="blank">Zone</td><td class="blank">Longueur</td><td class="blank">Adresse</td><td class="blank">Contenu</td></tr>');
for($i=0; $i < count($natif["zones"]); $i++)
{
	$zone=$natif["zones"][$i];
	print('<tr>');
	print('<td class="blank">'.$zone["label"].'</td>');
	print('<td class="blank">'.$zone["length"].'</td>');
	print('<td class="blank">'.$zone["adress"].'</td>');
	print('<td class="blank">'.$natif["data"][$i]["content"].'</td>');
	print('</tr>');
}
print('</table>');
print('</div>');

// Fin
print('</div><br><br></body></html>');

?>