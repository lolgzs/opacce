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

class ZendAfi_View_Helper_Album_XspfPlaylistVisitor extends  Zend_View_Helper_Abstract {
	protected $_builder;
	protected $_tracks;

	public function album_xspfPlaylistVisitor($album) {
		$this->_builder = new Class_Xml_Builder();
		$this->_tracks = [];

		$album->acceptVisitor($this);

		return '<?xml version="1.0" encoding="UTF-8"?>'."\n"
			.$this->_builder->playlist(['version' => '1', 'xmlns' => 'http://xspf.org/ns/0/'],
																 $this->_builder->trackList(implode($this->_tracks)));
	}


	public function visitAlbum($album) {		
	}

	public function visitRessource($ressource, $index) {
		$this->_tracks []= $this->_builder->track(
			 $this->_builder->title($ressource->getTitre())
			 .$this->_builder->image($this->absoluteUrl($ressource->getThumbnailUrl()))
			 .$this->_builder->location($this->absoluteUrl($ressource->getOriginalUrl()))
		);
	}


	public function absoluteUrl($url) {
		if (preg_match('/http[s]?:\/\//', $url))
			return $url;
		return 'http://' . $_SERVER['SERVER_NAME'] . $url;
	}
}


?>