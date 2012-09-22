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
class ZendAfi_View_Helper_Notice_Entete extends Zend_View_Helper_HtmlElement {
	public function notice_Entete($notice, $preferences) {
		if (!array_isset('entete', $preferences))
			return '';

		$html='<table id="entete_notice">';
		foreach(str_split($preferences["entete"]) as $champ)	{
			$libelle = Class_Codification::getNomChamp($champ, 1);
			if (!$valeurs = ($champ == 'N') 
					? $notice->getAnnee()
					: $notice->getChampNotice($champ, $notice->getFacettes()))
				continue;

			$html.='<tr>';
			$html.='<td style="white-space:nowrap;text-align:right;padding-right:5px;vertical-align:top;">'.$libelle.'&nbsp;:</td>';
			$html.='<td valign="top">';

			if(gettype($valeurs) != "array") 
				$html.=$valeurs.BR;
			else	{
				foreach($valeurs as $item) {
					if (gettype($item) == "array"){ 
						if($item["url"]) $html.='<a href="'.$item["url"].'" class="notice">'.$item["libelle"].'</a>.';
					} else {
						$html.=$item;
					}

					$html.=BR;
				}
			}

			$html.='</td>';
			$html.='</tr>';
		}

		$html.='</table>';
		return $html;
	}
}

?>