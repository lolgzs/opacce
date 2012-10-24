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

abstract class Admin_AlbumControllerTestCase extends Admin_AbstractControllerTestCase { 
	protected $_category_wrapper;
	protected $_album_wrapper;

	public function setUp() {
		parent::setUp();

		Class_CosmoVar::getLoader()
			->newInstanceWithId('types_docs')
			->setListe("1:cd\r\n200:non identifié\r\n201:livres\r\n202:bd");

		$langue_loader = Class_CodifLangue::getLoader();
		$cus = $langue_loader->newInstanceWithId('cus')->setLibelle('couchitique');
		$fre = $langue_loader->newInstanceWithId('fre')->setLibelle('français');
		$dak = $langue_loader->newInstanceWithId('dak')->setLibelle('dakota');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CodifLangue')
			->whenCalled('findAllBy')
			->answers(array($cus, $fre, $dak));

		$this->_category_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie');
		$this->_album_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album');

		Class_AlbumCategorie::getLoader()
			->newInstanceWithId(2)
			->setParentId(0)
			->setLibelle('Favoris')
			->setSousCategories(array())
			->setAlbums(array());

		Class_AlbumCategorie::getLoader()
			->newInstanceWithId(6)
			->setLibelle('Adulte')
			->setParentId(2)
			->setSousCategories(array())
			->setAlbums(array());
				
		Class_AlbumCategorie::getLoader()
			->newInstanceWithId(38)
			->setParentId(0)
			->setSousCategories(array())
			->setAlbums(array())
			->setLibelle('Patrimoine');

		Class_Album::getLoader()
			->newInstanceWithId(43)
			->setTitre('Mes BD')
			->setAuteur('Laurent')
			->setTags('bd;dessin')
			->setDateMaj('2011-10-05 17:12:00')
			->setDescription('Les préférées')
			->setAnnee(1978)
			->beDiaporama()
			->setIdOrigine('DC023')
			->setMatiere('1;3;5')
			->setDewey('10;12')
			->setGenre('65;66;67')
			->setPdf('souvigny.pdf')
			->setProvenance('Prieuré, Souvigny')
			->setCote('MS001')
			->setVisible(false);

		Class_Album::getLoader()
			->newInstanceWithId(44)
			->setTitre('Bible Souvigny')
			->beLivreNumerique()
			->setThumbnailAttributes(['thumbnail_width' => 350,
																'thumbnail_left_page_crop_left' => 10,
																'thumbnail_left_page_crop_right' => 5,
																'thumbnail_left_page_crop_bottom' => 2,
																'thumbnail_right_page_crop_left' => 5])
			->setRessources([]);

	  Class_Album::getLoader()
			->newInstanceWithId(24)
			->setTitre('Mes Romans')
			->setLangue('');
	}
}




class Admin_AlbumControllerIndexTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->_category_wrapper
			->whenCalled('findAllBy')
			->with(['parent_id' => 0, 'order' => 'libelle'])
			->answers([Class_AlbumCategorie::newInstanceWithId(2)
								 ->setParentId(0)
								 ->setLibelle('Favoris')
								 ->setSousCategories([Class_AlbumCategorie::find(6)
																			->setAlbums([Class_Album::find(24)])])
								 ->setAlbums([Class_Album::find(43)]),
								 Class_AlbumCategorie::newInstanceWithId(38)
								 ->setParentId(0)
								 ->setSousCategories([])
								 ->setAlbums([])
								 ->setLibelle('Patrimoine')]);

		$this->_album_wrapper
				->whenCalled('getItemsOf')
				->with(0)
				->answers(array(Class_Album::getLoader()
						->newInstanceWithId(66)
						->setParentId(0)
						->setTitre("L'orphelin")))

				->whenCalled('getItemsOf')
				->with(2)
				->answers(array(Class_Album::getLoader()->find(43)))

				->whenCalled('getItemsOf')
				->with(6)
				->answers(array(Class_Album::getLoader()->find(24)))

				->whenCalled('countBy')
				->answers(1);
				
		$this->dispatch('/admin/album', true);
	}


	/** @test */
	public function controllerShouldBeAlbum() {
		$this->assertController('album');
	}


	/** @test */
	public function actionShouldBeIndex() {
		$this->assertAction('index');
	}


	/** @test */
	public function titreShouldBeCollections() {
		$this->assertXPathContentContains("//h1", 'Collections');
	}


	/** @test */
	public function categorieFavorisShouldBeAtRoot() {
		$this->assertXPathContentContains("//ul/li[@class='categorie']", 'Favoris');
	}


	/** @test */
	public function categoriePatrimoineShouldBeAtRoot() {
		$this->assertXPathContentContains("//ul/li[@class='categorie']", 'Patrimoine');
	}


	/** @test */
	public function albumMesBDShouldBeInCategorieFavoris() {
		$this->assertXPathContentContains("//div[@class='item-label']", 'Mes BD');
	}


	/** @test */
	public function albumMesBDShouldHaveIconForDiaporama() {
		$this->assertXPath("//div//img[contains(@src, 'images.png')]");
	}


	/** @test */
	public function categorieAdulteShouldBeInCategorieFavoris() {
		$this->assertXPathContentContains("//ul/li/ul/li[@class='categorie']", 'Adulte');
	}


	/** @test */
	public function albumMesRomansShouldBeInCategorieAdulte() {
		$this->assertXPathContentContains("//div[@class='item-label']", 'Mes Romans');
	}


	/** @test */
	public function categorieAdulteShouldHaveAnAjouteCategorieLink() {
		$this->assertXPath("//a[contains(@href, 'add_categorie_to/id/6')]");
	}


	/** @test */
	public function categorieAdulteShouldHaveAnAjouteAlbumLink() {
		$this->assertXPath("//a[contains(@href, 'add_album_to/id/6')]");
	}


	/** @test */
	public function categorieAdulteShouldHaveEditCategorieLink() {
		$this->assertXPath("//a[contains(@href, 'edit_categorie/id/6')]");
	}


	/** @test */
	public function categorieAdulteShouldNotHaveDeleteCategorieLink() {
		$this->assertNotXPath("//a[contains(@href, 'delete_categorie/id/6')]");
	}


	/** @test */
	public function categoriePatrimoineShouldHaveDeleteCategorieLink() {
		$this->assertXPath("//a[contains(@href, 'delete_categorie/id/38')]");
	}


	/** @test */
	public function albumMesRomansShouldHaveEditLink() {
		$this->assertXPath("//a[contains(@href, 'edit_album/id/24')]");
	}


	/** @test */
	public function albumMesRomansShouldHavePreviewLink() {
		$this->assertXPath("//a[contains(@href, 'preview_album/id/24')]");
	}


	/** @test */
	public function albumMesRomansPreviewLinkImgShouldBeShowDotGif() {
		$this->assertXPath("//a[contains(@href, 'preview_album/id/24')]//img[contains(@src, '/show.gif')]");
	}


	/** @test */
	public function albumMesBDPreviewLinkImgShouldBeHideDotGif() {
		$this->assertXPath("//a[contains(@href, 'preview_album/id/43')]//img[contains(@src, '/hide.gif')]");
	}


	/** @test */
	public function albumMesRomansShouldHaveDeleteLink() {
		$this->assertXPath("//a[contains(@href, 'delete_album/id/24')]");
	}


	/** @test */
	public function shouldHaveAButtonToAddACategory() {
		$this->assertXPath("//div[contains(@onclick, '/admin/album/add_categorie')]");
	}


	/** @test */
	public function categorieAlbumsNonClassesShouldBeVisible() {
		$this->assertXPathContentContains('//li', 'Albums non classés');
	}


	/** @test */
	public function categorieAlbumsNonClassesShouldHaveNoActions() {
		$this->assertNotXPath('//ul[@class="root"]/li[last()]/div[@class="actions"]');
	}


	/** @test */
	public function categorieAlbumsNonClassesShouldHaveAlbumOrphelin() {
		$this->assertXPathContentContains('//ul[@class="root"]/li[last()]//li', "L'orphelin");
	}


	/** @test */
	public function categorieAlbumsNonClassesShouldNotHaveCategories() {
		$this->assertNotXPath('//ul[@class="root"]/li[last()]//li[@class="categorie"]');
	}

}




class Admin_AlbumControllerWithoutBibNumTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('BIBNUM')->setValeur('0');
		$this->dispatch('/admin/album', true);
	}


	/** @test */
	public function buttonAddAlbumShouldNotBeVisible() {

		$this->assertNotXPath("//div[contains(@onclick, '/admin/album/add_categorie')]");
	}


	/** @test */
	public function noAjouteAlbumLinkShouldBeVisible() {
		$this->assertNotXPath("//a[contains(@href, 'add_album_to')]");
	}


	/** @test */
	public function noAddCategorieToLinkShouldBeVisible() {
		$this->assertNotXPath("//a[contains(@href, 'add_categorie_to')]");
	}
}




class Admin_AlbumControllerAddCategorieToFavorisTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/add_categorie_to/id/2');
	}


  /** @test */
  public function controllerShouldBeAlbum() {
		$this->assertController('album');
  }


  /** @test */
  public function actionShouldBeAddCategorieTo() {
		$this->assertAction('add_categorie_to');
  }


  /** @test */
  public function titleShouldBeAjouterUneCategorie() {
		$this->assertXPathContentContains('//h1', 'Ajouter une catégorie à la collection "Favoris"');
	}


	/** @test */
  public function formActionShouldBeEmpty() {
		$this->assertXPath('//form[@action=""]');
	}


	/** @test */
  public function formShouldHaveATextFieldForLibelle() {
		$this->assertXPath("//form[@id='categorie']//input[@type='text'][@name='libelle']");
	}


	/** @test */
	public function shouldHaveValidateButton() {
		$this->assertXPath("//div[contains(@onclick, \"document.forms['categorie'].submit()\")]");
	}


	/** @test */
	public function shouldHaveBackButton() {
		$this->assertXPathContentContains("//table//td", "Retour");
	}
}



class Admin_AlbumControllerPostAddCategorieToFavorisTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('libelle' => 'Informatique');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);


		$this->_category_wrapper
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/album/add_categorie_to/id/2');
		$this->new_cat = $this->_category_wrapper->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function newCategorieLibelleShouldBeInformatique() {
		$this->assertEquals('Informatique', $this->new_cat->getLibelle());
	}


	/** @test */
	public function newCategorieParentShouldBeFavoris() {
		$this->assertEquals('Favoris', $this->new_cat->getParentCategorie()->getLibelle());
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/album/index');
	}
}



class Admin_AlbumControllerInvalidPostAddCategorieToFavorisTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array();

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);


		$this->_category_wrapper
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/album/add_categorie_to/id/2');
	}


	/** @test */
	public function saveShouldNotHaveBeenCalled() {
		$this->assertFalse($this->_category_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	public function actionShouldBeAddCategorieTo() {
		$this->assertAction('add_categorie_to');
	}


	/** @test */
	public function shouldDisplayErrorUneValeurEstRequies() {
		$this->assertXPathContentContains("//form//li",'Une valeur est requise');
	}
}


class Admin_AlbumControllerAddCategorieAtRootTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/add_categorie');
	}


  /** @test */
  public function controllerShouldBeAlbum() {
		$this->assertController('album');
  }


  /** @test */
  public function actionShouldBeAddCategorieTo() {
		$this->assertAction('add_categorie');
  }
}



