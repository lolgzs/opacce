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
//PLANNIFICATION DES INTEGRATIONS
//////////////////////////////////////////////////
include("_init_frame.php");
require_once("fonctions/objets_saisie.php");
require_once("classe_bib.php");
$oBib=new bibliotheque();

?>

<script>
function mode_planif(sMode,nIdBib)
{
	sCoche="coche"+nIdBib;
	sIrreg="irreg"+nIdBib;
	if(sMode == "i")
	{ 
		document.getElementById(sCoche).style.display="none";
		document.getElementById(sIrreg).style.display="block";
	}
	else
	{ 
		document.getElementById(sCoche).style.display="block";
		document.getElementById(sIrreg).style.display="none";
	}
}
</script>

<h1>Plannification des intégrations</h1>
	
<?PHP
////////////////////////////////////////////////////////////////////////////////////
// Ecriture
////////////////////////////////////////////////////////////////////////////////////

if($_REQUEST["action"]=="VALIDER")
{
	extract($_POST);
	print('<br><div style="width=400px;margin-left:20px;">');
	print('<table class="blank" width="100%">');
	$liste=$oBib->getAll();
	foreach($liste as $ligne) 
	{ 
    print('<tr><td class="blank"><b>'.$ligne["nom_court"]."</b></td>");
    $id_bib=$ligne["id_bib"];
    $cmd="\$mode=\$combo_mode".$id_bib.";"; eval($cmd);
    $jours=rend_valeur_coche("lu",$id_bib);
    $jours.=rend_valeur_coche("ma",$id_bib);
    $jours.=rend_valeur_coche("me",$id_bib);
    $jours.=rend_valeur_coche("je",$id_bib);
    $jours.=rend_valeur_coche("ve",$id_bib);
    $jours.=rend_valeur_coche("sa",$id_bib);
    $jours.=rend_valeur_coche("di",$id_bib);
    $cmd="\$fois=intval(\$fois".$id_bib.");"; eval($cmd);
    $cmd="\$par=\$par".$id_bib.";"; eval($cmd);
    $cmd="\$ecart_ajouts=\$ecart_ajouts".$id_bib.";"; eval($cmd);
    $cmd="\$mail=\$mail".$id_bib.";"; eval($cmd);
   	
   	if($mode=="i") $jours="0000000";
   	else { $fois=0;	$par=""; }
   	if(is_numeric($ecart_ajouts) === false or !trim($ecart_ajouts)) $ecart_ajouts=0;
   	$mail=addslashes($mail);
   	
   	// Ecrire
   	$req="update int_bib set planif_mode='$mode', planif_jours='$jours', planif_fois=$fois, planif_par='$par', ecart_ajouts=$ecart_ajouts, mail='$mail' Where id_bib=$id_bib";
   	$sql->execute($req);
   	
   	print('<td class="blank">');
   	$bad='<span class="rouge">Programmation incorrecte !</span>';
   	if($mode=="i" && $fois==0) print($bad);
   	else if($mode=="r" && $jours=="0000000") print($bad);
   	else print('<span class="vert"><b>Ok</b></span>');
   	print('</td></tr>');
  }
	print('</table></div><br><h3>La plannification a été enregistrée avec succès</h3>');
	exit;
}

////////////////////////////////////////////////////////////////////////////////////
// AFFICHAGE DE LA LISTE 
////////////////////////////////////////////////////////////////////////////////////
if($_REQUEST["action"]=="")
{
  print('<form method="post" action="integre_plannification.php?action=VALIDER">');
  print('<div style="width:900px">');
  print('<table width="100%">');
  print('<tr>
    <th width="23%" align="left" valign="middle">Bibliothèque</th>
    <th width="57%" colspan="2" align="left" valign="middle">Plannification</th>
    <th width="5%" align="left" valign="middle">Ecart max.</th>
    <th width="15%" align="left" valign="middle">E-mail</th>
    </tr>');

	// Affichage de la liste des Bibliothèques
	$liste=$oBib->getAll();
	foreach($liste as $ligne)
	{ 
    $id_bib=$ligne["id_bib"];
    $sql_nom=$ligne["nom_court"];

		print ('<td class="milieu">'.$sql_nom.'</td>');
		print(rend_plannif($id_bib));
		print('<td>'.getChamp("ecart_ajouts".$id_bib,$ligne["ecart_ajouts"],2).'</td>');
		print('<td>'.getChamp("mail".$id_bib,$ligne["mail"],40).'</td>');
		print('</tr>');
	}
	print('<tr><td class="bouton"  colspan="10"><input type="submit" class="bouton" value="Valider les modifications"></th></tr>');
	print("</table></div>");
	print('</form>');
	print("</body></html>");
}

function rend_plannif($id_bib)
{
	global $sql;
	$enreg=$sql->fetchEnreg("Select * from int_bib where id_bib=$id_bib");
	
	if($enreg["planif_mode"]=="i") {$aff_irreg="block"; $aff_coche="none"; $si="selected";}
	else {$aff_irreg="none"; $aff_coche="block";}	
	for($i=0; $i<7; $i++) if($enreg["planif_jours"]{$i}=="1") $j[$i]="checked";
	$par[$enreg["planif_par"]]="selected";
	
	$mode='<td class="milieu">';
	$mode.='<select name="combo_mode'.$id_bib.'" id="combo_mode'.$id_bib.'" onchange="mode_planif(this.value,'.$id_bib.')"><option value="r">Régulier</option><option value="i"'.$si	.'>Irrégulier</option></select>';
	$mode.='</td>';
	
	$coche='<td class="milieu"><div id="coche'.$id_bib.'" style="display:'.$aff_coche.'">';
	$coche.='<input type="checkbox" name="lu'.$id_bib.'" id="lu'.$id_bib.'" '.$j[0].'>Lu';
	$coche.='<input type="checkbox" name="ma'.$id_bib.'" id="ma'.$id_bib.'" '.$j[1].'>Ma';
	$coche.='<input type="checkbox" name="me'.$id_bib.'" id="me'.$id_bib.'" '.$j[2].'>Me';
	$coche.='<input type="checkbox" name="je'.$id_bib.'" id="je'.$id_bib.'" '.$j[3].'>Je';
	$coche.='<input type="checkbox" name="ve'.$id_bib.'" id="ve'.$id_bib.'" '.$j[4].'>Ve';
	$coche.='<input type="checkbox" name="sa'.$id_bib.'" id="sa'.$id_bib.'" '.$j[5].'>Sa';
	$coche.='<input type="checkbox" name="di'.$id_bib.'" id="di'.$id_bib.'" '.$j[6].'>Di';
	$coche.='</div>';
	
	$irreg='<div id="irreg'.$id_bib.'" style="display:'.$aff_irreg.'">';
	$irreg.='<input type="text" size="1" name="fois'.$id_bib.'" id="fois'.$id_bib.'" value="'.$enreg["planif_fois"].'">&nbsp;fois par&nbsp;';
	$irreg.='<select name="par'.$id_bib.'" id="par'.$id_bib.'"><option value="s" '.$par["s"].'>Semaine</option><option value="q" '.$par["q"].'>Quinzaine</option><option value="m" '.$par["m"].'>Mois</option><option value="a" '.$par["a"].'>An</option></select>';
	$irreg.='</div></td>';
	return $mode.$coche.$irreg;
}

function rend_valeur_coche($jour,$id_bib)
{
	$cmd="\$valeur=\$_REQUEST['".$jour.$id_bib."'];"; 
	eval($cmd);
	if($valeur == "on") $valeur=1; else $valeur=0;
	return $valeur;
}
?>