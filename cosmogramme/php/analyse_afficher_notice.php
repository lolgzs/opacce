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
//         AFFICHAGE d'une Notice
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once("classe_unimarc.php");
require_once("classe_parseur.php");
$parseur=new parseur();
$unimarc=new notice_unimarc();

print('<h1>Notice unimarc</h1>');
 
$fichier=$_REQUEST["fichier"];
$adresse=$_REQUEST["adresse"];
$id_profil=$_REQUEST["profil_unimarc"];
if(!$id_profil) $id_profil=1;

// On détermine le path
if($_REQUEST["mode"]=="LOG") $path=getVariable("log_path");
elseif($_REQUEST["mode"]=="INTEGRATION") {$path=""; $fichier=urldecode($fichier); }
else $path=getVariable("ftp_path")."test/";

// Ouvrir le fichier puis la notice
$controle=$parseur->open( $path.$fichier,0,$adresse);
if( $controle==false) 
{
	print(BR.BR.BR.'<span class="rouge">Impossible d\'ouvrir le fichier : '. $fichier .'</span><br>');
	exit;
}
$ret=$parseur->nextEnreg();
if($ret["statut"] != "ok")
{
	print(BR.BR.BR.'<span class="rouge">Impossible de lire la notice</span><br>');
	exit;
}

$unimarc->ouvrirNotice($ret["data"],$id_profil);
$notice=$unimarc->getAll();

// Afficher la notice
$sep='<div class="separateur">:</div>';
print('<div class="notice_titre">'.$notice["titre_princ"].'</div>');
print('<div class="notice" style="margin-left:30px">');
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

// Fin
$bouton_retour='<input type="button" class="bouton" value="Retour à la liste" onclick="history.back()">';
print(BR.'<div style="margin-left:250px">'.$bouton_retour.'</div>'.BR.BR);
print('</body></html>');
?>