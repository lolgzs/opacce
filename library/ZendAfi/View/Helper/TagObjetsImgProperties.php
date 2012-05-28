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

/**
 * Propriétés pour les objets flash et javascript de representation des listes d'images
 */
class ZendAfi_View_Helper_TagObjetsImgProperties extends ZendAfi_View_Helper_BaseHelper {

	public function TagObjetsImgProperties($styles, $preferences) {
		$html = $this->getComboStyles($styles, $preferences["style_liste"]);

		// Chercher si les proprietes existent
		$objet = $preferences["style_liste"];
		$fic_defaut = $objet . "/valeurs_par_defaut.txt";
		$fic_html = $objet . "/valeurs_properties.html";

		if (!$path = $this->getBasePath($fic_defaut)) 
			return $html;
		
		// Bouton des proprietes
		$onclick = "oProp=getId('objet_props'); if(oProp.style.display=='block') oProp.style.display='none'; else oProp.style.display='block'";
		$html .= sprintf('&nbsp;<img src="' . URL_ADMIN_IMG . 'ico/copier.gif" title="%s" style="cursor:pointer" onclick="%s">',
										 $this->translate()->_("propriétés de l'objet"),
										 $onclick);

		// Valoriser les variables
		$defauts = file($path . $fic_defaut);
		$this->loadDefaults($defauts, $preferences);

		// Inclure le html
		$template = file_get_contents($path . $fic_html);
		while (false !== ($pos = strpos($template, '<?php'))) {
			$pos_fin = strpos($template, '?>', $pos);
			$commande = "\$bloc=" . substr($template, ($pos+6), ($pos_fin-$pos-7)) . ";";
			eval($commande);
			$template = substr($template, 0, $pos) . $bloc . substr($template, ($pos_fin+2));
		}

		// Bloc final
		$html .= '<div id="objet_props" style="display:none;border:1px solid #7f9db9;min-height:15px;background-color:#ffffff;padding:2px;margin-top:3px">';
		$html .= sprintf('<div style="color:#3C5188;background-color:#eeeeee;padding:4px;margin-bottom:3px;">%s</div>',
										 $this->translate()->_("Propriétés de l'objet"));
		$html .= $template;
		$html .= '</div>';
		return $html;
	}



	private function getComboStyles($styles, $valeur_select) {
		$onchange = "getId('styles_reload').value='1';document.forms[0].submit();";
		$combo = '<select name="style_liste" onchange="'.$onchange.'">';

		$combo = $this->addOptGroupOn($combo, 
																	$this->translate()->_('Objets java-script'), 
																	$styles["java"], 
																	$valeur_select);

		$combo = $this->addOptGroupOn($combo,
																	$this->translate()->_('Objets flash'),
																	$styles['flash'],
																	$valeur_select);

		$combo .= '</select>';

		// input hidden pour recharger la page en mode reload
		$combo .= '<input type="hidden" id="styles_reload" name="styles_reload" value="0">';

		return $combo;
	}


	private function addOptGroupOn($html, $groupLabel, $options, $current) {
		$html .= sprintf('<optgroup label="%s" style="font-style:normal;color:#FF6600">',
										 $this->translate()->_('Objets java-script'));

		foreach ($options as $clef => $libelle) {
			$selected = ($current == $clef) ? ' selected="selected"' : '';
			$html .= sprintf('<option style="color:#666666" value="%s"%s>%s</option>',
											 $clef, $selected, $libelle);
		}
		
		return $html .= '</optgroup>';
	}


	private function getBasePath($fic_defaut) {
		$path = null;
		if (file_exists(PATH_JAVA . $fic_defaut))
			$path = PATH_JAVA;
		if (!$path and file_exists(PATH_FLASH . $fic_defaut))
			$path = PATH_FLASH;
		return $path;
	}


	private function loadDefaults($content, &$preferences) {
		foreach ($content as $defaut) {
			$defaut = explode("=", trim($defaut));
			$clef = "op_" . $defaut[0];
			$valeur = $defaut[1];
			if (!isset($preferences[$clef]))
				$preferences[$clef] = $valeur;
		}
	}
}