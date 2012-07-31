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
		if (!$model = $this->findByIdOrigineAndLocation($json_model->id, $device_group->getLocation()))
			$model = $this->newInstance()->setIdOrigine($this->getIdOrigineWithLocation($json_model->id, $device_group->getLocation()));
		$model
				->setLibelle($json_model->libelle)
				->setOs($json_model->os)
				->setGroup($device_group)
				->setDisabled($json_model->maintenance)
				->save();
		return $model;
	}


	/**
	 * @param $id int
	 * @param $location Class_Multimedia_Location
	 * @return Class_Multimedia_Device
	 */
	public function findByIdOrigineAndLocation($id, $location) {
		return $this->findFirstBy(array('id_origine' => $this->getIdOrigineWithLocation($id, $location)));
	}


	/**
	 * @param $id int
	 * @param $location Class_Multimedia_Location
	 * @return string
	 */
	public function getIdOrigineWithLocation($id, $location) {
		return $location->getId() . '-' . (int)$id;
	}
}


class Class_Multimedia_Device extends Storm_Model_Abstract {
	/** @var Class_TimeSource */
	protected static $_time_source;
		
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


	/**
	 * @param $user Class_Users
	 * @return Class_Multimedia_DeviceHold
	 */
	public function getCurrentHoldForUser($user) {
		if (null !== ($hold = $this->getCurrentHold())
			and $user->getId() == $hold->getIdUser())
			return $hold;

		return $this->autoHoldByUser($user, $hold);
	}


	/**
	 * @param $user Class_Users
	 * @param $current_hold Class_Multimedia_DeviceHold
	 * @return Class_Multimedia_DeviceHold
	 */
	public function autoHoldByUser($user, $current_hold) {
		// pas de résa auto, on sort
		if (!$this->isAutoholdEnabled())
			return null;

		// une résa courante et on est dans le délai d'auth, on sort
		if (null !== $current_hold
			and $this->getCurrentTime() <= ($current_hold->getStart() + (60 * $this->getAuthDelay())))
			return null;

		// si je n'ai pas de début de créneau, on sort
		if (null == ($start = $this->getPreviousStartTime()))
			return null;

		// fin de créneau par défaut selon config
		$end = $start + (60 * $this->getAutoholdSlotsMax() * $this->getSlotSize());
				
		// si on dépasse la fin de journée on se limite à la fin de journée
		if ($end > ($next_closing = $this->getMaxTimeForToday()))
			$end = $next_closing;
				
		// si on dépasse la prochaine résa on se limite au début de la prochaine résa
		if (null != ($next_start = $this->getNextHoldStart())
			and $end > $next_start)
			$end = $next_start;

		if ($end <= $start)
			return null;

		$hold = Class_Multimedia_DeviceHold::getLoader()
				->newInstance()
				->setDevice($this)
				->setUser($user)
				->setStart($start)
				->setEnd($end);
		$hold->save();
		return $hold;
	}


	/** @return Class_Multimedia_DeviceHold */
	public function getCurrentHold() {
		return Class_Multimedia_DeviceHold::getLoader()
				->getHoldOnDeviceAtTime($this, $this->getCurrentTime());
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


	/** @return int */
	public function getAuthDelay() {
		return $this->getGroup()->getAuthDelay();
	}


	/** @return boolean */
	public function isAutoholdEnabled() {
		return $this->getGroup()->isAutoholdEnabled();
	}


	/** @return int */
	public function getPreviousStartTime() {
		return $this->getGroup()->getPreviousStartTime();
	}


	/** @return int */
	public function getNextHoldStart() {
		$from = $this->getCurrentTime();
		$to = $this->getMaxTimeForToday();
		if (null != ($hold = Class_Multimedia_DeviceHold::getLoader()
				                   ->getFirstHoldOnDeviceBetweenTimes($this, $from, $to)))
			return $hold->getStart();
	}


	/** @return int */
	public function getMaxTimeForToday() {
		return $this->getGroup()->getMaxTimeForToday();
	}


	/** @return int */
	public function getAutoholdSlotsMax() {
		return $this->getGroup()->getAutoholdSlotsMax();
	}


	/** @return int */
	public function getSlotSize() {
		return $this->getGroup()->getSlotSize();
	}
}