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

class FRBR_LinkLoader extends Storm_Model_Loader {
	/**
	 * @param $key string
	 * @return array
	 */
	public function getLinksForSource($key) {
		return $this->getLinksFor('source', $key);
	}


	/**
	 * @param $key string
	 * @return array
	 */
	public function getLinksForTarget($key) {
		return $this->getLinksFor('target', $key);
	}


	/**
	 * @param $name string
	 * @param $value string
	 */
	public function getLinksFor($name, $value) {
		return $this->findAllBy(['where' => $name . ' like \'%' . $value . '%\'',
				                     'order' => 'type_id']);
	}
}


class Class_FRBR_Link extends Storm_Model_Abstract {
	use Trait_Translator;

	const TYPE_NOTICE = 'afi:notice';
	const TYPE_EXTERNAL = 'afi:external';
	
	protected $_table_name = 'frbr_link';
	protected $_belongs_to = ['type' => ['model' => 'Class_FRBR_LinkType',
	                                     'referenced_in' => 'type_id']];

	protected $_loader_class = 'FRBR_LinkLoader';

	/** @var Storm_Model_Abstract */
	protected $_source_entity;
	/** @var Storm_Model_Abstract */
	protected $_target_entity;

	/**
	 * @return string
	 */
	public function getLinkCompleteLabel() {
		if (!$type = $this->getType())
			return '';

		return $type->getCompleteLabel();
	}


	public function getLibelle() {
		return '';
	}


	public function validate() {
		$this
			->validateAttribute('source', 'Zend_Validate_NotEmpty', $this->_('URL objet A est requis'))
			->validateAttribute('source', 'ZendAfi_Validate_Url', $this->_('N\'est pas une url valide'))
			->validateAttribute('target', 'Zend_Validate_NotEmpty', $this->_('URL objet B est requis'))
			->validateAttribute('target', 'ZendAfi_Validate_Url', $this->_('N\'est pas une url valide'));
	}


	public function beforeSave() {
		parent::beforeSave();
		$this->_detectSourceType();
		$this->_detectTargetType();
	}


	protected function _detectEntityTypeFromUrl($url, $callback) {
		if (false === strpos($url, 'http:'))
			return;

		$uri = Zend_Uri_Http::fromString($url);
		$callback(($uri->getHost() == $_SERVER['HTTP_HOST']) ?
			self::TYPE_NOTICE :
			self::TYPE_EXTERNAL);
	}


	protected function _detectSourceType() {
		$this->_detectEntityTypeFromUrl(
				$this->getSource(),
				function ($type) {$this->setSourceType($type);});
	}


	protected function _detectTargetType() {
		$this->_detectEntityTypeFromUrl(
				$this->getTarget(),
				function ($type) {$this->setTargetType($type);});
	}


	/** @return Class_Notice */
	public function getTargetNotice() {
		return $this->getEntityFor('target');
	}


	/** @return Class_Notice */
	public function getSourceNotice() {
		return $this->getEntityFor('source');
	}


	/**
	 * @param $type string
	 * @return Class_Notice
	 */
	public function getEntityFor($type) {
		if (self::TYPE_EXTERNAL == $this->{'get'. ucfirst($type) . 'Type'}())
			return;

		$attribute = '_' . $type .'_entity';
		if (!$this->$attribute) {

			try {		
				$key = $this->_extractKeyFromUrl($this->{'get'. ucfirst($type)}());
			} catch(Zend_Uri_Exception $e) {
				return $this->$attribute = null;
			}

			$this->$attribute = Class_Notice::getLoader()->getNoticeByClefAlpha($key);
		}
		return $this->$attribute;
	}


	/** @return string */
	public function getTypeLabelFromSource() {
		return $this->getType()->getFromSource();
	}


	/** @return string */
	public function getTypeLabelFromTarget() {
		return $this->getType()->getFromTarget();
	}


	/**
	 * @param $url string
	 * @return string
	 */
	protected function _extractKeyFromUrl($url) {
		if ('' == $url)
			return '';

		// simule un routage standard
		$request = new Zend_Controller_Request_Http($url);
		$request->setBaseUrl(BASE_URL);
		$router = new Zend_Controller_Router_Rewrite();
		$router->route($request);
		
		return $request->getParam('clef', '');
	}
}

?>