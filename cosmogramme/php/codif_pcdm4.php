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
//            GESTION DES INDICES PCDM4
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_pcdm4.php");
$pcdm4=new pcdm4();
extract($_POST);

?>
<script>
	function give_libelle(sIndice,sValeur)
	{
		if(sValeur == "") return;
		oChampRetour=document.getElementById("indice_a_ecrire");
		oChampRetour.value=oChampRetour.value + sIndice + "#" + sValeur + ";";
	}
	function give_suppression()
	{
		oChampRetour=document.getElementById("indice_a_ecrire");
		chaine="";
		for (var i=0;i<document.getElementsByName("selection").length;i++) 
		{
			if (document.getElementsByName("selection")[i].checked==true) 
			{
         	if(chaine > '') chaine=chaine+";";
         	chaine=chaine+document.getElementsByName("selection")[i].id;
    	}
     }
     oChampRetour.value=oChampRetour.value=chaine;
		}
</script>
<?PHP

// ----------------------------------------------------------------
// ACCUEIL
// ----------------------------------------------------------------
if($_REQUEST["action"]=="")
{
	print('<h1>Gestion des indices PCDM4</h1>');
	$nb=$sql->fetchOne("Select count(*) from codif_pcdm4");
	$nb1=$sql->fetchOne("Select count(*) from codif_pcdm4 where libelle=''" );
	print('<span class="orange"><b>La base contient '.number_format($nb, 0, ',', ' ').' fiches pcdm4</b></span>'.BR);
	print('<span class="orange"><b>Libellés non renseignés : '.number_format($nb1, 0, ',', ' ').'</b></span>'.BR.BR);
	
	print('<div class="liste">');

	$url="codif_pcdm4.php?action=NOLIB";
	print('<tr><form id="nolib" method="post" action="'.$url.'">');
	print('<table width="60%" cellspacing="0">');
	print('<tr><th align="left" width="50%">Indices sans libellés</th>');
	print('<th align="right"><input type="submit" class="bouton" value="Lancer">&nbsp;&nbsp;&nbsp;</th>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Nombre de décimales:&nbsp;</td>');
	print('<td><input type ="text" name="nbdec" value="3" size="3" align="left"></td>');
	print('</tr>');
	print('</table></form>');	
	
	$url="codif_pcdm4.php?action=MAJ";
	print('<tr><form id="suppression" method="post" action="'.$url.'">');
	print('<table width="60%" cellspacing="0">');
	print('<tr><th align="left" width="50%">Modification de libellés</th>');
	print('<th align="right"><input type="submit" class="bouton" value="Lancer">&nbsp;&nbsp;&nbsp;</th>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Nombre de décimales:&nbsp;</td>');
	print('<td><input type ="text" name="nbdec" value="3" size="3" align="left"></td>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Indices commencent par:&nbsp;</td>');
	print('<td><input type ="text" name="commencepar" value="" size="7" align="left"></td>');
	print('</tr>');
	print('</table></form>');	
	
	$url="codif_pcdm4.php?action=SUPPRESSION";
	print('<tr><form id="suppression" method="post" action="'.$url.'">');
	print('<table width="60%" cellspacing="0">');
	print('<tr><th align="left" width="50%">Suppression d\'indices</th>');
	print('<th align="right"><input type="submit" class="bouton" value="Lancer">&nbsp;&nbsp;&nbsp;</th>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Nombre de décimales:&nbsp;</td>');
	print('<td><input type ="text" name="nbdec" value="3" size="3" align="left"></td>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Indices commencent par:&nbsp;</td>');
	print('<td><input type ="text" name="commencepar" value="" size="7" align="left"></td>');
	print('</tr>');
	print('</table></form>');
	
	$url="codif_pcdm4.php?action=CONSOLIDATION";
	print('<tr><form id="consolidation" method="post" action="'.$url.'">');
	print('<table width="60%" cellspacing="0">');
	print('<tr><th align="left" width="50%">Consolidation par une liste d\'indices</th>');
	print('<th align="right"><input type="submit" class="bouton" value="Lancer">&nbsp;&nbsp;&nbsp;</th>');
	print('</tr>');
	print('<tr>');
	print('<td colspan="2" align="center"><input type ="checkbox" name="remplacer">Remplacer les libellés présents dans la base</td>');
	print('</tr>');
	print('</table></form>');
	
}

