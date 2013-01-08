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
//        ANALYSE DES ARTICLES DE PERIODIQUES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$page=$_REQUEST["page"];

//---------------------------------------------------------------------------------
// Menu
//---------------------------------------------------------------------------------
print('<h1>Analyse des articles de périodiques</h1>');

// comptages
$nb_chapeaux=$sql->fetchOne("select count(distinct clef_chapeau) from notices_articles order by 1");
$no_chapeau=$sql->fetchOne("select count(*) from notices_articles where clef_chapeau=''");
if(!$no_chapeau) $no_chapeau="aucune";
$no_unimarc=$sql->fetchOne("select count(*) from notices_articles where unimarc=''");
if(!$no_unimarc) $no_unimarc="aucune";

$url="analyse_articles_periodiques.php?type_liste=";
print('<div class="liste" style="margin-left:30px">');
print('<div class="liste_titre"><a class="liste" href="'.$url.'LISTE_CHAPEAUX">&raquo;&nbsp;Nombre de chapeaux : '.$nb_chapeaux.'</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'NO_UNIMARC">&raquo;&nbsp;Notices orphelines sans unimarc : '.$no_unimarc.'</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'NO_CHAPEAU">&raquo;&nbsp;Notices orphelines sans chapeau : '.$no_chapeau.'</a></div>');
print('</div>');
print(BR);

if(!$_REQUEST["type_liste"]) exit;
unset($_SESSION["url_retour"]);

//---------------------------------------------------------------------------------
// CHAPEAUX
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="LISTE_CHAPEAUX")
{
	print('<h5>Liste des chapeaux</h5>');

	$args_url="type_liste=LISTE_CHAPEAUX";
	$req="select clef_chapeau,count(*) from notices_articles group by 1 order by 1";
	$data=$sql->fetchAll($req);
	if(!$data) print(BR.'<h3>Aucune notice trouvée</h3>');
	
	// entete
	echo '<div style="margin-left:30px"><table>';
	echo '<tr>';
	echo '<th>&nbsp;</th>';
	echo '<th>Chapeau</th>';
	echo '<th>Articles</th>';
	echo '</tr>';

	// Liste
	foreach($data as $chapeau)
	{
		$url=rendUrlImg("loupe.png", "analyse_articles_periodiques.php","type_liste=CHAPEAU&clef_chapeau=".$chapeau["clef_chapeau"]);
		echo '<tr>';
		echo '<td>'.$url.'</td>';
		echo '<td>'.$chapeau["clef_chapeau"].'</td>';
		echo '<td align="right">'.$chapeau["count(*)"].'</td>';
		echo '</tr>';
	}
	
	// fin
	echo '</table></div>';
	exit;
}

//---------------------------------------------------------------------------------
// NOTICES POUR 1 CHAPEAU
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CHAPEAU")
{
	$clef_chapeau=$_REQUEST["clef_chapeau"];
	print('<h5>Articles de : '.$clef_chapeau.'</h5>');

	$args_url="type_liste=CHAPEAU&clef_chapeau=".$clef_chapeau;

	$req="SELECT id_article from notices_articles where clef_chapeau='$clef_chapeau'";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// NOTICES SANS UNIMARC
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="NO_UNIMARC")
{
	print('<h5>Notices orphelines sans unimarc</h5>');

	$args_url="type_liste=NO_UNIMARC";
	
	$req="SELECT id_article from notices_articles where unimarc=''";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// NOTICES SANS CHAPEAU
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="NO_CHAPEAU")
{
	print('<h5>Notices orphelines sans chapeau</h5>');

	$args_url="type_liste=NO_CHAPEAU";
	
	$req="SELECT id_article from notices_articles where clef_chapeau=''";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// Affichage de la liste
//---------------------------------------------------------------------------------
print('<div style="margin-left:30px">');
if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
else print($oListe->getHtmlArticles($liste,$args_url));
print('</body></html>');
?>