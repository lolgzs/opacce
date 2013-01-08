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
//  RECHERCHE PAR IDENTIFIANTS
//////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);

// Classe indexation
require_once("classe_indexation.php");
require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$ix= new indexation();

// On met les criteres de recherche dans la session
$page=$_REQUEST["page"];
if($_POST) $_SESSION["recherche"]=$_POST;
else $_POST=$_SESSION["recherche"];
$_SESSION["url_retour"]=URL_BASE."php/recherche_identifiants.php?lancer=oui&page=".$page;

// HTML
print('<h1>Accès aux notices par identifiants</h1>'.BR);
print('<form method="post" action="recherche_identifiants.php?lancer=oui">');
print('<table class="form" cellspacing="0" style="width:500px;margin-left:20px"><tr>');
print('<th class="form" colspan="2" align="left">Critères d\'accès</th></tr>');
print('<tr><td class="form_first" align="right">Numéro de notice</td><td class="form_first" align="left"><input type="text" name="numero_notice" size="10" value="'.stripSlashes($_POST["numero_notice"]).'"></td></tr>');
print('<tr><td class="form_first" align="right">Isbn</td><td class="form_first" align="left"><input type="text" name="isbn" size="15" value="'.stripSlashes($_POST["isbn"]).'"></td></tr>');
print('<tr><td class="form_first" align="right">Ean</td><td class="form_first" align="left"><input type="text" name="ean" size="15" value="'.stripSlashes($_POST["ean"]).'"></td></tr>');
print('<tr><td class="form_first" align="right">Numéro commercial</td><td class="form_first" align="left"><input type="text" name="id_commerciale" size="30" value="'.stripSlashes($_POST["id_commerciale"]).'"></td></tr>');
print('<th class="form" colspan="2"><input type="submit" value="Lancer" class="bouton"></th>');
print('</table></form>');

if(!$_REQUEST["lancer"]) quit("");

/////////////////////////////////////////////////////////////////
// Resultat de la recherche
/////////////////////////////////////////////////////////////////
extract($_POST);
if($numero_notice) {$condition = "id_notice=$numero_notice"; $titre="numéro de notice = ".$numero_notice;}
elseif($isbn) {$condition = "isbn='$isbn'"; $titre="isbn = ".$isbn; }
elseif($ean) {$condition = "ean='$ean'"; $titre="ean = ".$ean; }
elseif($id_commerciale) {$condition = "id_commerciale='$id_commerciale'"; $titre="numéro commercial = ".$id_commerciale; }
if(!$condition) quit("Vous n'avez indiqué aucun critère d'accès");
print('<h3>'.$titre.'</h3>');
$req="select id_notice from notices where ".$condition;
$liste=$oListe->getListe($req,$page);

// Résultat
if(!$liste) quit("Aucune notice trouvée");
$args_url="lancer=oui";
print($oListe->getHtml($liste,$args_url));
quit("");

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 class="erreur" style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

?>
