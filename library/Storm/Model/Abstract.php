<?php
/*
STORM is under the MIT License (MIT)

Copyright (c) 2010-2011 Agence Française Informatique http://www.afi-sa.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

abstract class Storm_Model_Abstract {
	/**
	 * @var array
	 */
	protected static $_loaders = array();

	/**
	 * @var string
	 */
	protected $_table_primary = null;

	/**
	 * @var array
	 */
	protected $_attributes = array('id' => null);

	/**
	 * @var array
	 */
	protected $_has_many_attributes_in_db = array();

	/**
	 * @var array
	 */
	protected $_has_many_attributes = array();

	/**
	 * Defines me -> * dependents relationship
	 * Attributes:
	 *   model:  dependent class name
	 *   role: the name of the accessor (also used for default id mapping in db)
	 *   order: order statetement while loading models
	 *   through: this relation relies on another one
	 *   unique: if true, a dependent could not appear twice
	 *   dependents: if 'delete', then deleting me will delete dependents
	 *   scope: an array of field / value pair that filters data
	 *
	 * Ex:
	 *	protected $_has_many = array('sessions' => array(
	 *																								 'model' => 'Class_SessionFormation',
	 *																								 'role' => 'formation',
	 *																								 'dependents' => 'delete',
	 *																								 'order' => 'date_debut desc',
	 *																								 'scope' => array('type' => 2)),
	 *
	 *															 'session_formation_inscriptions' => array('through' => 'sessions',
	 *																								                         'unique' => 'true'),
	 *
	 *															 'stagiaires' => array('through' => 'session_formation_inscriptions'));
	 * @var array
	 */
	protected $_has_many = array();

	/**
	 * @var array
	 */
	protected $_belongs_to_attributes = array();

	/**	
	 * Defines me -> * dependents relationship
	 * Attributes:
	 *   model:  dependent class name
	 *   referenced_in: field name used to reference dependent id
	 *   trough: this relation relies on another one
	 * protected $_belongs_to = array('librairy' => array('model' => 'Librairy',
	 *																						        'referenced_in' => 'id_library'),
	 *
	 *                                'zone' => array('through' => 'librairy'));
	 * @var array
	 */
	protected $_belongs_to = array();

	/**
	 * Store the list of errors found by validate() method.
	 * See also check($condition, $error)
	 * @var array
	 */
	protected $_errors = array();


	/**
	 * Default values for attributes of a new instance
	 * Should be defined in subclasses like:
	 * $_default_values = array('title' => 'new article', 'content' => '')
	 * @var array
	 */
	protected $_default_attribute_values = array();


	/**
	 * @param string $class
	 * @return Storm_Model_Loader
	 */
	protected static function _buildLoaderFor($class) {
		$reflection = new ReflectionClass($class);
		$class_vars = $reflection->getdefaultProperties();

		if (isset($class_vars['_loader_class'])) {
			$loader_class = $class_vars['_loader_class'];
			return new $loader_class($class);

		}

		return new Storm_Model_Loader($class);

	}

	/**
	 * @param string $class
	 * @return Storm_Model_Loader
	 */
	public static function getLoaderFor($class) {
		if (!(isset(self::$_loaders[$class]) || array_key_exists($class, self::$_loaders))) {
			self::setLoaderFor($class, self::_buildLoaderFor($class));

		}

		return self::$_loaders[$class];
	}


	/**
	 * @param string $class
	 * @param Storm_Model_Loader $loader
	 */
	public static function setLoaderFor($class, $loader) {
		self::$_loaders[$class] = $loader;
	}


	public static function unsetLoaders() {
		self::$_loaders = array();
	}


	/**
	 * @return bool
	 */
	public function hasBelongsToRelashionshipWith($field) {
		return array_key_exists($field, $this->_belongs_to);
	}


	/**
	 * @return bool
	 */
	public function save() {
		if ($valid = $this->isValid()) {
			$this->saveWithoutValidation();
		}

		return $valid;
	}


	public function saveWithoutValidation() {
		$this->_updateNullBelongsToIdFieldsFromDependents();

		$this->beforeSave();

		$this->getLoader()->save($this);
		$this->_saveDependencies();
	
		$this->afterSave();
	}


	protected function _updateNullBelongsToIdFieldsFromDependents() {
		$this->_belongs_to_attributes = array_filter($this->_belongs_to_attributes);
		foreach ($this->_belongs_to_attributes as $field => $dependent) {
			$id_field = $this->getIdFieldForDependent($field);
			if (null === $this->_get($id_field))
				$this->_set($id_field, $dependent->getId());
		}
		return $this;
	}


	public function beforeSave() {}

	public function afterSave() {}

	public function beforeDelete() {}

	public function afterDelete() {}

	/**
	 * Is this model valid for saving
	 *
	 * You can notify error by using addError or check methods
	 *
	 * @see Storm_Model_Abstract::addError
	 * @see Storm_Model_Abstract::check
	 *
	 * @example
	 * public function validate() {
	 *    $this->check($this->getRole() < 10, 'role should not exceed 10');
	 * }
	 *
	 */
	public function validate() {}


	/**
	 * Try to validate the model. If errors found, return false
	 * @see Storm_Model_Abstract::validate
	 * @return bool
	 */
	public function isValid() {
		$this->_errors = array();
		$this->validate();

		return !$this->hasErrors();

	}


	/**
	 * Return true if contains some errors (do not try to validate)
	 * @see Storm_Model_Abstract::validate
	 * @return bool
	 */
	public function hasErrors() {
		return count($this->getErrors()) > 0;
	}


	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->_errors;

	}

	/**
	 * @param string $attribute
	 * @param string $error
	 */
	public function addAttributeError($attribute, $error) {
		$this->_errors[$attribute] = $error;
	}


	/**
	 * @param string $error
	 */
	public function addError($error) {
		$this->_errors[] = $error;
	}


	/**
	 * @param bool $condition
	 * @param string $error
	 */
	public function check($condition, $error) {
		if (!$condition) {
			$this->addError($error);
		}
		return $this;
	}


	/**
	 * @param string $attribute
	 * @param bool $condition
	 * @param string $error
	 */
	public function checkAttribute($attribute, $condition, $error) {
		if (!$condition) {
			$this->addAttributeError($attribute, $error);
		}
		return $this;
	}


	/**
	 * Return an associative array with attribute
	 * name as $key and its value.
	 *
	 * Used by Loader while saving in order to build the
	 * SQL query.
	 *
	 * @return array
	 */
	public function attributesToArray() {
		$attributes = array();

		$all_attributes = array_merge($this->_default_attribute_values,
																	$this->_attributes);
																	
		foreach ($all_attributes as $name => $value) {
			$method = 'get'.$this->attributeNameToAccessor($name);

			if (method_exists($this, $method)) {
				$attributes[$name] = $this->$method();

			} else {
				$attributes[$name] = $value;

			}
		}

		return $attributes;
	}


	/**
	 * Return an associative array with attribute
	 * name as $key and its value.
	 *
	 * This method may be redefined in subclasses
	 * in order to provide some coupling with Zend_Form::populate()
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->attributesToArray();
	}


	/**
	 * Returns true if this instance has not been loaded from the
	 * database or saved.
	 *
	 * @return bool
	 */
	public function isNew() {
		return (bool)(!array_key_exists('id', $this->_attributes) |
									$this->_attributes['id'] === null);
	}


	public function delete() {
		$this->beforeDelete();
		$this->_deleteDependents();

		if ($this->getLoader()->delete($this)) {
			$this->afterDelete();
		}
	}

	/**
	 * @param string $id
	 * @return Storm_Model_Abstract
	 */
	public function setId($id) {
		if ($this->_table_primary != null) {
			$this->_set(strtolower($this->_table_primary), $id);
		}

		return $this->_set('id', $id);
	}


	/**
	 * @param string $field
	 * @return string
	 */
	public function getIdFieldForDependent($field) {
		if (
			array_key_exists($field, $this->_belongs_to)
			&& (array_key_exists('referenced_in', $this->_belongs_to[$field]))
		) {
			return $this->_belongs_to[$field]['referenced_in'];

		}

		return $field . '_id';

	}


	/**
	 * @param string $attribute
	 * @return bool
	 */
	public function isAttributeEmpty($attribute) {
		if (!array_key_exists($attribute, $this->_attributes)) {
			return true;

		}

		$value = $this->_get($attribute);
		return empty($value);
	}


	protected function _deleteDependents() {
		foreach ($this->_has_many as $field => $relation) {
			if (!array_key_exists('dependents', $relation)) {
				continue;
			}

			if ($relation['dependents'] != 'delete') {
				continue;
			}

			$dependents = $this->_getDependents($field);

			foreach ($dependents as $object) {
				$this->_removeDependent($field, $object);
				$object->delete();
			}
		}
	}


	protected function _saveDependencies() {
		foreach (array_keys($this->_has_many_attributes) as $field)
			$this->_saveDependents($field);
	}


	/**
	 * @param string $field
	 * @return Storm_Model_Abstract
	 */
	protected function _saveDependents($field) {
		if (array_key_exists('through', $this->_has_many[$field])) {
			return $this;
		}

		if (!array_key_exists($field, $this->_has_many_attributes_in_db)) {
			$this->_has_many_attributes_in_db[$field] = $this->_getDependentsFromLoader($field);
		}

		$dependents_to_delete = $this->_array_diff(
			$this->_has_many_attributes_in_db[$field],
			$this->_has_many_attributes[$field]
		);

		foreach ($dependents_to_delete as $dependent) {
			$dependent->delete();
		}

		foreach($this->_has_many_attributes[$field] as $dependent) {
			$dependent->save();
		}

		return $this;
	}

	/**
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @todo why not using real array_diff ??
	 */
	protected function _array_diff(Array &$array1, Array &$array2) {
		$diff = array();
		foreach($array1 as $element) {
			if (! in_array($element, $array2)) {
				$diff []= $element;

			}
		}

		return $diff;

	}


	/**
	 * Main purpose is to setup generic getters and setters:
	 *
	 * $car->getColor();
	 * $car->setColor('red');
	 * $car->addWheel($w = new Wheel());
	 * $car->removeWheel($w);
	 * $car->getWheels()   //return an array containing instances;
	 * $car->setWheels(array(new Wheel(), new Wheel(), new Wheel(), new Wheel()))
	 * $car->hasWheels()   //return true if attribute wheels not empty;
	 *
	 *
	 * @param string $method
	 * @param array $args
	 * @return Storm_Model_Abstract
	 * @throws Exception
	 */
	public function __call($method, $args) {
		if (!preg_match('/(get|set|add|remove|has|numberOf)(\w+)/', $method, $matches)) {
			throw new Storm_Model_Exception('Tried to call unknown method '.get_class($this).'::'.$method);

		}

		$attribute = $this->_accessorToAttributeName($matches[2]);

		switch ($matches[1]) {
			case 'get':
				return $this->_get($attribute);
				break;
			case 'set':
				$this->_set($attribute, $args[0]);
				return $this;
				break;
			case 'add':
				$this->_addDependent($this->_pluralize($attribute), $args[0]);
				break;
			case 'remove':
				$this->_removeDependent($this->_pluralize($attribute), $args[0]);
				break;
		  case 'has':
				return $this->_has($attribute);
				break;
			case 'numberOf':
				return $this->_numberOf($attribute);
				break;
		}

		return $this;

	}


	/**
	 * Return true if $attribute not empty
	 *
	 * @param String $attribute
	 * @return bool
	 */
	public function _has($attribute) {
		$dependent = $this->callGetterByAttributeName($attribute);
		return !empty($dependent);
	}


	/**
	 * Returns the number of dependents in a has_many relationship
	 * @param string $field name of the attribute
	 * @return int number of dependents
	 */
	protected function _numberOf($field) {
		if (!array_key_exists($field, $this->_has_many))
			throw new Storm_Model_Exception(
																			sprintf('Tried to call unknown method %s::numberOf%s',
																							get_class($this),
																							$this->attributeNameToAccessor($field)));

		if (array_key_exists($field, $this->_has_many_attributes))
			return count($this->_has_many_attributes[$field]);

		$map = $this->_has_many[$field];
		return $this->_getLoaderForModel($map['model'])->countBy(array('model' => $this,
																																	 'role' => $map['role']));
	}


	/**
	 * Update with a [name] => value formatted array
	 *
	 * @param Array $datas
	 * @return Storm_Model_Abstract
	 */
	public function updateAttributes(Array $datas) {
		foreach($datas as $name => $value) {
			$method = 'set'.$this->attributeNameToAccessor($name);
			$this->$method($value);
		}
		return $this;
	}


	/**
	 * Set initial attribute values with a [name] => value formatted array
	 * Used by Loader::newFromRow
	 * The difference with updateAttributes is that it doesn't call
	 * setAttributeName(...) magic.
	 *
	 * So we are sure that attributes are exactly those from db.
	 *
	 * @param Array $datas
	 * @return Storm_Model_Abstract
	 */
	public function initializeAttributes($datas) {
		foreach($datas as $name => $value)
			$this->_attributes[strtolower($name)] = $value;
		return $this;
	}


	/**
	 * UserId -> user_id
	 *
	 * @param string $accessor
	 * @return string
	 */
	protected function _accessorToAttributeName($accessor) {
		return Storm_Inflector::underscorize($accessor);
	}

	/**
	 * user_id -> UserId
	 *
	 * @param string $accessor
	 * @return string
	 */
	public function attributeNameToAccessor($name) {
		return Storm_Inflector::camelize($name);

	}


	public function isAttributeExists($field) {
		return
			array_key_exists($field, $this->_attributes) or
			array_key_exists($field, $this->_has_many) or
			array_key_exists($field, $this->_belongs_to) or
			$this->hasDefaultValueForAttribute($field);
	}


	/**
	 * Return the value of attribute $field. If cannot find it, raise Exception.
	 * @param string $field
	 * @return mixed
	 * @throws Storm_Model_Exception
	 */
	protected function _get($field) {
		if (isset($this->_attributes[$field]) || array_key_exists($field, $this->_attributes)) {
			return $this->_attributes[$field];
		}

		if (isset($this->_has_many[$field]) || array_key_exists($field, $this->_has_many)) {
			return $this->_getDependents($field);
		}

		if (isset($this->_belongs_to[$field]) || array_key_exists($field, $this->_belongs_to)) {
			return $this->_getDependent($field);
		}

		if ($this->hasDefaultValueForAttribute($field)) {
			 return $this->getDefaultValueForAttribute($field);
		}

		throw new Storm_Model_Exception(
												sprintf('Tried to call unknown method %s::get%s',
																get_class($this),
																$this->attributeNameToAccessor($field)));
	}


	/**
	 * Create compatibility with accessor like $miles->instrument.
	 * ex:
	 *    $miles->setInstrument("trumpet");
	 *    assert("trumpet" === $miles->getTrumpet())
	 *    assert("trumpet" === $miles->trumpet)
	 *
	 * If attribute not defined, returns null for compatibility purpose
   * with legacy code - Remember STORM is intended to get
	 * easily into messy crappy code. Sorry.
	 *
   *     assert(null ===  $miles->zork)
   *
	 * @param string $field name of the attribute / field
	 * @return mixed the value of attribute or null if does not exist.
	 */
	public function __get($field) {
		if (array_key_exists($key = strtolower($field), $this->_attributes) ||
				array_key_exists($key = strtoupper($field), $this->_attributes))
			return $this->_attributes[$key];

		return null;
	}


	/**
	 * Tells whether a default value is defined for an attribute
	 * @param string $field name of the attribute
	 * @return boolean true if a default value is defined for this attribute. Otherwise false.
	 */
	public function hasDefaultValueForAttribute($field) {
		return array_key_exists($field, $this->_default_attribute_values);
	}


	/**
	 * Return the default value for given attribute
	 * @param string $field name of the attribute
	 * @return mixed the default value defined
	 */
	public function getDefaultValueForAttribute($field) {
		return $this->_default_attribute_values[$field];
	}


	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Storm_Model_Abstract
	 */
	public function __set($field, $value) {
		$method = 'set'.$field;
		return $this->$method($value);

	}



	/**
	 * If has_many relationship specifies unique => true and $value in $dependents,
	 * then the constraint is violated.
	 *
	 * @param array $relationship
	 * @param array $dependents
	 * @param unknown_type $value
	 * @return boolean
	 */
	protected function _isUniqueConstraintViolated($relationship, $dependents, $value) {
		$is_unique = (array_key_exists('unique', $relationship) and (true === $relationship['unique']));
		if (!$is_unique)
			return false;

		if ($value->isNew())
			return false;

		foreach($dependents as $dependent) {
			if ($dependent->getId() == $value->getId()) 
				return true;
		}

		return false;
	}


	/**
	 * @param string $field
	 * @param unknown_type $value
	 * @return Storm_Model_Abstract
	 */
	protected function _addDependent($field, $value) {
		if (!$value) {
			return $this;
		}

		if (!array_key_exists($field, $this->_has_many)) {
			throw new Storm_Model_Exception(
													sprintf('Tried to call unknown method %s::add%s',
																	get_class($this),
																	$this->attributeNameToAccessor($this->_singularize($field))));

		}

		$dependents = $this->_get($field);
		if ($this->_isUniqueConstraintViolated($this->_has_many[$field], $dependents, $value))
			return $this;

		if (array_key_exists('through', $this->_has_many[$field])) {
			$through_field = $this->_has_many[$field]['through'];
			$role = $this->_has_many[$through_field]['role'];
			$model = $this->_has_many[$through_field]['model'];

			$intermediate = new $model;
			$intermediate->_set($role, $this);
			$intermediate->_set($this->_singularize($field), $value);

			$this->_addDependent($through_field, $intermediate);

			return $this;
		}

		$dependents[] = $value;
		$this->_set($field, $dependents);

		$role = $this->_has_many[$field]['role'];
		$value->_set($role, $this);

		return $this;

	}


	/**
	 * @param string $field
	 * @param Storm_Model_Abstract $value
	 * @param string $through_field
	 * @return Storm_Model_Abstract
	 */
	protected function _findDependentOfMeAndValueThrough($field, $value, $through_field) {
		$role = $this->_has_many[$through_field]['role'];

		$get_id_method = 'get'.ucfirst($role).'Id';

		$get_through_value_method = 'get'.ucfirst($this->_singularize($field));

		$dependents = $this->_get($through_field);

		foreach($dependents as $dependent) {
			if (($dependent->$get_id_method() == $this->getId()) && 
					($value == $dependent->$get_through_value_method())) {
				return $dependent;
			}
		}
		return null;
	}


	/**
	 * @param string $field
	 * @param Storm_Model_Abstract $value
	 * @param string $through_field
	 * @return Storm_Model_Abstract
	 */
	protected function _removeDependentThrough($field, $value, $through_field) {
		$dependent_to_remove = $this->_findDependentOfMeAndValueThrough($field,
																																		$value, 
																																		$through_field);
		if (isset($dependent_to_remove))
			$this->_removeDependent($through_field, $dependent_to_remove);

		return $this;
	}


	/**
	 * @param string $field
	 * @param unknown_type $value
	 * @return Storm_Model_Abstract
	 */
	protected function _removeDependent($field, $value) {
		if (!$value) 
			return $this;

		if (!array_key_exists($field, $this->_has_many))
			throw new Storm_Model_Exception('Tried to call unknown method '.get_class($this).'::remove'.$field);

		if (array_key_exists('through', $this->_has_many[$field]))
			return $this->_removeDependentThrough($field, $value, $this->_has_many[$field]['through']);


		$dependents = $this->_get($field);
		$dependents_without_value = array();
		foreach ($dependents as $dependent) {
			if ( (!$value->isNew() && ($dependent->getId() != $value->getId())) ||  ($value !== $dependent)) {
				$dependents_without_value []= $dependent;
			}
		}

		$this->_set($field, $dependents_without_value);
		return $this;
	}


	/**
	 * @param string $field
	 * @return array
	 */
	protected function _getDependents($field) {
		$through_dependency = array_key_exists('through', $this->_has_many[$field]);

		if ($through_dependency) {
			return $this->_getDependentsFromLoader($field);
		}

		if (array_key_exists($field, $this->_has_many_attributes)) {
			return $this->_has_many_attributes[$field];

		}

		$dependents = $this->_getDependentsFromLoader($field);

		// if not already done, set current data in db
		if (!array_key_exists($field, $this->_has_many_attributes_in_db)) {
			$this->_has_many_attributes_in_db[$field] = $dependents;

		}

		$this->_has_many_attributes[$field] = $dependents;
		return $dependents;
	}


	/**
	 * @param string $field
	 * @param string $through_field
	 * @return array
	 */
	protected function _getDependentsOfFieldThrough($field, $through_field) {
		$instances = $this->_getDependents($through_field);
		$dependents = array();

		$singularized_field = $this->_singularize($field);
		$getManyMethod = 'get' . ucfirst($field);
		$getOneMethod = 'get' . ucfirst($singularized_field);


		foreach ($instances as $instance) {
			if ($instance->hasBelongsToRelashionshipWith($singularized_field))
				$dependents []= $instance->$getOneMethod();
			else
				$dependents = array_merge($dependents, $instance->$getManyMethod());
		}
	
		return array_filter($dependents);
	}


	protected function _getLoaderForModel($model) {
		return call_user_func(array($model, 'getLoader'));
	}


	/**
	 * @param string $field
	 * @return array
	 */
	protected function _getDependentsFromLoader($field) {
		$map = $this->_has_many[$field];

		if (array_key_exists('through', $map))
			return $this->_getDependentsOfFieldThrough($field, $map['through']);

		$find_params = array('role' => $map['role'],
												 'model' => $this);

		if (array_isset('order', $map))
			$find_params['order'] = $map['order'];

		if (array_isset('scope', $map)) {
			foreach($map['scope'] as $scope_field => $scope_value)
				$find_params[$scope_field] = $scope_value;
		}
		
		$dependents = $this
			->_getLoaderForModel($map['model'])
			->findAllBy($find_params);

		return array_filter($dependents);
	}


	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Storm_Model_Abstract
	 */
	protected function _setDependents($field, $value) {
		if (array_key_exists('through', $this->_has_many[$field])) {
			$through_field = $this->_has_many[$field]['through'];
			$this->_setDependents($through_field, array());

			foreach ($value as $item) {
				$this->_addDependent($field, $item);
			}
		} else {
			$this->_has_many_attributes[$field] = $value;
		}

		return $this;
	}


	/**
	 * @param string $field
	 * @return mixed
	 */
	protected function _getDependentIdForField($field) {
		$field_id = $this->getIdFieldForDependent($field);

		if (array_key_exists($field_id, $this->_attributes))
			return $this->_attributes[$field_id];

		if (array_key_exists($field_id, $this->_default_attribute_values))
			return $this->_default_attribute_values[$field_id];

		return null;
	}


	/**
	 * @param string $field
	 */
	public function callGetterByAttributeName($attribute) {
		return call_user_func(array($this, 'get'.$this->attributeNameToAccessor($attribute)));
	}


	/**
	 * @param string $field
	 * @return Storm_Model_Abstract
	 */
	protected function _getDependent($field) {
		$through_dependency = array_key_exists('through', $this->_belongs_to[$field]);

		// delegate to a dependent
		if ($through_dependency) {
			$through_field = $this->_belongs_to[$field]['through'];

			if ($dependent = $this->_getDependent($through_field))
				return $dependent->callGetterByAttributeName($field);

			return null;
		}


		$id = $this->_getDependentIdForField($field);

		// if the instance is in cache, returns it
		if (array_key_exists($field, $this->_belongs_to_attributes)) {
			if (null == $dependent = $this->_belongs_to_attributes[$field])
				return null;
			if ($id == $dependent->getId())
				return $dependent;
		}


		if (null == $id) {
			return null;

		}

		// in runtime cache ?
		if (array_key_exists($field, $this->_belongs_to_attributes)) {
			if (null == $dependent = $this->_belongs_to_attributes[$field]) {
				return null;

			}

			if ($id == $dependent->getId()) {
				return $dependent;

			}

		}

		// otherwise in database ?
		$dependent = $this
			->_getLoaderForModel($this->_belongs_to[$field]['model'])
			->find($id);

		$this->_belongs_to_attributes[$field] = $dependent;

		return $dependent;

	}

	/**
	 * @param string $str
	 * @return string
	 */
	protected function _singularize($str) {
		return Storm_Inflector::singularize($str);
	}

	/**
	 * @param string $str
	 * @return string
	 */
	protected function _pluralize($str) {
		return Storm_Inflector::pluralize($str);
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Storm_Model_Abstract
	 */
	protected function _set($field, $value) {
		if (array_key_exists($field, $this->_has_many)) {
			return $this->_setDependents($field, $value);

		}

		if (array_key_exists($field, $this->_belongs_to)) {
			$id_field = $this->getIdFieldForDependent($field);
			$this->_attributes[$id_field] = (null == $value) ? null : $value->getId();
			$this->_belongs_to_attributes[$field] = $value;

			return $this;

		}

		$this->_attributes[$field] = $value;
		return $this;
	}

}
