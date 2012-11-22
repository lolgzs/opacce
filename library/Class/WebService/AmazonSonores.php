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
// OPAC3 - AMAZON : DOCUMENTS SONORES
//////////////////////////////////////////////////////////////////////////////////////////

class Class_WebService_AmazonSonores extends Class_WebService_Amazon
{
	
//------------------------------------------------------------------------------------------------------
// Notice complete
//------------------------------------------------------------------------------------------------------
	function rend_notice_ean($ean)
	{
		$req=$this->req_ean_sonore($ean)."&ResponseGroup=Large";
		if($req == "") return false;
		if(!$this->requete($req)) return false;
		
		$item=$this->xml->getNode("Items");
		$ret["nb_resultats"]=$this->xml->get_child_value($item,"totalresults");
		if($ret["nb_resultats"] >0 )
		{
			$root=$this->xml->get_child_node($item,"item");
			$ret["asin"]=$this->xml->get_child_value($root,"asin");
			$img=$this->xml->get_child_node($root,"mediumimage");
			if($img)
			{
				$ret["vignette"]=$this->xml->get_child_value($img,"url");
				$img=$this->xml->get_child_node($root,"largeimage");
				if($img) $ret["image"]=$this->xml->get_child_value($img,"url");
			}
			// Données album
			$node=$this->xml->get_child_node($root,"itemattributes");
			$ret["ean"]=$this->xml->get_child_value($node,"ean");
			$ret["titre"]=$this->xml->get_child_value($node,"title");
			$ret["editeur"]=$this->xml->get_child_value($node,"publisher");
			$dateClass = new Class_Date();
			$ret["date"]=$dateClass->LocalizedDate($this->xml->get_child_value($node,"releasedate"), 'yyyy-MM-dd');
			$ret["support"]=$this->xml->get_child_value($node,"binding");
			// auteurs
			$auteur=$this->xml->get_child_node($node,"creator");
			while($auteur)
			{
				$index=count($ret["auteurs"]);
				$ret["auteurs"][$index]["nom"]=$this->xml->get_value($auteur);
				$ret["auteurs"][$index]["responsabilite"]=$this->xml->get_attribut($auteur,"role");
				$auteur=$this->xml->get_sibling($auteur);
			}
			// Morceaux
			$ret["nombre_volumes"]=$this->xml->get_child_value($node,"numberOfDiscs");
			$node=$this->xml->get_child_node($root,"tracks");
			if($node)
			{
				$disc=$this->xml->get_child_node($node,"disc");
				while($disc)
				{
					$volume=$volume+1;
					$piste=0;
					$node_piste=$this->xml->get_child_node($disc,"track");
					while($node_piste)
					{
						$piste++;
						$ret["morceaux"][$volume][$piste]["titre"]=$this->xml->get_value($node_piste);
						//$ret["morceaux"][$volume][$piste]["url_ecoute"]=$this->get_url_ecoute($ret["asin"],$volume,$piste);
						$node_piste=$this->xml->get_sibling($node_piste);
					}
					$disc=$this->xml->get_sibling($disc);
				}
			}
		}
		return $ret;
	}
	
//------------------------------------------------------------------------------------------------------
// Images
//------------------------------------------------------------------------------------------------------
	public function getImages($ean)
	{
		if(!trim($ean)) return false;
		$req=$this->req_ean_sonore($ean)."&ResponseGroup=Images";
		if(!$this->requete($req)) return false;
		$item=$this->xml->getNode("item");
		if($item<0) return false;

		// Grande
		$img=$this->xml->get_child_node($item,"largeimage");
		if($img) $image=$this->xml->get_child_value($img,"url");

		// vignette
		$img=$this->xml->get_child_node($item,"mediumimage");
		if($img) $vignette=$this->xml->get_child_value($img,"url");
		if($image > "" and $vignette > "") return array("vignette" => $vignette,"image" => $image);
		
		if($vignette > "" and $image == "") $image=$vignette;
		if($image > "" and $vignette == "") $vignette=$image;
		if($vignette > "") return array("vignette" => $vignette,"image" => $image);
		else return false;
	}
	
//------------------------------------------------------------------------------------------------------
// Rend l'url pour ecouter les morceaux (OBSOLETE)
//------------------------------------------------------------------------------------------------------
	function get_url_ecoute($asin,$volume,$track)
	{
		//$volume=str_repeat("0",3-strlen($volume)).$volume;
		//$track=str_repeat("0",3-strlen($track)).$track;
		//$url="http://www.amazon.fr/gp/music/clipserve/".$asin.$volume.$track."/1/ref=mu_sam_ra".$volume."_".$track;
		return false;
	}
	
	//------------------------------------------------------------------------------------------------------
	// Formatte et rend l'argument ean pour la requete
	//------------------------------------------------------------------------------------------------------
	function req_ean_sonore($ean)
	{
		if(strlen($ean) != 13) return "";
		$isbn = str_replace("-","",$ean);
		$url="&Operation=ItemSearch&SearchIndex=Music&Keywords=" .$ean;
		return $url;
	}
}