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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Renvoie une combo avec des valeurs numériques incrémentielles
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_ComboNombres extends ZendAfi_View_Helper_BaseHelper
{    
    function ComboNombres($name,$valeur_select,$debut,$fin,$step)
	{
		// Composer le html
		$combo='<select name="'.$name.'">';
		for($i=$debut; $i<=$fin; $i+=$step)
		{
			if($valeur_select==$i) $selected=" selected"; else $selected="";
			$combo.='<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
		}
		$combo.='</select>';
		return $combo;
	}
}