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
//////////////////////////////////////////////////
// STATISTIQUES : NOMBRES DE NOTICES PAR BIBS
//////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_bib.php");
$oBib=new bibliotheque();

?>
<h1>Statistiques par bibliothèques</h1>
	
<?PHP
////////////////////////////////////////////////////////////////////////////////////
// INTEGRATIONS D'UNE BIB
////////////////////////////////////////////////////////////////////////////////////

// Entête
print('<div class="liste"><table width="800px">');
print(' <tr>
 <th width="50%" colspan="2">Bibliothèque</th>
 <th width="10%">Notices</th>
 <th width="10%">Exemplaires</th>
 <th width="10%">Abonnés</th>
 <th width="10%">Prêts</th>
 <th width="10%">Réservations</th>
</tr>');

// Lignes
$liste=$oBib->getAll();
foreach ($liste as $bib) 
{ 
	$id_bib=$bib["id_bib"];
	$nb=fetchEnreg("Select count(distinct notices.id_notice),count(*) from notices,exemplaires where notices.id_notice = exemplaires.id_notice and id_bib=$id_bib",true);
	$nb["abonnes"]=fetchOne("select count(*) from bib_admin_users where ROLE_LEVEL=2 and ID_SITE=$id_bib");
	$nb["prets"]=fetchOne("select count(*) from prets where ID_SITE=$id_bib");
	$nb["reservations"]=fetchOne("select count(*) from reservations where ID_SITE=$id_bib");
	$url=rendUrlImg("loupe.png", "analyse_liste_controle.php","type_liste=BIBLIOTHEQUE&id_bib=".$id_bib."&nom_bib=".$bib["nom_court"]);
	print ('<tr><td width="1%">'.$url.'</td>');
	print('<td>('.$id_bib.')&nbsp;'.stripslashes($bib["nom_court"]).'</td>');
	print('<td align="right">'.number_format($nb[0],0, ',', ' ').'</td>');
	print('<td align="right">'.number_format($nb[1],0, ',', ' ').'&nbsp;</td>');
	print('<td align="right">'.number_format($nb["abonnes"],0, ',', ' ').'&nbsp;</td>');
	print('<td align="right">'.number_format($nb["prets"],0, ',', ' ').'&nbsp;</td>');
	print('<td align="right">'.number_format($nb["reservations"],0, ',', ' ').'&nbsp;</td>');
	print('</tr>');
	$nb_notices+=$nb[0];
	$nb_ex+=$nb[1];
	$nb_abonnes+=$nb["abonnes"];
	$nb_prets+=$nb["prets"];
	$nb_reservations+=$nb["reservations"];
	flush();
}
// total
print ('<th colspan="2"><b>Total</b></th>');
print ('<th align="right"><b>'.number_format($nb_notices,0, ',', ' ').'</b></th>');
print ('<th align="right"><b>'.number_format($nb_ex,0, ',', ' ').'&nbsp;</b></th>');
print ('<th align="right"><b>'.number_format($nb_abonnes,0, ',', ' ').'&nbsp;</b></th>');
print ('<th align="right"><b>'.number_format($nb_prets,0, ',', ' ').'&nbsp;</b></th>');
print ('<th align="right"><b>'.number_format($nb_reservations,0, ',', ' ').'&nbsp;</b></th>');
print('</tr>');

// Fini
print("</table></div></body></html>");

?>