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
////////////////////////////////////////////////////////////////////////////////
// OPAC3 : FILTRE DE SELECTION : ZONE / BIBLIOTHEQUE / PROFIL
//
//	$combo = z -> rend combo zone
//           b -> rend combo bib
//           p -> rend combo profil
////////////////////////////////////////////////////////////////////////////////
 
class ZendAfi_View_Helper_Admin_ProfileSelect extends ZendAfi_View_Helper_BaseHelper
{
	public $user;                // User identifié
	
//-----------------------------------------------------------------------------------------------
// Main routine 
//-----------------------------------------------------------------------------------------------
	public function profileSelect($url,$id_zone=0, $id_bib=0,$combo="zb", $id_profil=0,$portail=false,$all=true,$champ_nom=false)
	{
		$titre=$this->translate()->_("Localisation");
		if($id_profil) $titre.=$this->translate()->_(" et profil");
		$this->user = Zend_Auth::getInstance()->getIdentity();
		$html = '<div class="form" style="font-size:10pt">'.$titre.'<br/><table cellpadding="3" cellspacing="1" border="0">';
	
		// Combo des zones
		if(preg_match("^z^",$combo))
		{
			$html.='<tr>
			<td style="width:140px;color:#575757;font-size:8pt;text-align:right;">'.$this->translate()->_('Territoire').'</td>
			<td>'.$this->getZoneSelect($id_zone,$portail,$all,$url).'</td>
			</tr>';
		}
	
		// Combo des bibliotheques
		if(preg_match("^b^",$combo))
		{
			$html.='<tr>
			<td style="color:#575757;font-size:8pt;text-align:right;">'.$this->translate()->_('Bibliothèque').'</td>
			<td>'.$this->getBibSelect($id_zone,$id_bib,$portail,$all,$url).'</td>
			</tr>';
		}
		
		// Combo des profils
		if(preg_match("^p^",$combo))
		{
			$html.='<tr>
			<td style="color:#575757;font-size:8pt;text-align:right;">'.$this->translate()->_('Profil').'</td>
			<td>'.$this->getProfilSelect($id_zone,$id_bib,$id_profil,$url).'</td>
			</tr>';
		}
		$html.='</table>';
		
		// Recherche d'abonnés
		if($champ_nom==true)
		{
			$html.='<br/>Recherche';
			$html.='<form method="post" action="'.BASE_URL.'/admin/users/index?recherche=1">';
			$html.='<table cellpadding="3" cellspacing="1" border="0">';
			$rech_user=$_SESSION["admin"]["rech_user"];
			$cls=new ZendAfi_Acl_AdminControllerRoles();
			$combo_roles=$cls->rendCombo($rech_user["role"],$this->user->ROLE_LEVEL,true);
			$html.='
			<tr>
				<td style="width:170px;color:#575757;font-size:8pt;text-align:right;">'.$this->translate()->_('Identifiant commence par').'</td>
				<td><input type="text" size="40" id="rech_user" name="login" value="'.$rech_user["login"].'"></td>
			</tr>
			<tr>
				<td style="color:#575757;font-size:8pt;text-align:right;">'.$this->translate()->_('Nom commence par').'</td>
				<td><input type="text" size="40" id="rech_user" name="nom" value="'.$rech_user["nom"].'"></td>
			</tr>
			<tr>
				<td style="color:#575757;font-size:8pt;text-align:right;">Rôle</td>
				<td>'.$combo_roles.'</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top:10px;padding-left:170px">
					<a href="#" onclick="document.forms[0].submit();">&raquo;&nbsp;'.$this->translate()->_('Lancer la recherche').'&nbsp;&laquo;</a>&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			</table>
			</form>';
		}
		
		$html.='</div>';
		return $html;
	}

//----------------------------------------------------------------------------------
// Combo des zones
//----------------------------------------------------------------------------------
	public function getZoneSelect($id_zone_selected,$portail,$all,$url)
	{
		$class_zone = new Class_Zone();
		$zone_array = $class_zone->getAllZone();
		$redirect = "location='".BASE_URL."/admin/".$url."?z='+ this.value + '&amp;b=ALL'";
		
		// Si l'user est minimum modo_portail
		if ($this->user->ROLE_LEVEL > 4)
		{
			$html[]='<select name="zone" id="zone" onchange="'.$redirect.'">';
			if($all == true) $html[]='<option value="ALL" >** tous **</option>';
			if($portail == true) 
			{
				if($id_zone_selected=="PORTAIL" or !$id_zone_selected) $sel = ' selected="selected"'; else $sel = "";
				$html[]='<option value="PORTAIL"'.$sel.'>Portail</option>';
			}
		}
		
		// Si l'user est admin_bib
		else $html[]='<select name="zone" id="zone" disabled="disabled">';
	
		foreach ($zone_array as $zone) {
			if($id_zone_selected == $zone->ID_ZONE and $id_zone_selected !="PORTAIL") $sel = 'selected="selected"'; else $sel="";

			// Si l'user est minimum modo_portail
			if($this->user->ROLE_LEVEL > 4) {
				$html[]='<option value="'.$zone->ID_ZONE.'" '.$sel.'>'.$zone->LIBELLE.'</option>';
			}
			// Si l'user est admin_bib
			else
			{
				$class_bib = new Class_Bib();
				$bib = $class_bib->getBib($this->user->ID_SITE);
				if($zone->ID_ZONE == $bib["ID_ZONE"])
				$html[]='<option value="'.$zone->ID_ZONE.'" '.$sel.'>'.$zone->LIBELLE.'</option>';
			}
		}
		$html[]='</select>';
		return (implode('',$html));
	}
    
//----------------------------------------------------------------------------------
// Combo des bibliotheques
//----------------------------------------------------------------------------------
	public function getBibSelect($id_zone,$id_bib,$portail,$all,$url)
	{
		$class_bib = new Class_Bib();
		$bib_array = $class_bib->getBibs($id_zone);
		$redirect = "location='".BASE_URL."/admin/".$url."?z=".$id_zone."&amp;b='+ this.value";
	
		if ($this->user->ROLE_LEVEL > 4) // Si l'user est minimum modo_portail
		{
			$html[]='<select name="bib" id="bib" onchange="'.$redirect.'">';
			if($id_zone != "PORTAIL")
			{ 
				if($id_bib == "ALL") $sel = 'selected="selected"'; else $sel="";
				if($all == true) $html[]='<option value="ALL" '.$sel.'>** '.$this->translate()->_('toutes').' **</option>';
			}
			if($portail == true and (!$id_zone or $id_zone=="PORTAIL" or $id_zone=="ALL")) 
			{
				if($id_bib == "PORTAIL") $sel = 'selected="selected"'; else $sel="";
				$html[]='<option value="PORTAIL" '.$sel.'>'.$this->translate()->_('Portail').'</option>';
			}
		}
		else $html[]='<select name="bib" id="bib" style="width:100%" disabled="disabled">';
	
		if($bib_array)
		{
			foreach ($bib_array as $bib)
			{
				if ($this->user->ROLE_LEVEL > 4) // Si l'user est minimum modo_portail
				{
					$bib_id = $bib["ID_SITE"];
					if($id_bib == $bib_id) $sel = 'selected="selected"'; else $sel="";
					$html[]='<option value="'.$bib["ID_SITE"].'" '.$sel.'>'.$bib["LIBELLE"].'</option>';
				}
				else // Si l'user est admin_bib
				{
					if ($bib["ID_SITE"] == $this->user->ID_SITE){ $html[]='<option value="'.$bib["ID_SITE"].'" >'.$bib["LIBELLE"].'</option>';}
				}
			}
		}
		$html[]='</select>';
		return implode('',$html);
	}
    
//----------------------------------------------------------------------------------
// Combo des profils
//----------------------------------------------------------------------------------
	function getProfilSelect($id_zone,$id_bib,$id_profil,$url)
	{
		$profil_array = Class_Profil::getLoader()->findAllByZoneAndBib($id_zone,$id_bib);
		$redirect = "location='".BASE_URL."/admin/".$url."?id_profil='+ this.value";
		$html[]='<select name="id_profil" id="id_profil" onchange="'.$redirect.'">';
		foreach ($profil_array as $profil) {
			if ($id_profil == $profil->getId())
				$selected='selected="selected"'; 
			else 
				$selected='';
			$html[]='<option value="'.$profil->getId().'" '.$selected.'>'.$profil->getLibelle().'</option>';
		}
		$html[]='</select>';
		return implode('',$html);
	}
}