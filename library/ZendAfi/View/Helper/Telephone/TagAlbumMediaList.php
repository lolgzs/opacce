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

class ZendAfi_View_Helper_Telephone_TagAlbumMediaList extends ZendAfi_View_Helper_TagAlbumMediaList {
		protected $_builder;

	public function tagAlbumMediaList($album) {
	  

			$ressources = $album->getRessources();

		$xspf_url = $this->view->url(['module' => 'telephone', 
																	'controller' => 'bib-numerique',
																	'action' => 'album-xspf-playlist', 
																	'id' => $album->getId()]);

		$podcast_url = $this->view->url(['module' => 'telephone', 
																		 'controller' => 'bib-numerique',
																		 'action' => 'Album-rss-feed', 
																		 'id' => $album->getId()]);
		$html='';
		foreach($ressources as $ressource) {
		$html .=  sprintf('<li data-role="list-divider"><a href=%s  data-ajax="false"><img src=%s><h3>%s</h3></a></li>', 
											$this->view->absoluteUrl($ressource->getOriginalUrl()),
											$this->view->absoluteurl($ressource->getThumbnailUrl()),
											$ressource->getTitre() ? $ressource->getTitre() : $ressource->getFichier());}

		$playlist = sprintf('<a href="%s" data-role="button" data-mini="true"  data-ajax="false" data-icon="list-alt">%s</a>',
										 $this->view->absoluteUrl($xspf_url.'.xspf'), 
												$this->view->_('Playlist'),
												['data-ajax' => 'false']);

		$rss = sprintf('<a href="%s"  data-role="button" data-mini="true"  data-icon="rss" data-ajax="false">%s</a>',
										 $this->view->absoluteUrl($podcast_url.'.xml'), 
										 $this->view->_('RSS'));
	  
		return '<ul data-role="listview">' . $html . '</ul><fieldset class="ui-grid-a"><div class="ui-block-a">' .$playlist.'</div><div class="ui-block-b">' .$rss . '</div></fieldset>'  ;

	}
}

?>