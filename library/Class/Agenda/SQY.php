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


/** [[file:~/public_html/afi-opac3/tests/library/Class/AgendaSQYImportTest.php::class%20AgendaSQYImportTest%20extends%20Storm_Test_ModelTestCase%20{][tests]]  */

Trait Trait_Agenda_SQY_ItemWrapper {
	protected static $_instances = [];
	protected $_wrapped_instance;
	protected $_attributes;

	public static function resetInstances() {
		static::$_instances = [];
	}


	public static function newInstance($attributes) {
		$id = $attributes['INDEX'];
		return static::$_instances[$id]= new static($attributes);
	}


	public static function getInstances() {
		return static::$_instances;
	}


	public static function getWrappedInstance($id) {
		return isset(static::$_instances[$id]) ? static::$_instances[$id] : null;
	}


	public function __construct($attributes) {
		$this->_wrapped_instance = new static::$_item_class();
		$this->_attributes = $attributes;
		$this->initialize();
	}


	public function initialize() {}


	public function __call($method, $args) {
		return isset(static::$_method_map[$method])
			? call_user_func_array([$this->_wrapped_instance, static::$_method_map[$method]], 
														 $args)
			: null;
	}
}




class Class_Agenda_SQY_EventWrapper {
	use Trait_Agenda_SQY_ItemWrapper;

	protected static $_item_class = 'Class_Article';
	protected static $_method_map = ['setTitle' => 'setTitre',
																	 'getTitre' => 'getTitre',
																	 'setAbstract' => 'setDescription',
																	 'getDescription' => 'getDescription',
																	 'setDescription' => 'setContenu',
																	 'getContenu' => 'getContenu',
																	 'getEventsDebut' => 'getEventsDebut',
																	 'getEventsFin' => 'getEventsFin',
																	 'getLieu' => 'getLieu',
																	 'getCategorie' => 'getCategorie',
																	 'getTags' => 'getTags'];


	public static function mapLocationsAndCategories() {
		$instances = static::getInstances();
		foreach($instances as $event) {
			$event->mapLocation()
						->mapCategory()
						->mapTags();
		}
	}


	public function formatDateForArticle($date) {
		return implode('-', array_reverse(explode('/', $date)));
	}


	public function setDateStart($date) {
		$this->_wrapped_instance->setEventsDebut($this->formatDateForArticle($date));
	}


	public function setDateEnd($date) {
		$this->_wrapped_instance->setEventsFin($this->formatDateForArticle($date));
	}


	public function mapLocation() {
		if (!$location_id = $this->_attributes['LOCATION'])
			return $this;

		$lieu = Class_Agenda_SQY_LocationWrapper::getWrappedInstance($location_id);
		$this->_wrapped_instance->setLieu($lieu);
		return $this;
	}


	public function mapCategory() {
		$category_id = $this->_attributes['CATEGORY'] ? $this->_attributes['CATEGORY'] : 0;

		$category_id = explode(',', $category_id)[0];
		$category = Class_Agenda_SQY_CategoryWrapper::getWrappedInstance($category_id);
		$this->_wrapped_instance->setCategorie($category);
		return $this;
	}


	public function mapTags() {
		$category_ids = array_merge(explode(',', $this->_attributes['CATEGORY']),
																explode(',', $this->_attributes['CATEGORY2']),
																explode(',', $this->_attributes['CATEGORY3']));
		$tags = [];
		foreach ($category_ids as $category_id) {
			if ($category = Class_Agenda_SQY_CategoryWrapper::getWrappedInstance($category_id))
				$tags []= $category->getLibelle();
		}
		$this->_wrapped_instance->setTags(implode(',', $tags));
		return $this;
	}
}




class Class_Agenda_SQY_CategoryWrapper {
	use Trait_Agenda_SQY_ItemWrapper {
		resetInstances as originalResetInstances;
	}

	protected static $_item_class = 'Class_ArticleCategorie';
	protected static $_method_map = ['setTitle' => 'setLibelle',
																	 'getLibelle' => 'getLibelle'];


	public static function resetInstances() {
		static::originalResetInstances();
		static::newInstance(['INDEX' => 0])->setTitle('Portail');
	}
}




class Class_Agenda_SQY_LocationWrapper {
	use Trait_Agenda_SQY_ItemWrapper;

	protected static $_item_class = 'Class_Lieu';
	protected static $_method_map = ['setTitle' => 'setLibelle',
																	 'getLibelle' => 'getLibelle',
																	 'setZip' => 'setCodePostal',
																	 'getCodePostal' => 'getCodePostal',
																	 'setCity' => 'setVille',
																	 'getVille' => 'getVille',
																	 'getPays' => 'getPays'];

	public function initialize() {
		$this->_wrapped_instance->setPays('France');
	}
}




class Class_Agenda_SQY_OrganizerWrapper {
	use Trait_Agenda_SQY_ItemWrapper;

	protected static $_item_class = 'StdClass';
	protected static $_method_map = [];
}




class Class_Agenda_SQY {
	/** @var Class_WebService_XMLParser */
	protected $_xml_parser;
	protected $_item_class;
	protected $_item;

	public function importFromXML($xml) {
		$this->_xml_parser = (new Class_WebService_XMLParser())->setElementHandler($this);
		$this->_xml_parser->parse($xml);
		Class_Agenda_SQY_EventWrapper::mapLocationsAndCategories();
		return $this;
	}


	public function getCategories() {
		return Class_Agenda_SQY_CategoryWrapper::getInstances();
	}


	public function getEvents() {
		return Class_Agenda_SQY_EventWrapper::getInstances();
	}


	public function getLocations() {
		return Class_Agenda_SQY_LocationWrapper::getInstances();
	}


	public function startItem($attributes) {
		$this->_item = call_user_func_array([$this->_item_class, 'newInstance'], 
																				[$attributes]);
	}


	public function startEvent() {
		$this->_item_class = $this->wrapperClassForTag('event');
	}


	public function startCategory() {
		$this->_item_class = $this->wrapperClassForTag('category');
	}


	public function startLocation() {
		$this->_item_class = $this->wrapperClassForTag('location');
	}


	public function startOrganizer() {
		$this->_item_class = $this->wrapperClassForTag('organizer');
	}


	public function wrapperClassForTag($tag) {
		$class_name = 'Class_Agenda_SQY_'.ucfirst($tag).'Wrapper';
		call_user_func([$class_name, 'resetInstances']);
		return $class_name;
	}


	public function endTitle($data) {
		$this->_item->setTitle($data);
	}

	
	public function endZip($data) {
		$this->_item->setZip($data);
	}


	public function endCity($data) {
		$this->_item->setCity($data);
	}


	public function endAbstract($data) {
		$this->_item->setAbstract($data);
	}


	public function endDescription($data) {
		$this->_item->setDescription($data);
	}


	public function endDate_Start($data) {
		$this->_item->setDateStart($data);
	}


	public function endDate_End($data) {
		$this->_item->setDateEnd($data);
	}
}

?>