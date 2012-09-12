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

class BibNumeriqueControllerViewAlbumActionTest extends AbstractControllerTestCase {
	/** @test */
	public function withoutAlbumShouldRedirect() {
		$this->dispatch('/opac/bib-numerique/view-album');
		$this->assertRedirect();
	}


	/** @test */
	public function withNonExistisngAlbumShouldRedirect() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('find')
			->answers(null);

		$this->dispatch('/opac/bib-numerique/view-album/id/999');
		$this->assertRedirect();
	}
}


abstract class AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		$album = Class_Album::getLoader()
			->newInstanceWithId(999)
			->beDiaporama()
			->setTitre('Premier volume')
			->setDescription("On ne peut que reconnaitre le talent de l'artiste !")
			->setCategorie(Class_AlbumCategorie::getLoader()
										 ->newInstanceWithId(2)
										 ->setLibelle('Les enluminures')
										 ->setParentCategorie(Class_AlbumCategorie::getLoader()
																					->newInstanceWithId(3)
																					->setLibelle('La bible de souvigny')))
			->setThumbnailWidth(200)
			->setThumbnailLeftPageCropRight(10)
			->setThumbnailRightPageCropTop(5)
			->setIdOrigine('DC23')
			->setPdf('volume1.pdf');

		$im = new Imagick();
		$im->newPseudoImage(50, 10, "gradient:red-black");
		$im->setImageFormat('jpg');
		$firstRessource = Class_AlbumRessource::getLoader()
																->newInstanceWithId(1)
																->setFichier('1.jpg')
			                          ->setImage($im)
																->setFolio('1R3')
			                          ->setAlbum($album);


		$album->setRessources(array($firstRessource,
																Class_AlbumRessource::getLoader()
																->newInstanceWithId(2)
																->setFichier('2.pdf')
																->setAlbum($album)
																->setTitre('Procedure de numerisation')
																->setLinkTo('http://wikipedia.org/numerisation')
																->setDescription('Comment numériser avec joie')));
	}
}




class BibNumeriqueControllerAlbumPremierVolumeTestToJSON extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/bib-numerique/album/id/999.json', true);
		$this->json = json_decode($this->_response->getBody());
	}


	/** @test */
	function actionShouldBeJSON() {
		$this->assertAction('album');
	}


	/** @test */
	public function downloadUrlShouldBeBibNumeriquePdf999() {
		$this->assertEquals('/bib-numerique/download_album/id/999.pdf', $this->json->album->download_url);
	}


	/** @test */
	function albumIdShouldBe999() {
		$this->assertEquals(999, $this->json->album->id);
	}


	/** @test */
	function albumWidthShouldBe200() {
		$this->assertEquals(200, $this->json->album->width);
	}


	/** @test */
	function albumHeightShouldBe40() {
		$this->assertEquals(40, $this->json->album->height);
	}


	/** @test */
	function albumTitleShouldBePremierVolume() {
		$this->assertEquals('Premier volume', $this->json->album->titre);
	}


	/** @test */
	function albumDescriptionShouldBeOnNePeutQueReconnatre() {
		$this->assertEquals("On ne peut que reconnaitre le talent de l'artiste !",
												$this->json->album->description);
	}


	/** @test */
	function firstRessourceIdShouldBeOne() {
		$this->assertEquals(1, $this->json->album->ressources[0]->id);
	}


	/** @test */
	function firstRessourceFolioNoShouldBeOneRThree() {
		$this->assertEquals('1R3', $this->json->album->ressources[0]->foliono);
	}


	/** @test */
	function firstRessourceThumbnailShouldPassResizeParamsOfRightPageCropTopFive() {
		$this->assertContains('/bib-numerique/thumbnail/width/200/crop_top/5/crop_right/0/crop_bottom/0/crop_left/0/id/1',
													$this->json->album->ressources[0]->thumbnail);
	}


	/** @test */
	function firstRessourceNavigatorThumbnailShouldPassResizeParamsOfRightPageCropTopFiveWidth50() {
		$this->assertContains(BASE_URL . '/userfiles/album/999/thumbs/media/1.jpg',
													$this->json->album->ressources[0]->navigator_thumbnail);
	}


	/** @test */
	function firstRessourceOriginalShouldBeOneDotJpg() {
		$this->assertContains('userfiles/album/999/big/media/1.jpg',
													$this->json->album->ressources[0]->original);
	}


	/** @test */
	function secondRessourceTitreShouldBeProcedureDeNumerisation() {
		$this->assertEquals('Procedure de numerisation', $this->json->album->ressources[1]->titre);
		
	}


	/** @test */
	function secondRessourcesLinkToShouldBeWikipedia() {
		$this->assertEquals('http://wikipedia.org/numerisation', $this->json->album->ressources[1]->link_to);
	}


	/** @test */
	function secondRessourceDescriptionShouldBeCommentNumeriser() {
		$this->assertEquals('Comment numériser avec joie', $this->json->album->ressources[1]->description);
	}


	/** @test */
	function secondRessourceThumbnailShouldPassResizeParamsOfLeftPageCropRightTen() {
		$this->assertContains('/bib-numerique/thumbnail/width/200/crop_top/0/crop_right/10/crop_bottom/0/crop_left/0/id/2',
													$this->json->album->ressources[1]->thumbnail);
	}


	/** @test */
	function secondRessourceNavigatorThumbnailShouldPassResizeParamsOfLeftPageCropRightTenWidth50() {
		$this->assertContains(BASE_URL . '/public/opac/images/earth-logo.jpg',
													$this->json->album->ressources[1]->navigator_thumbnail);
	}
}