class Admin_AlbumControllerPostAddCategorieAtRootTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('libelle' => 'Multimédia');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);


		$this->_category_wrapper
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/album/add_categorie');
		$this->new_cat = $this->_category_wrapper->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function newCategorieLibelleShouldBeMultimedia() {
		$this->assertEquals('Multimédia', $this->new_cat->getLibelle());
	}


	/** @test */
	public function newCategorieLibelleShouldNotHaveParent() {
		$this->assertFalse($this->new_cat->hasParentCategorie());
	}


  /** @test */
  public function titleShouldBeAjouterUneCollection() {
		$this->assertXPathContentContains("//h1", "Ajouter une collection");
	}
}



class Admin_AlbumControllerEditCategorieFavorisTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/edit_categorie/id/2');
	}

  /** @test */
  public function controllerShouldBeAlbum() {
		$this->assertController('album');
  }


  /** @test */
  public function actionShouldBeAddCategorieTo() {
		$this->assertAction('edit_categorie');
  }


	/** @test */
  public function formActionShouldBeEmpty() {
		$this->assertXPath('//form[@action=""]');
	}


	/** @test */
  public function formShouldHaveATextFieldForLibelleWithFavoris() {
		$this->assertXPath("//form[@id='categorie']//input[@type='text'][@name='libelle'][@value='Favoris']");
	}
}


class Admin_AlbumControllerDeleteCategorieFavorisTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->_category_wrapper
			->whenCalled('delete')
			->answers(true);

		$this->dispatch('/admin/album/delete_categorie/id/2');
	}


	/** @test */
	public function deleteShouldHaveBeenCalledOnFavoris() {
		$this->deleted_cat = $this->_category_wrapper->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals('Favoris', $this->deleted_cat->getLibelle());
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/album/index');
	}
}



class Admin_AlbumControllerAddAlbumToPatrimoineTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/add_album_to/id/38');
	}


	/** @test */
	public function controllerShouldBeAlbum() {
		$this->assertController('album');
	}


	/** @test */
	public function actionShouldBeAddAlbumTo() {
		$this->assertAction('add_album_to');
	}


	/** @test */
	public function pageShouldNotContainsALinkToEditImages() {
		$this->assertNotXPath('//a[contains(@href, "edit_images")]');
	}


  /** @test */
  public function titleShouldBeAjouterUnAlbum() {
		$this->assertXPathContentContains("//h1", "Ajouter un album dans la collection \"Patrimoine\"");
	}


	/** @test */
  public function formActionShouldBeEmpty() {
		$this->assertXPath("//form[@action='']");
	}


	/** @test */
  public function formShouldHaveATextFieldForTitre() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='titre']");
	}


	/** @test */
	public function formShouldHaveAFileFieldForFichier() {
		$this->assertXPath("//form[@id='album']//input[@type='file'][@name='fichier']");
	}


	/** @test */
	public function fieldForFichierShouldStateLimitedFilesExtensions() {
		$this->assertXPathContentContains('//form[@id="album"]//td', 
																			'(jpg, gif, png)', 
																			$this->_response->getBody());
	}


	/** @test */
  public function formShouldHaveATextAreaForDescription() {
		$this->assertXPath("//form[@id='album']//textarea[@name='description']");
	}


	/** @test */
	public function fieldForAuthorShouldBePresent() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='auteur']");
	}


	/** @test */
	public function fieldForRightsShouldBePresent() {
		$this->assertXPath("//form[@id='album']//input[@type='radio'][@name='droits']");
	}


	/** @test */
	public function fieldForRightsPrecisionShouldBePresent() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='droits_precision']");
	}


	/** @test */
	public function shouldHaveValidateButton() {
		$this->assertXPath("//div[contains(@onclick, \"document.forms['album'].submit()\")]");
	}


	/** @test */
	public function shouldHaveBackButton() {
		$this->assertXPathContentContains("//table//td", "Retour");
	}


	/** @test */
	public function permalienShouldNotBeVisible() {
		$this->assertNotXPathContentContains('//div', 'Permalien');
	}
}




class Admin_AlbumControllerPostAlbumRenaissanceToPatrimoineTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = ['titre' => 'Renaissance',
						 'sous_titre' => 'Ze Renaissance',
			       'description' => 'Oeuvres majeures sous François 1er'];

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$_FILES['fichier'] = array('size' => 0,
															 'name' => '',
															 'tmp_name' => '',
															 'error' => 4);
		$_FILES['pdf'] = array('size' => 0, 
													 'name' => '', 
													 'tmp_name' => '', 
													 'error' => 4);

		$this->_album_wrapper
			->whenCalled('save')
			->willDo(function($model) {
					$model->setId(67);
					return true;
				});

		$this->dispatch('/admin/album/add_album_to/id/38', true);
		$this->new_album = $this->_album_wrapper->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function newAlbumTitreShouldBeRenaissance() {
		$this->assertEquals('Renaissance', $this->new_album->getTitre());
	}


	/** @test */
	public function newAlbumSousTitreShouldBeZeRenaissance() {
		$this->assertEquals('Ze Renaissance', $this->new_album->getSousTitre());
	}


	/** @test */
	public function newAlbumIdShouldBeSixtySeven() {
		$this->assertEquals(67, $this->new_album->getId());
	}


	/** @test */
	public function newAlbumDescriptionShouldBeOeuvresMajeures() {
		$this->assertEquals('Oeuvres majeures sous François 1er', $this->new_album->getDescription());
	}


	/** @test */
	public function shouldRedirectToEditAlbum() {
		$this->assertRedirectTo('/admin/album/edit_album/id/67');
	}


	/** @test */
	public function categoryShouldBePatrimoine() {
		$this->assertEquals('Patrimoine', $this->new_album->getCategorie()->getLibelle());
	}
}



