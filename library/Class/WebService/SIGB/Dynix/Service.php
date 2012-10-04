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
class Class_Webservice_SIGB_Dynix_Service extends Class_WebService_SIGB_AbstractRESTService {
	protected $_client_id;

	/**
	 * @param string $server_root
	 * @return Class_Webservice_SIGB_Dynix_Service
	 */
	public function setServerRoot($server_root) {
		if ('/' == substr($server_root, -1))
			$server_root = substr($server_root, 0, -1);
		return parent::setServerRoot($server_root);
	}


	public function setClientId($client_id) {
		$this->_client_id = $client_id;
		return $this;
	}


	public function getNotice($id){
		return $this->httpGetNotice(['namespace' => 'standard',
																 'service' => 'lookupTitleInfo',
																 'clientID' => $this->_client_id,
																 'titleID' => $id,
																 'includeItemInfo' => 'true',
																 'includeAvailabilityInfo' => 'true'],

																Class_WebService_SIGB_Dynix_TitleInfoResponseReader::newInstance());
	}


	public function openSessionForUser($user) {
		$xml = $this->httpGet(['namespace' => 'security',
													 'service' => 'loginUser',
													 'clientID' => $this->_client_id,
													 'login' => $user->getLogin(),
													 'password' => $user->getPassword()]);
		return $this->_getTagData($xml, 'sessionToken');
	}


	public function closeSession($token) {
		$this->httpGet(['namespace' => 'security',
										'service' => 'logoutUser',
										'clientID' => $this->_client_id,
										'sessionToken' => $token]);
		return $this;
	}


	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function getEmprunteur($user) {
		$session_token = $this->openSessionForUser($user);
		$xml = $this->httpGet(['namespace' => 'patron',
													 'service' => 'lookupMyAccountInfo',
													 'includePatronHoldInfo' => 'ACTIVE',
													 'includePatronInfo' => 'true',
													 'includePatronCheckoutInfo' => 'ALL',
													 'clientID' => $this->_client_id,
													 'sessionToken' => $session_token]);

		$this->closeSession($session_token);

		return Class_WebService_SIGB_Dynix_LookupMyAccountInfoResponseReader::newInstance()
			->getEmprunteurFromXML($xml)
			->setService($this);
	}


	public function reserverExemplaire($user, $exemplaire, $code_annexe){
		$session_token = $this->openSessionForUser($user);
		$xml = $this->httpGet(['namespace' => 'patron',
													 'service' => 'createMyHold',
													 'titleKey' => $exemplaire->getIdOrigine(),
													 'pickupLibraryID' => $code_annexe,
													 'clientID' => $this->_client_id,
													 'sessionToken' => $session_token]);

		if ($error = $this->_getTagData($xml, 'string'))
			return ['statut' => false, 'erreur' => $error];

		return ['statut' => true, 'erreur' => ''];
	}


	public function supprimerReservation($user, $reservation_id){}


	public function prolongerPret($user, $pret_id){}


	/**
	 * @param array $options
	 * @return string
	 */
	public function buildQueryURL($options) {
		$namespace = $options['namespace'];
		unset($options['namespace']);

		$service = $options['service'];
		unset($options['service']);

		return sprintf('%s/rest/%s/%s?%s', 
									 $this->getServerRoot(), 
									 $namespace,
									 $service, 
									 http_build_query($options));
	}

}

?>