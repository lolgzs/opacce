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

abstract class AlbumHarlockTestCase extends Storm_Test_ModelTestCase {
	/** @var Storm_Test_ObjectWrapper */
	protected $_wrapper;

	/** @var Class_Album */
	protected $_album;


	protected function setUp() {
		parent::setUp();

		Class_CosmoVar::getLoader()
			->newInstanceWithId('types_docs')
			->setListe("0:non identifié\r\n1:livres\r\n2:bd");


		Class_CodifLangue::getLoader()
			->newInstanceWithId('frm')
			->setLibelle('français');

		$this->_album = Class_Album::getLoader()
			->newInstanceWithId(999)
			->setTitre('Harlock')
			->setTypeDocId(2)
			->setLangue('')
			->setNotes(serialize(array('305$a' => '20eme siecle')))
			->addNote('317$a', 'viens de l\'espace');


		$this->_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('save')->answers(true)->getWrapper();
	}
}




class AlbumHarlockBasicTest extends AlbumHarlockTestCase {
	/** @test */
	public function addFileShouldCallInitializeOnNewRessource() {
		$ressourceMock = Storm_Test_ObjectWrapper::on(new Class_AlbumRessource());
		$ressourceMock
			->whenCalled('setAlbum')->with($this->_album)->answers($ressourceMock)
			->getWrapper()->whenCalled('initializeWith')->answers(null);
		
		$wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('newInstance')
			->answers($ressourceMock)
			->getWrapper()->beStrict();

		$this->_album->addFile(null);

		$this->assertTrue($ressourceMock->methodHasBeenCalled('initializeWith'));
	}


	/** @test */
	public function permalinkShouldBeBibNumNotice999() {
		$this->assertEquals(array('module' => 'opac',
															'controller' => 'bib-numerique',
															'action' => 'notice',
															'id' => 999), 
												$this->_album->getPermalink());
	}


	/** @test */
	public function albumShouldBeVisible() {
		$this->assertTrue($this->_album->isVisible());
	}


	/** @test */
	public function getVisibleShouldReturnTrue() {
		$this->assertTrue($this->_album->getVisible());
	}


	/** @test */
	public function withVisibleFalseShouldNotBeVisible() {
		$this->_album->setVisible(false);
		$this->assertFalse($this->_album->isVisible());
	}

	
	/** @test */
	public function withIdOrigine_05DC03_PermalinkShouldBeBibNumNoticeIDO_05DC03() {
		$this->_album->setIdOrigine('05DC03');
		$this->assertEquals(array('module' => 'opac',
															'controller' => 'bib-numerique',
															'action' => 'notice',
															'ido' => '05DC03'), 
												$this->_album->getPermalink());
	}


	/** @test */
	function basePathShouldBeUserfiles_Slash_Album_999() {
		$this->assertEquals('..' . BASE_URL . '/userfiles/album/999/', $this->_album->getBasePath());
	}

	/** @test */
	function thumbnailsPathShouldBeUserfiles_Slash_thumbs_999() {
		$this->assertEquals('..' . BASE_URL . '/userfiles/album/999/thumbs/', $this->_album->getThumbnailsPath());
	}

	/** @test */
	function toArrayShouldContainsNativeAndThumbnailsAttributes() {
		$this->assertEquals(
				['id' => 999,
				 'titre' => 'Harlock',
				 'type_doc_id' => 2,
				 'fichier' => '',
				 'date_maj' => '',
				 'annee' => '',
				 'id_langue' => 'fre',
				 'id_origine' => '',
				 'thumbnail_width' => 400,
				 'thumbnail_left_page_crop_top' => 0,
				 'thumbnail_left_page_crop_right' => 0,
				 'thumbnail_left_page_crop_bottom' => 0,
				 'thumbnail_left_page_crop_left' => 0,
				 'thumbnail_right_page_crop_top' => 0,
				 'thumbnail_right_page_crop_right' => 0,
				 'thumbnail_right_page_crop_bottom' => 0,
				 'thumbnail_right_page_crop_left' => 0,
				 'thumbnail_crop_top' => 0,
				 'thumbnail_crop_right' => 0,
				 'thumbnail_crop_bottom' => 0,
				 'thumbnail_crop_left' => 0,
				 'display_one_page' => false,
				 'cfg_thumbnails' => '',
				 'sous_titre' => '',
				 'pdf' => '',
				 'auteur' => '',
				 'matiere' => '',
				 'provenance' => '',
				 'cote' => '',
				 'editeur' => '',
				 'visible' => true,
				 'droits' => ''],
				$this->_album->toArray());
	}


