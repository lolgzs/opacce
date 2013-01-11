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
	protected $_channel;
	protected $_doc;

	public function album_rssFeedVisitor($album) {
		$this->_doc = new DOMDocument('1.0', 'utf-8');
		$root = $this->appendTag($this->_doc, 'rss');
		$root->setAttribute('version', '2.0');
		$root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		$root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:itunes',  'http://www.itunes.com/dtds/podcast-1.0.dtd');
		$this->_channel = $this->appendTag($root, 'channel');

		$album->acceptVisitor($this);

		return $this->_doc->saveXML();
	}


	public function appendTag($parent, $name, $content = '') {
		$parent->appendChild($element = $this->_doc->createElement($name, $content));
		return $element;
	}


	public function appendTags($parent, $tags) {
		foreach ($tags as $name => $content) 
			$this->appendTag($parent, $name, $content);
	}


	public function visitAlbum($album) {		

		$this->appendTags($this->_channel,
											['title' 	=> $album->getTitre(),
											 'link'  	=> $this->view->absoluteUrl($album->getPermalink()),
											 'pubDate'  => gmdate('r', strtotime($album->getDateMaj()))]);

		$itunes_image = $this->_channel->appendChild($this->_doc->createElement('itunes:image'));
		$itunes_image->setAttribute('href', $this->view->absoluteUrl($album->getPermalinkThumbnail()));

		$description = $this->_channel->appendChild($this->_doc->createElement('description'));
		$description->appendChild($this->_doc->createCDATASection($album->getDescription()));

		$summary = $this->_channel->appendChild($this->_doc->createElement('itunes:summary'));
		$summary->appendChild($this->_doc->createCDATASection($album->getDescription()));
	}


	public function visitRessource($ressource, $index) {
		$media_url = $this->view->absoluteUrl($ressource->getOriginalUrl());

		$this->appendTags($item = $this->appendTag($this->_channel, 'item'),
											['title' => $ressource->getTitre(),
											 'link' => $media_url,
											 'itunes:order' => $ressource->getOrdre(),
											 'guid' => $media_url]);
		$enclosure = $item->appendChild($this->_doc->createElement('enclosure'));
		$enclosure->setAttribute('url', $media_url);
		$enclosure->setAttribute('type', Class_File_Mime::getType($ressource->getFileExtension()));

		$itunes_image = $item->appendChild($this->_doc->createElement('itunes:image'));
		$itunes_image->setAttribute('href', $this->view->absoluteUrl($ressource->getThumbnailUrl()));

		$description = $item->appendChild($this->_doc->createElement('description'));
		$description->appendChild($this->_doc->createCDATASection($ressource->getDescription()));

	}
}

?>