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

class ZendAfi_View_Helper_Telephone_RenderAlbum extends ZendAfi_View_Helper_RenderAlbum {
	public function renderAlbum($album) {
		return $album 
			? sprintf('<div id="resnum">%s</div>', $this->renderAlbumHelper($album))
			: '';
	}


	public function renderAlbumHelper($album) {
		if ($album->isLivreNumerique()) {
			Class_ScriptLoader::getInstance()->loadBooklet($album->getId(), '#resnum');
			return '';
		}

		if ($album->isDiaporama() && $album->hasOnlyImages())
			return $this->view->tagSlideshow($album);


		if ($album->isGallica())
			return  $this->view->gallicaPlayer($album);

		if ($album->isArteVod())
			return $this->view->tagVideo($album);

		return $this->view->tagAlbumMediaList($album);
	}
}

?>