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
//         AFFICHAGE d'une Notice article de périodique
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_unimarc.php");
require_once("classe_bib.php");
$unimarc=new notice_unimarc();
$bib=new bibliotheque();

// Lire la notice
print('<h1>Notice article de périodique</h1>');
$id_notice=$_REQUEST["id_notice"];
$notice_interne=$sql->fetchEnreg("select * from notices_articles where id_article=$id_notice");
$unimarc->ouvrirNotice($notice_interne["unimarc"],0);
$titre=$unimarc->getTitrePrincipal();

// Titre et barre de boutons
print('<div class="notice_titre">'.$titre.'</div>');
print('<div class="liste">');

$tout_fermer="document.getElementById('notice_interne').style.display='none';";
$tout_fermer.="document.getElementById('notice_unimarc').style.display='none';";
$tout_fermer.="document.getElementById('unimarc_natif').style.display='none';";
$b1='<input type="button" class="bouton" value="Notice interne" onclick="'.$tout_fermer.'document.getElementById(\'notice_interne\').style.display=\'block\';">';
$b3='<input type="button" class="bouton" value="Notice unimarc" onclick="'.$tout_fermer.'document.getElementById(\'notice_unimarc\').style.display=\'block\';">';
$b4='<input type="button" class="bouton" value="Unimarc natif" onclick="'.$tout_fermer.'document.getElementById(\'unimarc_natif\').style.display=\'block\';">';
if($_SESSION["url_retour"]){$url_retour="document.location='".$_SESSION["url_retour"]."'"; unset($_SESSION["url_retour"]);}
else $url_retour="history.back()";
$retour='<input type="button" class="bouton" value="Retour" onclick="'.$url_retour.'">';
print(BR.$b1."&nbsp;&nbsp;".$b2."&nbsp;&nbsp;".$b3."&nbsp;&nbsp;".$b4."&nbsp;&nbsp;".$retour.BR.BR);

$sep='<div class="separateur">:</div>';

// Notice interne
print('<div class="notice" id="notice_interne" style="display:block">');
print('<div class="notice_entete">Notice interne</div>');
print('<div class="notice_label">id article</div>'.$sep.'<div class="notice_valeur">'.$id_notice.'</div>');
print('<div class="notice_label">clef chapeau</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["clef_chapeau"].'</div>');
print('<div class="notice_label">clef numéro</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["clef_numero"].'</div>');
print('<div class="notice_label">clef unimarc</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["clef_unimarc"].'</div>');

print('<div class="notice_zone">Gestion</div>');
print('<div class="notice_label">date de mise à jour</div>'.$sep.'<div class="notice_valeur">'.rendDate($notice_interne["date_maj"],1).'</div>');
$lib=getLibCodifVariable("code_qualite",$notice_interne["qualite"]);
print('<div class="notice_label">Qualité</div>'.$sep.'<div class="notice_valeur">'.$notice_interne["qualite"]. " - ".$lib.'</div>');

if($notice_interne["clef_chapeau"]) $id_notice_mere=$sql->fetchOne("select id_notice from notices where clef_chapeau='".$notice_interne["clef_chapeau"]."' and tome_alpha='".$notice_interne["clef_numero"]."'");
if($id_notice_mere) $statut='<a href="/cosmogramme/php/analyse_afficher_notice_full.php?id_notice='.$id_notice_mere.'">lié à une notice mère (id='.$id_notice_mere.')</a>';
else $statut="notice orpheline";
print('<div class="notice_label">Statut</div>'.$sep.'<div class="notice_valeur">'.$statut.'</div>');

print('</div>');

// Notice unimarc
$notice=$unimarc->getAll();
print('<div class="notice" id="notice_unimarc" style="display:none">');
print('<div class="notice_entete">Notice unimarc</div>');
foreach($notice["label"] as $lig)
{
	print('<div class="notice_label">'.$lig[0] .'</div>'.$sep.'<div class="notice_valeur">'.$lig[1].'</div>');
}
foreach($notice["zones"] as $zone)
{
	print('<div class="notice_zone">&raquo;&nbsp;'.$zone["zone"].'</div>');
	if(trim($zone["indicateur1"])) print('<div class="notice_indicateur">indicateur 1</div>'.$sep.'<div class="notice_valeur">'.$zone["indicateur1"].'</div>');
	if(trim($zone["indicateur1=2"])) print('<div class="notice_indicateur">indicateur 2</div>'.$sep.'<div class="notice_valeur">'.$zone["indicateur2"].'</div>');
	for($i=0; $i < count($zone["champs"]); $i++)
	{
		$champ=$zone["champs"][$i];
		print('<div class="notice_champ">'.$champ["code"]. '</div>'.$sep.'<div class="notice_valeur">'.$champ["valeur"].'</div>'); 
	}
}
print('</div>');

// Unimarc natif
$natif=$unimarc->getUnimarcNatif();
print('<div class="notice" id="unimarc_natif" style="display:none">');
print('<div class="notice_entete">Unimarc natif</div>');
print('<div class="notice_zone">Bloc de label</div>');
print('<div class="notice_valeur">'.$natif["label"].'</div>');
// zones
print('<div class="notice_zone">Bloc des zones</div>');
print('<table class="blank">');
print('<tr><td class="blank">Zone</td><td class="blank">Longueur</td><td class="blank">Adresse</td><td class="blank">Contenu</td></tr>');
for($i=0; $i < count($natif["zones"]); $i++)
{
	$zone=$natif["zones"][$i];
	print('<tr>');
	print('<td class="blank">'.$zone["label"].'</td>');
	print('<td class="blank">'.$zone["length"].'</td>');
	print('<td class="blank">'.$zone["adress"].'</td>');
	print('<td class="blank">'.$natif["data"][$i]["content"].'</td>');
	print('</tr>');
}
print('</table>');
print('</div>');

// Fin
print('</div><br><br></body></html>');
?>