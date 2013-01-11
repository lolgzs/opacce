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

abstract class Class_WebService_SIGB_AbstractService {
	protected $_notice_cache;

	/**
	 * @return Class_WebService_SIGB_AbstractService
	 */
	public static function newInstance() {
		return new static();
	}


	public function getReservationsOf($emprunteur) {
		return array();
	}


	public function getEmpruntsOf($emprunteur) {
		return array();
	}


	abstract public function getEmprunteur($user);


	abstract public function reserverExemplaire($user, $exemplaire, $code_annexe);


	abstract public function supprimerReservation($user, $reservation_id);


	abstract public function prolongerPret($user, $pret_id);


	abstract public function getNotice($id);


	public function getPopupUrlForUserInformations($user) {
		return null;
	}


	public function getNoticeCache() {
		if (!isset($this->_notice_cache))
			$this->_notice_cache = new Class_WebService_SIGB_NoticeCache($this);
		return $this->_notice_cache;
	}


	public function cacheNotice($notice) {
		$this->getNoticeCache()->cacheNotice($notice);
	}


	public function getExemplaire($notice_id, $code_barre){
		return $this->getNoticeCache()->getExemplaire($notice_id, $code_barre);
	}


	public function saveEmprunteur($emprunteur) {}


	public function isConnected() {
		return true;
	}


	protected function _success() {
    return array('statut' => true, 'erreur' => '');
	}


	protected function _error($message) {
		return array('statut' => false,
								  'erreur' => $message);
	}

	public function isPergame() {
		return false;
	}
}

?>