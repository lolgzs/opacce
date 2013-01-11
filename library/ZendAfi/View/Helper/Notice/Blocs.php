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
class ZendAfi_View_Helper_Notice_Blocs extends ZendAfi_View_Helper_Notice_Abstract {
	public function notice_Blocs($notice, $preferences) {
		if (!array_isset('onglets', $preferences))
			return '';

		$blocs = $this->getBlocsFromPreferences($preferences);
		return $this->renderBlocsForNotice($blocs, $notice);
	}


	public function getBlocsFromPreferences($preferences) {
		return $this->selectOngletsFromPreferences($preferences, [1, 2]);
	}
	
	
	public function renderBlocsForNotice($blocs, $notice) {
		$id = $notice->getId();
		$isbn = $notice->getIsbn();

		$html='<table cellspacing="0" cellpadding="0" width="100%">';

		// Blocs
		$i = 0;
		foreach($blocs as $type => $bloc)	{
			$id_bloc="bloc_".$id."_".$i++;
			$js ='infos_bloc'.$this->getOnclick($type, $isbn, $id_bloc);

			if ((int)$bloc["aff"] == 1) 
				Class_ScriptLoader::getInstance()
					->addJQueryReady('infos_bloc'. str_replace('this.id',
																										 '"'.$id_bloc.'"',
																										 $this->getOnclick($type, $isbn, $id_bloc)));

			// Titre
			$html.='<tr>';
			$html.='<td width="10" style="text-align:center" valign="top"><img id="I'.$id_bloc.'" src="'.URL_IMG.'bouton/plus_carre.gif" border="0" onclick="'.$js.'" style="cursor:pointer;margin-top:5px"  alt="Déplier"  /></td>';
			$html.='<td><div id="'.$id_bloc.'" class="notice_bloc_titre" onclick="'. $js.'">'.$bloc["titre"].'</div></td>';
			$html.='</tr>';
			// Boite contenu
			$html.='<tr id="'.$id_bloc.'_contenu_row"><td></td>';
			$html.='<td><div id="'.$id_bloc.'_contenu" class="notice_bloc">';
			$html.='<table><tr>';
			$html.='<td class="notice_patience" style="text-align:right;width:15px"><img src="'.URL_IMG.'patience.gif" border="0"  alt="'.$this->_('Chargement en cours').'" /></td>';
			$html.='<td class="notice_patience">'.$this->_translate->_('Veuillez patienter : lecture en cours...').'</td>';
			$html.='</tr></table></div></td></tr>';
		}
		
		// fin
		$html.='</table>';
		return $html;
	}

}
?>