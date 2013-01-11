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
////////////////////////////////////////////////////////////////////////////////////////////
// AFFICHAGE DES TRAITEMENTS EN ATTENTE
////////////////////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_bib.php");
$oBib=new bibliotheque();
require_once("classe_profil_donnees.php");
$cls_profil= new profil_donnees();

// ----------------------------------------------------------------
// SUPPRESSION D'UN PANIER
// ----------------------------------------------------------------
if($_REQUEST["action"] == "SUPPRIMER")
{
	$id=$_REQUEST["id"];
	$fichier=$sql->fetchOne("select fichier from integrations where id=$id");
	unlink(getVariable("integration_path").$fichier);
	$sql->execute("delete from integrations where id =$id");
	
}

// ----------------------------------------------------------------
// LISTE
// ----------------------------------------------------------------
print('<h1>Traitements en cours ou en attente d\'intégration</h1>');
if(getVariable("clef_traitements") == "1") print('<br><h3>La base est bloquée</h3>');
else print('<br><h3>La base est débloquée</h3>');
print('<h3>Phase de traitement : '.getVariable("traitement_phase").'</h3>');

$liste = fetchAll("Select * from integrations where traite ='non' order by id");
if(!$liste) quit("");

print('<div class="liste"><table>');
print('<tr>');
print('<th>Bibliothèque</th>');
print('<th>Transféré le</th>');
print('<th>Opération</th>');
print('<th>Fichier</th>');
print('<th>Type</th>');
print('<th>Taille</th>');
print('<th>Effectué</th>');
print('<th>&nbsp;</th>');
print('</tr>');

foreach($liste as $enreg)
{
	$infos=$cls_profil->getInfosFichierIntegration($enreg["id"]);
	$suppr=rendUrlImg("suppression.gif", "integre_traitements_attente.php","action=SUPPRIMER&id=".$enreg["id"],"Supprimer ce fichier");
	print('<tr>');
	print('<td>'.$oBib->getNomCourt($enreg["id_bib"]).'</td>');
	print('<td align="center">'.rendDate($enreg["date_transfert"],1).'</td>');
	print('<td>'.getLibCodifVariable("import_type_operation",$enreg["type_operation"]).'</td>');
	print('<td align="left">'.$infos["fichier"].'</td>');
	print('<td align="left">'.$infos["type_fichier"].'</td>');
	print('<td align="right">'.$infos["taille"].'</td>');
	if($enreg["pointeur_reprise"])
	{
		$taille=filesize(getVariable("integration_path").$enreg["fichier"]);
		$pct=($enreg["pointeur_reprise"] / $taille) *100;
		$pct=number_format($pct, 2, ',', ' ')." %";
	}
	else $pct="0,00 %";
	print('<td align="right">'.$pct.'</td>');
	print('<td align="center">'.$suppr.'</td>');
	print('</tr>');
}
print('</div></table>');
print('</body></html>');

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

?>