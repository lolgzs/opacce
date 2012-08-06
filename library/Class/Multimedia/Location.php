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
	 * @param $from int
	 * @param $to int
	 * @return array
	 */
	public function getPossibleHours($increment, $from = null, $to = null) {
		if (0 == $increment)
			return array();

		$steps = $this->getPossibleTimes($increment, $from, $to);

		$hours = array();
		foreach ($steps as $step)
			$hours[date('H:i', $step)] = date('H\hi', $step);

		return $hours;
	}


	/**
	 * @param $increment int minutes
	 * @param $from int timestamp
	 * @param $to int timestamp
	 * @return array
	 */
	public function getPossibleTimes($increment, $from = null, $to = null) {
		if (null == $from)
			$from = strtotime('today');

		if (null == $to)
			$to = strtotime('tomorrow');

		if ($from > $to)
			return array();
				
		return range($from, $to, 60 * $increment);
	}


	/**
	 * @param $json_model stdClass
	 * @return Class_Multimedia_Location
	 */
	public function fromJsonModel($json_model) {
		if (!$model = $this->findByIdOrigine($json_model->id))
			$model = $this->newInstance()->setIdOrigine((int)$json_model->id);
		$model->setLibelle($json_model->libelle)->save();
		return $model;
	}


	/**
	 * @param int
	 * $return Class_Multimedia_Location
	 */
	public function findByIdOrigine($id) {
		return $this->findFirstBy(array('id_origine' => (int)$id));
	}
}


class Class_Multimedia_Location extends Storm_Model_Abstract {
	/** @var Class_TimeSource */
	protected static $_time_source;
	
	protected $_loader_class = 'Multimedia_LocationLoader';
	protected $_table_name = 'multimedia_location';
	protected $_has_many = ['groups' => ['model' => 'Class_Multimedia_DeviceGroup',
																			 'role' => 'location',
																			 'order' => 'libelle'],

													'devices' => ['through' => 'groups'],

													'ouvertures' => ['through' => 'bib']];

	protected $_belongs_to = ['bib' => ['model' => 'Class_Bib',
																			'referenced_in' => 'id_site']];

	protected $_default_attribute_values = ['days' => ''];


	/**
	 * @return string
	 */
	public function getLibelleBib() {
		if ($this->hasBib())
			return $this->getBib()->getLibelle();
		return '';
	}
	

	/**
	 * @param $date string (YYYY-MM-DD)
	 * @return array
	 */
	public function getStartTimesForDate($date) {
		$min_time = $this->getMinTimeForDate($date);
		$start_times = $this->getLoader()->getPossibleHours($this->getSlotSize(),
			                                                  $min_time,
			                                                  $this->getMaxTimeForDate($date));

		if ($min_time < ($current = $this->getCurrentTime())) {
			$hour = (int) date('H', $current);
			$minute = (int) date('i', $current);
			$i = 0;
 
			foreach (array_keys($start_times) as $time) {
				$parts = explode(':', $time);
				if ($hour <= (int)$parts[0] and $minute <= (int)$parts[1])
					break;
				++$i;
			}

			$start_times = array_slice($start_times, $i);
		}

		return $start_times;
	}


	/** @return int */
	public function getPreviousStartTime() {
		$current = $this->getCurrentTime();
		$times = $this->getLoader()->getPossibleTimes($this->getSlotSize());

		if (0 == count($times))
			return null;

		$previous = reset($times);
		foreach ($times as $time) {
			if ($time > $current)
				return $previous;
			$previous = $time;
		}

		return null;
	}


	/**
	 * @category testing
	 * @return int
	 */
	public function getCurrentTime() {
		return self::getTimeSource()->time();
	}


	/** @return Class_TimeSource */
	public static function getTimeSource() {
		if (null == self::$_time_source)
			self::$_time_source = new Class_TimeSource();
		return self::$_time_source;
	}


	/** @param $time_source Class_TimeSource */
	public static function setTimeSource($time_source) {
		self::$_time_source = $time_source;
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


	/** @return int */
	public function getMaxTimeForToday() {
		return $this->getMaxTimeForDate(date('Y-m-d', $this->getCurrentTime()));
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


	/** @return boolean */
	public function isAutoholdEnabled() {
		return 1 == $this->getAutohold();
	}
}