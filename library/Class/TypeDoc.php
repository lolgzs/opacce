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

class TypeDocLoader {
	protected $_all_instances;

	public function newInstance() {
		return new Class_TypeDoc();
	}


	public function newInstanceWithId($id) {
		return $this->newInstance()->setId($id);
	}


	public function find($id) {
		$instances = $this->findAll();
		if (array_key_exists($id, $instances))
			return $instances[$id];
		return null;
	}


	public function reset() {
		$this->_all_instances = null;
		return $this;
	}


	public function findAll() {
		if (isset($this->_all_instances))
			return $this->_all_instances;

		$types_docs = Class_CosmoVar::getLoader()->find('types_docs');

		$lines = explode("\r\n", $types_docs->getListe());
		$instances = array();

		foreach ($lines as $line) {
			$instance = $this->unserialize($line);
			$instances [$instance->getId()]= $instance;
		}

		$default_types = Class_TypeDoc::getDefaultTypeDocs();
		foreach($default_types as $id => $label) {
			if (!isset($instances[$id]))
				$instances[$id] = Class_TypeDoc::newWithLabel($label)->setId($id);
		}
		
		return $this->_all_instances = $instances;
	}


	public function save($model) {
		$serialized = array();

		foreach ($this->findAll() as $i => $type_doc) {
			if ($type_doc->getId() === $model->getId())
				$serialized [$i]= $this->serialize($model);
			else
				$serialized [$i]= $this->serialize($type_doc);
		}

		if ($model->isNew()) {
			$id = max(array_keys($serialized)) + 1;
			$model->setId($id);
			$serialized []= $this->serialize($model);
		}


		return $this->_saveSerialized($serialized);
	}


	public function delete($model) {
		$serialized = array();

		foreach ($this->findAll() as $i => $type_doc) {
			if ($type_doc->getId() !== $model->getId())
				$serialized [$i]= $this->serialize($type_doc);
		}

		return $this->_saveSerialized($serialized);
	}


	protected function serialize($type_doc) {
		return sprintf('%d:%s', $type_doc->getId(), $type_doc->getLabel());
	}


	public function unserialize($str) {
		$attrs = explode(':', $str);
		return $this
			->newInstanceWithId($attrs[0])
			->setLabel($attrs[1]);
	}


	protected function _saveSerialized($serialized) {
		$this->reset();
		return Class_CosmoVar::getLoader()
			->find('types_docs')
			->setListe(implode("\r\n", $serialized))
			->save();
	}


	public function findUsedTypeDocIds() {
		$rows = Class_Notice::getTable()->getAdapter()->fetchAll('select distinct(type_doc) from notices');
		return array_map(function($row){return $row['type_doc'];},
										 $rows);
	}
}


class Class_TypeDoc extends Storm_Model_Abstract {
  protected $_loader_class = 'TypeDocLoader';
	const LIVRE = 1;
	const PERIODIQUE = 2;
	const DISQUE = 3;
	const DVD = 4;
	const LIVRE_NUM = 100;
	const DIAPORAMA = 101;
	const EPUB = 102;
	const OAI = 103;
	const ARTEVOD = 104;

	public static function getDefaultTypeDocs() {
		return [self::LIVRE_NUM => 'Livres numérisés',
			      self::DIAPORAMA => 'Diaporamas',
						self::EPUB => 'E-Books',
						self::OAI => 'OAI',
			      self::ARTEVOD => 'Arte VOD'];
	}


	/**
	 * @param String label
	 * @return Class_Type_Doc
	 */	
	public static function newWithLabel($label) {
		$instance = new self();
		return $instance->setLabel($label);
	}


	/**
	 * @param Array types_docs
	 * @return array
	 */
	public static function toIdLabelArray($types_docs) {
		$id_label_array = array();
		foreach($types_docs as $type_doc)
			$id_label_array[$type_doc->getId()] = $type_doc->getLabel();
		return $id_label_array;
	}


	/**
	 * @param Array langues
	 * @return array
	 */
	public static function allByIdLabel() {
		return self::toIdLabelArray(self::getLoader()->findAll());
	}


	/**
	 * @param Array langues
	 * @return array
	 */
	public static function allByIdLabelForAlbum() {
		$all = self::allByIdLabel();
		$for_album = array();
		foreach($all as $id => $label) {
			if ($id >= self::LIVRE_NUM)
				$for_album[$id] = $label;
		}
		return $for_album;
	}


	/**
	 * return int identifiant type doc codifié Pergame
	 */
	public function toPergame() {
		return $this->getId();
	}
}

?>