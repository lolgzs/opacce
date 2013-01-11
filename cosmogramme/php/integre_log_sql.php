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
$log_erreur=new Class_log("erreur");
$log_warning=new Class_log("warning");
$log_sql=new Class_log("sql");

////////////////////////
// AFFICHAGE d'1 LOG
////////////////////////

if($_REQUEST["log"])
{
	print('<h1>Log des erreurs SQL du '.$_REQUEST["date"].'</h1>');
	print('<div style="margin-left:20px">');
	$log->afficher($_REQUEST["log"]);
	print('</div></body></html>');
	exit;
}

// ----------------------------------------------------------------
// Logs des erreurs sql
// ----------------------------------------------------------------
print('<h1>Logs des erreurs SQL</h1>');

print('<div class="liste"><table width="600px">');
print(' <tr>
 <th width="5%" align="center">&nbsp;</th>
 <th width="95%" align="left">Logs des erreurs SQL</th>
</tr>');

$liste=$log_sql->rendListe();
foreach($liste as $lig)
{
	$url=rendUrlImg("loupe.png", "integre_log_sql.php","log=".$lig["fic"]."&date=".$lig["date"]."&type=ERREUR");
	print('<tr>');
	print('<td>'. $url .'</td>');
	print('<td>'.$lig["date"].'</td>');
	print('</tr>');
}
print('</table></div>');

print('</body></html>');

?>