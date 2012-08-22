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

class ZendAfi_Auth_Adapter_CommSigb implements Zend_Auth_Adapter_Interface {
	protected $_identity = null;
	protected $_credential = null;
	protected $_authenticated_user = null;

	public function setIdentity($identity) {
		$this->_identity = $identity;
		return $this;
	}

	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}


	public function authenticate(){
		$this->_authenticated_user = null;
		return $this->tryFetchUserFromSIGB($this->_identity, $this->_credential);
	}
	

	/**
	 * @return Class_Users
	 */
	public function tryFetchUserFromSIGB($login, $password) {
		$user = Class_Users::newInstance()
			->setLogin($login)
			->setPassword($password);

		$bibs = Class_IntBib::findAllWithWebServices();
		foreach($bibs as $bib) {
			if (!$emprunteur = $bib->getSIGBComm()->getEmprunteur($user))
				continue;

			if (!$emprunteur->isValid())
				continue;

			$user
				->beAbonneSIGB()
				->save();
			$this->_authenticated_user = $user;
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $login);
		}

		return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login);
	}


	public function getResultObject() {
		return new StdClass();
	}
}

?>