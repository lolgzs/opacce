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

class ZendAfi_View_Helper_Album_OsmPlayer extends Zend_View_Helper_HtmlElement {
	public function album_osmPlayer($album) {
		$div_id = 'osmplayer'.$album->getId();

		$xspf_url = $this->view->url(['module' => 'opac', 
																	'controller' => 'bib-numerique',
																	'action' => 'album-xspf-playlist', 
																	'id' => $album->getId()]);

		$podcast_url = $this->view->url(['module' => 'opac', 
																		 'controller' => 'bib-numerique',
																		 'action' => 'album-rss-feed', 
																		 'id' => $album->getId()]);

		$this->view->osmPlayer('#'.$div_id,
													 ['playlist' => $xspf_url.'.xml',
														'height' => '500px']);
		return '<ul>'
			.'<li>'.$this->view->tagAnchor($this->view->absoluteUrl($xspf_url.'.xspf'), 
																		 $this->view->_('Téléchargez la playlist (VLC, WinAmp)'),
																		 ['data-ajax' => 'false']).'</li>'
			.'<li>'.$this->view->tagAnchor($this->view->absoluteUrl($podcast_url.'.xml'), 
																		 $this->view->_('Podcastez l\'album (iTunes, Lecteur RSS)'),
																		 ['data-ajax' => 'false']).'</li>'
			.'</ul>'
			.'<div id="'.$div_id.'"></div>';
	}
}

?>