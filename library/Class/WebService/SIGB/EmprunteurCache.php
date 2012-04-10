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

class Class_WebService_SIGB_EmprunteurCache {
	protected $_cache;

	public static function newInstance() {
		return new self(Zend_Registry::get('cache'));
	}


	/**
	 * @param $cache Zend_Cache
	 */
	public function __construct($cache) {
		$this->_cache = $cache;
	}


	/**
	 * @param $user Class_User
	 * @return string
	 */
	public function keyFor($user) {
		return md5('emprunteur_'.$user->getId());
	}


	/**
	 * @param $user Class_User
	 * @return boolean
	 */
	public function isCached($user) {
		return $this->_cache->test($this->keyFor($user));
	}


	/**
	 * @param $user Class_User
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function load($user) {
		return unserialize($this->_cache->load($this->keyFor($user)));
	}


	/**
	 * @param $user Class_User
	 * @param $emprunteur Class_WebService_SIGB_Emprunteur
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function save($user, $emprunteur) {
		$this->_cache->save(serialize($emprunteur), $this->keyFor($user));
		return $emprunteur;
	}


	/**
	 * @param $user Class_User
	 * @param $sigb Class_WebService_SIGB_AbstractService subclass
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function loadFromCacheOrSIGB($user, $sigb) {
		if ($this->isCached($user))
			return $this->load($user);

		return $this->save($user, $sigb->getEmprunteur($user));
	}


	/**
	 * @param $user Class_User
	 * @return Class_WebService_SIGB_EmprunteurCache
	 */
	public function remove($user) {
		$this->_cache->remove($this->keyFor($user));
		return $this;
	}
}

?>