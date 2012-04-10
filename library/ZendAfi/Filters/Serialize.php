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
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	FILTRE POUR LES SERIALIZE ET UNSERIALIZE
//////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Filters_Serialize
{	
	
//----------------------------------------------------------------
// Encodage
//----------------------------------------------------------------
	static function serialize($valeurs)
	{
		if(!$valeurs) return false;
		$cls=new ZendAfi_Filters_Serialize();
		array_walk_recursive($valeurs, array(&$cls,'encodeItem'));
		$chaine=serialize($valeurs);

		$chaine=utf8_encode($chaine);
		return $chaine;
	}
	
//----------------------------------------------------------------
// Decodage
//----------------------------------------------------------------
	static function unserialize($valeur)
	{
		$valeur=utf8_decode($valeur);
		$valeurs=unserialize($valeur);
		return $valeurs;
	}

//----------------------------------------------------------------
// Fonction callback pour l'encodage 
//----------------------------------------------------------------
	private function encodeItem(&$valeur,$key)
	{
		$valeur=stripslashes($valeur);
		return $valeur;
	}

}