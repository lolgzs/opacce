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
class Class_Webservice_SIGB_Nanook_Service extends Class_WebService_SIGB_AbstractRESTService {
	/**
	 * @param string $server_root
	 * @return Class_WebService_SIGB_AbstractRESTService
	 */
	public function setServerRoot($server_root) {
		if ('/' !== substr($server_root, -1))
			$server_root .= '/';
		return parent::setServerRoot($server_root);
	}

	
	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteur($user) {
		$this->_authenticate($user);
		return $this->ilsdiGetPatronInfo(array('patronId' => $user->getIdSigb()),
																		 Class_WebService_SIGB_Nanook_PatronInfoReader::newInstance());
	}


	/**
	 * @param Class_Users $user
	 * @param int $notice_id
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($user, $exemplaire, $code_bib_or_annexe) {
		$code_annexe = $code_bib_or_annexe;
		if ($annexe = Class_CodifAnnexe::getLoader()->findFirstBy(array('id_bib' => $code_bib_or_annexe)))
			$code_annexe = $annexe->getCode();

		return $this->ilsdiHoldTitle(
																 array('bibId'					=> $exemplaire->getIdOrigine(),
																			 'patronId'       => $user->getIdSigb(),
																			 'pickupLocation'	=> $code_annexe));
	}


	/**
	 * @param Class_Users $user
	 * @param int $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id) {
		return $this->ilsdiCancelHold(array(
																				'patronId'	=> $user->getIdSigb(),
																				'itemId'		=> $reservation_id));
	}


	/**
	 * @param Class_Users $user
	 * @param int $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id) {
		return $this->ilsdiRenewLoan(array(
																			 'patronId'	=> $user->getIdSigb(),
																			 'itemId'		=> $pret_id));
	}


	/**
	 * @param string $id
	 * @return Class_WebService_SIGB_Notice
	 */
	public function getNotice($id) {
		try {
			return $this->ilsdiGetRecords($id, 
				Class_WebService_SIGB_Nanook_GetRecordsResponseReader::newInstance());
		} catch (Exception $e) {
			return;
		}
	}


	/**
	 * @param array $options
	 * @return string
	 */
	public function buildQueryURL($options) {
		$parts = array();
		foreach ($options as $key => $value) {
				$value = urlencode($value);
				$parts[] = $key . '/' . $value;
		}

		return $this->getServerRoot() . implode('/', $parts);
	}


	/**
	 * @param $user Class_Users
	 */
	protected function _authenticate($user) {
		if (null != $user->getIdSigb())
			return;

		$this->ilsdiAuthenticatePatron($user);
	}
}
?>