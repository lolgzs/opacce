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
class ZendAfi_View_Helper_Album_BookMonoJsonVisitor extends ZendAfi_View_Helper_Album_BookJsonVisitorAbstract {
	protected 
		$_thumbnail_base_url,
		$_page_params;


	public function getPlayer() {
		return 'BookMonoWidget';
	}


	public function visitAlbum($album) {
		$this->_page_params = [			
			'width' => (int)$album->getThumbnailWidth(),
			'crop_top' => (int)$album->_getThumbnailCropTop(),
			'crop_right' => (int)$album->_getThumbnailCropRight(),
			'crop_bottom' => (int)$album->_getThumbnailCropBottom(),
			'crop_left' => (int)$album->_getThumbnailCropLeft() 
		];

		$this->_thumbnail_base_url = $this->view->url(
			array_merge(
				[	'controller' => 'bib-numerique',
					'action' => 'thumbnail' ],
				$this->_page_params),
			null,
			true);
		
		return parent::visitAlbum($album);		
	}


	public function getThumbnailURLForRessource($ressource, $index) {
		return $ressource->isThumbnailExistsForParams($this->_page_params)
			? $ressource->getThumbnailUrlForParams($this->_page_params)
			: $this->_thumbnail_base_url.'/id/'.$ressource->getId();
	}
}

?>