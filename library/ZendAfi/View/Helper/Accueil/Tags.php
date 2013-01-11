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
class ZendAfi_View_Helper_Accueil_Tags extends ZendAfi_View_Helper_Accueil_Base {
	public function getHtml() {
		/* Paramètres reçus
		 * limite: nombre de notices à analyser
		 * nombre: nombre de notices à afficher dans le nuage
		 * notices: 0: 'Toutes', bidon pour l'instant mais censé faire une recherche sur tout le catalogue
		 *          1: 'Les nouveautés'
		 *          2: 'Les plus consultées'
		 * pour l'instant mappe cette valeur sur 'tri' comme pour les kiosques / critiques
		 * type_tags: données à afficher dans le nuage
		 */
		
		/*
		 * On adapte les préférences à Class_Catalogue::getNotices,
		 * ce qui permet de réutiliser les fonctions
		 * de recherche comme les kiosques / critiques.
		 */
		$this->titre = $this->preferences["message"];

		$catalogue = new Class_Catalogue();
		$this->preferences["nb_notices"] = $this->preferences["limite"];
		$this->preferences["aleatoire"] = 0; //pour ne prendre en compte les limites

		$this->preferences["tri"] = ($this->preferences["notices"] > 0) ?
			$this->preferences["notices"] :
			$this->preferences["tri"] = -1; // Permet de ne pas faire d'order by.

		$notices = $catalogue->getNoticesByPreferences($this->preferences);
		$facettes = $this->_getFacettesFromNotices($notices);

		$nb = 0;
		$sorted = array();

		while (true) {
			// On cherche le plus fort pour chaque type
			$controle = array("nombre" => 0);
			$yen_a_plus = true;

			for ($i = 0; $i < strlen($this->preferences["type_tags"]); $i++) {
				$type = $this->preferences["type_tags"][$i];
				if (!array_key_exists($type, $facettes)) continue;
				$yen_a_plus = false;
				
				if (!array_key_exists($type, $sorted)) {
					arsort($facettes[$type]); 
					$sorted[$type] = true;
				}
				
				$lig = array_slice($facettes[$type], 0, 1);
				if (!$lig)
					continue;
				$compare = array_values($lig);

				if ($compare[0] > $controle["nombre"]) {
					$controle["nombre"] = $compare[0];
					$controle["type"] = $type;
					$controle["clef"] = $lig;
				}
			}

			if ($yen_a_plus)
				break;

			$nb++;
			if ($nb > $this->preferences['nombre'])
				break;

			$clef = key($controle['clef']);
			$table[$nb]["id"] = $clef;
			$table[$nb]["libelle"] = Class_Codification::getLibelleFacette($clef);
			$table[$nb]["nombre"] = current($controle['clef']);

			array_shift($facettes[$controle["type"]]);
		}
	
		// Html avec le view helper
		$cls = new ZendAfi_View_Helper_NuageTags();
		$this->contenu .= $cls->nuageTags($table).'<br />';
	
		return $this->getHtmlArray();
	}


	protected function _getFacettesFromNotices($notices) {
		$facettes = array('T' => array());
		foreach ($notices as $notice)	{
			$type = 'T' . $notice['type_doc'];
			array_key_exists($type, $facettes['T']) ? 
				$facettes['T'][$type]++ : 
				$facettes['T'][$type] = 1;

			$items = array_filter(explode(" ", trim($notice['facettes'])));
			foreach ($items as $item) {
				$type = substr($item, 0, 1);
				if (!array_key_exists($type, $facettes))
					$facettes[$type] = array();

				array_key_exists($item, $facettes[$type]) ? 
					$facettes[$type][$item]++ :
					$facettes[$type][$item] = 1;
			}
		}

		return $facettes;
	}
}