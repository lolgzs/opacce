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

class Class_WebService_SIGB_Microbib_Service extends Class_WebService_SIGB_AbstractService {
	protected $_search_client;

	public static function getService($wsdl) {
		$client = new Class_WebService_MappedSoapClient($wsdl,
																										array(
																													'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
																													'cache_wsdl' => WSDL_CACHE_BOTH,
																													'trace' => false));
		return new self($client);
	}


	public function __construct($search_client) {
		$this->_search_client = $search_client;
	}


	public function getNotice($id) {
		$xml = $this->_search_client->infos_exemplaires($id);
		return Class_WebService_SIGB_Microbib_InfosExemplairesResponseReader::noticeFromXML($xml);
	}


	public function getEmprunteur($user) {
		$xml = $this->_search_client->infos_abonne($user->getLogin(), $user->getPassword());
		return Class_WebService_SIGB_Microbib_InfosAbonneResponseReader::emprunteurFromXML($xml);
	}


	public function reserverExemplaire($user, $exemplaire, $code_annexe) {
		$xml = $this->_search_client->ajout_reservation($user->getLogin(), $exemplaire->getCodeBarres());
		return $this->_actionResponseFromXML($xml);
	}


	public function supprimerReservation($user, $code_barre) {
		$xml = $this->_search_client->annule_reservation($user->getLogin(), $code_barre);
		return $this->_actionResponseFromXML($xml);
	}


	public function prolongerPret($user, $code_barre) {
		$xml = $this->_search_client->prolonge_pret($user->getLogin(), $code_barre);
		return $this->_actionResponseFromXML($xml);
	}


	protected function _actionResponseFromXML($xml) {
		$response = Class_WebService_SIGB_Microbib_ActionResponseReader::responseFromXML($xml);

		if ($response == 'Ok')
			return array('statut' => true, 'erreur' => '');

		return array('statut' => false, 'erreur' => array_last(explode('_', $response)));		
	}
}

?>