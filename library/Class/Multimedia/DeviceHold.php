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
class Multimedia_DeviceHoldloader extends Storm_Model_Loader {
	/**
	 * @param $bean stdClass
	 * @return Class_Multimedia_DeviceHold
	 */
	public function newFromBean($bean) {
		$device = Class_Multimedia_Device::getLoader()->find((int)$bean->device);
		$user = Class_Users::getLoader()->getIdentity();
		$start = $this->getTimeFromDayAndTime($bean->day, $bean->time);
		$end = $this->getTimeFromStartAndDuration($start, $bean->duration);

		return $this->newInstance()
				->setDevice($device)
				->setUser($user)
				->setStart($start)
				->setEnd($end);
	}


	/**
	 * @param $day string
	 * @param $time string
	 * @return int
	 */
	public function getTimeFromDayAndTime($day, $time) {
		return strtotime($day . ' ' . $time . ':00');
	}


	/**
	 * @param $start int
	 * @param $duration int minutes
	 * @return int
	 */
	public function getTimeFromStartAndDuration($start, $duration) {
		return $start + ($duration * 60);
	}


	/**
	 * @param $user Class_Users
	 * @return array
	 */
	public function getFutureHoldsOfUser($user) {
		return $this->findAll($this->getTable()->select()
			                       ->where('id_user = ' . $user->getId())
			                       ->where('start > ' . time())
			                       ->order('start asc'));
	}


	/**
	 * @param $start int
	 * @param $end int
	 * @param $device Class_Multimedia_Device
	 * @return int
	 */
	public function countBetweenTimesForDevice($start, $end, $device) {
		return $this->_countBetweenTimesWithOptions($start, $end, array('role' => 'device',
				                                                            'model' => $device));
	}


	/**
	 * @param $start int
	 * @param $end int
	 * @param $user Class_Users
	 * @return int
	 */
	public function countBetweenTimesForUser($start, $end, $user) {
		return $this->_countBetweenTimesWithOptions($start, $end, array('role' => 'user',
				                                                            'model' => $user));
	}


		/**
	 * @param $start int
	 * @param $end int
	 * @param $options array
	 * @return int
	 */
	protected function _countBetweenTimesWithOptions($start, $end, $options) {
		return $this->countBy(array_merge($options, array(
				'where' => '(start <= ' . $start . ' and end >= ' . $end . ')'
									 . ' or (start > ' . $start . ' and end < ' . $end . ')'
									 . ' or (start < ' . $end . ' and end > ' . $end . ')'
				           . ' or (start < ' . $start . ' and end > ' . $start . ')')));
	}
}


class Class_Multimedia_DeviceHold extends Storm_Model_Abstract {
	protected $_loader_class = 'Multimedia_DeviceHoldLoader';
	protected $_table_name = 'multimedia_devicehold';
	protected $_belongs_to = array(
		'device' => array(
			'model' => 'Class_Multimedia_Device',
			'referenced_in' => 'id_device'),
		'user' => array(
			'model' => 'Class_Users',
			'referenced_in' => 'id_user'));

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}
}
?>