class BibNumeriqueControllerPermalinkTest extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findAllBy')
			->with(array('id_origine' => 999))
			->answers(array(
											Class_Exemplaire::getLoader()
											->newInstanceWithId(2)
											->setIdNotice(2)
											->setNotice(Class_Notice::getLoader()
																	->newInstanceWithId(2)),

											Class_Exemplaire::getLoader()
											->newInstanceWithId(34)
											->setIdNotice(123)
											->setNotice(Class_Notice::getLoader()
																	->newInstanceWithId(123)
																	->setTypeDoc(Class_TypeDoc::LIVRE_NUM))));
	}


	/** @test */
	public function permalinkNoticeWithIdOrigineShouldRdirectToRechercheNotice() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('findFirstBy')
			->with(array('id_origine' => 'DC23'))
			->answers(Class_Album::getLoader()->find(999));

		$this->dispatch('/opac/bib-numerique/notice/ido/DC23');
		$this->assertRedirectTo('/opac/recherche/viewnotice/id/123');
	}


	/** @test */
	public function permalinkNoticeWithIdShouldRdirectToRechercheNotice() {
		$this->dispatch('/opac/bib-numerique/notice/id/999');
		$this->assertRedirectTo('/opac/recherche/viewnotice/id/123');
	}


	/** @test */
	public function permalinkNoticeFolio1R3ShouldRedirectToRechercheNoticeHashPageTwo() {
		$this->dispatch('/opac/bib-numerique/notice/id/999/folio/1R3');
		$this->assertRedirectTo('/opac/recherche/viewnotice/id/123#/page/2');
	}


	/** @test */
	public function permalinkNoticeWithUnknowAlbumIdShouldRedirectToIndex() {
		$this->dispatch('/opac/bib-numerique/notice/id/xyz');
		$this->assertRedirectTo('/opac/index');
	}


	/** @test */
	public function permalinkNoticeWithUnknowExemplaireShouldRedirectToIndex() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findAllBy')
			->answers(array());

		$this->dispatch('/opac/bib-numerique/notice/id/999');
		$this->assertRedirectTo('/opac/index');
	}
}




class BibNumeriqueControllerAlbumPremierVolumeWithoutPDFTestToJSON extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	public function setUp() {
		parent::setUp();
		Class_Album::getLoader()->find(999)->setPdf(null);
		$this->dispatch('/opac/bib-numerique/album/id/999.json');
		$this->json = json_decode($this->_response->getBody());
	}


	/** @test */
	public function downloadUrlShouldBeNotSet() {
		$this->assertEmpty($this->json->album->download_url);
	}

}




