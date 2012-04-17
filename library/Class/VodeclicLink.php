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

class Class_VodeclicLink {
	protected $_user;

	public static function forUser($user) {
		return new self($user);
	}


	public function __construct($user) {
		$this->_user = $user;
	}


	public function baseUrl() {
		return "https://biblio.vodeclic.com/auth/biblio/sso";
	}


	public function url() {
		$hash = Class_Hash::sha256WithKey(Class_AdminVar::get('VODECLIC_KEY'));
		$partenaire = Class_AdminVar::get('VODECLIC_ID');
		$id = $this->_user->getIdabon();
		$params = array('id' => $id,
										'encrypted_id' => $hash->encrypt($id),
										'd' => $hash->encrypt(date('dmY')),
										'partenaire' => $partenaire);

		if ($email = $this->_user->getMail())
			$params = array_merge(array('email' => $email,
																	'encrypted_email' => $hash->encrypt($email)),
														$params);

		if ($nom = $this->_user->getNom())
			$params['nom'] = $nom;

		if ($prenom = $this->_user->getPrenom())
			$params['prenom'] = $prenom;

		return $this->baseUrl().'?'.http_build_query($params);
	}
}

?>