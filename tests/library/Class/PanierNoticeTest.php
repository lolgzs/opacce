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

class PanierNoticeLoaderTestFindAllBelongsToAdmin extends ModelTestCase {
	public function setUp() {
		$paniers_rowset = $this->_buildRowset(array(array('ID' => 3,
																											'LIBELLE' => 'fictions'),
																								array('ID' => 10,
																											'LIBELLE' => 'musique')));

		$this->select_paniers = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'notices_paniers')));

		$tbl_paniers = $this->_buildTableMock('Class_PanierNotice', array('fetchAll', 'select'));
		$tbl_paniers
			->expects($this->once())
			->method('select')
			->will($this->returnValue($this->select_paniers));

		$tbl_paniers
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($paniers_rowset));


		$this->paniers = Class_PanierNotice::getLoader()->findAllBelongsToAdmin();
	}

	public function testQuery() {
		$this->assertEquals("SELECT `notices_paniers`.*, `bib_admin_users`.* FROM `notices_paniers`\n ".
												"INNER JOIN `bib_admin_users` ON notices_paniers.id_user = bib_admin_users.id_user WHERE (bib_admin_users.ROLE_LEVEL > 3) ORDER BY `notices_paniers`.`libelle` ASC",
												$this->select_paniers->assemble());
	}


	public function testFirstPanier() {
		$first = $this->paniers[0];
		$this->assertEquals(3, $first->getId());
	}

	public function testSecondPanier() {
		$second = $this->paniers[1];
		$this->assertEquals(10, $second->getId());
	}
}


class PanierNoticeWithThreeNoticesTest extends ModelTestCase {
	public function setUp() {
		$this->fictions = new Class_PanierNotice();
		$this->fictions->updateAttributes(array('id' => 4,
																						'libelle' => 'Fictions',
																						'notices' => ';STARWARS;INDIANAJONES;SPIDERMAN'));


		$this->star_wars = new Class_Notice();
		$this->star_wars->setUrlVignette('http://premiere.com/star_wars.png');

		$this->indiana_jones = new Class_Notice();
		$this->indiana_jones->setUrlVignette('NO');

		$this->spiderman = new Class_Notice();
		$this->spiderman->setUrlVignette('http://premiere.com/spiderman.png');


		$this
			->_generateLoaderFor('Class_Notice', array('findAllBy'))
			->expects($this->once())
			->method('findAllBy')
			->with(array('clef_alpha' => array('STARWARS', 'INDIANAJONES', 'SPIDERMAN')))
			->will($this->returnValue(array($this->star_wars, $this->indiana_jones, $this->spiderman)));
	}

	public function testGetAllNotices() {
		$notices = $this->fictions->getNotices();
		$this->assertEquals(array($this->star_wars, $this->indiana_jones, $this->spiderman), 
												$notices);
	}

	public function testGetNoticesWithVignettesTrue() {
		$notices = $this->fictions->getNoticesOnlyVignettes(true);
		$this->assertEquals(array($this->star_wars, $this->spiderman),
												$notices);
	}

	public function testGetNoticesWithVignettesFalse() {
		$notices = $this->fictions->getNoticesOnlyVignettes(false);
		$this->assertEquals(array($this->star_wars, $this->indiana_jones, $this->spiderman),
												$notices);
	}
}


?>