// ----------------------------------------------------------------
// CONSOLIDATION PAR LISTE
// ----------------------------------------------------------------
if($_REQUEST["action"]=="CONSOLIDATION")
{
	print('<h1>Consolidation par une liste d\'indices</h1>');
	print('<div align="center">');
	print('<h3>Collez votre liste dans le champ ci-dessous, puis validez</h3>');
	print('<p align="left">NB : Les lignes doivent être séparées par des retours-chariot et l\'indice doit être séparé du libellé par une tabulation.</p>');
	$url="codif_pcdm4.php?action=ECRIRE_LISTE&remplacer=".$_REQUEST["remplacer"];
	print('<tr><form id="consolidation" method="post" action="'.$url.'">');
	print('<br><textarea name="liste_indice" cols="70" rows="20" style="margin-left:30px"></textarea>');
	print('<br><br><input type="submit" class="bouton" value="Valider la liste">');
	print('</form>');
}

// ----------------------------------------------------------------
// CONSOLIDATION PAR LISTE => ECRITURE
// ----------------------------------------------------------------
if($_REQUEST["action"]=="ECRIRE_LISTE")
{
	extract($_POST);
	print('<h1>Ecriture des indices</h1>');
	print('<div>');
	if(trim($liste_indice) > "" )
	{
		$indices=explode(chr(13).chr(10), $liste_indice);
		$majs=0; $pas_traites=0; $creations=0;
		for($i=0; $i<count($indices); $i++)
		{
			$elem=explode(chr(9),$indices[$i]);
			$indice=$elem[0]; $libelle=$elem[1];
			$indice=trim(str_replace(".","",$indice));
			if(!$indice) continue;
			$controle=$sql->fetchOne("select count(*) from codif_pcdm4 where id_pcdm4='$indice'");
			if($controle)
			{
				if($_REQUEST["remplacer"] == "on") 
				{
					$mode="Remplacement"; 
					$majs+=$pcdm4->ecrire($indice,$libelle);
				}
				else {$mode="Pas de traitement car existe déjà"; $pas_traites++;}
			} 
			else {$mode="Création"; $creations++; $sql->insert("codif_pcdm4",array("id_pcdm4"=>$indice,"libelle"=>$libelle));}
			print('<span class="violet" style="margin-left:20px"><b>'.$indice.' : </b></span><span>'.$mode.'</span>'.BR);
		}
		print('<br><h3>Traitement terminé avec succès</h3>');
		print('<h3>'.$creations." fiche(s) créée(s) - ".$majs." fiche(s) modifiée(s) - ".$pas_traites." fiche(s) non traitée(s)</h3>");
	}
}

// ----------------------------------------------------------------
// MISES A JOUR DE LIBELLES
// ----------------------------------------------------------------
if($_REQUEST["action"]=="MAJ")
{
	print('<h1>Mise à jour des libellés</h1>');
	print('<div align="center">');
	print('<h3>Modifiez les libellés puis validez en bas de la page</h3>');
	$url="codif_pcdm4.php?action=ECRIRE";
	print('<form method="POST" action="'.$url.'">');
	print('<input type="hidden" id="indice_a_ecrire" name="indice_a_ecrire">');
	print('<table>');
	
	$req="Select id_pcdm4,libelle from codif_pcdm4";
	if($nbdec >0) $where=" Where LENGTH(id_pcdm4)<= $nbdec";
	if($commencepar > "")
	{
		if( $where > "" ) $where .=" and "; else $where = " Where ";
		$where.="id_pcdm4 like '$commencepar%'";
	}
	$req.= $where ." order by id_pcdm4";
	$result=$sql->prepareListe($req);
	if($result)
	{ 
		While($ret=$sql->fetchNext($result))
		{
			print('<tr><td>'.$pcdm4->formatIndice($ret["id_pcdm4"]).'</td><td><input type="text" size="70" onblur="give_libelle(\''.$ret["id_pcdm4"].'\',this.value)" value="'.$ret["libelle"].'"></td></tr>');
		}
	}
	print('</table>');
	print('<br><input type="submit" class="bouton" value="Valider les libellés">');
	print('</form');
}

