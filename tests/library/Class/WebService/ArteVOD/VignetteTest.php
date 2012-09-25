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

class Class_WebService_ArteVOD_VignetteBlancheNeigeTest extends Storm_Test_ModelTestCase {
	protected $_album;
	protected $_file_writer;

	public function setUp() {
		parent::setup();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('save')
			->answers(true);


		$this->_album = Class_Album::newInstanceWithId(45)
			->setTitre('Blanche Neige')
			->beArteVOD()
			->setNotes([['field' => '856', 
									 'data' => ['x' => 'poster', 
															'a' => 'http://mediatheque.com/blanche_neige.jpg']]]);

		$this->_http_client = Storm_Test_ObjectWrapper::mock();
		Class_WebService_ArteVOD_Vignette::setDefaultHttpClient($this->_http_client);
		$this->_http_client
			->whenCalled('open_url')
			->with('http://mediatheque.com/blanche_neige.jpg')
			->answers('an image');

		$vignette = new Class_WebService_ArteVOD_Vignette();
		$vignette->setFileWriter($this->_file_writer = Storm_Test_ObjectWrapper::mock()
														 ->whenCalled('putContents')
														 ->with(PATH_TEMP.'blanche_neige.jpg', 'an image')
														 ->answers(8)
														 ->beStrict());
		$vignette->updateAlbum($this->_album);

	}


	public function tearDown() {
		Class_WebService_ArteVOD_Vignette::setDefaultHttpClient(null);
		parent::tearDown();
	}


	/** @test */
	public function getPosterShouldAnswerUrlBlancheNeigeDotJpg() {
		$this->assertEquals('http://mediatheque.com/blanche_neige.jpg', $this->_album->getPoster());
	}


	/** @test */
	public function albumShouldBeSaved() {
		$this->assertTrue(Class_Album::methodHasBeenCalled('save'));
	}


	/** @test */
	public function globalFILESShouldBeSet() {
		$this->assertEquals('blanche_neige.jpg', $_FILES['fichier']['name']);
		$this->assertEquals(PATH_TEMP.'blanche_neige.jpg', $_FILES['fichier']['tmp_name']);
		$this->assertEquals('8', $_FILES['fichier']['size']);
	}


	/** @test */
	public function albumUploaderIsValidShouldReturnsTrue() {
		$uploader = $this->_album->getUploadHandler('fichier');
		$uploader->setFolderManager(Storm_Test_ObjectWrapper::mock()
																->whenCalled('ensure')
																->answers(true));
		$this->assertTrue($uploader->validate());
	}


	/** @test */
	public function getFichierShouldAnswerBlancheNeigeDotJpg() {
		$this->assertEquals('blanche_neige.jpg', $this->_album->getFichier());
	}


	/** @test */
	public function fileShouldHaveBeenWritten() {
		$this->assertTrue($this->_file_writer->methodHasBeenCalled('putContents'));
	}


	/** @test */
	public function albumUploadMoverShouldBeAnInstanceOfClass_UploadMover_LocalFile() {
		$this->assertInstanceOf('Class_UploadMover_LocalFile', 
														$this->_album->getUploadHandler('fichier')->getUploadMover());
	}
}

?>