class BibNumeriqueControllerDownloadRessourcesTest extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	/** @test */
	public function thumbnailIdOneShouldRenderThumbnail() {
		$this->dispatch('/opac/bib-numerique/thumbnail/id/1');
		$this->assertEquals(Class_AlbumRessource::getLoader()->find(1)->getImage()->getImageBlob(),
												$this->_response->getBody());
	}
}



class BibNumeriqueControllerBookletTest extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/bib-numerique/booklet/id/999');
	}


	/** @test */
	function actionShouldBeBooklet() {
		$this->assertAction('booklet');
	}

	/** @test */
	function amberShouldBeLoaded() {
		$this->assertXPath('//script[contains(@src, "amber.js")]');
	}

	/** @test */
	function AFIAmberPackageShouldBeLoaded() {
		$this->assertXPathContentContains('//script', 'AFI.js', $this->_response->getBody());
	}

	/** @test */
	function AFITestsAmberPackageShouldBeLoaded() {
		$this->assertXPathContentContains('//script', 'AFI-Tests.js');
	}


	/** @test */
	function pageShouldContainsCodeToOpenBooklet() {
		$this->assertXPathContentContains('//script', 
																			"smalltalk.BibNumAlbum._load_in_scriptsRoot_('/bib-numerique/album/id/999.json', '#booklet_999', '" . BASE_URL . "/amber/afi/souvigny/')",
																			$this->_response->getBody());
	}
}




class BibNumeriqueControllerViewAlbumActionPremierVolumeTest extends AbstractBibNumeriqueControllerAlbumActionPremierVolumeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/bib-numerique/view-album/id/999');
	}


	/** @test */
	public function titreShouldBePremierVolume() {
		$this->assertXPathContentContains('//div[@class="titre"]//h1', 'Premier volume');
	}


	/** @test */
	public function categorieAnchorShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="breadcrum"]//a', 'Les enluminures');
	}


	/** @test */
	public function ancestorCategorieAnchorShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="breadcrum"]//a', 'La bible de souvigny');
	}


	/** @test */
	public function cycleJSShouldBePresentInScriptfirstRessourceShouldBePresent() {
		$this->assertXPathContentContains('//script', "\$('div.slideshow-999 .medias').cycle");
	}


	/** @test */
	public function viewerScriptShouldBePresent() {
		$this->assertXPath('//script[contains(@src, "prettyPhoto.js")]');
	}
}




class BibNumeriqueControllerViewCategorieActionTest extends AbstractControllerTestCase {
	/** @test */
	public function withoutAlbumShouldRedirect() {
		$this->dispatch('/opac/bib-numerique/view-categorie');
		$this->assertRedirect();
	}


	/** @test */
	public function withNonExistisngAlbumShouldRedirect() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('find')
			->answers(null);

		$this->dispatch('/opac/bib-numerique/view-categorie/id/999');
		$this->assertRedirect();
	}
}



class BibNumeriqueControllerViewCategorieActionLesEnluminuresTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		Class_AlbumCategorie::getLoader()
			->newInstanceWithId(2)
			->setLibelle('Les enluminures')
			->setParentCategorie(Class_AlbumCategorie::getLoader()
													 ->newInstanceWithId(3)
													 ->setLibelle('La bible de souvigny'))
			->setAlbums(array(Class_Album::getLoader()
												->newInstanceWithId(999)
												->setTitre('Premier volume')
												->setFichier('999.jpg')
												->setDescription('On ne peut que reconnaitre le talent de l\'artiste !')));
		
		$this->dispatch('/opac/bib-numerique/view-categorie/id/2');
	}


	/** @test */
	public function parentAnchorShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="breadcrum"]//a', 'La bible de souvigny');
	}


	/** @test */
	public function albumPremierVolumeShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="bibnumerique-albums"]//a', 'Premier volume');
	}


	/** @test */
	public function thumbnailOfPremierVolumeShouldBePresent() {
		$this->assertXPath('//div[@class="bibnumerique-albums"]//img[contains(@src, "999.jpg")]');
	}


	/** @test */
	public function noSubcategoryShouldBePresent() {
		$this->assertNotXPath('//div[@class="bibnumerique-categories"]');
	}
}




