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
// STATISTIQUES : NOMBRES DE NOTICES
//////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_stat.php");
$stat=new statistique();

?>
<h1>Statistiques globales</h1>
	
<?PHP
// Entête
print('<div class="liste"><table width="700px">');
print(' <tr>
 <th>Rubrique</th>
 <th>Nombre</th>
 <th>Pct</th>
 <th>Graphe</th>
</tr>');

// Rubriques
$nb=$sql->fetchOne("Select count(*) from notices");
$total=$nb;
$stat->printLigneStat("Notices",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from exemplaires");
$stat->printLigneStat("Exemplaires",$nb,$nb);

$homogene=getVariable("homogene_code_qualite");
$nb=$sql->fetchOne("Select count(*) from notices where qualite=$homogene");
$stat->printLigneStat("Homogènes",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where exportable=1");
$stat->printLigneStat("Libres de droits",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where isbn >''");
$stat->printLigneStat("Avec ISBN",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where ean >''");
$stat->printLigneStat("Avec EAN",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where ean ='' and isbn = '' and id_commerciale=''");
$stat->printLigneStat("Sans ISBN ni EAN ni no commercial",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where id_commerciale > ''");
$stat->printLigneStat("Avec no commercial",$nb,$total);

$nb=$sql->fetchOne("Select count(*) from notices where type_doc >0");
$stat->printLigneStat("Type de document identifié",$nb,$total);

// Fini
print("</table></div></body></html>");

?>