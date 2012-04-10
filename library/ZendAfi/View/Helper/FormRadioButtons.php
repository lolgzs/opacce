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
// OPAC3 - Build radio buttons
//////////////////////////////////////////////////////////////////////////////////////////////////////


/* Exemple d'utilisation:
   echo $this->formRadioButtons("display_order", 
 														 $this->preferences["display_order"], 
														 array(
																	 "selection" => "Par ordre de sélection",
																	 "creation_date_desc" => "Par date de création (plus récent en premier)",
																	 "publish_date_desc" => "Par date de début de publication (plus récent en premier)",
																	 "event_date_desc" => "Par date de début d'événement (plus récent en premier)"));
*/
class ZendAfi_View_Helper_FormRadioButtons extends ZendAfi_View_Helper_BaseHelper
{
	public function FormRadioButtons($option_name, $option_value, $buttons_description_dict) {
		$html = '';

		$all_options = array_keys($buttons_description_dict);
		if (! in_array($option_value, $all_options)) 
			$option_value = $all_options[0];

		foreach ($buttons_description_dict as $value => $label){
			$checked = ($value == $option_value) ? "checked='checked'" : "";
			$html .= '<input type="radio" class="'.$option_name.'"name="'.$option_name.'" value="'.$value.'" '.$checked.' >'.$label.'<br/>';
		}

		return $html;
	}
}
?>