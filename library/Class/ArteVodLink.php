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

class Class_ArteVodLink {
	protected $_album;
	protected $_user;

	public static function forAlbumAndUser($album, $user) {
		return new self($album, $user);
	}


	public function __construct($album, $user) {
		$this->_album = $album;
		$this->_user = $user;
	}


	public function baseUrl() {
		return $this->_album->getExternalUri();
	}


	public function url() {
		$hash = Class_Hash::sha256WithKey(Class_AdminVar::get('ARTE_VOD_SSO_KEY'));
		$id_abon = $this->_user->getIdabon();
		$params = ['sso_id' => 'afi',
							 'id' => $id_abon,
							 'id_encrypted' => $hash->encrypt($id_abon),
							 'd' => $hash->encrypt(date('dmY'))];

		$params['prenom'] = $this->_user->getPrenom();
		$params['nom'] = $this->_user->getNom();
		$params['email'] = $this->_user->getMail();

		return $this->baseUrl().'?'.http_build_query(array_filter($params));
	}
}


?>