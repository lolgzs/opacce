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
//        LISTES DE NOTICES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$page=$_REQUEST["page"];
unset($_SESSION["url_retour"]);

//---------------------------------------------------------------------------------
// Menu
//---------------------------------------------------------------------------------
print('<h1>Listes de controle</h1>');

$url="analyse_liste_controle.php?type_liste=";
print('<div class="liste" style="margin-left:30px">');
print('<div class="liste_titre"><a class="liste" href="'.$url.'CREATION">&raquo;&nbsp;Dernières notices créées</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'EAN">&raquo;&nbsp;Notices qui ont un ISBN et un EAN</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'CODE_BARRES">&raquo;&nbsp;Codes-barres non renseignés</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'EXEMPLAIRES">&raquo;&nbsp;Nombre d\'exemplaires trop élevé</a></div>');
print('</div>');
print(BR);

if(!$_REQUEST["type_liste"]) exit;

//---------------------------------------------------------------------------------
// Dernières notices créées
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CREATION")
{
	print('<h5>Dernières notices créées</h5>');
	$args_url="type_liste=CREATION";
	
	$req="Select id_notice from notices order by date_creation desc";
	$liste=$oListe->getListe($req,$page);
}
//---------------------------------------------------------------------------------
// Notices qui ont un EAN
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="EAN")
{
	print('<h5>Notices qui ont un ISBN et un EAN</h5>');
	$args_url="type_liste=EAN";
	
	$req="Select id_notice from notices where ean > '' and ISBN > ''";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// Notices avec des codes barres vides
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CODE_BARRES")
{
	print('<h5>Notices avec codes-barres non renseignés par dates de création décroissantes</h5>');
	$args_url="type_liste=CREATION";
	
	$req="Select distinct(notices.id_notice) from notices,exemplaires 
			where notices.id_notice=exemplaires.id_notice 
			and code_barres=''
			order by date_creation desc";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// Nombre d'exemplaires incohérent
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="EXEMPLAIRES")
{
	print('<h5>Notices avec plus de 50 exemplaires</h5>');
	$args_url="type_liste=EXEMPLAIRES";
	
	$req="Select notices.id_notice,count(*) from notices,exemplaires 
			where notices.id_notice=exemplaires.id_notice 
			group by 1 having count(*) > 50 order by 2 desc";
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// Dernières notices créées pour 1 bibliotheque
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="BIBLIOTHEQUE")
{
	print('<h5>Notices de la bibliothèque de : '.$_REQUEST["nom_bib"].'</h5>');
	$args_url="type_liste=BIBLIOTHEQUE&id_bib=".$_REQUEST["id_bib"]."&nom_bib=".$_REQUEST["nom_bib"];
	
	$id_bib=$_REQUEST["id_bib"];
	$req="Select distinct(notices.id_notice) from notices,exemplaires 
			where notices.id_notice=exemplaires.id_notice 
			and id_bib=$id_bib 
			order by alpha_titre";
	$liste=$oListe->getListe($req,$page);
} 

//---------------------------------------------------------------------------------
// Affichage de la liste
//---------------------------------------------------------------------------------
print('<div style="margin-left:30px">');
if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
else print($oListe->getHtml($liste,$args_url));
print('</div></body></html>');
?>