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

class ZendAfi_View_Helper_Avis extends ZendAfi_View_Helper_BaseHelper {
	protected $_vignette_link_to_avis = false;
	protected $_limit_nb_word = 0;
	protected $_actions = array();

	function setVignetteLinkToAvis() {
		$this->_vignette_link_to_avis = true;
		return $this;
	}

	function setLimitNbWord($limit) {
		$this->_limit_nb_word = $limit;
		return $this;
	}


	function setActions($actions) {
		$this->_actions = $actions;
	}


	function avis($avis, $limit_nb_word = 0, $vignette_link_to_avis = false){
		if (!$avis) return '';

		$url_vignette = URL_ADMIN_IMG."supports/vignette_vide.gif";
		$titre_notice = $this->translate()->_('Oeuvre non trouvée');
		$url_notice = $this->_getUrlNotice($avis);
		$url_click_vignette = $this->_getUrlClickVignette($avis);

		if (null !== $notice = $avis->getFirstNotice()) {
			$titre_notice = $notice->getTitrePrincipal();

			if (strlen($auteur_principal = $notice->getAuteurPrincipal()) > 0)
				$titre_notice = "$titre_notice ($auteur_principal)";

			$url_vignette = $notice->getUrlVignette();
		}
		
		$contenu_avis = $this->contenu_avis($avis);
		$html = sprintf(
			"<div class='critique'>".
				"<h2>%s</h2>".
				"<div class='vignette_notice'>".
					"<a href='%s'>".
						"<img alt='%s' src='%s'/>".
					"</a>".
					"<a href='%s'>%s</a>".
				"</div>%s".
			"</div>", 
			$titre_notice,
			$url_click_vignette,
			$this->translate()->_('vignette de la notice'), $url_vignette,
			$url_notice, $this->translate()->_('Voir la notice'),
			$contenu_avis);

		return $html;
	}


	public function contenu_avis($avis) {
		$entete = $avis->getEntete();
		$url_avis = $this->_getUrlAvis($avis);
		$format_text_avis = $this->_formatTextAvis($avis->getAvis());
		$text_avis = $format_text_avis['text_avis'];
		$lire_la_suite = '';
		if ($format_text_avis['lire_la_suite'] == true)
			$lire_la_suite = sprintf("<div class='lire_la_suite'><a href='$url_avis'>%s</a></div>", 
															 $this->translate()->_('Lire la suite'));

		$date_avis = $avis->getReadableDateAvis();

		$auteur = $avis->getUserName();
		$url_auteur = $this->view->url(array("module" => 'opac',
																				 "controller" => "blog",
																				 "action" => "viewauteur",
																				 "id" => $avis->getIdUser()));
		$read_speaker_tag = $this->_getReadSpeakerTag($avis);
		$actions_tag = $this->_getActionsTag($avis);
		$moderation_tag = $this->_getModerationTag($avis);

		$html = 
			"<div class='contenu_critique'>".
			    $this->view->noteImg($avis->getNote()).
					"<a class='entete_critique' href='$url_avis'>$entete</a>".
					"<span class='auteur_critique'>".
			       ('' != $auteur ? " <a href='$url_auteur'>$auteur</a>" : '').
							" - $date_avis".$actions_tag.
					"</span>".
					"$read_speaker_tag".
			    "<p>$text_avis</p>".
			    $moderation_tag.
					$lire_la_suite.
			"</div>";

		return $html;
	}


	protected function _getModerationTag($avis) {
		if ($avis->isWaitingForModeration())
			return sprintf('<div class="moderation">%s</div>', 
										 $this->translate()->_('En attente de modération'));
		return '';
	}


	protected function _getActionsTag($avis) {
		$html_actions = '';

		foreach($this->_actions as $action) {
		  $link = $this->view->tagAnchor($this->view->url(array('action' => $action.'avisnotice',
																														'id' => $avis->getId())),
																		 $this->view->boutonIco("type=$action"));
			$html_actions .= "<span rel='$action'>$link</span>";
		}

		return ($html_actions ? "<span class='actions'>$html_actions</span>" : '');
	}


	protected function _getUrlAvis($avis) {
		if (null == $avis->getId())
			return '#';
		return $this->view->url(array("module" => 'opac',
																	"controller" => "blog",
																	"action" => "viewavis",
																	"id" => $avis->getId())); 
	}


	protected function _getUrlNotice($avis) {
		if (null !== $notice = $avis->getFirstNotice())
			return $this->view->url(array("module" => 'opac',
																		'controller' => 'recherche',
																		'action' => 'viewnotice',
																		'id' => $notice->getId()));

		return '';
	}


	protected function _getUrlClickVignette($avis) {
		if ($this->_vignette_link_to_avis) 
			return $this->_getUrlAvis($avis);

		return $this->_getUrlNotice($avis);
	}


	protected function _getReadSpeakerTag($avis) {
		if (null == $avis->getId())
			return '';
		$read_speaker_helper = new ZendAfi_View_Helper_ReadSpeaker();
		return $read_speaker_helper->readSpeaker('blog', 
																						 'readavis', 
																						 array("id" => $avis->getId()));
	}


	protected function _formatTextAvis($txt_avis) {
		if (($this->_limit_nb_word <= 0) or 
				(count($words = explode(' ', $txt_avis)) <= $this->_limit_nb_word))
			return array('text_avis' => nl2br($txt_avis),
									 'lire_la_suite' => false);

		$fmt_avis = implode(' ', array_slice($words, 0, $this->_limit_nb_word));
		return array('text_avis' => nl2br($fmt_avis).' [...]',
								 'lire_la_suite' => true);
	}
}


?>