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
		return $this->findAllBy([$name => $value,
				                     'order' => 'type_id']);
	}
}


class Class_FRBR_Link extends Storm_Model_Abstract {
	use Trait_Translator;
	
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
			->validateAttribute('source', 'Zend_Validate_NotEmpty', $this->_('Un libellé objet A est requis'))
			->validateAttribute('target', 'Zend_Validate_NotEmpty', $this->_('Un libellé objet B est requis'));
	}


	public function beforeSave() {
		parent::beforeSave();
		if (false === strpos($this->getSource(), '/clef/'))
			return;

		$parts = explode('/', $this->getSource());
		$this->setSource($parts[array_search('clef', $parts) + 1]);
	}


	/** @return string */
	public function getTargetTitle() {
		return $this->getTargetNotice()->getTitrePrincipal();
	}


	/** @return string */
	public function getSourceTitle() {
		return $this->getSourceNotice()->getTitrePrincipal();
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
		$attribute = '_' . $type .'_entity';
		if (!$this->$attribute)
			$this->$attribute = Class_Notice::getLoader()->getNoticeByClefAlpha($this->$type);
		return $this->$attribute;
	}

	
	/** @param $view Zend_View */
	public function getTargetUrl($view) {
		return $view->urlNotice($this->getTargetNotice());
	}


	/** @param $view Zend_View */
	public function getSourceUrl($view) {
		return $view->urlNotice($this->getSourceNotice());
	}


	/** @return string */
	public function getTypeLabelFromSource() {
		return $this->getType()->getFromSource();
	}


	/** @return string */
	public function getTypeLabelFromTarget() {
		return $this->getType()->getFromTarget();
	}
}

?>