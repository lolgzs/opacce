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


	/**
	 * Génére le JSON pour le livre numérique
	 */
	public function albumAction() {
		$album = Class_Album::getLoader()->find((int)$this->_getParam('id'));

		$thumbnail_params = array($this->albumPageParams($album, 'right'),
															$this->albumPageParams($album, 'left'));

		$thumbnail_urls = array($this->albumPageThumbnailUrl($album, 'right'),
														$this->albumPageThumbnailUrl($album, 'left'));


		$json = new StdClass();
		$json->album->id = $album->getId();
		$json->album->titre = $album->getTitre();
		$json->album->download_url = '';
		if ($album->hasPdf())
			$json->album->download_url = $this->view->url(array('action' => 'download_album',
																													'id' => $album->getId().'.pdf'));
		$json->album->description = $album->getDescription();
		$json->album->ressources = array();
		$json->album->width = $album->getThumbnailWidth();
		$json->album->height = $album->getThumbnailHeight();

		$page = 0;
		foreach($album->getRessources() as $ressource) {
			$right_or_left = ($page++ % 2);
			$json_ressource = new StdClass();
			$json_ressource->id = $ressource->getId();
			$json_ressource->foliono = $ressource->getFolio();
			$json_ressource->titre = $ressource->getTitre();
			$json_ressource->link_to = $ressource->getLinkTo();
			$json_ressource->description = $ressource->getDescription();

			$params = $thumbnail_params[$right_or_left];
			$params['id'] = $ressource->getId();

			if ($ressource->isThumbnailExistsForParams($params))
				$json_ressource->thumbnail = $ressource->getThumbnailUrlForParams($params);
			else
				$json_ressource->thumbnail = $thumbnail_urls[$right_or_left].'/id/'.$ressource->getId();

			$json_ressource->navigator_thumbnail = $ressource->getThumbnailUrl();
			$json_ressource->original = $ressource->getOriginalUrl();
			$json->album->ressources []= $json_ressource;
		}


		$this->getHelper('ViewRenderer')->setNoRender();
		$this->_response->setHeader('Content-Type', 'application/json');
		$this->_response->setBody(json_encode($json));
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
		$response->setHeader('Content-Type', $mimeType . '; name="' . $parts['filename'] . '"', true);
		$response->setHeader('Content-Transfer-Encoding', 'binary', true);
		$response->setHeader('Content-Length', $fileInfos['size'], true);
		$response->setHeader('Expires', '0');
		$response->setHeader('Cache-Control', 'no-cache, must-revalidate');
		$response->setHeader('Pragma', 'no-cache');

		if ($as_attachment)
			$response->setHeader('Content-Disposition', 'attachment; filename="' . $parts['filename'] . '"', true);

		$response->sendHeaders();


		$file = fopen($filepath, 'rb');
		$file_data = fread($file, $fileInfos['size']);
		fclose($file);

		return $file_data;
	}

}