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
require_once 'AdminAbstractControllerTestCase.php';


abstract class Admin_AlbumControllerPharoVideosTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Album::newInstanceWithId(777)
				->setCategorie(Class_AlbumCategorie::newInstanceWithId(1)
					              ->setLibelle('Languages de prog.'));

	}
}


class Admin_AlbumControllerPharoVideosIndexTest extends Admin_AlbumControllerPharoVideosTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/edit_images/id/777', true);
	}


	/** @test */
	public function linkToAddRessourceShouldBePresent() {
		$this->assertXPath('//div[contains(@onclick, "/album/add-ressource/id/777")]');
	}
}



class Admin_AlbumControllerPharoVideosAddTest extends Admin_AlbumControllerPharoVideosTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/add-ressource/id/777', true);
	}


	/** @test */
	public function inputForTitreShouldBePresent() {
		$this->assertXPath('//input[@type="text"][@name="titre"]');
	}
}