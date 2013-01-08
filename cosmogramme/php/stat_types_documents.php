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
//        LISTES TYPES DE DOCUMENTS
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_liste_notices.php");
$oListe=new liste_notices();
$page=$_REQUEST["page"];
unset($_SESSION["url_retour"]);

print('<h1>Analyse des notices par types de documents</h1>');
flush();
//---------------------------------------------------------------------------------
// Liste generale
//---------------------------------------------------------------------------------
if(!isset($_REQUEST["type_doc"]))
{
	print('<h5>Tous types</h5>');
	print('<div class="liste"><table width="350px"><TR>');
	print('<th width="1%">&nbsp;</th>');
	print('<th>Type de doc.</th>');
	print('<th>Nombre</th></tr>');
	
	$liste=$sql->fetchAll("select type_doc,count(*) from notices group by 1",true);
	foreach($liste as $enreg)
	{
		print('<tr>');
		print('<td>'.rendUrlImg("loupe.png","stat_types_documents.php","type_doc=".$enreg[0]).'</td>');
		print('<td>'.getLibCodifVariable("types_docs",$enreg[0]).'</td>');
		print('<td align="right">'.number_format($enreg[1],0,""," ").'</td>');
		print('</tr>');
	}
	// articles de périodiques
	$nombre=$sql->fetchOne("select count(*) from notices_articles");
	print('<tr>');
	print('<td>'.rendUrlImg("loupe.png","stat_types_documents.php","type_doc=100").'</td>');
	print('<td>articles de périodiques</td>');
	print('<td align="right">'.number_format($nombre,0,""," ").'</td>');
	print('</tr>');

	print('</table></div></body></html>');
	exit;
	
}
//---------------------------------------------------------------------------------
// Pour 1 type de doc
//---------------------------------------------------------------------------------
if(isset($_REQUEST["type_doc"]))
{
	$type_doc=$_REQUEST["type_doc"];
	if($type_doc==100)
	{
		$libelle="articles de périodiques";
		if($_REQUEST["clef_chapeau"]) $req="Select id_article from notices_articles where clef_chapeau='".$_REQUEST["clef_chapeau"]."' and clef_numero='".$_REQUEST["clef_numero"]."' order by id_article";
		else $req="Select id_article from notices_articles where clef_chapeau > '' order by clef_chapeau,id_article";
	}
	else
	{
		$libelle=getLibCodifVariable("types_docs",$type_doc);
		$req="Select id_notice from notices where type_doc='$type_doc'";
	}
	print('<h5>Type de document : '.$libelle.'</h5>');
	$args_url="type_doc=".$type_doc;
	
	$liste=$oListe->getListe($req,$page);
}

//---------------------------------------------------------------------------------
// Affichage de la liste
//---------------------------------------------------------------------------------

print('<div style="margin-left:30px">');
if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
else print($oListe->getHtml($liste,$args_url,$type_doc));
print('</div></body></html>');
?>