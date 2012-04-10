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
require_once 'AbstractControllerTestCase.php';

class CatalogueControllerAppelMenuTest extends AbstractControllerTestCase {
	/** 
	 *Test non regression chatenay: ecran blanc sur menu vertical
	 *@test 
	 */
	function withWrongRequestShouldNotDoError500() {
		$query = "titre=Le+Dispositif+Trace&aleatoire=1&tri=1&nb_notices=0&nb_analyse=50&id_catalogue=0&id_panier=139999&id_user=20001&reset=true";
		$_REQUEST = array();
		foreach (explode('&', $query) as $param) {
			$params = explode('=', $param);
			$_REQUEST[$params[0]] = $params[1];
		}

		$this->dispatch('catalogue/appelmenu?'.$query);
		$this->assertXPathContentContains('//h2', 'Aucune notice trouvée');
	}

	/** 
	 *Test non regression Pontault-Combault: ecran blanc sur lien catalogue
	 *@test 
	 */
	function getFacettesSqlError() {
		$query = "titre=Catalogue&aleatoire=0&tri=1&nb_notices=+&nb_analyse=&id_catalogue=151&id_panier=0&id_user=0&reset=true";
		$this->_updateMagicREQUESTWith($query);
		$this->dispatch('catalogue/appelmenu?'.$query);
		$this->assertXPathContentContains('//h1', 'Catalogue');
	}


	protected function _updateMagicREQUESTWith($query) {
		$_REQUEST = array();
		foreach (explode('&', $query) as $param) {
			$params = explode('=', $param);
			$_REQUEST[$params[0]] = $params[1];
		}
		return $this;
	}
}

?>