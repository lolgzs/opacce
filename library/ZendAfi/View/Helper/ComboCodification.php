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
// OPAC3 - Renvoie le html pour les combos de codifications
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_ComboCodification extends ZendAfi_View_Helper_BaseHelper {    
	function ComboCodification($type,$valeur_select,$events="") {
		$profil = Class_Profil::getCurrentProfil();
		
		// Type de document
		if($type=="type_doc")
		{
			$name="type_doc";
			$id="select_type_doc";
			// Selection liée au profil
			if($profil->getSelTypeDoc())
				$controle=";".$profil->getSelTypeDoc().";";

			$data=fetchOne("Select liste from variables where clef='types_docs'");
			$v=explode(chr(13).chr(10),$data);
			$items[]=array("value"=>"","libelle"=>"tous");
			$controle = '';
			for($i=0; $i < count($v); $i++)
			{
				$elem=explode(":",$v[$i]);
				if($controle) if(strpos($controle,';'.$elem[0].';')=== false) continue;
				if($elem[0]) $items[]=array("value" => $elem[0],"libelle" => $elem[1]);
			}
		}

		// Section
		if($type=="section")
		{
			$name="section";
			$id="select_section";
			// Selection liée au profil
			if($profil->getSelSection()) 
				$controle=";".$profil->getSelSection().";";

			$data=fetchAll("Select id_section,libelle from codif_section order by libelle");
			$items[]=array("value"=>"","libelle"=> $this->translate()->_("toutes"));
			$controle = '';
			for($i=0; $i < count($data); $i++)
			{
				$code=$data[$i]["id_section"];
				$libelle=$data[$i]["libelle"];
				if($controle) if(strpos($controle,';'.$code.';')=== false) continue;
				if($code) $items[]=array("value" => $code,"libelle" => $libelle);
			}
		}

		// Composer le html
		if($events > "") $events=" ".$events;
		$combo='<select id="'.$id.'" name="'.$name.'"'.$events.' class="typeDoc">';
		foreach($items as $item)
		{
			if($valeur_select==$item["value"]) $selected=" selected='selected'"; else $selected="";
			$combo.='<option value="'.$item["value"].'"'.$selected.'>'.stripSlashes($item["libelle"]).'</option>';
		}
		$combo.='</select>';
		return $combo;
	}
}