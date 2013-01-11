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
class ZendAfi_View_Helper_Album_BookJsonVisitor extends ZendAfi_View_Helper_Album_BookJsonVisitorAbstract {
	public function getPlayer() {
		return 'BookWidget';
	}


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


	public function visitAlbum($album) {
		$this->thumbnail_params = [$this->albumPageParams($album, 'right'),
															 $this->albumPageParams($album, 'left')];

		$this->thumbnail_urls = [$this->albumPageThumbnailUrl($album, 'right'),
														 $this->albumPageThumbnailUrl($album, 'left')];


		return parent::visitAlbum($album);
	}


	public function getThumbnailURLForRessource($ressource, $index) {
		$right_or_left = ($index % 2);

		$params = $this->thumbnail_params[$right_or_left];
		$params['id'] = (int)$ressource->getId();

		return $ressource->isThumbnailExistsForParams($params)
			? $ressource->getThumbnailUrlForParams($params)
			: $this->thumbnail_urls[$right_or_left].'/id/'.$ressource->getId();

	}

}

?>