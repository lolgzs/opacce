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
/////////////////////////////////////////////////////////////////////////
// PATCHES SGBD
/////////////////////////////////////////////////////////////////////////
	
include("_init_frame.php");

// Entete
print("<h1>Mise à niveau de la base de données</h1>");
$niveau_client=getVariable("patch_level");
print('<h3>Niveau de patch : '.$niveau_client.' / '.PATCH_LEVEL.'</h3>');

// Recup des patches a executer
$path="./sql/patch/";
$handle = @opendir($path);
if(!$handle) afficherErreur("Impossible d'ouvrir le dossier : ".$path,true);
while (false !== ($fic = readdir($handle)))
{
	if(substr($fic,-4)!=".sql") continue;
	$numero=(int)substr($fic,6,3);
	if($numero > $niveau_client) $scripts[]=$path.$fic;
}
sort($scripts);
closedir($handle);

//---------------------------------------------------------------------------------
// Execution des patches
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="LANCER")
{
	$sql->ignoreErreurs(true);
	foreach($scripts as $script)
	{
		$num_patch=(int)substr($script,-7,3);
		print('<h3>Execution patch n° '.$num_patch.'</h3>');
		$num_instruction=0;
		$data=file($script);
		foreach($data as $ligne)
		{
			$ligne=trim($ligne);
			if(!$ligne or substr($ligne,0,2)=="--") continue;
			print($ligne.BR);
			flush();
			// Concatener la ligne d'instruction
			if(substr($ligne,-1)!=";")
			{
				$instruction.=$ligne;
				continue;
			}
			$instruction.=$ligne;
			
			// Executer
			$num_instruction++;
			if($_REQUEST["reprise"] > 0 and $_REQUEST["reprise"] >= $num_instruction){$instruction=""; continue;}
			try 
			{
				$sql->execute($instruction);
				$instruction="";
			}
			catch(Exception $e)
			{
				print('<h3 class="erreur">Erreur SQL</h3>');
				print('<div class="erreur_sql">');
				print("<b>Code : </b>".$e->getCode().BR);
				print('<b>Erreur : </b>'.$e->getMessage().BR.BR);
				print(rendBouton("Ignorer l'erreur et continuer","util_patch_sgbd.php","action=LANCER&reprise=".$num_instruction));
				print('</div>');
				exit;
			}
		}
		// Ecrire le patch dans la base
		setVariable("patch_level",$num_patch);
	}
	print('<h3>Mise à niveau de la base effectuée avec succès</h3>'.BR.BR);
	exit;
}

//---------------------------------------------------------------------------------
// Afficher
//---------------------------------------------------------------------------------
print('<div class="liste">');
print('<div style="width:100%;text-align:center">'.rendBouton("Lancer la mise à jour","util_patch_sgbd.php","action=LANCER").'</div>'.BR);
print('<table>');
foreach($scripts as $script)
{
	$data=file_get_contents($script);
	print('<tr><th align="left">Patch n° '.(int)substr($script,-7,3).'</th></tr>');
	print('<tr><td>'.str_replace(chr(13).chr(10),BR,$data).'</td></tr>');
}
print('</table>');
print('</div>'.BR.BR);

?>
