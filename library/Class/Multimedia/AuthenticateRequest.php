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

class Class_Multimedia_AuthenticateRequest {
	/** @var boolean */
	protected $_valid = false;

	/** @var string */
	protected $_error = '';

	/** @var Class_Users */
	protected $_user;

	/** @var Class_Multimedia_Device */
	protected $_device;


	/**
	 * @param Zend_Controller_Request_Abstract
	 * @return Class_Multimedia_AuthenticateRequest
	 */
	public static function newWithRequest($request) {
		$instance = new self();
		return $instance->validate($request);
	}


	/**
	 * @param Zend_Controller_Request_Abstract
	 * @return Class_Multimedia_AuthenticateRequest
	 */
	public function validate($request) {
		if (!($login = $request->getParam('login'))
			|| !($password = $request->getParam('password'))
			|| !($poste = $request->getParam('poste'))
			|| !($site = $request->getParam('site')))
			return $this->_error('MissingParameter');

		$auth = ZendAfi_Auth::getInstance(); 
		if (!$auth->authenticateLoginPassword($login, 
																					$password, 
																					[$auth->newAuthSIGB(), $auth->newAuthDb()])) {
			if (Class_Users::findFirstBy(['login' => $login]))
					return $this->_error('PasswordIsWrong');
			return 	$this->_error('UserNotFound');
		}

		$user = Class_Users::getIdentity();

		if (!$user->isAbonnementValid())
			return $this->_error('SubscriptionExpired');

		$this->_user = $user;

		if ($location = Class_Multimedia_Location::getLoader()->findByIdOrigine($site))
			$this->_device = Class_Multimedia_Device::getLoader()->findByIdOrigineAndLocation($poste, $location);
		
		if (!$this->_device)
			return $this->_error('DeviceNotFound');

		if (!$this->getCurrentHold())
			return $this->_error('DeviceNotHeldByUser');

		return $this->beValid();
	}


	/**
	 * @return Class_Multimedia_DeviceHold
	 */
	public function getCurrentHold() {
		if (!isset($this->_current_hold) && isset($this->_device) && isset($this->_user))
			$this->_current_hold = $this->_device->getCurrentHoldForUser($this->_user);
		return $this->_current_hold;
	}


	/**
	 * @return string
	 */
	public function getCurrentHoldEnd() {
		return $this->getCurrentHold()->getEnd();
	}
	

	/** @return boolean */
	public function isValid() {
		return $this->_valid;
	}


	/** @return Class_Multimedia_AuthenticateRequest */
	public function beValid() {
		$this->_valid = true;
		return $this;
	}


	/** @return string */
	public function getError() {
		return $this->_error;
	}


	/** @return Class_Users */
	public function getUser() {
		return $this->_user;
	}


	/** 
	 * @param string $code
	 * @return Class_Multimedia_AuthenticateRequest
	 */
	protected  function _error($code) {
		$this->_error = $code;
		return $this;
	}
}
?>