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
class AlbumRessourceLoader extends Storm_Model_Loader {
	/**
	 * @param Class_Album $album
	 * @return int
	 */
	public function getNextOrderFor($album) {
		$row = $this->getTable()->fetchRow(
			$this->getTable()->select()
									->from(
										$this->_table->info(Zend_Db_Table::NAME),
										array('order' => 'MAX(ordre)')
									)
									->where('id_album = ?', $album->getId())
		);

		return ($row->order + 1);
	}
}




class Class_AlbumRessource extends Storm_Model_Abstract {
	const BASE_PATH = 'media/';
	protected static $THUMBNAILS_BY_EXT = array('swf' => 'flash-logo.jpg',
																							'mov' => 'quicktime-logo.png',
																							'unknown' => 'earth-logo.jpg');
	protected static $_thumbnail_dir_checked = false;

	/** @var Class_MultiUpload */
	protected $_multiUploadHandler;

	/** @var Class_Upload */
	protected $_uploadHandler;

	/** @var Class_Folder_Manager */
	protected $_folderManager;

	/** @var Imagick */
	protected $_image;


	protected $_table_name		= 'album_ressources';
	protected $_primary_key		= 'id';
	protected $_loader_class	= 'AlbumRessourceLoader';

	protected $_belongs_to = array('album' => array('model' => 'Class_Album',
																									'referenced_in' => 'id_album'));

	protected $_default_attribute_values = array(
		'fichier' => '',
		'folio' => null,
		'titre' => '',
		'description' => '',
		'ordre' => 0,
		'link_to' => '',
		'matiere' => ''
	);

	/**
	 * @return AlbumRessourceLoader
	 */
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public static function sortByFileName($r1, $r2) {
		return strcmp($r1->getFichierWithoutId(), $r2->getFichierWithoutId());
	}


	/**
	 * Les fichiers sont formattés ID_NOM_DU_FICHIER.EXT
	 * Retourne NOM_DU_FICHIER.EXT
	 * @return string
	 */
	public function getFichierWithoutId() {
		$fichier = $fichier = $this->getFichier();
		if (false===strpos($fichier, '_'))
				return $fichier;

		$parts = explode('_', $fichier);
		if ((int)$this->getId() === (int)$parts[0])
			array_shift($parts);
		return implode('_', $parts);
	}


	/**
	 * @return array
	 */
	public function getPermalink() {
		if (!$this->hasAlbum())
			return array();

		$permalink = $this->getAlbum()->getPermalink();
		$permalink['folio'] = $this->getFolio();
		return $permalink;
	}	


	/**
	 * @param Zend_Controller_Request_Http $request
	 * @return array
	 */
	public function initializeWith($request) {
		$this->save();

		$upload = $this->getMultiUploadHandler($request);

		if (!$upload->handleUpload($this->getOriginalsPath(), $this->getId())) {
			$this->delete();
			return array('success' => 'false', 'error' => $upload->getError());
		}

		$this->setFichier($upload->getSavedFileName());

		if (!$this->createThumbnail()) {
			$this->delete();
			return array('success' => 'false', 'error' => reset($this->getErrors()));
		}

		$this->save();

		return array('success' => 'true');
	}


	/**
	 * @return bool
	 */
	public function receiveFile() {
		// fichier non requis
		if (!array_isset('fichier', $_FILES) or (0 == $_FILES['fichier']['size'])) {
			return true;
		}
		
		$upload = $this->getUploadHandler('fichier');

		if (!$upload->receive()) {
			$this->addError($upload->getError());
			return false;
		}

		$fileName = $upload->getSavedFileName();

		// store old file and thumb for future deletion
		$oldFileName	= $this->getFichier();
		$oldOriginal	= $this->getOriginalPath();
		$oldThumbnail	= $this->getThumbnailPath();

		if ($fileName != $oldFileName) {
			$this->setFichier($fileName)->save();
			unlink($oldOriginal);
		}

		unlink($oldThumbnail);

		return $this->createThumbnail();
	}


	/**
	 * @return bool
	 */
	public function createThumbnail() {
		if (!$this->isImage()) {
			return true;
		}

		if (!$this->getFolderManager()->ensure($this->getThumbnailsPath())) {
			$this->addError('Répertoire des vignettes non éditable');
			return false;
		}

		try {
			$image = $this->getImage();
			$image->thumbnailImage(160, 0);

			if (!$image->writeImage($this->getThumbnailPath())) {
				$this->addError('Erreur lors de l\'enregistrement de la vignette');
				return false;
			}

			return true;

		} catch (Exception $e) {
			$this->addError('Erreur lors de la création de la vignette '
											. (string)$e->getMessage());
			return false;
		}
	}


