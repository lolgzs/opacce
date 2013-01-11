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
abstract class ZendAfi_View_Helper_Album_BookJsonVisitorAbstract extends Zend_View_Helper_Json {
	protected 
		$json,
		$_download_ressource_base_url;

	public function getJSON() {
		return $this->json;
	}


	public function visitAlbum($album) {
		$this->_download_ressource_base_url = $this->view->url(['controller' => 'bib-numerique',
																														'action' => 'download-resource'], 
																													 null, 
																													 true);
		$this->json = new StdClass();
		$this->json->album = new StdClass();
		$this->json->album->ressources = array();

		$this->json->album->id = $album->getId();
		$this->json->album->titre = $album->getTitre();
		$this->json->album->description = $album->getDescription();
		$this->json->album->width = $album->getThumbnailWidth();
		$this->json->album->height = $album->getThumbnailHeight();
		$this->json->album->player = $this->getPlayer();

		$this->json->album->download_url = $album->hasPdf() 
			? $this->view->url(['action' => 'download_album', 'id' => $album->getId().'.pdf'])
			: '';
	}


	public function visitRessource($ressource, $index) {
			$json_ressource = new StdClass();
			$json_ressource->id = $ressource->getId();
			$json_ressource->foliono = $ressource->getFolio();
			$json_ressource->titre = $ressource->getTitre();
			$json_ressource->link_to = $ressource->getLinkTo();
			$json_ressource->description = $ressource->getDescription();

			$json_ressource->thumbnail = $this->getThumbnailUrlForRessource($ressource, $index);

			$json_ressource->navigator_thumbnail = $ressource->getThumbnailUrl();
			$json_ressource->original = $ressource->getOriginalUrl();
			$json_ressource->download = $this->_download_ressource_base_url.'/id/'.$ressource->getId();

			$this->json->album->ressources []= $json_ressource;
	}


	abstract public function getThumbnailURLForRessource($ressource, $index);


	abstract public function getPlayer();
}

?>