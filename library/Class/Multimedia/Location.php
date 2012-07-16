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

class Multimedia_LocationLoader extends Storm_Model_Loader {
	/**
	 * @return array
	 */
	public function getPossibleDays() {
		$days = array();
		$day = strtotime('next monday');
		for ($i = 0; $i < 7; ++$i) {
			$days[strftime('%w', $day)] = strftime('%A', $day);
			$day = strtotime('+1 day', $day);
		}
		return $days;
	}


	/**
	 * @param $increment int
	 * @return array
	 */
	public function getPossibleHours($increment, $from = null, $to = null) {
		if (0 == $increment)
			return array();

		if (null == $from)
			$from = strtotime('today');

		if (null == $to)
			$to = strtotime('tomorrow');

		$steps = range($from, $to, 60 * $increment);

		$hours = array();
		foreach ($steps as $step)
			$hours[date('H:i', $step)] = date('H\hi', $step);

		return $hours;
	}


	/**
	 * @param $json_model stdClass
	 * @return Class_Multimedia_Location
	 */
	public function fromJsonModel($json_model) {
		if (!$model = $this->findFirstBy(array('id_origine' => (int)$json_model->id)))
			$model = $this->newInstance()->setIdOrigine((int)$json_model->id);
		$model->setLibelle($json_model->libelle)->save();
		return $model;
	}
}


class Class_Multimedia_Location extends Storm_Model_Abstract {
	protected $_loader_class = 'Multimedia_LocationLoader';
	protected $_table_name = 'multimedia_location';
	protected $_has_many = array(
			'groups' => array(
					'model' => 'Class_Multimedia_DeviceGroup',
					'role' => 'location',
					'order' => 'libelle'),
			'devices' => array('through' => 'groups'));
	
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * @param $date string (YYYY-MM-DD)
	 * @return array
	 */
	public function getStartTimesForDate($date) {
		return $this->getLoader()->getPossibleHours($this->getSlotSize(),
			                                          $this->getMinTimeForDate($date),
			                                          $this->getMaxTimeForDate($date));
	}


	/**
	 * @param $date string (YYYY-MM-DD)
	 * @return int
	 */
	public function getMinTimeForDate($date) {
		return strtotime($date . ' ' . $this->getOpenHour() . ':00');
	}


	/**
	 * @param $date string (YYYY-MM-DD)
	 * @return int
	 */
	public function getMaxTimeForDate($date) {
		return strtotime($date . ' ' . $this->getCloseHour() . ':00');
	}
	

	/** @return array */
	public function getDurations() {
		if (0 == $this->getSlotSize())
			return array();

		$durations = array();
		$steps = range($this->getSlotSize(),
			             $this->getMaxSlots() * $this->getSlotSize(),
			             $this->getSlotSize());

		$durations = array();
		foreach ($steps as $step)
			$durations[$step] = $this->_getDurationLabel($step);
		return $durations;
	}


	/**
	 * @param $date string
	 * @param $time string
	 * @param $duration string
	 * @return array
	 */
	public function getHoldableDevicesForDateTimeAndDuration($date, $time, $duration) {
		$holdables = array();
		foreach ($this->getGroups() as $group)
			$holdables += $group->getHoldableDevicesForDateTimeAndDuration($date, $time, $duration);
				
		shuffle($holdables);
		if (3 < count($holdables))
			return array_slice($holdables, 0, 3);
		return $holdables;
	}


	/** @return string formatted date YYYY-MM-DD */
	public function getMinDate() {
		if (0 == ($delay = $this->getHoldDelayMin()))
			return date('Y-m-d');
		return date('Y-m-d', strtotime('+' . $delay . ' day'));
	}

		
	/** @return string formatted date YYYY-MM-DD */
	public function getMaxDate() {
		if (0 == ($delay = $this->getHoldDelayMax()))
			$delay = 365;
		return date('Y-m-d', strtotime('+' . $delay . ' day'));
	}


	/**
	 * @return array
	 */
	public function getDaysAsArray() {
		return explode(',', $this->getDays());
	}

		
	public function beforeSave() {
		if (is_array($days = $this->getDays()))
			$this->setDays(implode(',', $days));
	}


	/**
   * @param $duration int in minutes
	 * @return string
	 */
	protected function _getDurationLabel($duration) {
		$label = '';
		if (0 < ($hours = (int)($duration / 60)))
			$label .= $hours . 'h';
		if (0 < ($minutes = (int)($duration % 60)))
			$label .= $minutes . 'mn';
		return $label;
	}
}