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

class Multimedia_DeviceLoader extends Storm_Model_Loader {
	/**
	 * @param $json_model stdClass
	 * @param $device_group Class_Multimedia_DeviceGroup
	 * @return Class_Multimedia_DeviceGroup
	 */
	public function fromJsonModelWithGroup($json_model, $device_group) {
		$id_origine = $device_group->getLocation()->getId() . '-' . $json_model->id;
		if (!$model = $this->findFirstBy(array('id_origine' => $id_origine)))
			$model = $this->newInstance()->setIdOrigine($id_origine);
		$model
				->setLibelle($json_model->libelle)
				->setOs($json_model->os)
				->setGroup($device_group)
				->setDisabled($json_model->maintenance)
				->save();
		return $model;
	}
}


class Class_Multimedia_Device extends Storm_Model_Abstract {
	protected $_loader_class = 'Multimedia_DeviceLoader';
	protected $_table_name = 'multimedia_device';
	protected $_belongs_to = array(
		'group' => array(
			'model' => 'Class_Multimedia_DeviceGroup',
			'referenced_in' => 'id_devicegroup'));

	protected $_has_many = array(
		'holds' => array(
			'model' => 'Class_Multimedia_DeviceHold',
			'role' => 'device'));
	
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @param $start int
	 * @param $end int
	 * @return boolean
	 */
	public function isHoldableBetweenTimes($start, $end) {
		if ($this->isDisabled())
			return false;
		return !$this->hasHoldBetweenTimes($start, $end);
	}


	/**
	 * @param $start int
	 * @param $end int
	 * @return boolean
	 */
	public function hasHoldBetweenTimes($start, $end) {
		return 0 < $this->numberOfHoldBetweenTimes($start, $end);
	}

	/**
	 * @param $start int
	 * @param $end int
	 * @return int
	 */
	public function numberOfHoldBetweenTimes($start, $end) {
		return Class_Multimedia_DeviceHold::getLoader()
				->countBetweenTimesForDevice($start, $end, $this);
	}


	/** @return boolean */
	public function isDisabled() {
		return 1 == $this->getDisabled();
	}


	/** @return Class_Multimedia_Device */
	public function beDisabled() {
		$this->setDisabled(1);
		return $this;
	}


	/** @return string */
	public function getGroupLibelle() {
		return $this->getGroup()->getLibelle();
	}
}