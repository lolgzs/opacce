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
abstract class AlbumRessourceFileUploadTestCase extends ModelTestCase {
	/** @var Class_AlbumRessource */
	protected $_resource;

	/** @var Storm_Test_ObjectWrapper */
	protected $_wrapper;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_handler;


	protected function setUp() {
		parent::setUp();

		$this->_resource = Class_AlbumRessource::getLoader()
												->newInstance()
												->setAlbum(
													Class_Album::getLoader()
														->newInstanceWithId(999)
														->setTitre('Harlock')
												);

		$this->_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
												->whenCalled('save')->answers(true)->getWrapper()
												->whenCalled('delete')->answers(null)->getWrapper();

		$this->_handler = $this->getMockBuilder('Class_MultiUpload')
											->disableOriginalConstructor()
											->getMock();;
	}
}




class AlbumRessourceInitializationTest extends AlbumRessourceFileUploadTestCase {
	/** @test */
	public function initializeShouldSaveInstance() {
		$this->_handler->expects($this->any())
									->method('getError')
									->will($this->returnValue(null));

		$this->_handler->expects($this->any())
									->method('handleUpload')
									->will($this->returnValue(true));

		$this->_resource->setMultiUploadHandler($this->_handler)
										->initializeWith(null);

		$this->assertTrue($this->_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	public function whenHandlerHasErrorShouldReturnErrorArray() {
		$this->_handler->expects($this->any())
										->method('getError')
										->will($this->returnValue('Error message'));

		$this->assertEquals(
			array(
				'success' => 'false',
				'error' => 'Error message'
			),
			$this->_resource->setMultiUploadHandler($this->_handler)
												->initializeWith(null)
		);
	}


	/** @test */
	public function whenHandlerHasErrorShouldCallDelete() {
		$this->_handler->expects($this->any())
										->method('getError')
										->will($this->returnValue('Error message'));

		$this->_resource->setMultiUploadHandler($this->_handler)
										->initializeWith(null);

		$this->assertTrue($this->_wrapper->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function whenHandlerHasNoErrorShouldReturnSuccessArray() {
		$this->_handler->expects($this->any())
									->method('getError')
									->will($this->returnValue(null));

		$this->_handler->expects($this->any())
									->method('handleUpload')
									->will($this->returnValue(true));

		$this->assertEquals(
			array('success' => 'true'),
			$this->_resource->setMultiUploadHandler($this->_handler)
												->initializeWith(null)
		);
	}
}




class AlbumRessourceReceivingFileTest extends AlbumRessourceFileUploadTestCase {
	protected function setUp() {
		parent::setUp();

		$this->_handler = $this->getMockBuilder('Class_Upload')
											->disableOriginalConstructor()
											->getMock();;
	}


	/** @test */
	public function withNoFileShouldReturnTrue() {
		$_FILES['fichier']['size'] = 0;
		$this->assertTrue($this->_resource->receiveFile());
	}


	/** @test */
	public function withUploadErrorShouldReturnFalse() {
		$_FILES['fichier']['size'] = 1;
		$this->_handler
			->expects($this->once())
			->method('receive')
			->will($this->returnValue(false));

		$this->assertFalse($this->_resource->setUploadHandler($this->_handler)->receiveFile());
	}
}




class AlbumRessourceLoaderTest extends ModelTestCase {
	/** @var Zend_Db_Table_Select */
	protected $_select;

	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_table;

	protected function setUp() {
		parent::setUp();

		$this->_select = new Zend_Db_Table_Select(
											new Storm_Model_Table(array('name' => 'album_ressources'))
										);

		$this->_table = $this->_buildTableMock(
											'Class_AlbumRessource',
											array('select', 'info', 'fetchRow')
										);

		$this->_table->expects($this->any())
						->method('select')
						->will($this->returnValue($this->_select));

		$orderResult = new stdClass();
		$orderResult->order = 7;

		$this->_table->expects($this->any())
						->method('fetchRow')
						->will($this->returnValue($orderResult));

		$this->_table->expects($this->any())
						->method('info')
						->with('name')
						->will($this->returnValue('album_ressources'));

	}


	/** @test */
	public function withHarlockGetNextOrderShouldFilterOnHarlock() {
		Class_AlbumRessource::getLoader()->getNextOrderFor(
															Class_Album::getLoader()->newInstanceWithId(999));

		$this->assertRegExp('/\\(id_album = 999\\)/', $this->_select->assemble());

	}
}




class AlbumRessourceSortingTest extends ModelTestCase {
	/** @test */
	public function newInstanceShouldHaveOrderAfterSaving() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('save')->answers(true)->getWrapper()
			->whenCalled('getNextOrderFor')->answers(99);

		$arcadia = Class_AlbumRessource::getLoader()
								->newInstance()
								->setTitre('Arcadia');

		$arcadia->save();

		$this->assertEquals(99, $arcadia->getOrdre());
	}
}




class AlbumRessourceTypesTest extends ModelTestCase {
	/** @test */
	public function emptyFileShouldNotBeImage() {
		$resource = Class_AlbumRessource::getLoader()->newInstance()
									->setFichier('');

		$this->assertFalse($resource->isImage());
	}


	/** @test */
	public function pngFileShouldBeImage() {
		$resource = Class_AlbumRessource::getLoader()->newInstance()
									->setFichier('add.png');

		$this->assertTrue($resource->isImage());
	}


	/** @test */
	public function uppercaseGifFileShouldBeImage() {
		$resource = Class_AlbumRessource::getLoader()->newInstance()
									->setFichier('add.GIF');

		$this->assertTrue($resource->isImage());
	}
}


class AlbumRessourceTypeXMLTest extends ModelTestCase {
	public function setUp() {
		$this->resource = Class_AlbumRessource::getLoader()
			->newInstance()
			->setTitre('Mon XML')
			->setDescription('pour tester')
			->setFichier('add.xml');
	}


	/** @test */
	function isImageShouldReturnFalse() {
		$this->assertFalse($this->resource->isImage());
	}


	/** @test */
	function isVideoShouldReturnFalse() {
		$this->assertFalse($this->resource->isVideo());
	}


	/** @test */
	function isFlashShouldReturnFalse() {
		$this->assertFalse($this->resource->isFlash());
	}


	/** @test */
	function thumbnailURLShouldReturnEarthLogo() {
		$this->assertEquals('/afi-opac3/public/opac/images/earth-logo.jpg',
												$this->resource->getThumbnailURL());
	}
}



class AlbumRessourceTypeSWFTest extends ModelTestCase {
	public function setUp() {
		$this->resource = Class_AlbumRessource::getLoader()
											->newInstance()
											->setFichier('pacman.swf');
	}


	/** @test */
	function isImageShouldReturnFalse() {
		$this->assertFalse($this->resource->isImage());
	}


	/** @test */
	function isVideoShouldReturnFalse() {
		$this->assertFalse($this->resource->isVideo());
	}


	/** @test */
	function isFlashShouldReturnTrue() {
		$this->assertTrue($this->resource->isFlash());
	}


	/** @test */
	function thumbnailURLShouldReturnFlashLogo() {
		$this->assertEquals('/afi-opac3/public/opac/images/flash-logo.jpg',
												$this->resource->getThumbnailURL());
	}
}



class AlbumRessourceTypeMOVTest extends ModelTestCase {
	public function setUp() {
		$this->resource = Class_AlbumRessource::getLoader()
											->newInstance()
											->setFichier('bladerunner.mov');
	}


	/** @test */
	function isImageShouldReturnFalse() {
		$this->assertFalse($this->resource->isImage());
	}
	

	/** @test */
	function isVideoShouldReturnTrue() {
		$this->assertTrue($this->resource->isVideo());
	}


	/** @test */
	function isFlashShouldReturnFalse() {
		$this->assertFalse($this->resource->isFlash());
	}


	/** @test */
	function thumbnailURLShouldReturnQuicktimeLogo() {
		$this->assertEquals('/afi-opac3/public/opac/images/quicktime-logo.png',
												$this->resource->getThumbnailURL());
	}
}


abstract class AlbumRessourceThumbnailTestCase extends ModelTestCase {
	protected $_expected_filepath;
	protected $_ressource;
	protected $_size_params;
	protected $_expected_filename;

	public function setUp() {
		parent::setUp();

		$this->_expected_filepath = USERFILESPATH.'/temp/'.$this->_expected_filename;
		if (file_exists($this->_expected_filepath)) {
			unlink($this->_expected_filepath);
		}

		$this->_ressource = Class_AlbumRessource::getLoader()
			->newInstanceWithId(1)
			->setFichier('1.jpg')
			->setImage($this->_buildTestImage());
		$this->_ressource
			->setAlbum(Class_Album::getLoader()
								 ->newInstanceWithId(93)
								 ->setRessources(array($this->_ressource)));

		$this->_thumb_path = $this->_ressource->getThumbnailFilePath($this->_size_params);
	}


	protected function _buildTestImage() {
		$im = new Imagick();
		$im->newPseudoImage(50, 10, "gradient:red-black");
		$im->setImageFormat('jpg');
		return $im;
	}


	/** @test */
	public function thumbnailFilePathShouldBeMD5ForFilePathAndParams() {
		$this->assertEquals($this->_expected_filepath, $this->_thumb_path);
	}
}



class AlbumRessourceThumbnailResizeHundredPerHundredTwentyTest extends AlbumRessourceThumbnailTestCase {
	protected $_expected_filename = 'a05849302da9a6dbfc0176149184d5f6.jpg';
	protected $_size_params = array('width' => 100,
																	'height' => 120,
																	'crop_left' => 2,
																	'crop_right' => 1,
																	'crop_bottom' => 1,
																	'crop_top' => 1);

	/** @test */
	public function thumbnailFileShouldBeGeneratedInTempDir() {
		$this->assertTrue(file_exists($this->_expected_filepath));
	}


	/** @test */
	public function thumbnailHeightShouldBe120() {
		$this->assertEquals(120, $this->_ressource->getImage()->getImageHeight());
	}


	/** @test */
	public function thumbnailWidthShouldBe100() {
		$this->assertEquals(100, $this->_ressource->getImage()->getImageWidth());
	}


	/** @test */
	public function imageShouldBeCropped() {
		$im = $this->_buildTestImage();
		$im->cropImage(47, 8, 2, 1);
		$im->resizeImage(100, 120, Imagick::FILTER_LANCZOS, 1);
		$this->assertEquals($im->getImageBlob(), 
												$this->_ressource->getImage()->getImageBlob());
	}


	/** @test */
	public function withExistingFileShouldNotCreateNewThumbnail() {
			unlink($this->_expected_filepath);
			file_put_contents($this->_expected_filepath, 'some data');

			$this->_ressource->getThumbnailFilePath($this->_size_params);

			$this->assertEquals('some data', file_get_contents($this->_expected_filepath));
	}
}



class AlbumRessourceThumbnailResizeNoParamTest extends AlbumRessourceThumbnailTestCase {
	protected $_expected_filename = '5c93c2fb3ba41d726d671bd360647b44.jpg';
	protected $_size_params = array();

	/** @test */
	public function thumbnailHeightShouldBe10() {
		$this->assertEquals(10, $this->_ressource->getImage()->getImageHeight());
	}


	/** @test */
	public function thumbnailWidthShouldBe50() {
		$this->assertEquals(50, $this->_ressource->getImage()->getImageWidth());
	}


	/** @test */
	public function imageShouldNotBeCropped() {
		$im = $this->_buildTestImage();
		$this->assertEquals($im->getImageBlob(), 
												$this->_ressource->getImage()->getImageBlob());
	}

}


class AlbumRessourceThumbnailResizeNoWidthButCropParamTest extends AlbumRessourceThumbnailTestCase {
	protected $_expected_filename = 'ee3c084f7413076c92893d9d2dc34d15.jpg';
	protected $_size_params = array('width' => 100,
																	'crop_left' => 2);

	/** @test */
	public function imageShouldBeProportional() {
		$im = $this->_buildTestImage();
		$im->cropImage(48, 10, 0, 0);
		$im->resizeImage(100, 0, Imagick::FILTER_LANCZOS, 1);
		$this->assertEquals($im->getImageBlob(), 
												$this->_ressource->getImage()->getImageBlob());
	}
}



class AlbumRessourceFolioNumberTest extends ModelTestCase {
	protected $_ressource;

	public function setUp() {
		parent::setUp();
		$this->_ressource = Class_AlbumRessource::getLoader()->newInstanceWithId(4);
	}


	/** @test */
	public function withFilenameOneDotJpgFolioShouldBeOne() {
		$this->assertEquals('1', $this->_ressource->setFichier('1.jpg')->getFolio());
	}


	/** @test */
	public function withFilenameFourtyThreeUnderscoreTwoDotJpgFolioShouldBe43_2() {
		$this->assertEquals('43_2', $this->_ressource->setFichier('43_2.jpg')->getFolio());
	}


	/** @test */
	public function withFilenameB031906101_MS_001_0012VDotJpgFolioShouldBeMS_001_0012V() {
		$this->assertEquals('MS_001_0012V', $this->_ressource->setFichier('B031906101_MS_001_0012V.jpg')->getFolio());
	}


	/** @test */
	public function withFilename324_B031906101_MS_001_0012VDotJpgFolioShouldBeMS_001_0012V() {
		$this->assertEquals('MS_001_0012V', $this->_ressource->setFichier('324_B031906101_MS_001_0012V.jpg')->getFolio());
	}


	/** @test */
	public function withNoFileShouldAnswerId() {
		$this->assertEquals(4, $this->_ressource->setFichier('')->getFolio());
	}


	/** @test */
	public function forceFolioShouldUseIt() {
		$this->assertEquals('XX', $this->_ressource->setFichier('43_2.jpg')->setFolio('XX')->getFolio());
	}


	/** @test */
	public function withFileAndFolioEmptyStringShouldAnswerEmptyString() {
		$this->assertEquals('', $this->_ressource->setFichier('1.jpg')->setFolio('')->getFolio());
	}

}
