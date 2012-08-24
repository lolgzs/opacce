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

trait Trait_Translator {
	protected $_translate;

	/**
	 * @param string $libelle
	 * @return string
	 */
	public function traduire($libelle)	{
		return $this->_($libelle);
	}

	/**
	 * @return Zend_Translate
	 */
	public function _translate() {
		if (!$this->_translate)
			$this->_translate = Zend_Registry::get('translate');
		return $this->_translate;
	}

	/**
	 * @return string
	 */
	public function _()	{
		$args = func_get_args();
		if ('' == $args[0]) 
			return '';
		return call_user_func_array(array($this->_translate(), '_'), $args);
  }

	/**
	 * @return string
	 */
	public function _plural()	{
		$args = func_get_args();
		return call_user_func_array(array($this->_translate(), 'plural'), $args);
	}
}

?>