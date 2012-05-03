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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Class_Module_Tags
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Tags extends ZendAfi_View_Helper_Accueil_Base {


//---------------------------------------------------------------------
// Construction HTML 
//---------------------------------------------------------------------
	public function getHtml()
	{
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

		if ($this->preferences["notices"] > 0)
			$this->preferences["tri"] = $this->preferences["notices"];
		else
			$this->preferences["tri"] = -1; // Permet de ne pas faire d'order by.

		$notices = $catalogue->getNoticesByPreferences($this->preferences);

		foreach ($notices as $notice)	{
			$type="T".$notice["type_doc"];
			$facettes["T"][$type]++;
			$items=explode(" ",trim($notice["facettes"]));
			foreach($items as $item)
			{
				$type=substr($item,0,1);
				$facettes[$type][$item]++;
			}
		}

		// Constituer le tableau des tags
		$nb=0;
		while(true)
		{
			// On cherche le plus fort pour chaque type
			$controle=array("nombre" => 0);
			$yen_a_plus=true;
			for($i=0; $i<strlen($this->preferences["type_tags"]); $i++)
			{
				$type=$this->preferences["type_tags"][$i];
				if(!$facettes[$type]) continue;
				$yen_a_plus=false;
				
				if(!$sorted[$type]) {arsort($facettes[$type]); $sorted[$type]=true;}
				
				$lig=array_slice($facettes[$type],0,1);
				$compare=array_values($lig);
				if($compare[0] > $controle["nombre"])
				{
					$controle["nombre"]=$compare[0];
					$controle["type"]=$type;
					$controle["clef"]=$lig;
				}
			}
			// Si max atteint ou plus de facettes c'est fini
			if($yen_a_plus == true) break;
			$nb++;
			if($nb > $this->preferences["nombre"]) break;
	
			// On depile l'item des facettes et on empile dans le resultat
			foreach($controle["clef"] as $clef => $nombre);
			array_shift($facettes[$controle["type"]]);
			$table[$nb]["id"]=$clef;
			$table[$nb]["libelle"]=Class_Codification::getLibelleFacette($clef);
			$table[$nb]["nombre"]=$nombre;
		}
	
		// Html avec le view helper
		$cls=new ZendAfi_View_Helper_NuageTags();
		$this->contenu.= $cls->nuageTags($table).'<br />';
	
		// Valorisation du html accessible

		return $this->getHtmlArray();
	}
}