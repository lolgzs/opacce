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

class Class_WebService_SIGB_Koha_Service extends Class_WebService_SIGB_AbstractRESTService {
	public static function newInstance() {
		return new self();
	}


	public static function getService($server_root) {
		return self::newInstance()->setServerRoot($server_root);
	}


	/**
	 * @param Class_Users $user
	 * @return int
	 */
	protected function _authenticate($user) {
		$xml_auth = $this->httpGet(array('service' => 'LookupPatron',
																		 'id' => $user->getLogin(),
																		 'id_type' => 'cardnumber'));

		return $this->_getTagData($xml_auth, 'id');
	}


	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteur($user) {
		return $this->ilsdiGetPatronInfo(array('patron_id' => $this->_authenticate($user),
																					 'show_contact' => 0,
																					 'show_loans' => 1,
																					 'show_holds' => 1),
																		 Class_WebService_SIGB_Koha_PatronInfoReader::newInstance());
	}


	/**
	 * @param Class_Users $user
	 * @param Class_Exemplaire $exemplaire
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($user, $exemplaire, $code_annexe) {
		return $this->ilsdiHoldTitle(
																 array('patron_id' => $this->_authenticate($user),
																			 'bib_id' => $exemplaire->getIdOrigine(),
																			 'request_location' => '127.0.0.1'),
																 'code');
	}


	/**
	 * @param Class_Users $user
	 * @param int $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id) {

		$emprunteur_id = $this->_authenticate($user);

		$xml_cancel = $this->httpGet(array('service' => 'CancelHold',
																			 'patron_id' => $emprunteur_id,
																			 'item_id' => $reservation_id));

		$code = $this->_getTagData($xml_cancel, 'code');

		if ($code == 'Canceled')
			return $this->_success();

		return $this->_error($code);
	}


	/**
	 * @param Class_Users $user
	 * @param int $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id) {
		return $this->ilsdiRenewLoan(array(
																			 'patron_id'	=> $this->_authenticate($user),
																			 'item_id'		=> $pret_id),
																 'message');
	}


	public function getNotice($id) {
		return $this->ilsdiGetRecords($id, 
																	Class_WebService_SIGB_Koha_GetRecordsResponseReader::newInstance());
	}
}

?>