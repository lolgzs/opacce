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
class ZendAfi_View_Helper_TagSlider extends ZendAfi_View_Helper_BaseHelper {
	/** 
	 * @param string $name
	 * @param string $valeur_select
	 * @param int $min
	 * @param int $max
	 * @param int $step
	 * @param string $action
	 * @return string
	 */
	public function tagSlider($name, $valeur_select, $min, $max, $step, $action = '') {
		if ('' == (string)$valeur_select)
			$valeur_select = $min;

		$id_slider = $name . "-slider";

		$html = '
			<script type="text/javascript">
				$(function() {
				$("#' . $id_slider . '").slider({
					range: "min",
					value: ' . $valeur_select . ',
					min: ' . $min . ',
					max: ' . $max . ',
					step: ' . $step . ',
					slide: function(event, ui) {$("#' . $name . '").val(ui.value);' . $action . '}
					});
					$("#' . $name . '").val($("#' . $id_slider . '").slider("value"));
				});
			</script>';

		$html .= '<div style="width:90%;padding:0px 0px 15px 10px;font-size:6pt">
<input id="' . $name . '" name="' . $name . '" style="border:0;margin-bottom:3px;font-size:7pt;color:#666666">
<div id="' . $id_slider . '"></div></div>';

		return $html;
	}
}