	/** @test */
	public function setThumbnailLeftPageCropLeftShouldUpdateCfgThumbnails() {
		$expected_cfg = $this->_album->getThumbnailAttributes();
		$expected_cfg['thumbnail_left_page_crop_left'] = 20;
		
		$this->_album->setThumbnailLeftPageCropLeft(20);
		$this->assertEquals(ZendAfi_Filters_Serialize::serialize($expected_cfg),
												$this->_album->getCfgThumbnails());
	}


	/** @test */
	public function getThumbnalRightPageCropBottomShouldAnswer10WhenCfgThumbnailContainsIt() {
		$this->_album->setCfgThumbnails(ZendAfi_Filters_Serialize::serialize(array('thumbnail_right_page_crop_bottom' => 10)));
		$this->assertEquals(10, $this->_album->getThumbnailRightPageCropBottom());
	}


	/** @test */
	public function getThumbnailHeightShouldAnswerOne() {
		$this->assertEquals(1, $this->_album->getThumbnailHeight());
	}


	/** @test */
	function getTypeDocShouldReturnTypeDocWithLabelBD() {
		$this->assertEquals('bd', $this->_album->getTypeDoc()->getLabel());
	}


	/** @test */
	function annee2000ShouldBeValid() {
		$this->assertTrue($this->_album->setAnnee('2000')->isValid());
	}


	/** @test */
	function anneeNextYearShouldBeValid() {
		$this->assertTrue($this->_album->setAnnee(date('Y', strtotime('+1 year')))->isValid());
	}


	/** @test */
	function annee3000ShouldNotBeValid() {
		$this->assertFalse($this->_album->setAnnee('3000')->isValid());
	}


	/** @test */
	function annee799ShouldNotBeValid() {
		$this->assertFalse($this->_album->setAnnee('799')->isValid());
	}


	/** @test */
	function annee800ShouldBeSavedWithFourDigits() {
		$this->assertSame('0800', 
											$this->_album->setAnnee(800)->getAnnee());
	}


	/** @test */
	function langueShouldBeFre() {
		$this->assertEquals('fre', $this->_album->getLangue()->getId());
	}


	/** @test */
	public function thumbnailWidthShouldBe400() {
		$this->assertEquals(400, $this->_album->getThumbnailWidth());
	}


	/** @test */
	public function notesAsArrayShouldContains305and317() {
		$this->assertEquals(array('305$a' => '20eme siecle', '317$a' => 'viens de l\'espace'), 
												$this->_album->getNotesAsArray());
	}


	/** @test */
	public function isGallicaShouldAnswersFalse() {
		$this->assertFalse($this->_album->isGallica());
	}
}



abstract class AlbumHarlockFileUploadHandlerTestCase extends AlbumHarlockTestCase {
	/** @var PHPUnit_Framework_MockObject_MockObject */
	protected $_handler;


	protected function setUp() {
		parent::setUp();

		$this->_wrapper->whenCalled('delete')->answers(null)->getWrapper();

		$this->_handler = $this->getMockBuilder('Class_Upload')
											->disableOriginalConstructor()
											->getMock();
	}
}


class AlbumHarlockReceivingFileTest extends AlbumHarlockFileUploadHandlerTestCase {
	/** @test */
	public function withoutFileShouldReturnTrue() {
		$_FILES['fichier']['size'] = 0;
		$this->assertTrue($this->_album->receiveFile());
	}


	/** @test */
	public function withUploadErrorShouldReturnFalse() {
		$_FILES['fichier']['size'] = 1;
		$this->_handler
			->expects($this->once())
			->method('receive')
			->will($this->returnValue(false));

		$this->_handler
			->expects($this->once())
			->method('setAllowedExtensions')
			->will($this->returnValue($this->_handler));

		$this->assertFalse($this->_album->setUploadHandler($this->_handler)->receiveFile());
	}
}


class AlbumHarlockReceivingPdfTest extends AlbumHarlockFileUploadHandlerTestCase {
	/** @test */
	public function withoutFileShouldReturnTrue() {
		$_FILES['pdf']['size'] = 0;
		$this->assertTrue($this->_album->receivePdf());
	}


	/** @test */
	public function withUploadErrorShouldReturnFalse() {
		$_FILES['pdf']['size'] = 1;
		$this->_handler
			->expects($this->once())
			->method('receive')
			->will($this->returnValue(false));

		$this->_handler
			->expects($this->once())
			->method('setAllowedExtensions')
			->will($this->returnValue($this->_handler));

		$this->assertFalse($this->_album->setUploadHandler($this->_handler)->receivePdf());
	}
}




class AlbumHarlockWithRessourcesTest extends AlbumHarlockTestCase {
	/** @var array */
	protected $_resources;


