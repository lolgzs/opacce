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
	public function getEmprunteur($user){}


	public function reserverExemplaire($user, $exemplaire, $code_annexe){}


	public function supprimerReservation($user, $reservation_id){}


	public function prolongerPret($user, $pret_id){}


	public function getNotice($id){
		return $this->httpGetNotice(['service' => 'lookupTitleInfo',
																 'clientID' => 'myid',
																 'titleID' => $id,
																 'includeItemInfo' => 'true',
																 'includeAvailabilityInfo' => 'true'],

																Class_WebService_SIGB_Dynix_TitleInfoResponseReader::newInstance());
	}


	/**
	 * @param array $options
	 * @return string
	 */
	public function buildQueryURL($options) {
		$service = $options['service'];
		unset($options['service']);
		return sprintf('%s/%s?%s', $this->getServerRoot(), $service, http_build_query($options));
	}

}

?>