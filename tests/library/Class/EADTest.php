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

class EADFolioMoulinsTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function folioForFolDot3ShouldBeThreeR() {
		$this->assertEquals('0003R', Class_EAD::moulinsToFolio('Fol. 3'));
	}


	/** @test */
	public function folioForFolDot4vShouldBeFourV() {
		$this->assertEquals('0004V', Class_EAD::moulinsToFolio('Fol. 4 v'));
	}


	/** @test */
	public function folioForFolDot5VOShouldBeFiveV() {
		$this->assertEquals('0005V', Class_EAD::moulinsToFolio('Fol. 5VO'));
	}


	/** @test */
	public function folioFor1ShouldBe1R() {
		$this->assertEquals('0001R', Class_EAD::moulinsToFolio('1'));
	}


	/** @test */
	public function folioForXShouldBeEmptyString() {
		$this->assertEquals('', Class_EAD::moulinsToFolio('X'));
	}
}



class EADEmptyLoadTest extends Storm_Test_ModelTestCase {
	protected $_ead;

	public function setUp() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')
			->answers(true);

		$this->_ead = new Class_EAD();
		$this->_ead->load('');
	}


	/** @test */
	public function getAlbumsShouldAnswerEmptyArray() {
		$this->assertEquals(array(), $this->_ead->getAlbums());
	}


	/** @test */
	public function noCategorieShouldHaveBeenSaved() {
		$this->assertFalse(Class_AlbumCategorie::getLoader()->methodHasBeenCalled('save'));
	}
}



class EADMoulinsTest extends PHPUnit_Framework_TestCase {
	protected static $ead;
	protected static $folio_souvigny_1R;
	protected static $folio_souvigny_3R;
	protected static $folio_souvigny_3V;


	public static  function setUpBeforeClass() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Matiere')
			->whenCalled('save')
			->willDo(function($model) {
					if ($model->getCodeAlpha()=='BIBLE DE SOUVIGNY') {
						$model->setId(99);
						$model->getLoader()->cacheInstance($model);
					}
					return true;
				})

			->whenCalled('findFirstBy')
			->with(array('code_alpha' => 'BIBLE DE SOUVIGNY'))
			->answers(null)

			->whenCalled('findFirstBy')
			->with(array('code_alpha' => 'THEOLOGIE'))
			->answers(Class_Matiere::getLoader()
								->newInstanceWithId(158)
								->setCodeAlpha('THEOLOGIE'))

			->whenCalled('findFirstBy')
			->with(array('code_alpha' => 'MEDECINE'))
			->answers(Class_Matiere::getLoader()
								->newInstanceWithId(234)
								->setCodeAlpha('MEDECINE'))

			->whenCalled('findFirstBy')
			->with(array('code_alpha' => 'PURGATOIRE'))
			->answers(Class_Matiere::getLoader()
								->newInstanceWithId(98)
								->setCodeAlpha('PURGATOIRE'));

		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('findFirstBy')
			->with(array('id_origine' => 'D09030001'))
			->answers(Class_Album::getLoader()
								->newInstanceWithId(1111)
								->setIdOrigine('D09030001')
								->setTitre('Bible')
								->setEditeur('Le moine')
								->setAnnee(1234)
								->setCategorie(Class_AlbumCategorie::getLoader()
															 ->newInstanceWithId(9)
															 ->setLibelle('Patrimoine'))
								->setRessources(array(
																			self::$folio_souvigny_1R = Class_AlbumRessource::getLoader()
																			->newInstanceWithId(23)
																			->setFolio('MS_001_0001R')
																			->setTitre('Mon titre'),

																			self::$folio_souvigny_3R = Class_AlbumRessource::getLoader()
																			->newInstanceWithId(24)
																			->setFolio('MS_001_0003R'),

																			self::$folio_souvigny_3V = Class_AlbumRessource::getLoader()
																			->newInstanceWithId(25)
																			->setFolio('MS_001_0003V'))))

			->whenCalled('findFirstBy')
			->answers(null)

			->whenCalled('save')
			->answers(true);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')
			->willDo(function($model) {
					$model->setId(123);
					return true;
				});

