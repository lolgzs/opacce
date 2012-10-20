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
class ZendAfi_View_Helper_Album_JsonVisitor extends Zend_View_Helper_Json {
	protected $json;

	public function albumPageParams($album, $left_or_right) {
		return array('width' => (int)$album->getThumbnailWidth(),
								 'crop_top' => (int)$album->_get('thumbnail_'.$left_or_right.'_page_crop_top'),
								 'crop_right' => (int)$album->_get('thumbnail_'.$left_or_right.'_page_crop_right'),
								 'crop_bottom' => (int)$album->_get('thumbnail_'.$left_or_right.'_page_crop_bottom'),
								 'crop_left' => (int)$album->_get('thumbnail_'.$left_or_right.'_page_crop_left'));
	}


	public function albumPageThumbnailUrl($album, $left_or_right, $width = null) {
		return $this->view->url(array_merge(
																				array('controller' => 'bib-numerique',
																							'action' => 'thumbnail'),
																				$this->albumPageParams($album, $left_or_right)),
														null,
														true);
	}


	public function album_jsonVisitor($album) {
		$this->json = new StdClass();
		$this->json->album = new StdClass();
		$this->json->album->ressources = array();

		$album->acceptVisitor($this);

		return $this->json($this->json);
	}


	public function visitAlbum($album) {
		$this->thumbnail_params = [$this->albumPageParams($album, 'right'),
															 $this->albumPageParams($album, 'left')];

		$this->thumbnail_urls = [$this->albumPageThumbnailUrl($album, 'right'),
														 $this->albumPageThumbnailUrl($album, 'left')];

		$this->json->album->id = $album->getId();
		$this->json->album->titre = $album->getTitre();
		$this->json->album->description = $album->getDescription();
		$this->json->album->width = $album->getThumbnailWidth();
		$this->json->album->height = $album->getThumbnailHeight();

		$this->json->album->download_url = $album->hasPdf() 
			? $this->view->url(['action' => 'download_album', 'id' => $album->getId().'.pdf'])
			: '';
	}


	public function visitRessource($ressource, $index) {
			$right_or_left = ($index % 2);
			$json_ressource = new StdClass();
			$json_ressource->id = $ressource->getId();
			$json_ressource->foliono = $ressource->getFolio();
			$json_ressource->titre = $ressource->getTitre();
			$json_ressource->link_to = $ressource->getLinkTo();
			$json_ressource->description = $ressource->getDescription();

			$params = $this->thumbnail_params[$right_or_left];
			$params['id'] = (int)$ressource->getId();

			$json_ressource->thumbnail = $ressource->isThumbnailExistsForParams($params)
				? $ressource->getThumbnailUrlForParams($params)
				: $json_ressource->thumbnail = $this->thumbnail_urls[$right_or_left].'/id/'.$ressource->getId();

			$json_ressource->navigator_thumbnail = $ressource->getThumbnailUrl();
			$json_ressource->original = $ressource->getOriginalUrl();
			$json_ressource->download = $this->view->url(['controller' => 'bib-numerique',
																										'action' => 'download-resource',
																										'id' => $ressource->getId()], 
																									 null, 
																									 true);

			$this->json->album->ressources []= $json_ressource;
	}
}

?>