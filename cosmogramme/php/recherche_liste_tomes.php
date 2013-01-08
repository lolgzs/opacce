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
// LISTE DES TOMES DE SERIES
//////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

unset($_SESSION["url_retour"]);

require_once("classe_liste_notices.php");
$oListe=new liste_notices();

//-----------------------------------------------------------------
// Resultat de la recherche
//-----------------------------------------------------------------
$clef_chapeau=$_REQUEST["clef_chapeau"];
print('<h3>Liste des tomes pour : '.$clef_chapeau.'</h3>');
$req="select id_notice from notices where clef_chapeau='$clef_chapeau'";
$liste=$oListe->getListe($req,$page);

// Résultat
if(!$liste) quit("Aucune notice trouvée");
print($oListe->getHtml($liste,""));

?>