	protected function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('findAllBy')
			->answers(array(
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(1)
				  ->setIdAlbum(999)
					->setTitre('Nausica'),
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(2)
				  ->setIdAlbum(999)
				  ->setFolio('03R')
					->setTitre('Arcadia'),
			));

		$this->_resources = $this->_album->getRessources();
	}


	/** @test */
	public function harlockShouldHaveTwoResources() {
		$this->assertEquals(2, count($this->_resources));
	}


	/** @test */
	public function firstResourceShouldBeNausica() {
		$this->assertEquals('Nausica', $this->_resources[0]->getTitre());
	}


	/** @test */
	public function firstRessouncPermalinkShouldBeBibNumNoticeId999Folio1() {
		$this->assertEquals(array('module' => 'opac',
															'controller' => 'bib-numerique',
															'action' => 'notice',
															'id' => 999,
															'folio' => 1), 
												$this->_resources[0]->getPermalink());
	}


	/** @test */
	public function secondResourceShouldBeArcadia() {
		$this->assertEquals('Arcadia', $this->_resources[1]->getTitre());
	}


	/** @test */
	public function firstRessouncPermalinkShouldBeBibNumNoticeId999Folio03R() {
		$this->assertEquals(array('module' => 'opac',
															'controller' => 'bib-numerique',
															'action' => 'notice',
															'id' => 999,
															'folio' => '03R'), 
												$this->_resources[1]->getPermalink());
	}
}




class AlbumHarlockWithOnlyImagesRessourcesTest extends AlbumHarlockTestCase {
	protected function setUp() {
		parent::setUp();
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('findAllBy')
			->answers(array(
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(1)
					->setTitre('Nausica')
				  ->setFichier('1.png'),
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(2)
					->setTitre('Arcadia')
 				  ->setFichier('2.gif'),
			));
	}


	/** @test */
	public function hasImageShouldBeTrue() {
		$this->assertTrue($this->_album->hasImage());
	}


	/** @test */
	public function hasImageOnlyShouldBeTrue() {
		$this->assertTrue($this->_album->hasImageOnly());
	}


	/** @test */
	public function getImagesShouldReturnAllRessources() {
		$this->assertEquals(2, count($this->_album->getImages()));
	}


	/** @test */
	public function hasFileShouldBeFalse() {
		$this->assertFalse($this->_album->hasFile());
	}


	/** @test */
	public function getFilesShouldReturnEmpty() {
		$this->assertEmpty($this->_album->getFiles());
	}
}




class AlbumHarlockWithNotOnlyImagesRessourcesTest extends AlbumHarlockTestCase {
	protected function setUp() {
		parent::setUp();
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('findAllBy')
			->answers(array(
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(1)
					->setTitre('Nausica')
				  ->setFichier('1.png'),
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(2)
					->setTitre('Arcadia')
 				  ->setFichier('2.pdf'),
			));
	}


	/** @test */
	public function hasImageShouldBeTrue() {
		$this->assertTrue($this->_album->hasImage());
	}


	/** @test */
	public function hasImageOnlyShouldBeFalse() {
		$this->assertFalse($this->_album->hasImageOnly());
	}


	/** @test */
	public function getImagesShouldReturnOneRessource() {
		$this->assertEquals(1, count($this->_album->getImages()));
	}


	/** @test */
	public function hasFileShouldBeTrue() {
		$this->assertTrue($this->_album->hasFile());
	}


	/** @test */
	public function getFilesShouldReturnOneRessource() {
		$this->assertEquals(1, count($this->_album->getFiles()));
	}
}




class AlbumHarlockWithNotImagesRessourcesTest extends AlbumHarlockTestCase {
	protected function setUp() {
		parent::setUp();
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('findAllBy')
			->answers(array(
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(1)
					->setTitre('Nausica')
				  ->setFichier('1.zip'),
				Class_AlbumRessource::getLoader()
					->newInstanceWithId(2)
					->setTitre('Arcadia')
 				  ->setFichier('2.pdf'),
			));
	}


	/** @test */
	public function hasImageShouldBeFalse() {
		$this->assertFalse($this->_album->hasImage());
	}


	/** @test */
	public function hasImageOnlyShouldBeFalse() {
		$this->assertFalse($this->_album->hasImageOnly());
	}


	/** @test */
	public function getImagesShouldReturnNoRessource() {
		$this->assertEmpty($this->_album->getImages());
	}


	/** @test */
	public function hasFileShouldBeTrue() {
		$this->assertTrue($this->_album->hasFile());
	}


	/** @test */
	public function getFilesShouldReturnTwoRessources() {
		$this->assertEquals(2, count($this->_album->getFiles()));
	}
}