class Admin_AlbumControllerPostAlbumWithoutTitreToPatrimoineTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$data = array('description' => 'Oeuvres majeures sous François 1er');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$this->dispatch('/admin/album/add_album_to/id/38');
	}


	/** @test */
	public function saveShouldNotHaveBeenCalled() {
		$this->assertFalse($this->_album_wrapper->methodHasBeenCalled('save'));
	}


	/** @test */
	public function actionShouldBeAddAlbumTo() {
		$this->assertAction('add_album_to');
	}


	/** @test */
	public function shouldDisplayErrorUneValeurEstRequies() {
		$this->assertXPathContentContains("//form//li",'Une valeur est requise');
	}


	/** @test */
  public function derniereModificationDivShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//div", 'Dernière modification');
	}

}



class Admin_AlbumControllerEditAlbumMesBDTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->_category_wrapper
				->whenCalled('getAllLibelles')
				->answers(array('2' => 'Favoris',
				'6' => 'Favoris>Adulte'));
		$this->dispatch('/admin/album/edit_album/id/43');
	}


	/** @test */
	public function actionShouldBeEditAlbum() {
		$this->assertAction('edit_album');
	}


	/** @test */
	public function permalienShoulBeVisible() {
		$this->assertXPath('//input[@value="http://localhost'.BASE_URL.'/bib-numerique/notice/ido/DC023"]',
											 $this->_response->getBody());
	}


	/** @test */
	public function permalienVignetteShoulBeVisible() {
		$this->assertXPath('//input[@value="http://localhost'.BASE_URL.'/bib-numerique/notice-thumbnail/ido/DC023"]');
	}


	/** @test */
	public function pageShouldContainsALinkToEditImages() {
		$this->assertXPath('//a[contains(@href, "album/edit_images/id/43")]');
	}


	/** @test */
  public function formActionShouldBeEmpty() {
		$this->assertXPath("//form[@action='']");
	}


	/** @test */
  public function divShouldDisplayMajDate() {
		$this->assertXPathContentContains("//div", '5 octobre 2011 17:12:00');
	}


	/** @test */
  public function formShouldHaveATextFieldForTitre() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='titre'][@value='Mes BD']");
	}


	/** @test */
	public function formShouldHaveACheckBoxForVisible() {
		$this->assertXPath('//form//input[@type="checkbox"][@name="visible"]');
	}


	/** @test */
  public function formShouldHaveATextFieldForCote() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='cote'][@value='MS001']");
	}


	/** @test */
  public function formShouldHaveATextFieldForProvenance() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='provenance'][@value='Prieuré, Souvigny']");
	}


	/** @test */
	public function formShouldHaveAComboToSelectCategorie() {
		$this->assertXPathContentContains("//form//select[@name='cat_id']/option[1][@value=2]", 'Favoris');
	}


	/** @test */
	public function comboCategoriesShouldDisplayFullPath() {
		$this->assertXPathContentContains("//form//select[@name='cat_id']/option[2][@value=6]", 'Favoris&gt;Adulte');
	}

	/** @test */
  public function formShouldHaveATextFieldForAuteur() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='auteur'][@value='Laurent']");
	}


	/** @test */
  public function formShouldHaveATextFieldForAnnee() {
		$this->assertXPath("//form[@id='album']//input[@type='text'][@name='annee'][@value='1978'][@maxlength='4']");
	}


	/** @test */
  public function formShouldHaveATextAreaForTags() {
		$this->assertXPathContentContains("//form[@id='album']//textarea[@name='tags']", 'bd;dessin');
	}


	/** @test */
  public function formShouldHaveATextAreaForDescription() {
		$this->assertXPathContentContains("//form[@id='album']//textarea[@name='description']",
																			"Les préférées");
	}


	/** @test */
	public function formShouldHaveFileInputForPDF() {
		$this->assertXPath("//form[@id='album']//input[@type='file'][@name='pdf']");
	}


	/** @test */
	function formShouldHaveAComboBoxForTypeDocSelection() {
		$this->assertXPathContentContains("//select[@name='type_doc_id']//option[@value='200']", 
																			'non identifié');

		$this->assertXPathContentContains("//select[@name='type_doc_id']//option[@value='201']", 
																			'livres');

		$this->assertXPathContentContains("//select[@name='type_doc_id']//option[@selected='selected'][@value='101']", 
																			'Diaporama',
																			$this->_response->getBody());
	}


	/** @test */
	function formShouldHaveTagSuggestForMatiere() {
		$this->assertXPath("//input[@name='matiere'][@value='1;3;5']");
	}

	/** @test */
	function formShouldHaveTagSuggestForDewey() {
		$this->assertXPath("//input[@name='dewey'][@value='10;12']");
	}


	/** @test */
	function formShouldHaveAComboBoxForLangueSelection() {
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='cus']", 
																			'couchitique');
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='fre'][@selected='selected']", 
																			'français');
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='dak']", 
																			'dakota');
	}


	/** @test */
	function formShouldHaveTagSuggestForGenre() {
		$this->assertXPath("//input[@name='genre'][@value='65;66;67']");
	}

}



