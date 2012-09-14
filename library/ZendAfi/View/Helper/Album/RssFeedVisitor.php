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

class ZendAfi_View_Helper_Album_RssFeedVisitor extends  Zend_View_Helper_Abstract {
	protected $_data_rss;

	public function album_rssFeedVisitor($album) {
		$album->acceptVisitor($this);

		$feed = Zend_Feed::importArray($this->_data_rss, 'rss');
		return $feed->saveXML();
	}


	public function visitAlbum($album) {		
		$this->_data_rss = [
			'title' 	=> $album->getTitre(),
			'link'  	=> $this->view->absoluteUrl($album->getPermalink()),
			'charset'	  => 'utf-8',
			'description' => $album->getDescription(),
			'lastUpdate'  => strtotime($album->getDateMaj()),
			'entries' => []];
	}


	public function visitRessource($ressource, $index) {
		$this->_data_rss['entries'] []= [
			'title' => $ressource->getTitre(),
			'link' => $this->view->absoluteUrl($ressource->getOriginalUrl()),
			'description' => $ressource->getDescription()
		];
	}
}

?>