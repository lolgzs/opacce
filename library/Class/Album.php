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
/**
 * utlisation Storm :
 * - récupérer tous les Albums
 * $albums = Class_Album::getLoader()->findAll();
 *
 * - récupérer album avec l'id 2
 * $album = Class_Album::getLoader()->find(2);
 *
 * - tous les albums de la catégorie 3
 * $albums = Class_Album::getLoader()->findAllBy(array('cat_id' => 3));
 *
 * ou bien
 *
 * $albums = Class_AlbumCategorie::getLoader()->find(3)->getAlbums()
 *
 * - modifier le libelle d'un album et sauver:
 * $album->setLibelle('Mon album')->save();
 *
 * - modifier les attributs en masse
 * $post = array('libelle' => 'Mon album', 'commentaire' => 'bla bla');
 * $album->updateAttributes($post)->save()
 *
 * - Récupérer l'album id 4 et afficher son libelle
 * echo Class_Album::getLoader()->find(4)->getLibelle();
 *
 * - Créer une nouvelle catégorie, son album et sauvegarder.
 * Class_AlbumCategorie::getLoader()
 *    ->newInstance()
 *    ->setLibelle('Mes Favoris')
 *    ->addAlbum(Class_Album::getLoader()
 *                     ->newInstance()
 *										 ->setLibelle('Mes BD')
 *                     ->setCommentaire('bla bla'))
 *    ->save();
 *
 */

class AlbumLoader extends Storm_Model_Loader {
	public function getItemsOf($categoryId) {
		return $this->findAll('select id, titre, type_doc_id from album where cat_id=' . $categoryId);
	}
}


class Class_Album extends Storm_Model_Abstract {
	const BASE_PATH			= 'album/';
	const THUMBS_PATH		= 'thumbs/';
	const ORIGINAL_PATH	= 'big/';
	const ANNEE_MIN = 800;
	const DEFAULT_CODE_LANGUE = 'fre';
	const VIDEO_URL_FIELD = '856';
	const VIDEO_URL_TYPE = 'video';
	
	
	protected static $DEFAULT_THUMBNAIL_VALUES;

  protected $_table_name = 'album';
  protected $_table_primary = 'id';
	protected $_loader_class = 'AlbumLoader';
	protected $_belongs_to = array(
		'categorie' => array(
			'model'					=> 'Class_AlbumCategorie',
			'referenced_in' => 'cat_id'),

		'type_doc' => array('model' => 'Class_TypeDoc'),

		'langue' => array('model' => 'Class_CodifLangue',
											'referenced_in' => 'id_langue')
	);

	protected $_has_many = array(
		'ressources' => array(
			'model'				=> 'Class_AlbumRessource',
			'role'				=> 'album',
			'dependents'	=> 'delete',
			'order'				=> 'ordre'
		)
	);

	protected $_default_attribute_values = array('titre' => '',
																							 'sous_titre' => '',
																							 'editeur' => '',
																							 'fichier' => '',
																							 'pdf' => '',
																							 'auteur' => '',
																							 'date_maj' => '',
																							 'matiere' => '',
																							 'type_doc_id' => 0,
																							 'annee' => '',
																							 'id_langue' => self::DEFAULT_CODE_LANGUE,
																							 'id_origine' => '',
																							 'cfg_thumbnails' => '',
																							 'provenance' => '',
																							 'cote' => '',
																							 'notes' => '',
																							 'visible' => true);
	/** @var Class_Upload */
	protected $_uploadHandler;

	protected $path_flash;							// Path pour le modèle de settings
	public $arbre_array;


	/**
	 * @return Storm_Model_Loader
	 */
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @return array
	 */
	public static function thumbnailAttributeKeys() {
		return array_keys(self::getDefaultThumbnailValues());
	}


	/**
	 * @return array
	 */
	public static function getDefaultThumbnailValues() {
		if (!isset(self::$DEFAULT_THUMBNAIL_VALUES))
			self::$DEFAULT_THUMBNAIL_VALUES = array('thumbnail_width' => 400,
																							'thumbnail_left_page_crop_top' => 0,
																							'thumbnail_left_page_crop_right' => 0,
																							'thumbnail_left_page_crop_bottom' => 0,
																							'thumbnail_left_page_crop_left' => 0,
																							'thumbnail_right_page_crop_top' => 0,
																							'thumbnail_right_page_crop_right' => 0,
																							'thumbnail_right_page_crop_bottom' => 0,
																							'thumbnail_right_page_crop_left' => 0);
		return self::$DEFAULT_THUMBNAIL_VALUES;
	}