class Admin_AlbumControllerEditAlbumMesRomans extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Album::getLoader()
				->newInstanceWithId(24)
				->setTitre('Mes Romans')
				->setLangue('')
				->setNotes([['field' => 856,
							       'data' => ['x' => 'video',
											          'a' => 'http://www.youtube.com/watch?v=FqXYGBZooHg&feature=html5_ns&list=UUzfAMGBG12oxX7dSYAurMGA&playnext=1']]]);
		$this->dispatch('/admin/album/edit_album/id/24');
	}

	
	/** @test */
	function formShouldHaveEmptyTagSuggestForMatiere() {
		$this->assertXPath("//input[@name='matiere'][@value='']");
	}

	/** @test */
	function formShouldHaveAComboBoxForLangueSelection() {
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='cus']", 
																			'couchitique');
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='fre'][@selected='selected']", 
																			'français');
		$this->assertXPathContentContains("//select[@name='id_langue']//option[@value='dak']", 
																			'dakota');
	}
}


class Admin_AlbumControllerPostEditAlbumMesBDTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$data = array('titre' => 'Mes BD',
									'description' => "Les préférées de l'année",
									'tags' => 'selection',
									'auteur' => 'Pat',
									'type_doc_id' => 201,
									'annee' => 1998,
									'matiere' => '5;6',
									'dewey' => '15',
									'genre' => '12',
									'cote' => 'MS003',
									'provenance' => 'Annecy');

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);

		$_FILES['fichier'] = array('size' => 0,
															 'name' => '', 
															 'tmp_name' => '', 
															 'error' => 4);
		$_FILES['pdf'] = array('size' => 0, 
													 'name' => '', 
													 'tmp_name' => '', 
													 'error' => 4);

		$this->dispatch('/admin/album/edit_album/id/43');

		$this->bd = Class_Album::getLoader()->find(43);
	}


	/** @test */
	public function descriptionShouldBeUpdated() {
		$this->assertEquals("Les préférées de l'année", $this->bd->getDescription());
	}


	/** @test */
	public function shouldRedirectToEditAlbumIdFourtyThree() {
		$this->assertRedirectTo('/admin/album/edit_album/id/43');
	}


	/** @test */
	function tagsShouldContainsSelection() {
		$this->assertEquals('selection', $this->bd->getTags());
	}


	/** @test */
	function auteurShouldBePat() {
		$this->assertEquals('Pat', $this->bd->getAuteur());
	}


	/** @test */
	function anneeShouldBe1998() {
		$this->assertSame('1998', $this->bd->getAnnee());
	}


	/** @test */
	function dateMajShouldBeNow() {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),
													$this->bd->getDateMaj());
	}


	/** @test */
	function typeDocShouldBeLivres() {
		$this->assertEquals('livres', $this->bd->getTypeDoc()->getLabel());
	}


	/** @test */
	function matiereShouldContainsFiveAndSix() {
		$this->assertEquals('5;6', $this->bd->getMatiere());
	}


	/** @test */
	function deweyShouldContainsFifteen() {
		$this->assertEquals('15', $this->bd->getDewey());
	}


	/** @test */
	function genreShouldContainsTwelve() {
		$this->assertEquals('12', $this->bd->getGenre());
	}


	/** @test */
	public function coteShouldBeMS003() {
		$this->assertEquals('MS003', $this->bd->getCote());
	}


	/** @test */
	public function provenanceShouldBeAnnecy() {
		$this->assertEquals('Annecy', $this->bd->getProvenance());
	}
}



class Admin_AlbumControllerDeleteAlbumMesBDTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->_album_wrapper
			->whenCalled('delete')
			->answers(true);

		$this->dispatch('/admin/album/delete_album/id/43');
	}


	/** @test */
	public function deleteShouldHaveBeenCalledOnMesBD() {
		$this->deleted_album = $this->_album_wrapper->getFirstAttributeForLastCallOn('delete');
		$this->assertEquals('Mes BD', $this->deleted_album->getTitre());
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/album/index');
	}
}



abstract class Admin_AlbumControllerAlbumHarlockTestCase extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$harlock = Class_Album::getLoader()
			->newInstanceWithId(999)
			->setTitre('Harlock')
			->setIdOrigine('HL22')
			->setTypeDocId(Class_TypeDoc::DIAPORAMA)
			->setCategorie(
										 Class_AlbumCategorie::getLoader()->newInstanceWithId(999)
										 ->setLibelle('')
										 );

		$harlock->setRessources(array(Class_AlbumRessource::getLoader()
																	->newInstanceWithId(1)
																	->setTitre('Arcadia')
																	->setDescription('Le vaisseau spatial')
																	->setFichier('1.png')
																	->setLinkTo('/afi-opac3/cms/viewarticle/id/2')
																	->setMatiere('999')
																	->setOrdre(5)
																	->setAlbum($harlock),
																	
																	Class_AlbumRessource::getLoader()
																	->newInstanceWithId(2)
																	->setTitre('Nausica')
																	->setFichier('2.png')
																	->setFolio('4R')
																	->setOrdre(1)
																	->setAlbum($harlock)));
	}
}




class Admin_AlbumControllerAlbumHarlockSortRessourcesActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('save')->answers(true);

		Class_AlbumRessource::getLoader()->find(1)->setFichier('ZZ.jpg');
		Class_AlbumRessource::getLoader()->find(2)->setFichier('AA.jpg');
		$this->dispatch('/admin/album/sortressources/id/999');
	}


	/** @test */
	public function arcadiaOrdreShouldAnswerTwo() {
		$this->assertEquals(2, Class_AlbumRessource::getLoader()->find(1)->getOrdre());
	}


	/** @test */
	public function nausicaOrdreShouldAnswerOne() {
		$this->assertEquals(1, Class_AlbumRessource::getLoader()->find(2)->getOrdre());
	}


	/** @test */
	public function responseShouldRedirectToEditImages() {
		$this->assertRedirectTo('/admin/album/edit_images/id/999');
	}


	/** @teset */
	public function ressourcesShouldHaveBeenSaved() {
		$this->assertTrue(Class_AlbumRessource::getLoader()->methodHasBeenCalled('save'));
	}
}




