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
class Class_Sorter {
		/* Fonction générique pour trier des données
	 * $items: liste des données sous forme de tableau associatif
	 * $field: champ sur lequel baser le tri
	 * $order: SORT_ASC ou SORT_DESC
	 * (on peut passer un deuxième champ pour faire le tri sur 2 attributs)
	 */
	public static function sortItems(&$items, $field1, $order1, $field2=null, $order2=null){
		$keys1 = array();
		$keys2 = array();

		foreach($items as $art){
			$keys1[]=$art[$field1];
			if ($field2) $keys2[]=$art[$field2];
		}

		if ($field2) 		
			array_multisort(
										$keys1, 
										$order1, 
										SORT_STRING,
										$keys2, 
										$order2, 
										SORT_STRING,
										$items);		
		else
			array_multisort(
										$keys1, 
										$order1, 
										SORT_STRING,
										$items);		
	}
}
?>