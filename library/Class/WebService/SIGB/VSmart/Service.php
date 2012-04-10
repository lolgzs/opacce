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

class Class_WebService_SIGB_VSmart_Service extends Class_WebService_SIGB_AbstractRESTService {
	const MOULINS_POPUP_SERVER = '46.20.169.9/moulins';

	protected static $SOAP_CLIENT_CLASS='Class_WebService_MappedSoapClient';

	protected $_soap_client;
  protected $_authenticate_wsdl;
	protected $_popup_root;

	public static function newInstance() {
		return new self();
	}

	public static function getService($server_root) {
		return self::newInstance()->setServerRoot($server_root);
	}


	public static function setSoapClientClass($classname) {
		self::$SOAP_CLIENT_CLASS = $classname;
	}


	public static function getSoapClientClass() {
		return self::$SOAP_CLIENT_CLASS;
	}


	public function getSoapClient() {
		if (!isset($this->_soap_client)) {
			$soap_class = self::getSoapClientClass();
			$this->_soap_client = new $soap_class($this->_authenticate_wsdl,
																						array('cache_wsdl' => WSDL_CACHE_BOTH));
		}

		return $this->_soap_client;
	}


	/**
	 * @param Class_Users $opacUser
	 * @return string
	 */
	public function getAuthenticateTokenForUser($opacUser) {
		$user = new User();
		$user->EncodedUserId = base64_encode('RES.'.$opacUser->getLogin());
		$user->EncodedPassword = base64_encode('DATE.'.$opacUser->getPassword());
		$response = $this->getSoapClient()->User($user);
		return $response->UserResult;
	}


	/**
	 * @param Class_Users $user
	 * @param array $params
	 * @return string
	 */
	public function buildPopupUrlForUser($user, $params) {
		$token = $this->getAuthenticateTokenForUser($user);

		return sprintf('%s?%s',
									 $this->_popup_root,
									 http_build_query(array('Token' => $token) + $params));
	}


	/**
	 * @param Class_Users $user
	 * @param int $exemplaire_id
	 * @return string
	 */
	public function getPopupUrlForReservation($user, $exemplaire_id) {
		$record_number = str_replace('/', ':', $exemplaire_id);
		return $this->buildPopupUrlForUser($user,
																			 array('Function' => 'Reservation',
																						 'RecordNumber' => $record_number));
	}


	/**
	 * @param Class_Users $user
	 * @return string
	 */
	public function getPopupUrlForUserInformations($user) {
			return $this->buildPopupUrlForUser($user,
																				 array('Function' => 'UserActivities',
																							 'Module' => 'ADM'));
	}


	public function setServerRoot($server_root) {
		$matches = array();
		if (preg_match_all('/([^\/]+\/[^\/]+)\/?/', $server_root, $matches))
			$server_root = $matches[1][0];
		$this->_server_root = sprintf('http://%s/VubisSmartHttpApi.csp', $server_root);
		$this->_authenticate_wsdl = sprintf('http://%s/SSO.Authenticate.CLS?WSDL=1', $server_root);
		$this->_popup_root = sprintf('http://%s/LoginWebSso.csp', self::MOULINS_POPUP_SERVER);
		return $this;
	}


	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_VSmart_BorrowerReader
	 */
	public function getEmprunteur($user) {
		$xml = $this->httpGet(array('fu' => 'GetBorrower',
																'MetaInstitution' => 'RES',
																'BorrowerId' => $user->getLogin()));

		return Class_WebService_SIGB_VSmart_BorrowerReader
			::newInstanceForService($this)
			->getEmprunteurFromXML($xml)
			->setId($user->getLogin())
			->setPassword($user->getPassword());
	}


	/**
	 * @param string $id
	 * @return type
	 */
	public function getNotice($id) {
		$ids = explode('/', $id);
		$xml = $this->httpGet(array('fu' => 'BibSearch',
																'Application' => 'Bib',
																'Database' => $ids[0],
																'RequestType' => 'RecordNumber',
												 'Request' => $ids[1]));

 		$notice = Class_WebService_SIGB_VSmart_SearchResponseReader
			::newInstance()
			->getNoticeFromXML($xml);

		if ($notice)
			$this->cacheNotice($notice);

		return $notice;
	}


	/**
	 *
	 * @param array $params
	 * @return array
	 */
	protected function _callAPIFunction($params) {
		$params['MetaInstitution'] = 'RES';
		$params['Language'] = 'fre';
		$xml = $this->httpGet($params);

		return Class_WebService_SIGB_VSmart_FunctionOutputReader
			::newInstanceParse($xml)
			->getReussite();
	}


	/**
	 * @param Class_Users $user
	 * @param int $exemplaire_id
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($user, $exemplaire, $code_annexe){
		try{
			return array('popup' => $this->getPopupUrlForReservation($user, $exemplaire->getIdOrigine()));
		} catch (Exception $e) {
			return array('erreur' => $e->getMessage());
		}

		$ids = explode('/', $exemplaire_id);
		return $this->_callAPIFunction(array('fu' => 'ReservationTitle',
																				 'BorrowerId' => $user->getLogin(),
																				 'Database' => $ids[0],
																				 'ReserveArea' => $code_annexe,
																				 'BibRecord' => $ids[1],
																				 'PickupLocation' => 'INST/LOC'));
	}


	/**
	 * @param Class_Users $user
	 * @param int $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id){
		return $this->_callAPIFunction(array('fu' => 'ReservationCancel',
																				  'BorrowerId' => $user->getLogin(),
																				  'ItemId' => $reservation_id));
	}


	/**
	 * @param Class_Users $user
	 * @param int $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id){
		return $this->_callAPIFunction(array('fu' => 'Renewal',
																				  'BorrowerId' => $user->getLogin(),
																				  'ItemId' => $pret_id));
	}
}



/** Classes SOAP **/
class User {
  public $EncodedUserId; // string
  public $EncodedPassword; // string
}


class UserResponse {
  public $UserResult; // string
}


?>