class Admin_AlbumControllerAlbumHarlockMoveImageActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	/** @test */
	public function ressourceNausicaShouldBeAfterArcadiaWithCorrectIds() {
		$this->dispatch('/admin/album/move-image/id/2/after/1');
		$this->assertGreaterThan(Class_AlbumRessource::getLoader()->find(1)->getOrdre(),
														 Class_AlbumRessource::getLoader()->find(2)->getOrdre());
	}


	/** @test */
	public function ressourceNausicaShouldStayBeforeArcadiaWithWrongIds() {
		$this->dispatch('/admin/album/move-image/id/zork/after/1');
		$this->assertGreaterThan(Class_AlbumRessource::getLoader()->find(2)->getOrdre(),
														 Class_AlbumRessource::getLoader()->find(1)->getOrdre());
	}
}




class Admin_AlbumControllerAlbumHarlockEditImagesActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/edit_images/id/999');
	}

	/** @test */
	public function pageShouldContainsALinkToEditAlbum() {
		$this->assertXPath('//a[contains(@href, "album/edit_album/id/999")]');
	}


	/** @test */
	public function pageShouldContainsALinkToSortRessoruces() {
		$this->assertXPath('//a[contains(@href, "album/sortressources/id/999")]');
	}


	/** @test */
	public function multiUploadShouldBePresent() {
		$this->assertXpath('//div[@id="albumRessourcesUpload_conteneur"]');
	}


	/** @test */
	public function checkAllSelectionShouldBePresent() {
		$this->assertXPath('//input[@type="checkbox"][@class="all_mass_deletions"]');
	}


	/** @test */
	public function massDeletionLinkShouldBePresent() {
		$this->assertXPath('//a[@onclick="return fireMediaMassDeletion();"]');
	}


	/** @test */
	public function shouldHaveTwoRessources() {
		$this->assertXpathCount('//ul[@class="tree"]/li[@class="ressource"]', 2);
	}


	/** @test */
	public function linkToEditRessourceNausicaShouldBePresent() {
		$this->assertXpath('//a[contains(@href, "album/edit_ressource/id/2")]');
	}


	/** @test */
	public function selectionCheckboxNausicaShouldBePresent() {
		$this->assertXPath('//input[@type="checkbox"][@value="2"][@onclick="toggleMediaSelection(this);"]');
	}


	/** @test */
	public function withoutAlbumShouldRedirect() {
		$this->bootstrap();
		parent::setUp();
		$this->dispatch('/admin/album/edit_images');
		$this->assertRedirectTo('/admin/album');
	}
}



class Admin_AlbumControllerAlbumHarlockEditRessourceOneActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/edit_ressource/id/1');
	}


	/** @test */
	function inputTitreShouldContainsArcadia() {
		$this->assertXPath('//input[@name="titre"][@value="Arcadia"]');
	}


	/** @test */
	public function pageShouldContainsALinkToEditAlbum() {
		$this->assertXPath('//a[contains(@href, "album/edit_album/id/999")]');
	}


	/** @test */
	public function pageShouldDisplayNumberOfMediaInPanel() {
		$this->assertXPathContentContains('//a[contains(@href, "album/edit_images/id/999")]', '002');
	}

	
	/** @test */
	public function panelShouldContainsIconSupportForAlbumHarlock() {
		$this->assertXPath("//div//img[contains(@src, 'images.png')]");
	}


	/** @test */
	public function inputFolioShouldNotBePresent() {
		$this->assertNotXPath('//input[@name="folio"]');
	}


	/** @test */
	function textAreaDescriptionShouldContainsLeVaisseauSpatial() {
		$this->assertXPathContentContains('//textarea[@name="description"]', 'Le vaisseau spatial');
	}


	/** @test */
	function inputFileShouldBeVisible() {
		$this->assertXPath('//input[@type="file"]');
	}


	/** @test */
	function inputLinkToShouldContainsViewArticleTwo() {
		$this->assertXPath('//input[@name="link_to"][contains(@value, "cms/viewarticle/id/2")]');
	}


	/** @test */
	function formShouldHaveTagSuggestForMatiere() {
		$this->assertXPath("//input[@name='matiere'][@value='999']");
	}
}


class Admin_AlbumControllerAlbumHarlockEditRessourceTwoActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/edit_ressource/id/2');
	}

	/** @test */
	function formShouldHaveTagSuggestEmptyForMatiere() {
		$this->assertXPath("//input[@name='matiere'][@value='']");
	}


	/** @test */
	public function permalienShoulBeVisible() {
		$this->assertXPath('//input[@value="http://localhost'.BASE_URL.'/bib-numerique/notice/ido/HL22/folio/4R"]',
											 $this->_response->getBody());
	}
}


class Admin_AlbumControllerAlbumHarlockEditOtherRessourcesActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	/** @test */
	function ressourceTwoInputLinkToShouldBeEmpty() {
		$this->dispatch('/admin/album/edit_ressource/id/2');
		$this->assertXPath('//input[@name="link_to"][@value=""]');
	}


	/** @test */
	function ressourceInexistantShouldRedirectToAlbumIndex() {
		$this->dispatch('/admin/album/edit_ressource/id/6666666666');
		$this->assertRedirectTo('/admin/album');
	}
}


