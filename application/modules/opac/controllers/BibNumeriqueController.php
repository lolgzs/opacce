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
class BibNumeriqueController extends Zend_Controller_Action {
	public function viewAlbumAction() {
		if (null === ($album = Class_Album::getLoader()->find((int)$this->_getParam('id')))) {
			$this->_redirect('opac/');
			return;
		}

		$this->view->album = $album;
	}


	/**
	 * Génére le JSON pour le livre numérique
	 */
	public function albumAction() {
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));

		$this->getHelper('ViewRenderer')->setNoRender();
		$this->_response->setBody($this->view->album_JsonVisitor($album));
	}


	public function albumXspfPlaylistAction() {
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));
		$playlist = $this->view->album_XspfPlaylistVisitor($album);


		$this->getHelper('ViewRenderer')->setNoRender();
		$response = $this->_response;
		$response->clearAllHeaders();
		$response->setHeader('Content-Type', 'application/xspf+xml; name="' . $album->getId(). '.xspf"', true);
		$response->setHeader('Content-Disposition', 'attachment; filename="' . $album->getId(). '.xspf"', true);
		$response->setHeader('Content-Transfer-Encoding', 'base64', true);
		$response->setHeader('Content-Length', count($playlist), true);
		$response->setHeader('Expires', '0');
		$response->setHeader('Cache-Control', 'no-cache, must-revalidate');
		$response->setHeader('Pragma', 'no-cache');
		$response->setBody($playlist);
	}


	public function albumRssFeedAction() {
		$this->getHelper('ViewRenderer')->setNoRender();
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));
		$rss = $this->view->album_RssFeedVisitor($album);
		$this->_response->setBody($rss);
	}


	public function downloadalbumAction() {
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));
		echo $this->_renderFile($album->getBasePath().$album->getPdf(), true);
	}


	public function bookletAction() {
		$id = $this->_getParam('id');
		$container_id = sprintf('booklet_%d', $id);
		Class_ScriptLoader::getInstance()
			->addAmberPackage('AFI')
			->addAmberReady(sprintf("smalltalk.BibNumAlbum._load_in_scriptsRoot_('%s.json', '%s', '%s')",
															$this->view->url(array('action' => 'album', 'id' => $id)),
															'#'.$container_id,
															AMBERURL."afi/souvigny/"))
			->loadAmber();
		$this->view->container_id = $container_id;
	}


	public function viewCategorieAction() {
		if (null === ($categorie = Class_AlbumCategorie::getLoader()->find((int)$this->_getParam('id')))) {
			$this->_redirect('opac/');
			return;
		}

		$this->view->categorie = $categorie;
		$this->view->albums = $categorie->getAlbums();
		$this->view->subCategories = $categorie->getSousCategories();
	}


	public function getResourceAction() {
		echo $this->_getResource();
	}


	public function downloadResourceAction() {
		echo $this->_getResource(true);
	}


	public function thumbnailAction() {
		if (null === ($ressource = Class_AlbumRessource::getLoader()->find((int)$this->_getParam('id'))))
			exit;
		// renvoi direct du contenu du fichier
		$this->_helper->getHelper('ViewRenderer')->setNoRender(true);
		
		$keys = array('width', 'crop_top', 'crop_right', 'crop_bottom', 'crop_left', 'id');
		$params = $this->_request->getParams();
		$thumbnail_params = array();
		foreach ($keys as $key) {
			if (array_key_exists($key, $params))
				$thumbnail_params[$key] = (int)$params[$key];
		}

		echo $this->_renderFile($ressource->getThumbnailFilePath($thumbnail_params));
	}

	/*
	 * Interpretation des permaliens type http://localhost/afi-opac3/bib-numerique/notice/ido/D09030001/folio/1R4
	 * sur les Albums + folios
	 */
	public function noticeAction() {
		if (!$album = $this->findAlbumByIdOrIdOrigine()) {
			$this->_redirect('opac/index');
			return;
		}

		$exemplaires = Class_Exemplaire::getLoader()->findAllBy(array('id_origine' => $album->getId()));
		$exemplaire = null;
		foreach($exemplaires as $ex) {
			if ($ex->hasNotice() && $ex->getNotice()->isLivreNumerique())
				$exemplaire = $ex;
		}

		if (!$exemplaire) {
			$this->_redirect('opac/index');
			return;
		}

		$url_notice = 'opac/recherche/viewnotice/id/'.$exemplaire->getIdNotice();
		if ($folio = $this->_getParam('folio'))
			$url_notice .= '#/page/'.$album->indexOfRessourceByFolio($folio);

		$this->_redirect($url_notice);
	}


	public function noticeThumbnailAction() {
		$this->_helper->getHelper('ViewRenderer')->setNoRender(true);
		if (!$album = $this->findAlbumByIdOrIdOrigine())
			return;

		echo $this->_renderFile($album->getThumbnailPath() ,false);
	}


	protected function findAlbumByIdOrIdOrigine() {
		if ($id_origine = $this->_getParam('ido')) 
			return Class_Album::getLoader()->findFirstBy(array('id_origine' => $this->_getParam('ido')));
		
		return Class_Album::getLoader()->find((int)$this->_getParam('id'));
	}


	protected function _getResource($as_attachment = false) {
		if (null === ($resource = Class_AlbumRessource::getLoader()->find((int)$this->_getParam('id')))) {
			// ressource inexistante, on ne fait rien
			exit;
		}

		if ('' == $resource->getFichier()) {
			exit;
		}

		echo $this->_renderFile($resource->getOriginalPath(), $as_attachment);
	}


	protected function _renderFile($filepath, $as_attachment = false) {
		// renvoi direct du contenu du fichier
		$this->_helper->getHelper('ViewRenderer')->setNoRender(true);
		
		// on va devoir modifier les entetes HTTP
		$response = Zend_Controller_Front::getInstance()->getResponse();
		$response->canSendHeaders(true);

		// extension
		$ext = explode('.', $filepath);
		$ext = end($ext);

		// puis son type mime
		$mimeType = Class_File_Mime::getType($ext);
		$fileInfos = stat($filepath);
		$parts = pathinfo($filepath);

		$response->clearAllHeaders();
		$response->setHeader('Content-Type', $mimeType . '; name="' . $parts['basename'] . '"', true);
		$response->setHeader('Content-Transfer-Encoding', 'binary', true);
		$response->setHeader('Content-Length', $fileInfos['size'], true);
		$response->setHeader('Expires', '0');
		$response->setHeader('Cache-Control', 'no-cache, must-revalidate');
		$response->setHeader('Pragma', 'no-cache');

		if ($as_attachment)
			$response->setHeader('Content-Disposition', 'attachment; filename="' . $parts['basename'] . '"', true);

		$response->sendHeaders();


		$file = fopen($filepath, 'rb');
		$file_data = fread($file, $fileInfos['size']);
		fclose($file);

		return $file_data;
	}

}