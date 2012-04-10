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
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Notation avec des etoiles
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_TagRating extends ZendAfi_View_Helper_BaseHelper
{
	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	public function TagRating($name,$valeur_select,$saisie="true")
	{
		// Controle de la valeur
		$valeur_select=intval($valeur_select);

		// Objet affichage
		$html='<div style="width:100px">';
		$html.='<select name="'.$name.'" class="rating" id="'.$name.'">';
		for ($i = 1; $i <= 5; $i++) {
			$selected = ($i == $valeur_select) ? "selected='1'" : '';
			$html.='<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}
		$html.='</select>';
		$html.='</div>';

		// Javascript d'initialisation
		$html.='<script type="text/javascript">';
		$html.=sprintf('$("#%s").rating({showCancel: true,startValue:"%s",disabled:%s});',
									 $name, $valeur_select, $saisie);
		$html.='</script>';

		return $html;
	}

}