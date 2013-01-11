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
// AFFICHAGE DES LOGS D'INTEGRATION
////////////////////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_log.php");
$log=new Class_log("integration");

// ----------------------------------------------------------------
// AFFICHAGE d'1 LOG COMPLET
// ----------------------------------------------------------------
if($_REQUEST["log"])
{
	print('<h1>Log du '.$_REQUEST["date"].'</h1>');
	print('<div style="margin-left:20px">');
	$log->afficher($_REQUEST["log"]);
	print('</div></body></html>');
	exit;
}

// ----------------------------------------------------------------
// LISTE
// ----------------------------------------------------------------
print('<h1>Journal des intégrations</h1>');
print('<div class="liste">');
print('<table width="600px">');

// ----------------------------------------------------------------
// Logs d'intégration + Erreurs + warnings
// ----------------------------------------------------------------
print('<tr>');
print('<th colspan="2">Date</th>');
print('<th colspan="2">Rapport</th>');
print('<th width="15%">Notices traitées</th>');
print('<th width="15%">Erreurs</th>');
print('<th  width="15%">Anomalies</th>');
print('</tr>');

$liste_integration=$log->rendListe();
for($i=0; $i < count($liste_integration); $i++)
{
	// Date
	$lig=$liste_integration[$i];
	print('<tr>');
	$url_synthese=rendUrlImg("loupe.png", "integre_journal_integrations.php","date=".$lig["date_sql"],"Synthèse par bibliothèques");
	print('<td>'. $url_synthese .'</td>');
	print('<td>'.$lig["date"].'</td>');
	
	// Integration
	$url=rendUrlImg("loupe.png", "integre_log.php","log=".$lig["fic"]."&date=".$lig["date"]."&type=INTEGRATION","Afficher le détail");
	print('<td>'. $url .'</td>');
	print('<td>'. $lig["taille"] .'</td>');
	
	// Notices traitees
	$nb=(double)$sql->fetchOne("select sum(pointeur_reprise) from integrations where traite != 'non' and traite='".$lig["date_sql"]."'"); 
	print('<td align="right">'. number_format($nb, 0, ',', ' ') .'</td>');
	
	// Erreurs
	$nb=(double)$sql->fetchOne("select sum(nb_erreurs) from integrations where traite != 'non' and traite='".$lig["date_sql"]."'"); 
	print('<td align="right">'. number_format($nb, 0, ',', ' ') .'</td>');
	
	// Warnings
	$nb=(double)$sql->fetchOne("select sum(nb_warnings) from integrations where traite != 'non' and traite='".$lig["date_sql"]."'"); 
	print('<td align="right">'. number_format($nb, 0, ',', ' ') .'</td>');
	print('</tr>');
}
print('</table></div>');
print('</body></html>');

?>