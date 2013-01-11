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
// OPAC3 : Nuages de tags
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_NuageTags extends ZendAfi_View_Helper_BaseHelper {

	function nuageTags($tags,$calcul=3) {
		Class_ScriptLoader::getInstance()->addSkinStyleSheet('nuage_tags');

		// Parametres
		if (!$tags)
      return;

		$url = BASE_URL . '/opac/recherche/rebond?facette=reset&amp;code_rebond=';
		
		// Déterminer les tranches
		$tranches = $this->calcultranches($tags, $calcul);
		
		// Remettre dans un ordre aleatoire
		shuffle($tags);
		
		// Fabriquer le Html
		$html='<div class="nuage">';
		foreach ($tags as $tag) {
			for ($niveau = 0; $niveau < 9; $niveau++) {
				if (!array_key_exists($niveau, $tranches))
					break;
				if ($tag['nombre'] >= $tranches[$niveau])
          break;
			}
			$niveau = (10 - $niveau);
			$classe="nuage_niveau" . $niveau;
			$html.='<span class="nuage"><a class="'.$classe.'" href="'.$url.$tag["id"].'">'.$tag["libelle"].' </a></span>';
		}
		$html.='</div>';
		return $html;
	}

	//------------------------------------------------------------------------------------------------------
	// Calcul des tranches selon methode parametree
	//------------------------------------------------------------------------------------------------------
	private function calcultranches($tableau,$methode=3)
	{
		$tranches = [];
		// Nouveau mode
		if($methode == 3)
			{
				$distinct = array();
				// determiner le nombre de valeurs distinctes
				foreach ($tableau as $index => $value)
					$distinct[$value['nombre']] = $value['nombre'];

				// Si plus de 10 tranches on fusionne les tranches de plus faible ecart
				if(count($distinct) > 10)
					{
						while( count($distinct) > 10)
							{
								// Recherche du plus petit ecart
								$ecart_min=100000;
								$sauve_index_nombre=0;
								foreach($distinct as $key => $index_nombre)
									{
										$ecart= abs($index_nombre-$key);
										if($ecart <= $ecart_min) 
											{
												$ecart_min=$ecart;
												$ecart_index_suppr=$sauve_index_nombre;
												$ecart_index_ref=$key;
											}
										$sauve_index_nombre=$key;
									}
								// Fusionner les tranches qui ont l'ecart mini
								if (array_key_exists($ecart_index_suppr, $distinct))
									$distinct[$ecart_index_ref]=$distinct[$ecart_index_suppr];
								else
									$distinct[$ecart_index_ref] = 0;
								unset($distinct[$ecart_index_suppr]);
							}
					}
				
				// Constitution des tranches
				$index=10;
				foreach($distinct as $tranche => $valeur) 
					{
						$index--;
						if($valeur >  0)$tranches[]=$valeur;
					}		
				return $tranches;
			}

		// Min et max
		$nb_elements = count($tableau);
		$max = $tableau[1]["nombre"];
		$min = isset($tableau[$nb_elements]["nombre"]) ? $tableau[$nb_elements]["nombre"] : 0;
		
		// Si peu de nombres on fabrique les tranches en dur
		if($max < 11)
			{ 
				$tranches=array(10,9,8,7,6,5,4,3,2,1);
				return $tranches;
			}
		
		// Calcul par répartition simple
		if(!$methode)
			{
				if($max < 11) $tranches=array(10,9,8,7,6,5,4,3,2,1);
				else
					{
						$tranche=intVal(($max - $min)/10);
						for($i=0;$i<10; $i++) $tranches[$i]=intval($min + ($i * $tranche));
						$tranches=array_reverse($tranches);
					}
				return $tranches;
			}
		
		// Calcul par ecart à la moyenne
		$sumX = $sumX2 = 0;
		foreach($tableau as $data) {
			$sumX += $data["nombre"];
			$sumX2 += $data["nombre"] * $data["nombre"];
		}

		$mean = $sumX / $nb_elements;
		$stdDev = intval(sqrt($sumX2 - $mean * $mean * $nb_elements) / $nb_elements); 
		$fBreakVal = intval($mean - ($stdDev * 3));
		for( $i = 0; $i < 10; $i++)
			{
				if($fBreakVal >= $min and $fBreakVal <= $max) $tranches[]= $fBreakVal;
				$fBreakVal = $fBreakVal + $stdDev;
			}
		
		// Calcul par ecart à la moyenne pondéré
		if($methode == 2)
			{
				$nb=count($tranches)-1;
				$tranches[$nb+1]=$max;
				for($i=$nb; $i > $nb-5; $i--)
					{
						$tranches[$i]=intval(($tranches[$i+1] + $tranches[$i]) /2);
					}
			}
		$tranches=array_reverse($tranches);
		return $tranches;
	}
}