		self::$ead = new Class_EAD();
		self::$ead->loadFile('./tests/fixtures/ead_moulins.xml');
	}


	public static function tearDownAfterClass() {
		Storm_Model_Abstract::unsetLoaders();
	}


	public function setUp() {
		$this->_ead = self::$ead;
		$this->_folio_souvigny_1R = self::$folio_souvigny_1R;
		$this->_folio_souvigny_3R = self::$folio_souvigny_3R;
		$this->_folio_souvigny_3V = self::$folio_souvigny_3V;
		$this->_souvigny = array_first($this->_ead->getAlbums());
		$this->_eglise_izeure = array_at(89, $this->_ead->getAlbums());
	}


	/** @test */
	public function getAlbumsSizeShouldBe95() {
		$this->assertEquals(95, count($this->_ead->getAlbums()));
	}


	/** @test */
	public function firstAlbumTitreShouldBeBibliaSacraBibleDeSouvigny() {
		$this->assertEquals('Biblia sacra (Bible de Souvigny)', $this->_souvigny->getTitre());
	}


	/** @test */
	public function firstAlbumAnneeShouldBeNull() {
		$this->assertEquals(null, $this->_souvigny->getAnnee());
	}

 
	/** @test */
	public function firstAlbumIDOrigineShouldBeD09030001() {
		$this->assertEquals($this->_souvigny->getIdOrigine(), 'D09030001');
	}


	/** @test */
	public function firstAlbumShouldBeExistingSouvignyInDB() {
		$this->assertEquals(1111, $this->_souvigny->getId());
	}


	/** @test */
	public function firstAlbumCategorieShouldBePatrimoine() {
		$this->assertEquals('Patrimoine', $this->_souvigny->getCategorie()->getLibelle());
	}


	/** @test */
	public function firstAlbumIdLangShouldBeLatin() {
		$this->assertEquals('lat', $this->_souvigny->getIdLangue());
	}


	/** @test */
	public function firstAlbumShouldHaveBeenSaved() {
		$this->assertTrue(Class_Album::getLoader()->methodHasBeenCalledWithParams('save', array($this->_souvigny)));
	}


	/** @test */
	public function firstAlbumMatiereShouldBe99() {
		$this->assertEquals('99',
												$this->_souvigny->getMatiere());
	}


	/** @test */
	public function firstAlbumProvenanceShouldBeSouvignyPrieure() {
		$this->assertEquals('Souvigny, Prieuré',
												$this->_souvigny->getProvenance());
	}


	/** @test */
	public function firstAlbumCoteShouldBeMS001() {
		$this->assertEquals('MS001', $this->_souvigny->getCote());
	}


	/** @test */
	public function firstAlbumNote316ShouldContainsDescriptionReliure() {
		$notes = $this->_souvigny->getNotesAsArray();
		$this->assertContains('Reliure en bois recouverte de cuir', $notes['316$a']);
	}


	/** @test */
	public function matiereSouvignyShouldHaveLibelleBibleDeSouvigny() {
		$this->assertEquals('Bible de Souvigny', 
												Class_Matiere::getLoader()->find(99)->getLibelle());
	}


	/** @test */
	public function dateCreationShouldBeTodayYYYY_MM_DD() {
		$this->assertEquals(date('Y-m-d'), 
												Class_Matiere::getLoader()->find(99)->getDateCreation());
	}


	/** 
	 * numéro_de_notice: 99
	 * @test 
	 */
	public function ninetyFourthAlbumTitleShouldBeBibliographieDuBerry() {
		$this->assertEquals('Bibliographie du Berry, du Nivernais, de la Marche et du Forez', 
												array_at(93, $this->_ead->getAlbums())->getTitre());
		
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumIdLangShouldBeFre() {
		$this->assertEquals('fre', $this->_eglise_izeure->getIdLangue());
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumNote305ShouldBeXVIIIe_siecle() {
		$this->assertEquals('XVIIIe siècle', $this->_eglise_izeure->getNote('305$a'));
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumIdShouldBeNull() {
		$this->assertEquals(null, $this->_eglise_izeure->getId());
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumShouldHaveBeenSaved() {
		$this->assertTrue(Class_Album::getLoader()
											->methodHasBeenCalledWithParams('save', 
																											array($this->_eglise_izeure)));
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumCategorieShouldImportCurrDate() {
		$this->assertEquals(sprintf('import du %s', date('d M Y')),
												$this->_eglise_izeure->getCategorie()->getLibelle());
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function eightyNinethAlbumTypeDocShouldBeLivreNumerique() {
		$this->assertEquals(Class_TypeDoc::LIVRE_NUM,
												$this->_eglise_izeure->getTypeDocId());
	}


	/** 
	 * numéro_de_notice: 95
	 * @test 
	 */
	public function categorieImportShouldHaveBeenSaved() {
		$this->assertTrue(Class_AlbumCategorie::getLoader()
											->methodHasBeenCalledWithParams('save', 
																											array($this->_eglise_izeure->getCategorie())));
	}

	/** @test */
	public function eightyNinethAlbumCatIdShouldBe123() {
		$this->assertEquals(123, $this->_eglise_izeure->getCatId());
	}


	/** 
	 * test non régression
	 * @expectedException Storm_Model_Exception
	 * @expectedExceptionMessage Tried to call unknown method Class_Album::getCat
	 * @test 
	 */
	public function albumGetCatShouldRaiseError() {
		$this->_eglise_izeure->getCat();
	}


	/** @test */
	public function folioSouvigny1RTitreShouldBeMonTitre() {
		$this->assertEquals('Mon titre', $this->_folio_souvigny_1R->getTitre());
	}


	/** @test */
	public function folioSouvigny3RTitreShouldBePraefatioInPentateuchum() {
		$this->assertEquals('Praefatio in Pentateuchum', $this->_folio_souvigny_3R->getTitre());
	}


	/** @test */
	public function folioSouvigny3VTitreShouldBeCapitulaGenesis() {
		$this->assertEquals('Capitula Genesis', $this->_folio_souvigny_3V->getTitre());
	}


	/** 
	 * numéro_de_notice: 2, avec controlaccess / title
	 * @test 
	 */
	public function secondAlbumTitleShouldBeCodeAvecGloses() {
		$this->assertEquals('Code avec gloses',
												array_at(1, $this->_ead->getAlbums())->getTitre());
	}


	/** 
	 * numéro_de_notice: 2, avec controlaccess / title
	 * @test 
	 */
	public function secondAlbumAuteurShouldBeJustinien() {
		$this->assertEquals('Justinien',
												array_at(1, $this->_ead->getAlbums())->getAuteur());
	}


	/** 
	 * numéro_de_notice: 17, avec auteur sur plusieurs lignes
	 * @test 
	 */
	public function albumSeventiethAuteurShouldBeFaure() {
		$this->assertEquals('Faure, François, trésorier et receveur général du duché de Bourbonnais',
												array_at(15, $this->_ead->getAlbums())->getAuteur());
	}


	/** 
	 * numéro_de_notice: 17, avec auteur sur plusieurs lignes
	 * @test 
	 */
	public function albumSeventiethCoteShouldBeMS017() {
		$this->assertEquals('MS017',
												array_at(15, $this->_ead->getAlbums())->getCote());
	}


	/** 
	 * numéro_de_notice: 8, avec matiere / sujet
	 * @test 
	 */
	public function albumEighthMatiereShouldBe158() {
		$this->assertEquals('158',
												array_at(7, $this->_ead->getAlbums())->getMatiere());
	}


	/** 
	 * numéro_de_notice: 49, avec matiere / sujet
	 * @test 
	 */
	public function albumFourtyFifthMatiereShouldBe234colon98() {
		$this->assertEquals('234;98',
												array_at(45, $this->_ead->getAlbums())->getMatiere());
	}


	/** 
	 * numéro_de_notice: 18, avec annee, pas siecle
	 * @test 
	 */
	public function albumSixteenthAnneeShouldBe1844() {
		$this->assertEquals('1844',
												array_at(16, $this->_ead->getAlbums())->getAnnee());
	}


	/** 
	 * numéro_de_notice: 18, avec annee, pas siecle
	 * @test 
	 */
	public function albumSixteenthSiecle305_a_ShouldBeEmpty() {
		$notes = array_at(16, $this->_ead->getAlbums())->getNotesAsArray();
		$this->assertFalse(array_key_exists('305$a', $notes));
	}


	/** 
	 * numéro_de_notice: 72, avec deux title, ne prends que le premier
	 * @test 
	 */
	public function albumSeventyTwoTitreShouldBeLeJardinOuParterrreSpirituel() {
		$album = array_at(66, $this->_ead->getAlbums());
		$this->assertEquals('Le jardin ou parterre spirituel', $album->getTitre());
		return $album;
	}


	/** 
	 * @depends albumSeventyTwoTitreShouldBeLeJardinOuParterrreSpirituel
	 * @test 
	 */
	public function albumSeventyTwoNote200DollarBSupportShouldBePapier($album) {
		$this->assertEquals('Papier', $album->getNote('200$b'));
	}

}


?>