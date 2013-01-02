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


Trait Trait_Agenda_SQY_ItemWrapper {
	protected static $_instances = [];
	protected $_wrapped_instance;

	public static function resetInstances() {
		static::$_instances = [];
	}

	public static function newInstance() {
		return static::$_instances []= new static();
	}

	public static function getInstances() {
		return static::$_instances;
	}

	public function __construct() {
		$this->_wrapped_instance = new static::$_item_class();
		$this->initialize();
	}

	public function initialize() {
	}

	public function __call($method, $args) {
		if (!isset(static::$_method_map[$method]))
			return null;

		return call_user_func_array([$this->_wrapped_instance, static::$_method_map[$method]], 
																$args);
	}
}


class Class_Agenda_SQY_EventWrapper {
	use Trait_Agenda_SQY_ItemWrapper;

	protected static $_item_class = 'Class_Article';
	protected static $_method_map = [];
}


class Class_Agenda_SQY_CategoryWrapper {
	use Trait_Agenda_SQY_ItemWrapper;

	protected static $_item_class = 'Class_ArticleCategorie';
	protected static $_method_map = ['setTitle' => 'setLibelle',
																	 'getLibelle' => 'getLibelle'];
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
		$this->_item = call_user_func([$this->_item_class, 'newInstance']);
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
}

?>