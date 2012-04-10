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
// OPAC3 - Dernières critiques sur les notices
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Critiques extends ZendAfi_View_Helper_Accueil_Base {
	public function getHtml() {
		extract($this->preferences);

		$this->titre = $this->_getTitle();

		if ($rss_avis)
			$this->rss_interne = $this->_getRSSurl('rss', 'critiques');

		$fetched_avis = Class_AvisNotice::getLoader()->getAvisFromPreferences($this->preferences);

		if ($display_order == 'Random')
			shuffle($fetched_avis);

		$selected_avis = array();
		$only_img = ($this->preferences['only_img'] == '1');

		foreach($fetched_avis as $avis) {
			if (count($selected_avis) >= $nb_aff_avis)
				break;

			if (null == $notice = $avis->getFirstNotice()) {
				continue;
			}

			if (($only_img===false) || ( $avis->getFirstNotice()->hasVignette() === true))
				$selected_avis []= $avis;
		}

		if (count($selected_avis) == 0)
			$this->contenu = sprintf('<p>%s</p>', $this->translate()->_('Aucune critique récente'));
		else {
			$avis_helper = new ZendAfi_View_Helper_Avis;
			$avis_helper
				->setLimitNbWord($this->preferences['nb_words'])
				->setVignetteLinkToAvis();
			foreach ($selected_avis as $avis)
				$this->contenu .= $avis_helper->avis($avis);
		}

		$this->contenu .= '<div class="clear"></div>';
		return $this->getHtmlArray();
	}


	protected function _getTitle() {
    return $this->view->getHelper('tagAnchor')->baseURL($this->preferences['titre'],
																	 'blog',
																	 'viewcritiques',
																	 array('id_module' => $this->id_module));
	}
}
?>