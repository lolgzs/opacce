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
require_once 'ModelTestCase.php';

class CatalogueTestGetRequetesPanier extends ModelTestCase {
	public function setUp() {
		$panier_row = $this->_buildRowset(array(array('ID' => 3,
																									'ID_USER' => 3,
																									'ID_PANIER' => 2,
																									'NOTICES' => ';STARWARS;JAMESBOND;')));

		$this->select_paniers = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'notices_paniers')));

		$tbl_paniers = $this->_buildTableMock('Class_PanierNotice', array('fetchAll', 'select'));
		$tbl_paniers
			->expects($this->once())
			->method('select')
			->will($this->returnValue($this->select_paniers));

		$tbl_paniers
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($panier_row));

		$catalogue = new Class_Catalogue();
		$this->requetes = $catalogue->getRequetesPanier(array('id_panier' => 2,
																													'id_user' => 3,
																													'aleatoire' => 0,
																													'tri' => 0,
																													'only_img' => 1,
																													'avec_avis' => 0,
																													'nb_notices' => 3));
	}

	public function testRequeteListe() {
		$this->assertEquals("select * from notices where notices.clef_alpha in('STARWARS','JAMESBOND') and url_vignette > '' and url_vignette != 'NO'  order by alpha_titre LIMIT 0,3",
												$this->requetes['req_liste']);
	}

	public function testRequeteComptage() {
		$this->assertEquals("select count(*) from notices where notices.clef_alpha in('STARWARS','JAMESBOND') and url_vignette > '' and url_vignette != 'NO' ",
												$this->requetes['req_comptage']);
	}

	public function testRequeteFacettes() {
		$this->assertEquals("select id_notice,type_doc,facettes from notices where notices.clef_alpha in('STARWARS','JAMESBOND')  and url_vignette > '' and url_vignette != 'NO' LIMIT 0,3",
												$this->requetes['req_facettes']);
	}
}

?>