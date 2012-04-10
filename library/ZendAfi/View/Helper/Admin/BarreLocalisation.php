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
// OPAC3 : Affichage localisation zone / bib
////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Admin_BarreLocalisation extends ZendAfi_View_Helper_BaseHelper
{

	//----------------------------------------------------------------------------------
	// Main routine
	//----------------------------------------------------------------------------------
	public function BarreLocalisation($id_zone = 0, $id_bib = 0)
	{
		$html=sprintf('<p style="color:#0058A5;font-weight:bold;margin-top:20px">%s : ',
									$this->translate()->_('Localisation'));
		if($id_zone=="PORTAIL") 
			$html.=$this->translate()->_("Portail");
		else	{
			if ( ($bib = Class_Bib::getLoader()->find($id_bib))
					 and ($zone = Class_Zone::getLoader()->find($id_zone)) )
				$html .= sprintf("%s/%s", 
												 $zone->getLibelle(), 
												 $bib->getLibelle());
		}
		$html.='</p>';
		return $html;
	}
}

?>