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
class Class_WebService_SIGB_Carthame_Service extends Class_WebService_SIGB_AbstractRESTService {
	protected function _getReserveDocumentErrorMessage($code) {
		$messages = $this->_getReserveDocumentErrors();
		if (array_key_exists($code, $messages))
			return $messages[$code];
		return Zend_Registry::get('translate')->_('Réservation impossible');
	}

	protected function _getReserveDocumentErrors() {
		$t = $this->translate = Zend_Registry::get('translate');
		return array(
				'101' => $t->_('Reservation not allowed to public'),
				'102' => $t->_('No copies allowed for this document'),
				'103' => $t->_('Maximum amount of reservation for this document'),
				'104' => $t->_('No copy or copy cannot be reserved, none on order'),
				'106' => $t->_('This document does not belong to any document family'),
				'107' => $t->_('No copy, on order but cannot be reserved'),
				'108' => $t->_('All copies are on loan to a customer whose loans are not reserved'),
				'109' => $t->_('No copies available to reserve because of the availability of copies'),
				'201' => $t->_('Customer has overdue loans'),
				'202' => $t->_('Customer has reached maximum number of reservations'),
				'203' => $t->_('Already exists at least one reservation for this customer for this notice'),
				'204' => $t->_('This notice is already on loan to this customer'),
				'304' => $t->_('No withdrawal possible : subscription problem, no available copy to reserve'),
				'401' => $t->_('Customer has no valid subscription'),
				'403' => $t->_('Customer has overdue payments'),
				'501' => $t->_('The notice\'s document type for reservation does not belong to any document family'),
				'502' => $t->_('Reservation limit reached'),
				'503' => $t->_('Reservation limit reached for this document type'),
				'504' => $t->_('Reservation limit reached for this annexe'),
				'505' => $t->_('No valid customer subscription or payments overdue'),
				'506' => $t->_('Notice\'s document family cannot be reserved'));
	}

	/**
	 * @return Class_WebService_SIGB_Carthame_Service
	 */
	public static function newInstance() {
		return new self();
	}

	/**
	 * @param string $server_root
	 * @return Class_WebService_SIGB_Carthame_Service
	 */
	public static function getService($server_root) {
		return self::newInstance()->setServerRoot($server_root);
	}

	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteur($user) {
		$userId = $this->_authenticate($user);
		$xml = $this->httpGet(array(
										'sigb'		=> 'ktm',
										'version'	=> 'standalone',
										'action'	=> 'accountDetails',
										'userid'	=> $userId));
		$account = Class_WebService_SIGB_Carthame_AccountResponseReader::newInstance()
									->getAccountFromXml($xml);

		return $account->setId($userId)
										->setService($this);

	}

	/**
	 * @param Class_Users $user
	 * @param Class_Exemplaire exemplaire
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($user, $exemplaire, $code_annexe) {
		$userId = $this->_authenticate($user);
		$notice_id = $exemplaire->getIdOrigine();

		$xml = $this->httpGet(array(
			'sigb'		=> 'ktm',
			'version'	=> 'standalone',
			'action'	=> 'reserveInfo',
			'userid'	=> $userId,
			'nn'			=> $notice_id
		));

		if (1 <= (int)$this->_getTagData($xml, 'error')) {
			$error_code = (string)$this->_getTagData($xml, 'code');
			$error_message = $this->_getReserveDocumentErrorMessage($error_code);
			return $this->_error($error_message);
		}

		$reserveInfoReader = Class_WebService_SIGB_Carthame_ReserveInfoResponseReader::newInstance()->readXML($xml);
		if (!$reserveInfoReader->isSiteAllowed($code_annexe)) {
			return $this->_error(sprintf('Réservation impossible. Autorisée seulement sur %s',
																	 implode(',' , $reserveInfoReader->getSites())));
		}

		$xml = $this->httpGet(array(
			'sigb'		=> 'ktm',
			'version'	=> 'standalone',
			'action'	=> 'reserveDocument',
			'userid'	=> $userId,
			'nn'			=> $notice_id,
			'site'		=> $reserveInfoReader->getId($code_annexe),
		));

		if (1 === (int)$this->_getTagData($xml, 'error'))
			return $this->_success();

		$error_code = (string)$this->_getTagData($xml, 'code');
		$error_message = $this->_getReserveDocumentErrorMessage($error_code);
		return $this->_error($error_message);
	}

	/**
	 * @param Class_Users $user
	 * @param string $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id) {
		$xml = $this->httpGet(array(
			'sigb'		=> 'ktm',
			'version'	=> 'standalone',
			'action'	=> 'reserveCancel',
			'resid'		=> $reservation_id,
		));

		if (0 === (int)$this->_getTagData($xml, 'error'))
			return $this->_success();

		return $this->_error((string)$this->_getTagData($xml, 'code'));
	}

	/**
	 * @param Class_Users $user
	 * @param string $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id) {
		$xml = $this->httpGet(array(
			'sigb'		=> 'ktm',
			'version'	=> 'standalone',
			'action'	=> 'prolongLoan',
			'loanid'	=> $pret_id,
		));

		if (0 === (int)$this->_getTagData($xml, 'error'))
			return $this->_success();

		return $this->_error((string)$this->_getTagData($xml, 'code'));
	}

	/**
	 * @param string $id
	 * @return Class_WebService_SIGB_Notice
	 */
	public function getNotice($id) {
		$xml = $this->httpGet(array(
								'sigb'		=> 'ktm',
								'version'	=> 'standalone',
								'action'	=> 'copyDetails',
								'nn'			=> (string)$id));
		return Class_WebService_SIGB_Carthame_RecordResponseReader::newInstance()
							->getNoticeFromXML($xml);

	}

	/**
	 * @param Class_Users $user
	 * @return string
	 */
	protected function _authenticate($user) {
		$response = $this->httpGet(array(
			'sigb'		=> 'ktm',
			'version'	=> 'standalone',
			'action'	=> 'login',
			'username'=> (string)$user->getLogin(),
			'password'=> (string)$user->getPassword(),
		));

		return $this->_getTagData($response, 'id');
	}
}
?>