// ----------------------------------------------------------------
// SUPPRESSION INDICES
// ----------------------------------------------------------------
if($_REQUEST["action"]=="SUPPRESSION")
{
	print('<h1>Suppression d\'indices</h1>');
	print('<div align="center">');
	print('<h3>Cochez les indices à supprimer puis validez en bas de la page</h3>');
	$url="codif_pcdm4.php?action=DELETE";
	print('<form method="POST" action="'.$url.'">');
	print('<input type="hidden" id="indice_a_ecrire" name="indice_a_ecrire">');
	print('<table>');
	
	$req="Select id_pcdm4,libelle from codif_pcdm4";
	if($nbdec >0) $where=" Where LENGTH(id_pcdm4)<= $nbdec";
	if($commencepar > "")
	{
		if( $where > "" ) $where .=" and "; else $where = " Where ";
		$where.="id_pcdm4 like '$commencepar%'";
	}
	$req.= $where." order by id_pcdm4";
	$result=$sql->prepareListe($req);
	if($result)
	{ 
		While($ret=$sql->fetchNext($result))
		{
			print('<tr>');
			$coche='<input type="checkbox" name="selection" id="'.$ret["id_pcdm4"].'">';
			print('<td>'.$coche.'</td><td>'.$pcdm4->formatIndice($ret["id_pcdm4"]).'</td><td>'.$ret["libelle"].'</td></tr>');
		}
	}
	print('</table>');
	print('<br><input type="submit" class="bouton" value="Supprimer les indices cochés" onclick="give_suppression(); return true;">');
	print('</form');
}

// ----------------------------------------------------------------
// TRAITEMENT DES SUPPRESSIONS
// ----------------------------------------------------------------
if($_REQUEST["action"]=="DELETE")
{
	print('<h1>Suppression d\'indices</h1>');
	print('<div>');
	$indice=explode(";",$indice_a_ecrire);
	for($i=0; $i<count($indice); $i++)
	{
		print("Suppression : ".$pcdm4->formatIndice($indice[$i])."<br>");
		$indice[$i]=str_replace(".","",$indice[$i]);
		$req="Delete From codif_pcdm4 Where id_pcdm4 ='".$indice[$i]."'";
		$sql->execute($req);
		$pcdm4->majFulltext($indice[$i]);
	}
	print('<br><h3>Traitement terminé avec succès</h3>');
}

// ----------------------------------------------------------------
// INDICES SANS LIBELLE
// ----------------------------------------------------------------
if($_REQUEST["action"]=="NOLIB")
{
	print('<h1>Indices pcdm4 sans libellés</h1>');
	print('<div align="center">');
	print('<h3>Renseignez les libellés puis validez en bas de la page</h3>');
	$url="codif_pcdm4.php?action=ECRIRE";
	print('<form method="POST" action="'.$url.'">');
	print('<input type="hidden" id="indice_a_ecrire" name="indice_a_ecrire">');
	print('<table>');
	
	$ret=$pcdm4->getIndicesSanslibelle("",$nbdec);
	for($i=0; $i<count($ret); $i++)
	{
		print('<tr><td>'.$ret[$i].'</td><td><input type="text" size="70" onblur="give_libelle(\''.$ret[$i].'\',this.value)"></td></tr>');
	}
	print('</table>');
	print('<br><input type="submit" class="bouton" value="Valider les libellés">');
	print('</form');
}

// ----------------------------------------------------------------
// ECRITURE DES LIBELLES 
// ----------------------------------------------------------------
if($_REQUEST["action"]=="ECRIRE")
{
	print('<h1>Mise à jour des Indices PCDM4</h1>');
	if(trim($indice_a_ecrire)=="")
	{
		print('<br><h3>Aucun indice n\'a été modifié</h3>');
	}
	else
	{
		$nb=0;
		$liste=explode(";",$indice_a_ecrire);
		for($i=0; $i<count($liste); $i++)
		{
			$elem=explode("#",$liste[$i]);
			print($elem[0]." - " .$elem[1]."<br>");
			$nb+=$pcdm4->ecrire($elem[0],$elem[1]);
		}
		print("<br><h3>".$nb. " indice(s) ont été mis à jour avec succès.</h3>");
	}
}

?>

</center></div>
</body>
</html>

