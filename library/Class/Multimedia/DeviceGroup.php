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

class Multimedia_DeviceGroupLoader extends Storm_Model_Loader {
	/**
	 * @param $json_model stdClass
	 * @param $location Class_Multimedia_Location
	 * @return Class_Multimedia_DeviceGroup
	 */
	public function fromJsonModelWithLocation($json_model, $location) {
		$id_origine = $location->getId() . '-' . $json_model->id;
		if (!$model = $this->findFirstBy(array('id_origine' => $id_origine)))
			$model = $this->newInstance()->setIdOrigine($id_origine);
		$model
				->setLibelle($json_model->libelle)
				->setLocation($location)
				->save();
		return $model;
	}
}


class Class_Multimedia_DeviceGroup extends Storm_Model_Abstract {
	protected $_loader_class = 'Multimedia_DeviceGroupLoader';
	protected $_table_name = 'multimedia_devicegroup';

	protected $_belongs_to = array(
		'location' => array(
			'model' => 'Class_Multimedia_Location',
			'referenced_in' => 'id_location'));

	protected $_has_many = array(
		'devices' => array(
			'model' => 'Class_Multimedia_Device',
			'role' => 'group'));


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @param $date string
	 * @param $time string
	 * @param $duration string
	 * @return array
	 */
	public function getHoldableDevicesForDateTimeAndDuration($date, $time, $duration) {
		$devices = $this->getDevices();
		$holdables = array();
		$start = strtotime($date . ' ' . $time . ':00');
		$end = $start + (60 * $duration);

		foreach ($devices as $device) {
			if (!$device->isHoldableBetweenTimes($start, $end))
				continue;
			$holdables[] = $device;
			if (3 == count($holdables))
				return $holdables;
		}
		return $holdables;
	}
}