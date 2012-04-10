<?php
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
/*
 * FONCTIONS DE TRAITEMENTS REPERTOIRES ET FICHIERS
 */

// Lit le contenu entier d'un fichier
function lire_fichier( $fic)
{
	@$handle = fopen( $fic, "r") or erreur_admin( "Impossible d'ouvrir le fichier : " .$fic );
	$data=fread ($handle, filesize ($fic));
	fclose($handle);
	return $data;
}

// Lit un fichier en éliminant les lignes vides et les commentaires
function lire_fichier_ressource( $fic )
{
	$lig = @file( $fic ) or erreur_admin( "Impossible d'ouvrir le fichier : " . $fic);
	for( $i = 0; $i < count($lig); $i++)
	{
		$lig[$i] = trim($lig[$i]);
		if( ! $lig[$i] or strLeft( $lig[$i],2 ) == "//") continue;
		$data .= NL.$lig[$i];
	}
	return $data;
}

// Rend tous les fichiers de tous les dossiers et sous-dossiers d'une racine
function parse_dossier( $root )
{
	if( file_exists($root) == false ) return false;
	$liste = array();
	@$dir = opendir( $root) or die("Impossible d'ouvrir le dossier : " .$root);
	while (($file = readdir($dir)) !== false) 
	{
		if( subStr( $file, 0, 1 )!= ".") 
		{
			if(filetype($root. "/" .$file) == "dir")
			{
				$liste1 = parse_dossier( $root. "/" .$file);
				$liste = array_merge( $liste, $liste1); 
			}
			else
			{
				$index = count($liste);
				$liste[$index][0]= $root;
				$liste[$index][1]= $file;
			}
		}
	}
	closedir( $dir);
	return $liste;
}
?>