	/**
	 * @return Imagick
	 */
	public function getImage() {
		if (!isset($this->_image)) {
			try {
				$this->_image = new Imagick($this->getOriginalPath());
			} catch (Exception $e) {
				$this->_image = new Imagick();
				$this->_image->newPseudoImage(50, 50, "canvas:black");
				$this->_image->setImageFormat('jpg');
			}
		}

		return $this->_image;
	}


	/**
	 * @param $image Imagick
	 * @return Class_AlbumRessource
	 */
	public function setImage($image) {
		$this->_image = $image;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->getImage()->getImageWidth();
	}


	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->getImage()->getImageHeight();
	}


	/**
	 * @codeCoverageIgnore
	 * @param Zend_Controller_Request_Http $request
	 * @return Class_MultiUpload
	 */
	public function getMultiUploadHandler($request) {
		if (null === $this->_multiUploadHandler) {
			$this->_multiUploadHandler = Class_MultiUpload::newInstanceWith($request);
		}

		return $this->_multiUploadHandler;
	}


	/**
	 * @category testing
	 * @param Class_MultiUpload $handler
	 */
	public function setMultiUploadHandler($handler) {
		$this->_multiUploadHandler = $handler;
		return $this;
	}


	/**
	 * @codeCoverageIgnore
	 * @param string $name
	 * @return Class_Upload
	 */
	public function getUploadHandler($name) {
		if (null === $this->_uploadHandler) {
			$this->_uploadHandler = Class_Upload::newInstanceFor($name)
				->setBaseName($this->getId())
				->setBasePath($this->getOriginalsPath());
		}

		return $this->_uploadHandler;
	}


	/**
	 * @category testing
	 * @param Class_Upload $handler
	 */
	public function setUploadHandler($handler) {
		$this->_uploadHandler = $handler;
		return $this;
	}


	/**
	 * @return Class_Folder_Manager
	 */
	public function getFolderManager() {
		if (null === $this->_folderManager) {
			$this->_folderManager = new Class_Folder_Manager();
		}

		return $this->_folderManager;
	}


	/**
	 * @category testing
	 * @param Class_Folder_Manager $folderManager
	 * @return Class_AlbumRessource
	 */
	public function setFolderManager($folderManager) {
		$this->_folderManager = $folderManager;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getNextOrder() {
		return $this->getLoader()->getNextOrderFor($this->getAlbum());
	}


	/**
	 * @param string $prefix
	 * @return string
	 */
	public function getLocatedFile($prefix) {
		return $prefix . $this->getFichier();
	}


	/**
	 * @return string
	 */
	public function getThumbnailsPath() {
		return $this->getAlbum()->getThumbnailsPath() . self::BASE_PATH;
	}


	public static function checkThumbnailDirExists() {
		if (self::$_thumbnail_dir_checked) return;

		self::$_thumbnail_dir_checked = true;

		if (!file_exists(USERFILESPATH.'/temp'))
			mkdir(USERFILESPATH.'/temp');

		return;
	}


	public function getThumbnailSubPathForParams($params) {
		self::checkThumbnailDirExists();

		return '/temp/'.md5($this->getOriginalPath().serialize($params)).'.'.$this->getFileExtension();
	}


	public function getThumbnailFilePathForParams($params) {
		return USERFILESPATH.$this->getThumbnailSubPathForParams($params);
	}


	public function getThumbnailUrlForParams($params) {
		return USERFILESURL.$this->getThumbnailSubPathForParams($params);
	}


	public function isThumbnailExistsForParams($params) {
		return file_exists($this->getThumbnailFilePathForParams($params));
	}


	/**
	 * @params array resize / crop image parameters
	 * ex:
	 * array('width' => 100,
	 *			 'height' => 120,
	 *			 'crop_left' => 20,
	 *			 'crop_right' => 10,
	 *			 'crop_bottom' => 5,
	 *			 'crop_top' => 5);
	 * @return string
	 */
	public function getThumbnailFilePath($params) {
		$filepath = $this->getThumbnailFilePathForParams($params);

		if (!$this->isThumbnailExistsForParams($params))
			$this->_resizeAndCropThumbnailTo($filepath, $params);

		return $filepath;
	}


	/**
	 * @params string $destination_file : file path to write the image to
	 * @params array $params resize / crop image parameters
	 * @return AlbumRessource
	 */
	public function _resizeAndCropThumbnailTo($filepath, $params) {
			$image = $this->getImage();
			$resize_params = array_merge(array('width' => $image->getImageWidth(),
																				 'height' => 0,
																				 'crop_left' => 0,
																				 'crop_right' => 0,
																				 'crop_bottom' => 0,
																				 'crop_top' => 0), $params);


			$image->cropImage($image->getImageWidth() - $resize_params['crop_left'] - $resize_params['crop_right'], 
												$image->getImageHeight() - $resize_params['crop_top'] - $resize_params['crop_bottom'], 
												$resize_params['crop_left'], 
												$resize_params['crop_top']);

			$image->resizeImage($resize_params['width'], 
													$resize_params['height'], 
													Imagick::FILTER_LANCZOS, 
													1);

			$image->writeImage($filepath);
			return $this;
	}



	/**
	 * @return string
	 */
	public function getThumbnailPath() {
		return $this->getLocatedFile($this->getThumbnailsPath());
	}


	/**
	 * @return string
	 */
	public function getThumbnailsUrl() {
		return $this->getAlbum()->getThumbnailsUrl() . self::BASE_PATH;
	}


	/**
	 * @return string
	 */
	public function getThumbnailUrl() {
		if ($this->isImage())
			return $this->getLocatedFile($this->getThumbnailsUrl());
		return $this->_getDefaultThumbnailUrl();
	}


	/**
	 * @return string
	 */
	public function _getDefaultThumbnailUrl() {
		$extension = $this->getFileExtension();
		if (array_key_exists($extension, self::$THUMBNAILS_BY_EXT))
			return URL_SHARED_IMG.'/'.self::$THUMBNAILS_BY_EXT[$extension];
		return URL_SHARED_IMG.'/'.self::$THUMBNAILS_BY_EXT['unknown'];
	}


	/**
	 * @return string
	 */
	public function getOriginalsPath() {
		return $this->getAlbum()->getOriginalsPath() . self::BASE_PATH;
	}


	/**
	 * @return string
	 */
	public function getOriginalPath() {
		return $this->getLocatedFile($this->getOriginalsPath());
	}


	/**
	 * @return string
	 */
	public function getOriginalsUrl() {
		return $this->getAlbum()->getOriginalsUrl() . self::BASE_PATH;
	}


	/**
	 * @return string
	 */
	public function getOriginalUrl() {
		return $this->getLocatedFile($this->getOriginalsUrl());
	}


	public function beforeDelete() {
		parent::beforeDelete();
		$this->deleteFiles();
	}


	public function beforeSave() {
		parent::beforeSave();

		if ($this->isNew()) {
			$this->setOrdre($this->getNextOrder());
		}

		if ($this->hasAlbum())
			$this->getAlbum()->updateDateMaj();
	}


	public function deleteFiles() {
		if ('' != $this->getFichier()) {
			$this->unlink($this->getOriginalPath());
			$this->unlink($this->getThumbnailPath());
		}
	}


	public function unlink($filename) {
		if (file_exists($filename))
			unlink($filename);
	}


	/**
	 * @return bool
	 */ 	
	public function isImage() {
		return $this->isFileExtensionIn(array('png', 'jpg', 'jpeg', 'gif'));
	}


	/**
	 * @return bool
	 */ 	
	public function isFlash() {
		return $this->isFileExtensionIn(array('swf'));
	}


	/**
	 * @return bool
	 */ 	
	public function isVideo() {
		return $this->isFileExtensionIn(array('mov'));
	}


	/**
	 * @return bool
	 */ 	
	public function isFile() {
		return !($this->isImage() or $this->isVideo() or $this->isFlash());
	}


	/**
	 * @return string
	 */
	public function getFileExtension() {
		if ('' == $this->getFichier()) {
			return '';
		}

		$parts = explode('.', $this->getFichier());
		return strtolower(end($parts));
	}


	/** 
	 * @param array $extensions
	 * @return bool
	 */
	protected function isFileExtensionIn(array $extensions) {
		return in_array($this->getFileExtension(), $extensions);
	}


	/**
	 * @return string
	 */
	public function getFolioFromFilename() {
		if (!$fichier = $this->getFichier())
			return $this->getId();

		$matches = array();
		preg_match_all('/([0-9_]*B[0-9]*_)?(.+)\.\w+$/', $this->getFichier(), $matches);
		return $matches[2][0];
	}


	/**
	 * @return string
	 */
	public function getFolio() {
		if (null === $folio = parent::_get('folio'))
			$folio = $this->getFolioFromFilename();
		return $folio;
	}
}

?>