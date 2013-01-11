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
//        RECAP QUALITES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$page=$_REQUEST["page"];
unset($_SESSION["url_retour"]);

print('<h1>Analyse des notices par qualité</h1>');

//---------------------------------------------------------------------------------
// Liste generale
//---------------------------------------------------------------------------------
if(!isset($_REQUEST["qualite"]))
{
	print('<h5>Toutes qualités</h5>');
	print('<div class="liste"><table width="350px"><TR>');
	print('<th colspan="2" width="1%">&nbsp;</th>');
	print('<th>Qualité</th>');
	print('<th>Nombre</th>');
	print('<th>Pct.</th></tr>');
	
	$total=$sql->fetchOne("select count(*) from notices");
	$liste=$sql->fetchAll("select qualite,count(*) from notices group by 1",true);
	foreach($liste as $enreg)
	{
		if($enreg[1] > 0) $pct=number_format(($enreg[1] / $total) * 100,2, ',',' ');
		else $pct="0";
		print('<tr>');
		print('<td>'.rendUrlImg("loupe.png","stat_qualite.php","qualite=".$enreg[0],"Afficher les notices").'</td>');
		print('<td>'.rendUrlImg("bibliotheque.gif","stat_qualite.php","qualite=".$enreg[0]."&detail=oui","Détail par bibliothèques").'</td>');
		print('<td>'.getLibCodifVariable("code_qualite",$enreg[0]).'</td>');
		print('<td align="right">'.number_format($enreg[1],0, ',',' ').'</td>');
		print('<td align="right">'.$pct.' %</td>');
		print('</tr>');
	}
	print('</table></div></body></html>');
	exit;	
}

//---------------------------------------------------------------------------------
// Détail par bibliothèques pour 1 qualite
//---------------------------------------------------------------------------------
if($_REQUEST["detail"] == "oui")
{
	require_once("classe_bib.php");
	$oBib=new bibliotheque();
	
	print('<h5>Qualité : '.getLibCodifVariable("code_qualite",$_REQUEST["qualite"]).'</h5>');
	print('<div class="liste"><table width="350px"><TR>');
	print('<th width="1%">&nbsp;</th>');
	print('<th>Bibliothèque</th>');
	print('<th>Total</th>');
	print('<th>'.getLibCodifVariable("code_qualite",$_REQUEST["qualite"]).'</th>');
	print('<th>Pct.</th></tr>');
	
	$liste=$oBib->getAll();
	foreach ($liste as $bib) 
	{ 
		$id_bib=$bib["id_bib"];
		$nb_bib=$sql->fetchOne("Select count(distinct notices.id_notice) from notices,exemplaires where notices.id_notice = exemplaires.id_notice and id_bib=$id_bib");
		$nb_qualite=$sql->fetchOne("Select count(distinct notices.id_notice) from notices,exemplaires where notices.id_notice = exemplaires.id_notice and id_bib=$id_bib and qualite=".$_REQUEST["qualite"],true);
		$url=rendUrlImg("loupe.png", "analyse_liste_controle.php","type_liste=BIBLIOTHEQUE&id_bib=".$id_bib."&nom_bib=".$bib["nom_court"]);
		print ('<tr><td width="1%">'.$url.'</td>');
		print('<td>'.stripslashes($bib["nom_court"]).'</td>');
		print ('<td align="right">'.$nb_bib.'</td>');
		print ('<td align="right">'.$nb_qualite.'&nbsp;</td>');
		if($nb_qualite) $pct=($nb_qualite / $nb_bib) * 100;
		else $pct=0;
		print ('<td align="right">'.number_format($pct, 2, ',', ' ').'&nbsp;%.&nbsp;</td>');
		print('</tr>');
		flush();
	}
	print('</table></div></body></html>');
	exit;	
}


//---------------------------------------------------------------------------------
// Pour 1 qualite
//---------------------------------------------------------------------------------
if(isset($_REQUEST["qualite"]))
{
	$qualite=$_REQUEST["qualite"];
	print('<h5>Qualité : '.getLibCodifVariable("code_qualite",$qualite).'</h5>');
	$args_url="qualite=".$qualite;
	
	$req="Select id_notice from notices where qualite=$qualite";
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