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

class Class_Multimedia_ReservationBean extends StdClass {
	public static function sessionNameSpace()  {
		return new Zend_Session_Namespace('abonneController');
	}

	public static function newInSession() {
		$bean = new self();
		self::sessionNameSpace()->holdBean = $bean;
		return $bean;
	}


	public static function current() {
		if (null == ($bean = self::sessionNameSpace()->holdBean))
			return self::newInSession();
		return $bean;
	}


	public function __construct() {
		$this->location = 0;
		$this->day = '';
		$this->time = '';
		$this->duration = 0;
		$this->group = 0;
		$this->device = 0;
	}


	public function currentState() {
		if (null == $this->getLocation())
			return 'multimedia-hold-location';

		if ('' == $this->day)
			return 'multimedia-hold-day';

		if ('' == $this->time || 0 == $this->duration)
			return 'multimedia-hold-hours';

		if (null == $this->getGroup())
			return 'multimedia-hold-group';

		if (null == $this->getDevice())
			return 'multimedia-hold-device';

		return 'multimedia-hold-confirm';
	}


	public function isCurrentStateValidForRequest($request) {
		foreach(['location', 'day', 'time', 'group', 'device'] as $param)
			$this->$param = $request->getParam($param, $this->$param);

		return $request->getActionName() == $this->currentState();
	}

	
	public function getGroups() {
		return $this->getLocation()->getGroups();
	}


	public function getLocation() {
		return Class_Multimedia_Location::find((int)$this->location);
	}

	public function getGroup() {
		return Class_Multimedia_DeviceGroup::find((int)$this->group);
	}


	public function getDevice() {
		return Class_Multimedia_Device::getLoader()->find((int)$this->device);
	}
}

?>