class AlbumHarlockSortingResourcesTest extends AlbumHarlockTestCase {
	/** @var Class_AlbumRessource */
	protected $_nausica;

	/** @var Class_AlbumRessource */
	protected $_arcadia;

	/** @var Class_AlbumRessource */
	protected $_sylphide;


	protected function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
																						->whenCalled('save')->answers(true);

		$this->_nausica = Class_AlbumRessource::getLoader()
												->newInstanceWithId(1)
												->setTitre('Nausica')
												->setFichier('1_AB.jpg')
												->setOrdre(1);

		$this->_arcadia = Class_AlbumRessource::getLoader()
												->newInstanceWithId(2)
												->setTitre('Arcadia')
												->setFichier('03.jpg')
												->setOrdre(2);

		$this->_sylphide = Class_AlbumRessource::getLoader()
												->newInstanceWithId(3)
												->setTitre('Sylphide')
												->setFichier('3_25.jpg')
												->setOrdre(3);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('findAllBy')
			->answers(array($this->_nausica, $this->_arcadia, $this->_sylphide))
			->getWrapper()
			->whenCalled('save')->answers(true);
	}


	/** @test */
	public function movingNausicaAtTopShouldNotChangeOrder() {
		$this->_album->moveRessourceAfter($this->_nausica, 0);

		$this->assertEquals(1, $this->_nausica->getOrdre());
		$this->assertEquals(2, $this->_arcadia->getOrdre());
		$this->assertEquals(3, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingNausicaAfterHerselfShouldNotChangeOrder() {
		$this->_album->moveRessourceAfter($this->_nausica, $this->_nausica->getId());

		$this->assertEquals(1, $this->_nausica->getOrdre());
		$this->assertEquals(2, $this->_arcadia->getOrdre());
		$this->assertEquals(3, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingNausicaAfterArcadiaShouldSortArcadiaThenNausicaThenSylphide() {
		$this->_album->moveRessourceAfter($this->_nausica, 2);

		$this->assertEquals(2, $this->_nausica->getOrdre());
		$this->assertEquals(1, $this->_arcadia->getOrdre());
		$this->assertEquals(3, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingNausicaAfterSylphideShouldSortArcadiaThenSylphideThenNausica() {
		$this->_album->moveRessourceAfter($this->_nausica, 3);

		$this->assertEquals(3, $this->_nausica->getOrdre());
		$this->assertEquals(1, $this->_arcadia->getOrdre());
		$this->assertEquals(2, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingArcadiaToTopShouldSortArcadiaThenNausicaThenSylphide() {
		$this->_album->moveRessourceAfter($this->_arcadia, 0);

		$this->assertEquals(2, $this->_nausica->getOrdre());
		$this->assertEquals(1, $this->_arcadia->getOrdre());
		$this->assertEquals(3, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingArcadiaAfterSylphideShouldSortNausicaThenSylphideThenArcadia() {
		$this->_album->moveRessourceAfter($this->_arcadia, 3);

		$this->assertEquals(1, $this->_nausica->getOrdre());
		$this->assertEquals(3, $this->_arcadia->getOrdre());
		$this->assertEquals(2, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingSylphideToTopShouldSortSylphideThenNausicaThenArcadia() {
		$this->_album->moveRessourceAfter($this->_sylphide, 0);

		$this->assertEquals(2, $this->_nausica->getOrdre());
		$this->assertEquals(3, $this->_arcadia->getOrdre());
		$this->assertEquals(1, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function movingSylphideAfterNausicaShouldSortNausicaThenSylphideThenArcadia() {
		$this->_album->moveRessourceAfter($this->_sylphide, 1);

		$this->assertEquals(1, $this->_nausica->getOrdre());
		$this->assertEquals(3, $this->_arcadia->getOrdre());
		$this->assertEquals(2, $this->_sylphide->getOrdre());
	}


	/** @test */
	public function sortRessourceByFileNameShouldSortArcadiaThenSylphideThenNausica() {
		$this->_album
			->addRessource($albator = Class_AlbumRessource::getLoader()
										 ->newInstanceWithId(5)
										 ->setTitre('Sylphide')
										 ->setFichier('05.jpg')
										 ->setOrdre(8));

		$this->_album->sortRessourceByFileName();


		$this->assertEquals(1, $this->_arcadia->getOrdre());
		$this->assertEquals(2, $albator->getOrdre()); 
		$this->assertEquals(3, $this->_sylphide->getOrdre(), $this->_sylphide->getFichierWithoutId()); 
		$this->assertEquals(4, $this->_nausica->getOrdre());

	}
}