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
////////////////////////////////////////////////////////////////////////////////////////////
// AFFICHAGE DES FICHIERS EN ATTENTE
////////////////////////////////////////////////////////////////////////////////////////////
include("_init_frame.php");
require_once("classe_maj_auto.php");
$cls_maj_auto=new maj_auto();

// ----------------------------------------------------------------
// SUPPRESSION
// ----------------------------------------------------------------
if($_REQUEST["action"] == "SUPPRIMER")
{
	unlink($_REQUEST["fichier"]);
}

// ----------------------------------------------------------------
// LISTE
// ----------------------------------------------------------------
print('<h1>Fichiers en attente d\'intégration</h1>');

// Parser le repertoire ftp
$path=getVariable("ftp_path");
if(strRight($path,1) != "/") $path.="/";
$dir = opendir($path) or AfficherErreur("Impossible d'ouvrir le dossier des transferts (variable ftp_path) : " .$path);
while (($file = readdir($dir)) !== false)
{
	if(is_dir($path.$file) and $file !="test" and substr($file,0,1) != ".") $dossier[]["nom"]=$file;
}
closedir($dir);
if(!$dossier) AfficherErreur("Il n'y a aucun sous-dossier dans le dossier des transferts.");

$nb_fic=0;
for($i=0; $i < count($dossier); $i++)
{
	$dir = opendir($path.$dossier[$i]["nom"]);
	if($dir == false) continue;
	while (($file = readdir($dir)) !== false)
	{
		if(is_file($path.$dossier[$i]["nom"]."/".$file)) {$dossier[$i]["fichiers"][]=$file; $nb_fic++; }
		elseif(substr($file,0,4)=="site")
		{
			// Sous repertoires pergame
			$dir_pergame = opendir($path.$dossier[$i]["nom"]."/".$file);
			while (($file_pergame = readdir($dir_pergame)) !== false)
			{
				if(is_file($path.$dossier[$i]["nom"]."/".$file."/".$file_pergame)) {$dossier[$i]["fichiers"][]=$file."/".$file_pergame; $nb_fic++; }
			}
			closedir($dir_pergame);
		}
	}
	closedir($dir);
}
if(!$nb_fic) quit("Il n'y a aucun fichier en attente d'intégration");
else echo BR.'&raquo;&nbsp;NB : Vous pouver télécharger un fichier par un click droit sur son nom, puis enregistrer sous'.BR.BR;

// Affichage
print('<div class="liste"><table>');
print('<tr>');
print('<th>Dossier</th>');
print('<th>Fichier</th>');
print('<th>Transféré le</th>');
print('<th>Taille</th>');
print('<th>Statut</th>');
print('<th>Suppr.</th>');
print('</tr>');

for($i=0; $i < count($dossier); $i++)
{
	if(! count($dossier[$i]["fichiers"])) continue;
	$fic=0;
	foreach($dossier[$i]["fichiers"] as $file)
	{
		$fichier=$path.$dossier[$i]["nom"]."/".$file;
		$infos=stat($fichier);
		$taille=number_format(($infos["size"] / 1024),0, ',', ' ')." ko";
		$date=date("d-m-Y", $infos["mtime"]);
		if($fic > 0) $d="&nbsp;"; else $d=$dossier[$i]["nom"];
		$suppr=rendUrlImg("suppression.gif", "integre_fichiers_attente.php","action=SUPPRIMER&fichier=".$fichier,"Supprimer ce fichier");
		$controle=$sql->fetchOne("Select count(*) from int_maj_auto where nom_fichier='". $dossier[$i]["nom"]."/".$file ."'");
		if($controle) $statut = '<font color="darkgreen">Programmé</font>'; else $statut = '<font color="red">non programmé</font>';
		
		print('<tr>');
		print('<td>'.$d.'</td>');
		print('<td>'.'<a href="'.URL_BASE.getVariable("ftp_path").$dossier[$i]["nom"].'/'.$file.'">'.$file.'</a></td>');
		print('<td align="center">'.$date	.'</td>');
		print('<td align="right">'.$taille.'</td>');
		print('<td>'.$statut.'</td>');
		print('<td align="center">'.$suppr.'</td>');
		print('</tr>');
		$fic++;
	}
}
print('</div></table>');
print('</body></html>');

function quit($msg)
{
	if($msg) print(BR.BR.'<h3 style="margin-left:30px">'.$msg.'</h3>');
	print('</body></html>'); 
	exit;
}

?>