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

require_once 'AlbumControllerTest.php';

class Admin_AlbumControllerPreviewEPubTest extends Admin_AlbumControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();

		$album = Class_Album::newInstanceWithId(999)
			->beEPUB()
			->setTitre('Informatique')
			->setRessources([Class_AlbumRessource::newInstanceWithId(4)
											 ->setUrl('pragmatic_programmers.epub')
											 ->setTitre('Pragmatic Programmers')
											 ->setVignette('pragprog.jpg')]);

		$this->dispatch('/admin/album/preview_album/id/999', true);
	}


	/** @test */
	public function linkToDownloadEPubShouldBeModuleOPACBibNumDownload() {
		$this->assertXpath('//a[@href="/bib-numerique/download-resource/id/4"]');
	}
}


?>