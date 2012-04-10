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
class ZendAfi_View_Helper_Accueil_Rss extends ZendAfi_View_Helper_Accueil_Base {
	/**
	 * @return array
	 */
	public function getHtml() {
		extract($this->preferences);

		$contenu = '';

		// Sélection de catégories ou d'articles
		if ($type_aff == 1) {
			$feeds = Class_Rss::getLoader()->getFluxFromIdsAndCategories(
																				explode('-', $id_items),
																				explode('-', $id_categorie)
																		);

			$titre = sprintf('<a href="%s" title="%s">%s</a>',
											htmlspecialchars(BASE_URL.'/rss/viewselection/id/'.$this->id_module),
											$this->translate()->_('Voir les fils RSS sélectionnés'),
											$titre);

			$contenu .= $this->getFlux($feeds, 0);

		}

		// Les plus recents
		if ((2 == $type_aff) 	&& ($nb_aff > 0))	{
			$last_rss = Class_Rss::getLoader()->getLastRss(50);

			shuffle($last_rss);
			$last_rss = array_slice($last_rss, 0, 2);
			if (!$titre)
				$titre = $this->translate()->_("Derniers fils RSS ajoutés");

			$titre = sprintf('<a href="%s" title="%s">%s</a>',
											htmlspecialchars(BASE_URL.'/opac/rss/viewrecent/nb/20'),
											$this->translate()->_('Liste des derniers fils RSS ajoutés'),
											$titre);

			$contenu .= $this->getFlux($last_rss, $nb_aff);
		}

		$this->titre = $titre;
		$this->contenu = $contenu;

		return $this->getHtmlArray();

	}

	/**
	 * @param array $fluxs
	 * @param string $nb_aff
	 * @return string
	 */
	private function getFlux($fluxs, $nb_aff) {
		if (!$fluxs) {
			return '';
		}

		if ((1 == $nb_aff)|| (1 == count($fluxs))) {
			return $this->_getOnlyOneRssHtml($fluxs[0]);
		}

		return $this->_getManyRssHtml($fluxs);

	}

	/**
	 * @param array $channels
	 * @return string
	 */
	protected function _getManyRssHtml(array $channels) {
		$html = '';

		$parts = array();

		foreach ($channels as $channel) {
			$parts[] = $this->_getOneRssHtml($channel);

		}

		return implode('<div style="width:100%;background:transparent url('.URL_IMG.'box/menu/separ.gif) repeat-x scroll center bottom;margin-bottom:5px">&nbsp;</div>', $parts);

	}

	/**
	 * @param Class_Rss $channel
	 * @return string
	 */
	protected function _getOneRssHtml($channel) {
		if (1 == $this->division) {
			$channel->setTitre($this->fixLibelleBoiteGauche($channel->getTitre()));
			$channel->setDescription($this->extractHeader($channel->getDescription()));
		}

		return '<h2>' .
							$this->view->tagAnchor(
								$this->view->url(array(
									'controller'	=> 'rss',
									'action'			=> 'main',
									'id_flux'			=> $channel->getId()
								)),
								'&raquo;&nbsp;' . $channel->getTitre()
							) .
			      '</h2>' . $channel->getDescription() . '<br />';
	}

	/**
	 * @param Class_Rss $channel
	 * @return string
	 */
	protected function _getOnlyOneRssHtml($channel) {
		if (!$channel)
			return "";

		$contentId = 'rss_content_' . $channel->getId();

		$html = $this->_getOneRssHtml($channel);

		$html .= '<div class="' . $contentId. '"><div class="rss-loading" style="padding-top:10px;">' . $this->view->tagImg(URL_IMG . 'patience.gif', array('style' => 'vertical-align:middle')) . ' ' . $this->view->_('Chargement en cours...') . '</div></div>';
		$html .= '<script type="text/javascript" src="' . URL_ADMIN_JS . 'rss.js"></script>';
		$html .= sprintf('<script type="text/javascript">$(document).ready(loadRssByContentName(\'div.%s\', %d, %d))</script>',
										 $contentId,
										 Class_Profil::getCurrentProfil()->getId(),
										 $this->id_module);

		return $html;

	}

}