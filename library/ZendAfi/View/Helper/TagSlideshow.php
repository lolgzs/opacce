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
class ZendAfi_View_Helper_TagSlideshow extends Zend_View_Helper_HtmlElement {
	protected $_default_preferences = array(
																					'op_largeur_img' => 400,
																					'op_hauteur_boite' => 400,
																					'nb_aff' => 0);
	protected $_preferences;
	protected $_album;


	public function setPreferences($preferences) {
		if (null == $preferences)
			$this->_preferences = $this->_default_preferences;
		else
			$this->_preferences  = array_merge($this->_default_preferences, 
																				 $preferences);
		return $this;
	}


	public function setAlbum($album) {
		$this->_album = $album;
		return $this;
	}


	public function tagSlideshow($album, $preferences = null) {
		if (!$album)
			return '';

		return $this
			->setAlbum($album)
			->setPreferences($preferences)
			->renderSlideShowScripts()
			->renderAlbumMedias();
	}


	public function renderSlideShowScripts() {
		if (!$this->_album)
			return $this;

		return $this->renderSlideShowScriptsOn(
																					 sprintf('div.slideshow-%d .medias',
																									 $this->_album->getId()),
																					 array('width' => $this->_preferences['op_largeur_img'],
																								 'height' => $this->_preferences['op_hauteur_boite']));
	}


	/**
	 * @param Class_Album $album
	 * @return ZendAfi_View_Helper_Accueil_BibNumerique
	 */
	public function renderSlideShowScriptsOn($selector, $options=null) {
		$cycle_options = array('pause' => 1, 
													 'fx' => 'fade');
		if (array_isset('op_transition', $this->_preferences) 
				&& in_array($this->_preferences['op_transition'], array('fade', 'shuffle', 'scrollHorz')))
			$cycle_options['fx'] = $this->_preferences['op_transition'];

		if (array_isset('op_timeout', $this->_preferences))
			$cycle_options['timeout'] = 1000 * $this->_preferences['op_timeout'];

		if ($options)
			$cycle_options = array_merge($cycle_options, $options);

		Class_ScriptLoader::getInstance()
			->addScript(URL_JAVA . 'diaporama/jquery.cycle.all.min')
			->addJQueryReady(sprintf(
										 '$(\'%s\').cycle(%s);',
										 $selector,
										 json_encode($cycle_options)))
			->loadPrettyPhoto();
		return $this;
	}



	public function renderAlbumMedias() {
		if (!$this->_album)
			return '';

		$medias = $this->_getMedias($this->_album);

		$html = sprintf('<div class="slideshow slideshow-%d">'.
										  '<h2></h2>'.
										    '<div class="medias">%s</div>'.
										  '<p></p>'.
										'</div>', 
										$this->_album->getId(),
										$this->_renderAllMedias($medias));
		return $html;
	}

	/**
	 * @param array $medias
	 * @return string
	 */
	protected function _renderAllMedias($medias) {
		$html = '';
		foreach ($medias as $media) 
			$html .= $this->_renderMedia($media);
		return $html;
	}


	/**
	 * @param Class_AlbumRessource $media
	 * @return string
	 */
	protected function _renderMedia($media) {
		$content = $this->view->tagImg($this->view->url(array('controller' => 'bib-numerique',
																													'action' => 'thumbnail',
																													'width' => $media->getAlbum()->getThumbnailWidth(),
																													'id' => $media->getId()),
																										null,
																										true), 
																	 array('style' => sprintf('width: %spx',  
																														$this->_preferences['op_largeur_img']),
																				 'title' => htmlspecialchars($media->getTitre()),
																				 'alt' => htmlspecialchars($media->getDescription())));
		
		$datas = array('titre' => $media->getTitre(), 
									 'content' => $content, 
									 'description' => $media->getDescription());
		if ($media->hasLinkTo()) {
			foreach ($datas as $idx => $data)
				$datas[$idx] = sprintf('<a href="%s">%s</a>', 
															 $media->getLinkTo(),
															 $data);
		} else {
			$datas['content'] = sprintf('<a href="%s" rel="prettyphoto[%s]" title="%s">%s</a>',
																	$this->view->url(array('module' => 'opac',
																												 'controller' => 'bib-numerique',
																												 'action' => 'get-resource',
																												 'id' => $media->getId())),
																	htmlentities($media->getAlbum()->getTitre()),
																	$media->getTitre(),
																	$content);
		}

		

		$content = sprintf('<h2>%s</h2>'.
											   '%s'.
											 '<p>%s</p>',
											 $datas['titre'],
											 $datas['content'], 
											 $datas['description']);

		return sprintf('<div>%s</div>', $content);
	}



	public function _getMedias($album) {
		if (null === $album)
			return array();

		$medias = $album->getImages();

		if (array_key_exists('order', $this->_preferences)
				&& Class_Systeme_ModulesAccueil_BibliothequeNumerique::ORDER_RANDOM == $this->_preferences['order']) {
			shuffle($medias);
		}

		if (0 < ($limit = (int)$this->_preferences['nb_aff']))
			$medias = array_slice($medias, 0, $limit);

		return $medias;
	}
}
?>