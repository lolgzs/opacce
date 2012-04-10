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
// OPAC3 - Class module Recherche Guidee
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_RechGuidee extends ZendAfi_View_Helper_Accueil_Base
{

//---------------------------------------------------------------------
// Construction du Html
//--------------------------------------------------------------------- 
	public function getHtml()
	{
		if (!array_isset('recherche', $_SESSION)) {
			$_SESSION['recherche'] = array('selection_bib' => '');
		}
			

		$class_rech = new Class_MoteurRecherche();
		$ret=$class_rech->lancerRechercheGuidee(0,'',$_SESSION["recherche"]["selection_bib"]);
		$_SESSION["recherche"]["fil_ariane"]=$ret["fil_ariane"]["fil"];
		if($ret)
		{
			$rubrique = '';
			foreach ($ret["rubriques"] as $rub) {
				$rubrique.='<a href="'.htmlspecialchars($rub["url"]).'">&raquo;&nbsp;'.$rub["libelle"].'</a><br />';
			}
		}
		$this->titre = $this->preferences["titre"];
		$this->contenu = 
			'<table width="98%">
				<tr>
					<td>'.$rubrique.'</td>
				</tr>
			</table>
			';
		
		// Retour
		return $this->getHtmlArray();
	}

}
