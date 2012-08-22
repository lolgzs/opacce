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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Connection abonnés
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable {
	/**
	 * _authenticateCreateSelect() - This method creates a Zend_Db_Select object that
	 * is completely configured to be queried against the database.
	 *
	 * @return Zend_Db_Select
	 */
	protected function _authenticateCreateSelect() {
		// build credential expression
		if (empty($this->_credentialTreatment) || (strpos($this->_credentialTreatment, "?") === false)) {
			$this->_credentialTreatment = '?';
		}

		$credentialExpression = new Zend_Db_Expr(
																						 '(CASE WHEN ' . 
																						 $this->_zendDb->quoteInto(
																																			 $this->_zendDb->quoteIdentifier($this->_credentialColumn, true)
																																			 . ' = ' . $this->_credentialTreatment, $this->_credential
																																			 )
																						 . ' THEN 1 ELSE 0 END) AS '
																						 . $this->_zendDb->quoteIdentifier('zend_auth_credential_match')
																						 );

		// get select
		$dbSelect = $this->_zendDb->select();
		$dbSelect
			->from($this->_tableName, array('*', $credentialExpression))
			->where($this->_zendDb->quoteIdentifier($this->_identityColumn, true) . ' = ?', $this->_identity)
			->where($this->_zendDb->quoteIdentifier($this->_credentialColumn, true) . ' = ?', $this->_credential)
			->where($this->_zendDb->quoteIdentifier($this->_credentialColumn, true) . ' <> ?', '');

		return $dbSelect;
	}


	public function getResultObject() {
		return $this->getResultRowObject(null,'password');
	}
}

?>