	/**
	 * @param Class_Matiere $matiere
	 * @return Class_Album
	 */	
	public function addMatiere($matiere) {
		$matieres = explode(';', $this->getMatiere());
		$matieres []= $matiere->getId();
		return $this->setMatiere(implode(';', array_unique(array_filter($matieres))));
	}


	/**
	 * @return int
	 */
	public function getRessourcesCount() {
		return $this->numberOfRessources();
	}


	/**
	 * @param string $param
	 * @return bool
	 */
	public function hasDefaultValue($param) {
		return array_key_exists($param, self::getDefaultThumbnailValues());
	}


	/**
	 * @return array
	 */
	public function getPermalink() {
		$permalink = array('module' => 'opac',
											 'controller' => 'bib-numerique',
											 'action' => 'notice');
		if ($this->hasIdOrigine())
			$permalink['ido'] = $this->getIdOrigine();
		else
			$permalink['id'] = $this->getId();

		return $permalink;
	}


	/**
	 * @return array
	 */
	public function getPermalinkThumbnail() {
		$permalink = $this->getPermalink();
		$permalink['action'] = 'notice-thumbnail';
		return $permalink;
	}
	

	/**
	 * @return int
	 */	
	public function indexOfRessourceByFolio($folio) {
		$pageno = 1;
		$ressources = $this->getRessources();
		foreach($ressources as $ressource) {
			$pageno ++;
			if ($ressource->getFolio() == $folio)
				return $pageno;
		}
		return 0;
	}


	/**
	 * @return int
	 */	
	public function getRessourceByFolio($folio) {
		$ressources = $this->getRessources();
		foreach($ressources as $ressource) {
			if ($ressource->getFolio() == $folio)
				return $ressource;
		}
		return null;
	}


	/**
	 * @param string $param
	 * @return mixed
	 */
	public function getDefaultValue($param) {
		$default_values = self::getDefaultThumbnailValues();
		return $default_values[$param];
	}


	/**
	 * Si un attribut n'est pas trouvé, regarde s'il n'est pas dans cfgThumbnails (paramètres des vignettes).
	 * @param string $field
	 * @return mixed
	 */
	public function _get($field) {
		if ($field !== 'cfg_thumbnails' and 
				array_key_exists($field, $this->getThumbnailAttributes()))
			return $this->getThumbnailAttribute($field);

		if ($this->isAttributeExists($field))
			return parent::_get($field);

		if ($this->hasDefaultValue($field))
			return $this->getDefaultValue($field);

		throw new Storm_Model_Exception(sprintf('Tried to call unknown method Class_Album::get%s',
																						$this->attributeNameToAccessor($field)));
	}


	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Class_Album
	 * @see _get
	 */
	public function _set($field, $value) {
		if (in_array($field, self::thumbnailAttributeKeys()))
			return $this->setThumbnailAttribute($field, $value);

		return parent::_set($field, $value);
	}


	public function getThumbnailAttribute($attribute) {
		$cfg_thumbnails = $this->getThumbnailAttributes();
		if (!array_key_exists($attribute, $cfg_thumbnails))
			return $this->getDefaultValue($attribute);
		return $cfg_thumbnails[$attribute];
	}


	public function  __construct() {
		$this->path_flash = PATH_FLASH . 'page_flip/';
	}


	/** @return string */
	public function getLibelle() {
		return $this->getTitre();
	}


	/** @return array */
	public function getHierarchy() {
		$hierarchy = array();
		$this->getCategorie()->getHierarchyOn($hierarchy);
		return $hierarchy;
	}


	/** @return boolean */
	public function isLivreNumerique() {
		return $this->getTypeDocId() == Class_TypeDoc::LIVRE_NUM;
	}


	/** @return boolean */
	public function isDiaporama() {
		return $this->getTypeDocId() == Class_TypeDoc::DIAPORAMA;
	}


	public function beLivreNumerique() {
		return $this->setTypeDocId(Class_TypeDoc::LIVRE_NUM);
	}


