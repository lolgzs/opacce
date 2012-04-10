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

/*
 * Comme les exemplaires sont retournés dans la notice via webservices,
 * le cache permet de n'avoir qu'une seul requête sur la notice lorsqu'on
 * demande un exemplaire
 */

class Class_WebService_SIGB_NoticeCache {
	protected $provider;
	protected $cache;

	public function __construct($provider){
		$this->provider = $provider;
		$this->cache = array();
	}


	public function cacheNotice($notice) {
		$this->cache[$notice->getId()] = $notice;
	}


	protected function getOrLoadNotice($notice_id){
		if (!array_key_exists($notice_id, $this->cache))
			$this->cache[$notice_id] = $this->provider->getNotice($notice_id);
		return $this->cache[$notice_id];
	}


	public function getExemplaire($notice_id, $code_barre) {
		$notice = $this->getOrLoadNotice($notice_id);

		if (!isset($notice) || ($exemplaire = $notice->getExemplaireByCodeBarre($code_barre)) == null){
			$exemplaire = new Class_WebService_SIGB_Exemplaire(null);
		}

		return $exemplaire;
	}
}

?>