class Admin_AlbumControllerAlbumHarlocPostRessourceOneActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('save')
			->answers(true);

		$this->postDispatch('/admin/album/edit_ressource/id/1',
												['titre' => 'Atlantis',
												 'description' => 'autre vaisseau',
												 'link_to' => 'http://www.atlantis.com',
												 'matiere' => '666',
												 'folio' => '3R']);
		
		$this->ressource = Class_AlbumRessource::find(1);
	}


	/** @test */
	function linkToUrlShouldBeAtlantisDotCom() {
		$this->assertEquals('http://www.atlantis.com', $this->ressource->getLinkTo());
	}


	/** @test */
	function titreShouldBeAtlantis() {
		$this->assertEquals('Atlantis', $this->ressource->getTitre());
	}


	/** @test */
	public function folioShouldBeThreeR() {
		$this->assertEquals('3R', $this->ressource->getFolio());
	}


	/** @test */
	function descriptionShouldBeAutreVaisseau() {
		$this->assertEquals('autre vaisseau', $this->ressource->getDescription());
	}


	/** @test */
	function responseShouldRedirectToEditRessourceIdOne() {
		$this->assertRedirectTo('/admin/album/edit_ressource/id/1', 
														$this->_response->isRedirect());
	}


	/** @test */
	function albumDateMajShouldBeNow() {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),
													Class_Album::getLoader()->find(999)->getDateMaj());
	}


	/** @test */
	function albumShouldHaveBeenSaved() {
		$this->assertTrue($this->_album_wrapper->methodHasBeenCalled('save'));
	}

	/** @test */
	function matiereShouldBe666() {
		$this->assertEquals('666', $this->ressource->getMatiere());
	}
}


class Admin_AlbumControllerAlbumHarlocPostErrorsRessourceOneActionTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	/** @test */
	function withUrlBlobShouldDisplayUrlNotValid() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('save')
			->answers(true);

		$this->postDispatch('/admin/album/edit_ressource/id/1',
												array('link_to' => 'blob'));

		$this->assertXPathContentContains("//ul[@class='errors']//li", 
																			"'blob' n'est pas une URL valide",
																			$this->_response->getBody());
	}
}




class Admin_AlbumControllerPreviewMesBDTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/preview_album/id/43');
	}


	/** @test */
	public function titreShouldBeVisualisationAlbumMesBD() {
		$this->assertXPathContentContains('//h1', 'Visualisation de l\'album "Mes BD"');
	}


	/** @test */
	public function formThumbnailsShouldHaveInputForThumbnailWidth() {
		$this->assertXPath('//form[@id="thumbnails"]//input[@name="thumbnail_width"][@value="400"]');
	}


	/** @test */
	public function formThumbnailsShouldNotHaveInputForThumbnailRightPageCropLeft() {
		$this->assertNotXPath('//input[@name="thumbnail_right_page_crop_left"]');
	}


	/** @test */
	public function formThumbnailsShouldNotHaveInputForThumbnailLeftPageCropLeft() {
		$this->assertNotXPath('//input[@name="thumbnail_left_page_crop_left"]');
	}


	/** @test */
	public function pageShouldContainsPoidsDeLaVignette() {
		$this->assertXPathContentContains('//div', 'Poids de la première vignette');
	}
}




class Admin_AlbumControllerPreviewAlbumBibleSouvignyTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/preview_album/id/44');
	}

	/** @test */
	public function titreShouldBeVisualisationAlbumBibleSouvigny() {
		$this->assertXPathContentContains('//h1', 'Visualisation de l\'album "Bible Souvigny"');
	}

	/** @test */
	public function formThumbnailsShouldHaveInputForThumbnailWidth() {
		$this->assertXPath('//form[@id="thumbnails"]//input[@name="thumbnail_width"][@value="350"]');
	}

	/** @test */
	public function formThumbnailsShouldHaveInputForThumbnailRightPageCropLeft() {
		$this->assertXPath('//form[@id="thumbnails"]//input[@name="thumbnail_right_page_crop_left"][@value="5"]');
	}


	/** @test */
	public function pageShouldContainsLinkToEdit() {
		$this->assertXPath("//a[contains(@href, 'album/edit_album/id/44')]");
	}

	/** @test */
	public function bookletShouldBeLoaded() {
		$this->assertXPathContentContains('//script', 'smalltalk.BibNumAlbum');
	}
}


class Admin_AlbumControllerPreviewAlbumBibleSouvignyPostTest extends Admin_AlbumControllerTestCase {
	protected $_souvigny;
	public function setUp() {
		parent::setUp();
		$this->_souvigny = Class_Album::getLoader()->find(44);
		$this->postDispatch('/admin/album/preview_album/id/44',
												array('thumbnail_right_page_crop_right' => 34));
	}

	/** @test */
	public function thumbnailRightPageCropRightShouldBeThirtyFour() {
		$this->assertEquals(34, $this->_souvigny->getThumbnailRightPageCropRight(),
												$this->_response->getBody());
	}


	/** @test */
	public function albumSouvignyShouldHaveBeenSaved() {
		$this->assertEquals($this->_souvigny, 
												Class_Album::getLoader()->getFirstAttributeForLastCallOn('save'));
	}


	/** @test */
	public function shouldRedirectToCurrentUrl() {
		$this->assertRedirectTo('/admin/album/preview_album/id/44');
	}
}


class Admin_AlbumControllerPreviewAlbumBibleSouvignyPostWrongDataTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/album/preview_album/id/44',
												array('thumbnail_right_page_crop_right' => 'zork'));
	}
	

	/** @test */
	public function albumSouvignyShouldNotHaveBeenSaved() {
		$this->assertFalse(Class_Album::getLoader()->methodHasBeenCalled('save'));
	}
}




