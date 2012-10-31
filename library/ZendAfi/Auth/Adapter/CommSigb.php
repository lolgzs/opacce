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

	/**
	 * @param string $identity
	 * @return Zend_Auth_Adapter_CommSigb
	 */
	public function setIdentity($identity) {
		$this->_identity = $identity;
		return $this;
	}

	/**
	 * @param string $credential
	 * @return Zend_Auth_Adapter_CommSigb
	 */
	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}


	/**
	 * @return Zend_Auth_Result
	 */
	public function authenticate() {
		$this->_authenticated_user = null;

		$matching_users_in_db = Class_Users::findAllBy(['login' => $this->_identity, 
																										'role_level' => ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB]);
		if (1 == count($matching_users_in_db))
			$user = $matching_users_in_db[0];
		else
			$user = Class_Users::newInstance()
				->setLogin($this->_identity)
				->setPassword($this->_credential)
				->beAbonneSIGB();

		$result = $this->authenticateUserFromSIGB($user);
		if ($result->isValid())
			$this->_authenticated_user = $user;

		return $result;
	}
	

	/**
	 * @param $user Class_Users 
	 * @return Zend_Auth_Result
	 */
	public function authenticateUserFromSIGB($user) {
		$bibs = Class_IntBib::findAllWithWebServices();
		foreach($bibs as $bib) {
			if (!$emprunteur = $bib->getSIGBComm()->getEmprunteur($user))
				continue;

			if (!$emprunteur->isValid())
				continue;

			$emprunteur->updateUser($user);
			$user->setIdSite($bib->getId());

			if (!$user->save())
				continue;

			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user->getLogin());
		}

		return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $user->getLogin());
	}


	/**
	 * @return Std_Class
	 */
	public function getResultObject() {
		$result = new StdClass();

		$fields = $this->_authenticated_user->toArray();
		foreach($fields as $field => $value) {
			$prop_name = strtoupper($field);
			$result->$prop_name = $value;
		}
		return $result;
	}
}

?>