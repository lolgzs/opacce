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
// OPAC3 - Class module zone - carte des territoires
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Accueil_CarteZones extends ZendAfi_View_Helper_Accueil_Base
{

	//---------------------------------------------------------------------
	// Construction du Html
	//---------------------------------------------------------------------
	public function getHtml()
	{
		$this->titre = $this->preferences["titre"];
		$this->contenu = sprintf(
				'<table cellpadding="0" cellspacing="0" border="0" style="padding:0px;" width="100%%">
					<tr>
						<td valign="top" style="padding:0px;">
							<a href="%s"><img border="0" width="134" src="%s" alt="%s"/></a>
						</td>
					</tr>',
				BASE_URL.'/bib/', 
				sprintf("%s/photobib/global.jpg", USERFILESURL),
				$this->translate()->_('Carte des zones'));

		if($this->preferences["message_carte"]) $this->contenu.='<tr><td>'.$this->preferences["message_carte"].'</td></tr>';
		$this->contenu.='</table>';

		return $this->getHtmlArray();
	}

}