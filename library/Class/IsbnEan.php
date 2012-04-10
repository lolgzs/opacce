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
///////////////////////////////////////////////////////////////////
// OPAC3 : fonctions ISBN et EAN
///////////////////////////////////////////////////////////////////

class Class_IsbnEan
{
	static function getIsbn10($isbn)
	{
		$isbn=str_replace("-","",$isbn);
		if( strlen($isbn) < 12) return $isbn;
		if(substr($isbn,0,3) != "978") return $isbn;
		$isbn=substr($isbn,3,9);
		// Clef de controle
		for($i=0;$i<9;$i++)
		{
			$facteur=10-$i;
			$somme=$somme+(strmid($isbn,$i,1)*$facteur);
		}
		$car=0;
		while(($car+$somme) % 11 != 0) $car++;
		if ($car==10) $clef="X"; else $clef=$car;
		return $isbn.$clef;
	}
	
}
?>