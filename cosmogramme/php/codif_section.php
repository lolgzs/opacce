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
//        CODIFS : SECTIONS
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once( "fonctions/objets_saisie.php");

?>

<h1>Codification des sections</h1>
 
<?PHP

//---------------------------------------------------------------------------------
// CREER
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="CREER")
{
	$action=array("libelle" => "** nouvelle section **","regles" => "995\$j=a");
	print(BR.BR.BR);
	afficherSection($action,"block");
	print('</form></body></html>');
	exit;
}
//---------------------------------------------------------------------------------
// VALIDER
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="VALIDER")
{
	// Vérif de la syntaxe des règles
	$regles=trim($_POST["regles"]);
	if(! $regles) erreurFormat("Vous devez définir au moins une règle");
	$regles=str_replace(" ","",$regles);
	$test = explode("\n",$regles);
	foreach($test as $regle)
		{
			$nb++;
			$zone=substr($regle,0,3); if(intval($zone) != $zone) erreurFormat("La zone est mal définie pour la règle n° ".$nb);
			if(substr($regle,3,1) != "\$") erreurFormat("Le \$ est absent ou mal positionné pour la règle n° ".$nb);
			$champ=substr($regle,4,1); if(($champ < "0" or $champ >"9") and ($champ < "a" or $champ > "z")) erreurFormat("Le champ est mal défini pour la règle n° ".$nb);
			$valeurs=substr($regle,6); if(!trim($valeurs)) erreurFormat("Indiquez des valeurs pour la règle n° ".$nb);
			$signe=substr($regle,5,1); if( strpos("=/*",$signe) === false) erreurFormat("Signe de comparaison incorrect pour la règle n° ".$nb);
			$elem=explode(" ",$regle);
		}
	
	// ecriture
	if(!isset($_POST["invisible"])) $_POST["invisible"]="0";
	$id_section=$_POST["id_section"];
	$_POST["regles"]=$regles;
	if(!$id_section) $sql->insert("codif_section",$_POST);
	else 
	{
		unset($_POST["id_section"]);
		$sql->update("update codif_section set @SET@ where id_section=$id_section",$_POST);
	}

}
//---------------------------------------------------------------------------------
// SUPPRIMER
//---------------------------------------------------------------------------------

if($_REQUEST["action"]=="SUPPRIMER")
{
	$id_section=$_REQUEST["id_section"];
	if($id_section)	$sql->execute("delete from codif_section where id_section =$id_section");
}

//---------------------------------------------------------------------------------
// LISTE 
//---------------------------------------------------------------------------------
print('<div class="liste">');

$liste=$sql->fetchAll("Select * from codif_section order by libelle");
for($p=0; $p< count($liste); $p++)
{
	$section=$liste[$p];
	$img="plus.gif";
	$display="none";
	if($section["id_section"] == $_REQUEST["id_section"])
		{
			$img="moins.gif";
			$display="block";
		}
		print('<div class="liste_img"><img id="Isection'.$section["id_section"].'" src="'.URL_IMG.$img.'" onclick="contracter_bloc(\'section'.$section["id_section"].'\')" style="cursor:pointer"></div>');
		print('<div class="liste_titre">'. $section["libelle"].'</div>');
		affichersection($section,$display);
}
print('</div>');
// Bouton ajouter
$bouton_ajout=rendBouton("Ajouter une section","codif_section","action=CREER");
print(BR.BR.$bouton_ajout);

print('</body></html>');
exit;

function affichersection($section,$display)
{
	print('<div class="form" id="section'.$section["id_section"].'" style="width:600px;margin-left:20px;display:'.$display.'">');
	print('<form method="post" action="'.URL_BASE.'php/codif_section.php?action=VALIDER">');
	print('<input type="hidden" name="id_section" value="'.$section["id_section"].'">');
	print('<table class="form" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2" align="left">Création de section</th></tr>');
	print('<tr><td class="form_first" align="right" width="35%">Libellé</td><td class="form_first">'.getChamp("libelle",$section["libelle"],43).'</td></tr>');
	print('<tr><td class="form_first" align="center" colspan="2"><div class="commentaire">Syntaxe : [zone$champ][signe][valeur1;valeur2;etc...] - Ex : 995$a = a<br>Signes : "=" égal - "/" commence par - "*" contient</div></td></tr>');
	print('<tr><td class="form" align="right" valign="top">Règles de reconnaissance</td><td class="form">'.getTextarea("regles",$section["regles"],40,5).'</td></tr>');
	if($section["invisible"]==1) $checked="checked"; else $checked="";
	print('<tr><td class="form_first" align="right" width="35%">Rejeter les exemplaires</td><td class="form_first"><input type="checkbox" value="1" '.$checked.' name="invisible"></td></tr>');
	
	// Boutons maj
	print('<tr><th class="form" colspan="2" align="center">');
	$bouton_valider='<input type="submit" class="bouton" value="Valider">';
	$bouton_supprimer=rendBouton("Supprimer","codif_section","action=SUPPRIMER&id_section=".$section["id_section"]);
	print($bouton_valider.str_repeat("&nbsp;",5).$bouton_supprimer);
	print('</th></tr></table></form></div>');
}

function erreurFormat($erreur)
{
	print('<span class = "rouge">'.$erreur.'</span>');
	$action=$_POST;
	print(BR.BR.BR);
	afficherSection($action,"block");
	print('</form></body></html>');
	exit;
}
?>