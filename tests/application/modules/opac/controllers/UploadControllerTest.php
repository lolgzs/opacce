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

class UploadControllerFormOnInexistantFileTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/upload/form?extensions=.jpg%252C.jpeg%252C.gif%252C.png&amp;largeur_vignette=300&amp;hauteur_vignette=320&amp;poids=500&amp;largeur_conseil=&amp;hauteur_conseil=&amp;path=photobib&amp;filename=zork666.png&amp;input_name=carte');
	}


	/** @test */
	function imgSourceShouldBeBlank() {
		$this->assertXPath("//form//img[contains(@src, 'blank.gif')]", $this->_response->getBody());
	}
}



class UploadControllerFormOnExistingFileTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		file_put_contents(USERFILESPATH.'/photobib/zork 666.png', 'some data');
		$this->dispatch('/upload/form?'.
										http_build_query(array('extensions' => '.jpg,.jpeg,.gif,.png',
																					 'largeur_vignette' => 300,
																					 'hauteur_vignette' => 320,
																					 'poids' => 500,
																					 'path' => 'photobib',
																					 'filename' => 'zork+666.png',
																					 'input_name' => 'carte')));
	}

	public function tearDown() {
		unlink(USERFILESPATH.'/photobib/zork 666.png');
	}


	/** @test */
	public function imgSrcShouldContainsZorkSpace666DotPng() {
		$this->assertXPath("//form//img[contains(@src, 'zork 666.png')]", $this->_response->getBody());
	}
}

?>