class Admin_AlbumControllerImportEADTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/album/import_ead');
	}


	/** @test */
	public function menuGaucheAdminShouldContainsLinkToImportEAD() {
		$this->assertXPath('//div[@class="menuGaucheAdmin"]//a[contains(@href, "admin/album/import_ead")]');
	}


	/** @test */
	public function titreShouldBeRessourcesEAD() {
		$this->assertXPathContentContains('//h1', 'Import EAD');
	}


	/** @test */
	public function pageShouldContainsFormImportEAD() {
		$this->assertXPath('//form[contains(@action, "admin/album/import_ead")]');
	}


	/** @test */
	public function formImportEADShouldContainsFileInputForXML() {
		$this->assertXPath('//input[@type="file"][@name="ead"]');
	}


	/** @test */
	public function formShouldHaveSubmitButtonImportEAD() {
		$this->assertXPath('//input[@type="submit"][@value="Importer le fichier EAD"]');
	}
}




/** LL: quand j'aurais trouvé comment contourner is_uploaded_file */
abstract class Admin_AlbumControllerPostImportEADTest extends Admin_AlbumControllerAlbumHarlockTestCase {
	public function setUp() {
		parent::setUp();

		ZendAfi_Form::beValid();

		$_FILES = array("ead" => array('name' => 'ead_moulins.xml',
																	 'type' => 'text/xml',
																	 'tmp_name' => 'tests/fixtures/ead_moulins.xml',
																	 'error' => 0,
																	 'size' => 130448));

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')
			->answers(true);

		$this->postDispatch('admin/album/import_ead', array());
	}


	/** @test */
	public function saveAlbumCategorieShouldHaveBeenCalled() {
		$this->assertTrue(Class_AlbumCategorie::getLoader()->methodHasBeenCalled('save'));
	}
}



class Admin_AlbumControllerPreviewFilmArteVODTest extends Admin_AlbumControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		Class_Album::getLoader()
			->newInstanceWithId(102)
			->setTitre('Mulholland drive')
			->beArteVOD()
			->setNotes(array(
											 array('field' => '856',
														 'data' => array('x' => 'trailer',
																						 'a' => 'http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4')),
											 array('field' => '856',
														 'data' => array('x' => 'trailer',
																						 'a' => 'http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.flv')),
											 
											 array('field' => '856',
														 'data' => array('x' => 'poster',
																						 'a' => 'http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg'))));
			
		$this->dispatch('/admin/album/preview_album/id/102', true);
	}


	/** @test */
	public function formVignetteShouldNotBeVisible() {
		$this->assertNotXPath('//form');
	}


	/** @test */
	public function albumhouldHaveIconForArteVOD() {
		$this->assertXPath("//div//img[contains(@src, 'artevod.png')]");
	}


	/** @test */
	public function pageShouldNotContainsPoidsDeLaVignette() {
		$this->assertNotXPathContentContains('//div', 'Poids de la première vignette');
	}


	/** @test */
	public function videoTagShouldContainsTwoSources() {
		$this->assertXPathCount('//video//source', 2, $this->_response->getBody());
	}


	/** @test */
	public function videoTagShouldContainsMp4() {
		$this->assertXPath('//video//source[@src="http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4"][@type="video/mp4"]');
	}


	/** @test */
	public function videoTagShouldContainsFlv() {
		$this->assertXPath('//video//source[@src="http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.flv"][@type="video/flv"]');
	}


	/** @test */
	public function posterShouldBeInVideoTag() {
		$this->assertXPath('//video[@poster="http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg"]');
	}


	/** @test */
	public function pageShouldContainsVideoJS() {
		$this->assertXPath('//link[contains(@href, "http://vjs.zencdn.net/c/video-js.css")]');
		$this->assertXPath('//script[contains(@src, "http://vjs.zencdn.net/c/video.js")]');
	}
}



class Admin_AlbumControllerMassRessourceDeleteActionTest extends Admin_AlbumControllerTestCase {
	/** @var Storm_Test_ObjectWrapper */
	protected $_ressource_wrapper;
	
	public function setUp() {
		parent::setUp();

		$this->_ressource_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
				->whenCalled('delete')->answers(null);
	}


	/** @test */
	public function withoutIdShouldNotDelete() {
		$this->dispatch('/admin/album/mass-ressource-delete?ids=37', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasNotBeenCalled('delete'));
	}


	/** @test */
	public function withoutIdsShouldNotDelete() {
		$this->dispatch('/admin/album/mass-ressource-delete/id/999', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasNotBeenCalled('delete'));
	}


	/** @test */
	public function withEmptyIdsShouldNotDelete() {
		$this->dispatch('/admin/album/mass-ressource-delete/id/999?ids=', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasNotBeenCalled('delete'));
	}


	/** @test */
	public function withValidRessourceShouldDeleteIt() {
		Class_AlbumRessource::newInstanceWithId(37)
				->setAlbum(Class_Album::newInstanceWithId(999));
				
		$this->dispatch('/admin/album/mass-ressource-delete/id/999?ids=37', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function withRessourceOfAnotherAlbumShouldNotDeleteIt() {
		Class_AlbumRessource::newInstanceWithId(37)
				->setAlbum(Class_Album::newInstanceWithId(7));
				
		$this->dispatch('/admin/album/mass-ressource-delete/id/999?ids=37', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasNotBeenCalled('delete'));
	}


	/** @tests */
	public function withRessourceOfEmptyAlbumShouldNotDeleteIt() {
		Class_AlbumRessource::newInstanceWithId(37)
				->setAlbum(null);

		$this->dispatch('/admin/album/mass-ressource-delete/id/999?ids=37', true);
		$this->assertTrue($this->_ressource_wrapper->methodHasNotBeenCalled('delete'));
	}
}
?>