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
	 * @return Class_Webservice_SIGB_Nanook_Service
	 */
	public static function newInstance() {
		return new self();
	}


	/**
	 * @param string $server_root
	 * @return Class_Webservice_SIGB_Nanook_Service
	 */
	public static function getService($server_root) {
		return self::newInstance()->setServerRoot($server_root);
	}


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

		try {
			$xml = $this->httpGet(array('service'					=> 'HoldTitle',
																	'bibId'						=> $exemplaire->getIdOrigine(),
																	'patronId'        => $user->getIdSigb(),
																	'pickupLocation'	=> $code_annexe));
		} catch (Exception $e) {
			return $this->_getNetworkError();
		}

		if (0 === strpos($xml, '<html>'))
			return $this->_getNetworkError();

		if ('' != $this->_getTagData($xml, 'error'))
			return $this->_error('Réservation impossible');

		return $this->_success();
	}


	/**
	 * @param Class_Users $user
	 * @param int $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id) {
		try {
			$xml = $this->httpGet(array('service'		=> 'CancelHold',
																	'patronId'	=> $user->getIdSigb(),
																	'itemId'		=> $reservation_id));
		} catch (Exception $e) {
			return $this->_getNetworkError();
		}

		if (0 === strpos($xml, '<html>'))
			return $this->_getNetworkError();

		if ('' != $this->_getTagData($xml, 'error'))
			return $this->_error('Annulation impossible');

		Return $this->_success();
	}


	/**
	 * @param Class_Users $user
	 * @param int $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id) {
		try {
			$xml = $this->httpGet(array('service'		=> 'RenewLoan',
																	'patronId'	=> $user->getIdSigb(),
																	'itemId'		=> $pret_id));
		} catch (Exception $e) {
			return $this->_getNetworkError();
		}

		if (0 === strpos($xml, '<html>'))
			return $this->_getNetworkError();

		if ('' != $this->_getTagData($xml, 'error'))
			return $this->_error('Prolongation impossible');

		return $this->_success();
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
	 * @return array
	 */
	protected function _getNetworkError() {
		return $this->_error('Service indisponible');
	}
}
?>