	public function beDiaporama() {
		return $this->setTypeDocId(Class_TypeDoc::DIAPORAMA);
	}


	public function beEPUB() {
		return $this->setTypeDocId(Class_TypeDoc::EPUB);
	}


	public function beOAI() {
		return $this->setTypeDocId(Class_TypeDoc::OAI);
	}


	public function isOAI() {
		return $this->getTypeDocId() == Class_TypeDoc::OAI;
	}


	public function beArteVOD() {
		return $this->setTypeDocId(Class_TypeDoc::ARTEVOD);
	}


	public function isArteVOD() {
		return $this->getTypeDocId() == Class_TypeDoc::ARTEVOD;
	}


	/**
	 * @param Zend_Controller_Request_Http $request
	 * @return array
	 */
	public function addFile($request) {
		return Class_AlbumRessource::getLoader()
			       ->newInstance()
 			       ->setAlbum($this)
			       ->initializeWith($request);
	}


	/**
	 * @param string $prefix
	 * @return string
	 */
	public function getBase($prefix) {
		return str_replace('//', '/', 
											 $prefix.'/'.self::BASE_PATH.'/'.$this->getId().'/');
	}


	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->getBase(USERFILESURL);
	}


	/**
	 * @return string
	 */
	public function getBasePath() {
		return $this->getBase(USERFILESPATH);
	}


	/**
	 * @return String
	 */
	public function getIdLangue() {
		if (!$id_langue = $this->_get('id_langue'))
			return self::DEFAULT_CODE_LANGUE;
		return $id_langue;
	}


	/**
	 * @return Class_CodifLangue
	 */
	public function getLangue() {
		return Class_CodifLangue::getLoader()->find($this->getIdLangue());
	}


	/**
	 * @param string $prefix
	 * @return string
	 */
	public function getThumbnails($prefix) {
		return $prefix . self::THUMBS_PATH;
	}


	/**
	 * @return string
	 */
	public function getThumbnailsPath() {
		return $this->getThumbnails($this->getBasePath());
	}


	/**
	 * @return string
	 */
	public function getThumbnailsUrl() {
		return $this->getThumbnails($this->getBaseUrl());
	}


	/**
	 * @param string $prefix
	 * @return string
	 */
	public function getOriginals($prefix) {
		return $prefix . self::ORIGINAL_PATH;
	}


	/**
	 * @return string
	 */
	public function getOriginalsPath() {
		return $this->getOriginals($this->getBasePath());
	}


	/**
	 * @return string
	 */
	public function getOriginalsUrl() {
		return $this->getOriginals($this->getBaseUrl());
	}


	public function deleteVignette() {
		if ('' != $this->getFichier()) {
			unlink($this->getVignettePath());
			$this->setFichier('')->save();
		}
	}


	public function getVignettePath() {
		return $this->getBasePath() . $this->getFichier();
	}


	public function deletePdf() {
		if ('' != $this->getPdf()) {
			unlink($this->getBasePath() . $this->getPdf());
			$this->setPdf('')->save();
		}
	}


	/**
	 * @return bool
	 */
	public function receiveFile() {
		if (!$this->_isFileInRequest('fichier'))
			return true;

		$oldFile = $this->getFichier();
		if (!$this->_uploadFile('fichier', 
														array('jpg', 'gif', 'png', 'jpeg'),
														array($this->getBasePath() . $oldFile,
																	$this->getBasePath() . 'thumb_'.$oldFile)))
				return false;

		return $this->createThumbnail();
	}


	/**
	 * @return bool
	 */
	public function receivePDF() {
		if (!$this->_isFileInRequest('pdf'))
			return true;

		return $this->_uploadFile('pdf', 
															array('pdf'), 
															array($this->getBasePath() . $this->getPdf()));
	}


	protected function _isFileInRequest($name) {
		return (0 !== $_FILES[$name]['size']);
	}


	protected function _uploadFile($name, $extensions, $delete_files) {		
		$upload = $this
			->getUploadHandler($name)
			->setAllowedExtensions($extensions);

		if (!$upload->receive()) {
			$this->addError($upload->getError());
			return false;
		}

		$oldFile = $this->_get($name);
		$this->_set($name, $upload->getSavedFileName())->save();

		// s'il y avait un ancien fichier et que le nouveau nom est différent
		// on tente un nettoyage
		if (('' != $oldFile) && ($oldFile != $this->_get('fichier'))) {
			foreach ($delete_files as $delete_file)
				unlink($delete_file);
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function createThumbnail() {
		if ('' == $this->getFichier()) {
			return true;
		}

		try {
			$image = new Imagick($this->getBasePath() . $this->getFichier());
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


	/** @return string */
	public function getThumbnailUrl() {
		if ('' == $this->getFichier())
			return '';

		return $this->getBaseUrl() . 'thumb_' . $this->getFichier();
	}


	public function getThumbnailPath() {
		return $this->getBasePath() . 'thumb_' . $this->getFichier();
	}


	/**
	 * @param Class_AlbumRessource $resourceToMove
	 * @param int $targetId
	 */
	public function moveRessourceAfter($resourceToMove, $targetId) {
		$targetId = (int)$targetId;

		if (0 == $targetId) {
			$this->_moveResourceToTop($resourceToMove);
			return;
		}

		$sourceId = $resourceToMove->getId();

		if ($sourceId == $targetId) {
			return;
		}

		$sourceKey = $targetKey = null;
		$resources = $this->getRessources();

		foreach ($resources as $k => $resource) {
			if ($resource->getId() == $sourceId)
				$sourceKey = $k;

			if ($resource->getId() == $targetId)
				$targetKey = $k;

			if ((0 < $targetKey) && (0 < $sourceKey))
				break;
		}

		if ((null === $targetKey) || (null === $sourceKey))
			return;

		$newRessources = array();

		$order = 1;
		foreach ($resources as $k => $resource) {
			if ($k == $sourceKey) {
				continue;
			}

			$newRessources[] = $resource->setOrdre($order);

			if ($k == $targetKey) {
				$order++;
				$newRessources[] = $resources[$sourceKey]->setOrdre($order);
			}

			$order++;
		}

		$this->save();
	}


	/**
	 * @return Array
	 */
	public function getRessourcesWithTitre(){
		$ressources_with_titre = array();
		$ressources = $this->getRessources();
		foreach($ressources as $ressource) {
			if ($ressource->hasTitre())
				$ressources_with_titre[]=$ressource;
		}
		return $ressources_with_titre;
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
				->setBasePath($this->getBasePath());
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


	public function beforeDelete() {
		parent::beforeDelete();
		$this->deleteFiles();
	}


	public function beforeSave() {
		$this->updateDateMaj();
	}


	public function updateDateMaj() {
		$date = new Class_Date();
		$this->setDateMaj($date->DateTimeDuJour());
	}


	public function deleteFiles() {
		try {
			$iterator = new DirectoryIterator($this->getBasePath());
			foreach ($iterator as $fileInfo) {
				if ($fileInfo->isFile()) {
					unlink($fileInfo->getPathname());
				}
			}

			$this->_removeFolders();

		} catch (Exception $e) {
			// on arrive ici si DirectoryIterator ne peut pas ouvrir le rep
			// on ne peut rien faire
		}
	}


	/**
	 * Ceci échouera si les reps ne sont pas vides
	 */
	protected function _removeFolders() {
		rmdir($this->getOriginalsPath() . Class_AlbumRessource::BASE_PATH);
		rmdir($this->getOriginalsPath());
		rmdir($this->getThumbnailsPath() . Class_AlbumRessource::BASE_PATH);
		rmdir($this->getThumbnailsPath());
		rmdir($this->getBasePath());
	}


	/**
	 * @param Class_AlbumRessource $resourceToMove
	 */
	protected function _moveResourceToTop($resourceToMove) {
		$sourceId		= $resourceToMove->getId();
		$sourceKey	= null;

		$resources = $this->getRessources();

		foreach ($resources as $k => $resource) {
			if ($resource->getId() == $sourceId) {
				$sourceKey = $k;
				break;
			}
		}

		if (null === $sourceKey)
			return;

		$newRessources = array($resources[$sourceKey]->setOrdre(1));

		$order = 2;

		foreach ($resources as $k => $resource) {
			if ($k == $sourceKey) {
				continue;
			}

			$newRessources[] = $resource->setOrdre($order);

			$order++;
		}

		$this->setRessources($newRessources);
		$this->save();
	}


	/**
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */ 
	public function __call($name, $arguments) {
		$supportedTypes = array('Image', 'Flash', 'Video', 'File');
		$supportedPluralTypes = array('Images', 'Flashs', 'Videos', 'Files');

		if ('has' === substr($name, 0, 3)) {
			$type = substr($name, 3);
			if (('Only' == substr($type, -4)) 
					&& (in_array(substr($type, 0, strlen($type) - 4), 
											 $supportedTypes))) {
				return $this->_hasOnlyKindOf(substr($type, 0, strlen($type) - 4));
			}

			if (in_array($type, $supportedTypes)) {
				return $this->_hasKindOf($type);
			}
		}

		if (('get' === substr($name, 0, 3))
				&& (in_array(substr($name, 3), $supportedPluralTypes))
		) {
			return $this->_getKindOf(substr($name, 3));
		}

		return parent::__call($name, $arguments);
	}


	/** 
	 * @param string $type
	 * @return bool
	 */
	protected function _hasKindOf($type) {
		foreach ($this->getRessources() as $ressource) {
			if ($ressource->{'is' . $type}()) {
				return true;
			}
		}

		return false;
	}

	
	/**
	 * @param string $type
	 * @return bool
	 */
	protected function _hasOnlyKindOf($type) {
		foreach ($this->getRessources() as $ressource) {
			if (!$ressource->{'is' . $type}()) {
				return false;
			}
		}

		return true;
	}


	/** 
	 * @param string $type
	 * @return array
	 */
	public function _getKindOf($type) {
		$type = substr($type, 0, strlen($type) - 1);
		$items = array();
		foreach ($this->getRessources() as $ressource) {
			if ($ressource->{'is' . $type}()) {
				$items[] = $ressource;
			}
		}
		return $items;
	}


	/** 
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->getCategorie()->getAbsolutePath().'>'.$this->getTitre();
	}


	public function setAnnee($annee) {
		if (!$annee)
			return $this->_set('annee', '');
		return $this->_set('annee', sprintf('%04d', $annee)); 
	}


	public function validate() {
		$next_year = date('Y', strtotime('+1 year'));
		$this->check((0==(int)$this->getAnnee()) || (($this->getAnnee() >= self::ANNEE_MIN) and ($this->getAnnee() <= $next_year)), 
								 sprintf("L'année doit être comprise entre %s et %s", 
												 self::ANNEE_MIN, 
												 $next_year));
	}


	public function setThumbnailAttributes($attributes) {
		if (is_array($attributes))
			return $this->setCfgThumbnails(ZendAfi_Filters_Serialize::serialize($attributes));
		return $this->setCfgThumbnails($attributes);
	}


	public function setThumbnailAttribute($attribute, $value) {
		$attributes = $this->getThumbnailAttributes();
		$attributes[$attribute] = $value;
		return $this->setThumbnailAttributes($attributes);
	}


	public function getNotesAsArray() {
		if (!$notes = unserialize($this->getNotes()))
			return array();
		return $notes;
	}


	/**
	 * @param $field string
	 * @param $datas array
	 */
	public function getNoteForFieldAndDatas($field, $datas = []) {
		$notes = $this->getNotesAsArray();

		foreach ($notes as $note) {
			if (!array_key_exists('field', $note)
				or !array_key_exists('data', $note)
				or $field != $note['field'])
				continue;

			foreach ($datas as $k => $v) {
				if ($note['data'][$k] != $v)
					continue 2;
			}

			return $note['data']['a'];
		}
	}


	/** @return string */
	public function getVideoUrl() {
		return $this->getNoteForFieldAndDatas(self::VIDEO_URL_FIELD,
			                                    ['x' => self::VIDEO_URL_TYPE]);
	}

		
	/**
	 * @param $url string
	 * @return Class_Album
	 */
	public function setVideoUrl($url) {
		return $this->setNotes([[
				'field' => self::VIDEO_URL_FIELD, 
				'data' => ['x' => self::VIDEO_URL_TYPE, 'a' => $url]]]);
	}

		
	public function setNotes($array_or_string) {
		if (is_array($array_or_string)) 
			parent::setNotes(serialize($array_or_string));
		else
			parent::setNotes($array_or_string);
		return $this;
	}


	public function getNote($field) {
		$notes = $this->getNotesAsArray();
		if (isset($notes[$field]))
			return $notes[$field];
		return null;
	}


	public function addNote($field, $value) {
		$notes = $this->getNotesAsArray();
		$notes[$field] = $value;
		return $this->setNotes($notes);
	}


	public function clearNotes() {
		return $this->setNotes('');
	}


	/** 
	 * @return int
	 */
	public function getThumbnailHeight() {
		if (!$this->hasRessources())
			return 1;
		$first_ressource =  array_first($this->getRessources());
		return round(($first_ressource->getHeight() / $first_ressource->getWidth()) * $this->getThumbnailWidth());
	}


	/** 
	 * @return array
	 */
	public function getThumbnailAttributes() {
		if (!$attributes = ZendAfi_Filters_Serialize::unserialize($this->getCfgThumbnails()))
			$attributes = array();
		return $attributes;
	}


	/** 
	 * @return int
	 */
	public function getFirstThumbnailSize() {
		if (!$this->hasRessources())
			return 0;
		$first_ressource =  array_first($this->getRessources());
		$filepath = $first_ressource->getThumbnailFilePath(array('width' => $this->getThumbnailWidth()));
		$fileInfos = stat($filepath);
		return $fileInfos['size'];
	}


	/** 
	 * @return int
	 */
	public function getNavigatorThumbnailWidth() {
		return 80;
	}


	/** 
	 * @return array
	 */
	public function toArray() {
		$attributes = parent::toArray();
		unset($attributes['notes']);
		return array_merge($attributes,
											 $this->getDefaultThumbnailValues(),
											 $this->getThumbnailAttributes());
	}


	/** 
	 * @return Class_Album
	 */
	public function sortRessourceByFileName() {
		$ressources = $this->getRessources();
		usort($ressources, array('Class_AlbumRessource', 'sortByFileName'));
		foreach($ressources as $i => $ressource)
			$ressource->setOrdre($i + 1);
		return $this;
	}


	public function getStatus() {
		return 'none';
	}


	public function formatedCount() {
		return sprintf('%03d', $this->getRessourcesCount());
	}

	
	public function isGallica() {
		return ($this->isOAI() && (false !== strpos($this->getIdOrigine(), 'gallica')));
	}


	public function getGallicaArkId() {
		if (!$this->isGallica())
			return '';
		return array_last(explode('/', $this->getIdOrigine()));
	}


	/** 
	 * Return arteVOD trailer video list
	 * @return array 
	 */
	public function getTrailers() {
		$trailers = array();
		$trailers_url = $this->getUnimarc856Values('trailer');
		foreach($trailers_url as $url)
			$trailers []= Class_Video::newWithUrl($url);

		return $trailers;
	}


	/** 
	 * Return arteVOD poster url
	 * @return string 
	 */
	public function getPoster() {
		if ($posters_url = $this->getUnimarc856Values('poster'))
			return $posters_url[0];
		return '';
	}


	/** 
	 * Return arteVOD poster url
	 * @return string 
	 */
	public function getUnimarc856Values($field) {
		$values = array();
		$unimarc_array = $this->getNotesAsArray();
		foreach($unimarc_array as $unimarc_value) {
			if (!is_array($unimarc_value) 
				|| !isset($unimarc_value['field']) 
				|| '856' !== $unimarc_value['field'] 
				|| !isset($unimarc_value['data']) 
				|| !isset($unimarc_value['data']['x'])
				|| !isset($unimarc_value['data']['a'])
				|| $field !== $unimarc_value['data']['x'])
				continue;

			$values []= $unimarc_value['data']['a'];
		}
		return $values;
	}


	/** 
	 * @return boolean
	 */
	public function isVisible() {
		return (bool)$this->getVisible();
	}


	/**
	 * @return string url
	 */
	public function getExternalUri()  {
		if ($values = $this->getUnimarc856Values(Class_WebService_ArteVOD_Film::TYPE_EXTERNAL_URI))
			return $values[0];
		return '';
	}


	public function setExternalUri($uri)  {
		$notes = $this->getNotesAsArray();
		$notes [] = ['field' => '856', 
								 'data' => array('x' => Class_WebService_ArteVOD_Film::TYPE_EXTERNAL_URI, 'a' => $uri)];
		return $this->setNotes($notes);
	}
}

?>