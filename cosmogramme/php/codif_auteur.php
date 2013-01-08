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
//            GESTION DES AUTEURS
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_indexation.php");
$indexation = new indexation();

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

<h1>Gestion des autorités auteurs</h1>
<?PHP

// ----------------------------------------------------------------
// ACCUEIL
// ----------------------------------------------------------------
if($_REQUEST["action"]=="")
{
	$nb=fetchOne("Select count(*) from codif_auteur");
	print('<span class="orange"><b>La base contient '.number_format($nb, 0, ',', ' ').' fiches auteurs</b></span>'.BR.BR);
	
	print('<div class="liste">');

	// recherche
	$url="codif_auteur.php?action=LANCER";
	print('<form id="nolib" method="post" action="'.$url.'">');
	print('<table width="60%" cellspacing="0">');
	print('<tr><th align="left"colspan="2">Recherche</th>');
	print('</tr>');
	print('<tr>');
	print('<td align="right">Nom commence par</td>');
	print('<td><input type ="text" name="nom" value="" size="20" align="left"></td>');
	print('</tr>');
	print('<tr><th class ="form" colspan="2" align="center"><input type="submit" class="bouton" value="Lancer la recherche"></td></tr>');
	print('</table>');	
	print('</form>');

	// indexation des renvois
	$url=URL_BASE.'php/util_renvois.php';
	print(BR.'<table width="60%" cellspacing="0">');
	print('<tr><th align="left"colspan="2">Indexation des renvois</th>');
	print('</tr>');
	print('<tr><td>Cette fonction cherche les autorités sur le serveur de cache et indexe les notices à tous les renvois.</td></tr>');
	print('<tr><td align="center" style="padding:10px">'.rendBouton("Lancer l'indexation",$url,"type_autorite=auteur").'</td></tr>');
	print('</table>');

	// suppression des autorités non utilisées
	$url=URL_BASE.'php/util_suppression_autorites.php';
	print(BR.'<table width="60%" cellspacing="0">');
	print('<tr><th align="left"colspan="2">Suppression des autorités non utilisées</th>');
	print('</tr>');
	print('<tr><td>Cette fonction supprime les autorités référencées par aucune notice.</td></tr>');
	print('<tr><td align="center" style="padding:10px">'.rendBouton("Lancer la suppression",$url,"type_autorite=auteur").'</td></tr>');
	print('</table>');
}

// ----------------------------------------------------------------
// LANCER
// ----------------------------------------------------------------
if($_REQUEST["action"] == "LANCER")
{
	$rech=$indexation->alphaMaj($_POST["nom"]);
	$req="Select * from codif_auteur ";
	if($rech) $req.="Where formes like '".$rech."%' ";
	$req.="order by formes";
	$handle=$sql->prepareListe($req);
	if(!$handle)
	{
		print(BR.BR.'<h3>Aucun résultat trouvé</h3>');
		exit;
	}
	print('<div class="liste"><table><tr>');
	print('<th width="1%">&nbsp;</th>');
	print('<th width="1%">id</th>');
	print('<th width="40%">Intitulé</th>');
	print('<th width="50%">Formes rejetées</th>');
	print('<th width="1%">id bnf</th>');
	print('<th width="1%">Mots renvois</th>');
	print('<th width="1%">date maj renvois</th>');
	print('</tr>');
	while($auteur=$sql->fetchNext($handle))
	{
		$url=rendUrlImg("loupe.png", "codif_auteur.php","action=NOTICES&id_auteur=A".$auteur["id_auteur"]);
		print('<tr><td valign="top">'.$url.'</td>');
		print('<td valign="top">'.$auteur["id_auteur"].'</td>');
		print('<td valign="top">'.$auteur["libelle"].'</td>');
		print('<td valign="top">'.$auteur["formes"].'</td>');
		print('<td valign="top">'.$auteur["id_bnf"].'</td>');
		print('<td valign="top">'.$auteur["mots_renvois"].'</td>');
		print('<td valign="top">'.$auteur["date_creation"].'</td>');
		print('</tr>');
	}
	print('</table>');
}
// ----------------------------------------------------------------
// LISTE DE NOTICES
// ----------------------------------------------------------------
if($_REQUEST["action"] == "NOTICES")
{
	require_once("classe_liste_notices.php");
	$oListe=new liste_notices();
	$id_auteur=$_REQUEST["id_auteur"];
	$page=$_REQUEST["page"];
	
	$req="Select id_notice from notices where MATCH(facettes) AGAINST('+".$id_auteur."' IN BOOLEAN MODE) order by alpha_titre";
	$liste=$oListe->getListe($req,$page);
	print('<div class="liste">');
	if(!$liste) print(BR.'<h3>Aucune notice trouvée</h3>');
	else 
	{
		$args_url="action=NOTICES&id_auteur=".$id_auteur;
		print($oListe->getHtml($liste,$args_url));
	}
}

?>

</div>
</body>
</html>

