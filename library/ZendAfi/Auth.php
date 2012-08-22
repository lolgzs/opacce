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

class ZendAfi_Auth extends Zend_Auth {
	public static function getInstance()  {
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public static function setInstance($instance) {
		self::$_instance = $instance;
		return self::$_instance;
	}


	public function getOrderedAdaptersForLoginPassword($login, $password) {
		return  [ $this->newAuthDb(), $this->newAuthSIGB() ];
	}

	
	public function newAuthDb() {
		$authAdapter = new ZendAfi_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
		$authAdapter->setTableName('bib_admin_users');
		$authAdapter->setIdentityColumn('LOGIN');
		$authAdapter->setCredentialColumn('PASSWORD');
		return $authAdapter;
	}


	public function newAuthSIGB() {
		return new ZendAfi_Auth_Adapter_CommSigb();
	}


	public function authenticateLoginPassword($login, $password, $adapters = null) {
		if (!$adapters)
			$adapters = $this->getOrderedAdaptersForLoginPassword($login, $password);

		foreach ($adapters as $authAdapter) {
			$authAdapter->setIdentity($login);
			$authAdapter->setCredential($password);
	
			if (!$this->authenticate($authAdapter)->isValid()) continue;
			$this->getStorage()->write($authAdapter->getResultObject());
			return true;
		}
		return false;
	}
}

?>