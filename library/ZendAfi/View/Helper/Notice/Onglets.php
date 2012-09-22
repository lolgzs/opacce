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
class ZendAfi_View_Helper_Notice_Onglets extends ZendAfi_View_Helper_Notice_Abstract {
	public function notice_Onglets($notice, $preferences) {
		if (!array_isset('onglets', $preferences))
			return '';

		$onglets = $this->getOngletsFromPreferences($preferences);
		return $this->renderOngletsForNotice($onglets, $notice);
	}


	public function getOngletsFromPreferences($preferences) {
		return $this->selectOngletsFromPreferences($preferences, [3]);
	}
	
	
	public function renderOngletsForNotice($onglets, $notice) {
		$id = $notice->getId();
		$isbn = $notice->getIsbn();
		
		// Html du set
		$tag_patience ='<div class="notice_patience" style="text-align:right;width:15px"><img src="'.URL_IMG.'patience.gif" border="0" alt="'.$this->_('Chargement en cours').'" /></div>';
		$tag_patience.='<div class="notice_patience">'.$this->_translate->_('Veuillez patienter : lecture en cours...').'</div>';

		$html_onglets = $html_contenu = '';

		// Onglets
		$i=0;
		$tabs = array();
		foreach($onglets as $type => $onglet) {
			$id_onglet = sprintf('set%d_onglet_%d', $id, $i++);
			$js_onclick = $this->getOnclick($type, $isbn, $id_onglet);

			if($i==1) 
				Class_ScriptLoader::getInstance()
					->addJQueryReady("infos_onglet". str_replace("this.id",
																											 "'".$id_onglet."'", 
																											 $js_onclick));

			$html_onglets.= sprintf('<div id="%s" class="titre_onglet" style="width:%d%%" onclick="infos_onglet%s">%s</div>',
															$id_onglet,
															$onglet["largeur"] ? $onglet["largeur"] : 20,
															$js_onclick,
															$onglet["titre"]);

			// Boite contenu
			$html_contenu.=sprintf('<div id="%s_contenu_row">%s</div>', 
														 $id_onglet, 
														 sprintf('<div id="%s_contenu" class="onglet">%s</div>', 
																		 $id_onglet, 
																		 $tag_patience));
		}

		// Contenu et fin
		return sprintf('<div class="onglets">'.
									   '<div class="onglets_titre">'.
									     '<div>%s</div>'.
									   '</div>'.
									   '<div class="onglets_contenu">%s</div>'.
									 '</div>', 
									 $html_onglets, 
									 $html_contenu);
	}

}
?>