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


	public static function getTransitionDefinitions() {
		return array('fade' => 'Transparence',
								 'shuffle' => 'Mélange',
								 'scrollHorz' => 'Défilement horizontal',
								 'scrollVert' => 'Défilement vertical',
								 'curtainX' => 'Rideau horizontal',
								 'curtainY' => 'Rideau vertical');
	}


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

		return $this->renderSlideShowScriptsOn(Class_ScriptLoader::getInstance(),
																					 sprintf('div.slideshow-%d .medias',
																									 $this->_album->getId()),
																					 array('width' => $this->_preferences['op_largeur_img'],
																								 'height' => $this->_preferences['op_hauteur_boite'],
																								 'fit' => true,
																								 'aspect' => true));
	}


	/**
	 * @param Class_Album $album
	 * @return ZendAfi_View_Helper_Accueil_BibNumerique
	 */
	public function renderSlideShowScriptsOn($script_loader, $selector, $options=null) {
		$cycle_options = array('pause' => 1, 
													 'fx' => 'fade',
													 'animIn' => array('opacity' =>  1),
													 'animOut' => array('opacity' =>  0),
													 );
		if (array_isset('op_transition', $this->_preferences)
				&& in_array($this->_preferences['op_transition'], array_keys(self::getTransitionDefinitions())))
			$cycle_options['fx'] = $this->_preferences['op_transition'];

		if (array_isset('op_timeout', $this->_preferences))
			$cycle_options['timeout'] = 1000 * $this->_preferences['op_timeout'];

		if ($options)
			$cycle_options = array_merge($cycle_options, $options);

		$script_loader
			->addScript(URL_JAVA . 'diaporama/jquery.cycle.all.min')
			->addJQueryReady(sprintf(
															 '$(\'%s\').cycle(%s);
                                var container = $(\'%1$s\').parent();
                                container.addClass(\'slideshow\');
										            container.prepend(\'<div class="controls"><a href="#"></a><a href="#"></a></div>\');
                                container.find(\'.controls a:first-child\').click( 
                                                                  function(event){ 
                                                                         event.preventDefault(); 
                                                                         $(\'%1$s\').cycle(\'prev\') } );
                                container.find(\'.controls a + a\').click(
                                                                  function(event){
                                                                         event.preventDefault(); 
                                                                         $(\'%1$s\').cycle(\'next\') } );
                                container.find(\'.controls a\').css(\'top\', (container.parent().height()/3)+\'px\')',
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
		$params = array('width' => (int)$media->getAlbum()->getThumbnailWidth(),
										'id' => (int)$media->getId());

		if ($media->isThumbnailExistsForParams($params))
			$url_media = $media->getThumbnailUrlForParams($params);
		else
			$url_media = $this->view->url(array('controller' => 'bib-numerique',
																					'action' => 'thumbnail',
																					'width' => $media->getAlbum()->getThumbnailWidth(),
																					'id' => $media->getId()),
																		null,
																		true);

		$content = $this->view->tagImg($url_media, 
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