abstract class BibNumeriqueControllerBibleDeSouvignyTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		Class_AlbumCategorie::getLoader()
			->newInstanceWithId(3)
			->setLibelle('La bible de souvigny')
			->setAlbums(array())
			->setSousCategories(array(Class_AlbumCategorie::getLoader()
																->newInstanceWithId(2)
																->setLibelle('Les enluminures')));
	}
}




class BibNumeriqueControllerViewCategorieActionLaBibleDeSouvignyTest extends BibNumeriqueControllerBibleDeSouvignyTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/bib-numerique/view-categorie/id/3');
	}


	/** @test */
	public function noParentAnchorShouldBePresent() {
		$this->assertNotXPath('//div[@class="breadcrum"]//a');
	}


	/** @test */
	public function noAlbumShouldBePresent() {
		$this->assertNotXPath('//div[@class="albums"]');
	}


	/** @test */
	public function subcategoryAnchorShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="bibnumerique-categories"]//a', 'Les enluminures');
	}
}




class BibNumeriqueControllerAlbumMultiMediasTest extends AbstractControllerTestCase {
	protected $_xpath;

	public function setUp() {
		parent::setUp();

		$this->_xpath = new Storm_Test_XPathXML();
		$this->_xpath->registerNameSpace('xspf', 'http://xspf.org/ns/0/');
	
		$album = Class_Album::newInstanceWithId(999)
			->beDiaporama()
			->setTitre('Plein de medias')
			->setRessources([Class_AlbumRessource::newInstanceWithId(2)
											 ->setFichier('mimi_jolie.mp3')
											 ->setTitre('Emilie jolie')
											 ->setVignette('mimi_jolie.png'),
											 
											 Class_AlbumRessource::newInstanceWithId(4)
											 ->setFichier('dark_night.mp4')
											 ->setTitre('Batman Dark Knight')
											 ->setVignette('batman.jpg'),


											 Class_AlbumRessource::newInstanceWithId(4)
											 ->setUrl('http://progressive.totaleclips.com.edgesuite.net/107/e107950_227.mp4')
											 ->setTitre('Hunger Games')
											 ->setVignette('hunger.jpg')]);

		$this->dispatch('/opac/bib-numerique/album-xspf-playlist/id/999.xml', true);
	}


	/** @test */
	public function xmlVersionShouldOneDotZero() {
		$this->_xpath->assertXmlVersion($this->_response->getBody(), "1.0");
	}


	/** @test */
	public function xmlEncodingShouldBeUtf8() {
		$this->_xpath->assertXmlEncoding($this->_response->getBody(), "UTF-8");
	}


	/** @test */
	public function firstTrackTitleShouldBeMimiJolie() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), 
																							'//xspf:playlist/xspf:trackList/xspf:track/xspf:title', 
																							'Emilie jolie');
	}


	/** @test */
	public function firstTrackImageShouldBeMimiJolieDotPng() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), 
																							'//xspf:playlist/xspf:trackList/xspf:track/xspf:image', 
																							'http://localhost' . BASE_URL . '/userfiles/album/999/thumbs/media/mimi_jolie.png');
	}


	/** @test */
	public function firstTrackLocationShouldBeMimiJolieDotMp3() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), 
																							'//xspf:playlist/xspf:trackList/xspf:track/xspf:location', 
																							'http://localhost' . BASE_URL . '/userfiles/album/999/big/media/mimi_jolie.mp3');
	}


	/** @test */
	public function secondTrackTitleShouldBeBatmanDarkKnight() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), 
																							'//xspf:playlist/xspf:trackList/xspf:track/xspf:title', 
																							'Batman Dark Knight');
	}


	/** @test */
	public function thirsTrackLocationShouldBeTotaleClipsDotCom() {
		$this->_xpath->assertXPathContentContains($this->_response->getBody(), 
																							'//xspf:playlist/xspf:trackList/xspf:track/xspf:location', 
																							'http://progressive.totaleclips.com.edgesuite.net/107/e107950_227.mp4');
	}

}

?>