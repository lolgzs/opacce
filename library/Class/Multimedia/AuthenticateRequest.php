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
			|| !($site = $request->getParam('site'))) {
			$this->_error = 'MissingParameter';
			return $this;
		}

		if (!$user = Class_Users::getLoader()->findFirstBy(array('login' => $login))) {
			$this->_error = 'UserNotFound';
			return $this;
		}

		if (($user->getPassword() !== $password)) {
			$this->_error = 'PasswordIsWrong';
			return $this;
	  }

		if (!$user->isAbonnementValid()) {
			$this->_error = 'SubscriptionExpired';
			return $this;
    }

		$this->_user = $user;
		
		if ($location = Class_Multimedia_Location::getLoader()->findByIdOrigine($site))
			$this->_device = Class_Multimedia_Device::getLoader()
				->findByIdOrigineAndLocation($poste, $location);

		return $this->beValid();
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


	/** @return Class_Multimedia_Device */
	public function getDevice() {
		return $this->_device;
	}
}
?>