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


/**
 * Load data from database for a given model
 *
 * Model has to
 * 	- extend Storm_Model_Abstract
 * 	- define its table name
 * 	- implement static getLoader (will be deprecated when PHP 5.3 compatibility arise)
 *
 * @example
 * class MyModel extends Storm_Model_Abstract {
 * 	public $_table_name='my_model';
 *
 * 	public static function getLoader() {
 * 		return self::getLoaderFor(__CLASS__);
 * 	}
 * }
 */
class Storm_Model_Loader {
	/**
	 * @var string
	 */
	protected $_model;

	/**
	 * @var Storm_Model_Table
	 */
	protected $_table;

	/**
	 * @var array
	 */
	protected $_loaded_instances = array();

	/**
	 * @param string $class
	 */
	public function __construct($class) {
		$this->_model = $class;
		$this->_id_field = strtolower($this->_class_var($class,'_table_primary'));

		if ($this->_id_field == null) {
			$this->_id_field = 'id';
		}

	}

	/**
	 * @param string $class
	 * @param string $var
	 * @return mixed
	 */
	protected function _class_var($class, $var) {
		$reflection = new ReflectionClass($class);
		$class_vars = $reflection->getdefaultProperties();

		return $class_vars[$var];
	}


	/**
	 * @param Storm_Model_Table $tbl
	 * @return Storm_Model_Loader
	 */
	public function setTable($tbl) {
		$this->_table = $tbl;
		return $this;

	}

	/**
	 * @return Storm_Model_Table
	 */
	public function getTable() {
		if (!isset($this->_table)) {
			$table_name = $this->_class_var($this->_model,'_table_name');
			$this->_table = new Storm_Model_Table(array('name' => $table_name));

		}

		return $this->_table;

	}

	/**
	 * @return string
	 */
	public function getIdField() {
		return $this->_id_field;
	}

	/**
	 * @param mixed $select
	 * @return array
	 */
	public function findAll($select=null) {
		if (is_string($select)) {
			$rowset = $this
				->getTable()
				->getAdapter()
				->fetchAll($select);

		} else {
			$rowset = $this
				->getTable()
				->fetchAll($select);

			if ($rowset == null) {
				return array();

			}

			$rowset = $rowset->toArray();

		}

		$instances = array();

		foreach ($rowset as $row) {
			$instances []= $this->newFromRow($row);

		}

		return $instances;
	}

	/**
	 * @param int $id
	 * @return Storm_Model_Abstract
	 */
	public function find($id) {
		if (array_key_exists($id, $this->_loaded_instances)) {
			return $this->_loaded_instances[$id];
		}

		$rowset = $this->getTable()->find($id)->toArray();

		if (count($rowset) > 0) {
			$instance = $this->newFromRow($rowset[0]);
			$this->cacheInstance($instance);

			return $instance;
		}

		return null;
	}


	/**
	 * Add a model into runtime cache
	 *
	 * @param Storm_Model_Abstract $instance
	 * @return Storm_Model_Loader
	 */
	public function cacheInstance($instance) {
		$this->_loaded_instances[$instance->getId()] = $instance;
		return $this;

	}


	/**
	 * @return a new instance of my model
	 */
	public function newInstance() {
		$class = $this->_model;
		return new $class();
	}


	/**
	 * Create a new instance and cache it
	 * @param mixed $id primary key
	 * @return Storm_Model_Abstract
	 */
	public function newInstanceWithId($id) {
		$instance =  $this
			->newInstance()
			->setId($id);
		$this->cacheInstance($instance);
		return $instance;
	}


	/**
	 * Create an instance and assigns its
	 * attributes using an associative array
	 * (name_of_attribute => value)
	 *
	 * @param array $row
	 * @return Storm_Model_Abstract
	 */
	public function newFromRow($row) {
		$row = array_change_key_case($row, CASE_LOWER);
		$id = $row[$this->getIdField()];
		unset($row[$this->getIdField()]);

		return $this
			->newInstanceWithId($id)
			->initializeAttributes($row);
	}


	/**
	 * Insert (if new) or update the record in DB
	 *
	 * @param Storm_Model_Abstract $model
	 * @return int
	 */
	public function save($model) {
		$data = $model->attributesToArray();
		$id = $data['id'];
		unset($data['id']);

		if ($model->isNew()) {
			if ($result = $this->getTable()->insert($data))
				$model->setId($this->getTable()->getAdapter()->lastInsertId());
			return $result;
		}

		$data[$this->_id_field] = $id;

		return $this->getTable()
					->update($data, $this->_id_field . "='" . $id . "'");

	}

	/**
	 * @param Storm_Model_Abstract $model
	 *
	 */
	public function delete($model) {
		$this->getTable()
			->delete($this->_id_field . '=' . $model->getId());

	}


	/**
	 * @param string $field
	 * @return string
	 */
	protected function _getIdFieldForDependent($field) {
		$model_instance = new $this->_model;
		return $model_instance->getIdFieldForDependent($field);

	}


	/**
	 * @param array $args
	 * @return array
	 *
	 * @example
	 * Class_NewsletterSubscription::getLoader()->findAllBy(array(
	 * 		'role' => 'newsletter',
	 * 		'model' => $concerts
	 * ));
	 * Return related rows
	 *
	 * @example
	 * Class_AvisNotice::getLoader()->findAllBy(array(
	 * 		'order' => 'date_avis desc',
	 * 		'limit' => 10,
	 *    'where' => 'note>2'
	 * ));
	 * Return first 10 rows ordered by date where note > 2
	 *
	 * @example
	 * Class_AvisNotice::getLoader()->findAllBy(array(
	 * 		'clef_oeuvre' => 'MILLENIUM',
	 * 		'user_id' => '12'
	 * ));
	 * Return rows where clef_oeuvre = 'MILLENIUM' and user_id = '12'
	 *
	 */
	public function findAllBy($args) {
		if ($select = $this->_generateSelectFor($args))
			return $this->findAll($select);
		return array();
	}


	/**
	 * Sends a count request and returns the value
	 * @param array $args
	 * @return int
	 */
	public function countBy($args) {
		if (!$select = $this->_generateSelectFor($args))
			return 0;

		$select->from($this->getTable(),
									array(sprintf('count(%s) as numberof', $this->getIdField())));
		$rows = $this->getTable()->fetchAll($select)->toArray();
		return $rows[0]['numberof'];
	}


	public function _generateSelectFor($args) {
		if (array_key_exists('role', $args) && array_key_exists('model', $args)) {
			$model = $args['model'];
			$role = $args['role'];
			unset($args['model']);
			unset($args['role']);
						
			if ($model->isNew()) return null;

			$select = $this->getTable()->select();
			$field = $this->_getIdFieldForDependent($role);
			$select = $select->where($field . '=?', $model->getId());
		}
		else
			$select = $this->getTable()->select();

		foreach ($args as $field => $value) {
			switch($field) {
				case 'order':
					$select->order($value);
					break;
				case 'limit':
					$select->limit($value);
					break;
				case 'where':
					$select->where($value);
					break;
				default:
					$comparison = is_array($value) ? ' in (?)' : '=?';
					$select->where($field.$comparison, $value);
			}
		}

		return $select;
	}


	/**
	 * @param array $args
	 * @return Storm_Model_Abstract
	 */
	public function findFirstBy($args) {
		$args['limit'] = 1;
		$instances = $this->findAllBy($args);

		if (count($instances) == 0) {
			return null;

		}

		return array_first($instances);

	}
}
