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
//        RECHJERCHE DE DOUBLONS
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$page=$_REQUEST["page"];

//---------------------------------------------------------------------------------
// Menu
//---------------------------------------------------------------------------------
print('<h1>Recherche de doublons</h1>');

$url="analyse_recherche_doublons.php?type_liste=";
print('<div class="liste" style="margin-left:30px">');
print('<div class="liste_titre"><a class="liste" href="'.$url.'ISBN">&raquo;&nbsp;Doublons d\'ISBN</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'EAN">&raquo;&nbsp;Doublons d\'EAN</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'COMMERCIAL">&raquo;&nbsp;Doublons sur identifiant commercial</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'CODES_BARRES">&raquo;&nbsp;Doublons de codes-barres</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'CODES_BARRES_TOUS_SITES">&raquo;&nbsp;Doublons de codes-barres sites confondus</a></div>');
print('<div class="liste_titre"><a class="liste" href="'.$url.'CLEF_ALPHA">&raquo;&nbsp;Doublons sur clef alphabétique</a></div>');
print('</div>');
print(BR.BR);

if(!$_REQUEST["type_liste"]) exit;
unset($_SESSION["url_retour"]);

//---------------------------------------------------------------------------------
// ISBN
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="ISBN")
{
	print('<h5>Doublons d\'ISBN</h5>');

	$args_url="type_liste=ISBN";
	
	$req="SELECT isbn,count(*) FROM notices where isbn > '' group by 1 having count(*) > 1";
	$liste=$oListe->getListeByColonne($req,"isbn",$page);
}
//---------------------------------------------------------------------------------
// EAN
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="EAN")
{
	print('<h5>Doublons d\'EAN</h5>');

	$args_url="type_liste=EAN";
	
	$req="SELECT ean,count(*) FROM notices where ean > '' group by 1 having count(*) > 1";
	$liste=$oListe->getListeByColonne($req,"ean",$page);
}
//---------------------------------------------------------------------------------
// ID_COMMERCIALE
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="COMMERCIAL")
{
	print('<h5>Doublons sur identifiant commercial</h5>');

	$args_url="type_liste=COMMERCIAL";
	
	$req="SELECT id_commerciale,count(*) FROM notices where id_commerciale > '' group by 1 having count(*) > 1";
	$liste=$oListe->getListeByColonne($req,"id_commerciale",$page);
}
//---------------------------------------------------------------------------------
// CODES_BARRES
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CODES_BARRES")
{
	print('<h5>Doublons de codes-barres</h5>');

	$args_url="type_liste=CODES_BARRES";
	
	$req="SELECT code_barres,id_bib FROM exemplaires where code_barres > '' group by 1,2 having count(*) > 1";
	$liste=$oListe->getListeByColonnes($req,$page,true);
}

//---------------------------------------------------------------------------------
// CODES_BARRES_TOUS_SITES
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CODES_BARRES_TOUS_SITES")
{
	print('<h5>Doublons de codes-barres</h5>');

	$args_url="type_liste=CODES_BARRES";
	
	$req="SELECT code_barres FROM exemplaires where code_barres > '' group by 1 having count(*) > 1";
	$liste=$oListe->getListeByColonnes($req,$page,true);
}

//---------------------------------------------------------------------------------
// CLEF ALPHABETIQUE
//---------------------------------------------------------------------------------
if($_REQUEST["type_liste"]=="CLEF_ALPHA")
{
	print('<h5>Doublons sur clef alphabétique</h5>');

	$args_url="type_liste=CLEF_ALPHA";
	
	$req="select clef_alpha FROM `notices` WHERE isbn='' and ean='' group by 1 having count(*) > 1";
	$liste=$oListe->getListeByColonnes($req,$page);
}

//---------------------------------------------------------------------------------
// Affichage de la liste
//---------------------------------------------------------------------------------
print('<div style="margin-left:30px">');
if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
else print($oListe->getHtml($liste,$args_url));
print('</body></html>');
?>