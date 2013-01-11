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
// OPAC3 - Combo des menu paramétrés
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_ComboProfils extends ZendAfi_View_Helper_BaseHelper
{
	//------------------------------------------------------------------------------------------------------
	// Main routine
	//------------------------------------------------------------------------------------------------------
	function comboProfils($id_zone='ALL',$id_bib='ALL',$id_profil=1,$autoload=false)
	{
		if($autoload==true) 
			$onchange=' onchange="window.location=\''.BASE_URL.'?id_profil=\' + this.value"'; 
		else 
			$onchange="";

		$profils = Class_Profil::getLoader()->findAllByZoneAndBib($id_zone,$id_bib);
		$profils_by_bib = array();
		foreach ($profils as $profil) {
			if ($profil->hasParentProfil()) continue;

			if ($profil->isInPortail()) {
				$libelle = $this->translate()->_('Portail');
			} else {
				$libelle = $profil->getBibLibelle();
			}

			if (!array_key_exists($libelle, $profils_by_bib))
				$profils_by_bib[$libelle] = array();

			$profils_by_bib[$libelle] []= $profil;
		}
		ksort($profils_by_bib);

		
		$html = '<select id="select_clef_profil" name="clef_profil"'.$onchange.'>';
		$rupture="";
		foreach ($profils_by_bib as $bib => $profils) {
			$html.='<optgroup label="'.htmlentities($bib).'" style="font-style:bold;color:#FF6600">';
	
			foreach($profils as $profil) {
				$libelle = $profil->getLibelle();
				
				if ($id_profil == $profil->getId()) $selected='selected="selected"'; else $selected='';
				$html.='<option  style="color:#666" value="'.$profil->getId().'" '.$selected.'>'.$libelle.': '.$this->translate()->_('Accueil').'</option>';

				foreach ($profil->getSubProfils() as $page) {
					if ($id_profil == $page->getId()) $selected='selected="selected"'; else $selected='';
					$html.='<option  style="color:#666" value="'.$page->getId().'" '.$selected.'>'.$libelle.': '.$page->getLibelle().'</option>';

				}
			}
			$html.='</optgroup>';
		}

		$html.='</select>';
		return $html;
	}
}