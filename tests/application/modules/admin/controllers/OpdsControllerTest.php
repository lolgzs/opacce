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

abstract class Admin_OpdsControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_OpdsCatalog')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array(Class_OpdsCatalog::getLoader()->newInstanceWithId(1)
											->setLibelle('Ebooks gratuits')
											->setUrl('http://www.ebooksgratuits.com/opds/'),

											Class_OpdsCatalog::getLoader()->newInstanceWithId(2)
											->setLibelle('PragPub Magazine')
											->setUrl('http://pragprog.com/magazines.opds')))

			->whenCalled('save')
			->answers(true)

			->whenCalled('delete')
			->answers(true);
	}
}



class Admin_OpdsControllerIndexActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds');
	}


	/** @test */
	public function pageTitleShouldBeCataloguesOPDS() {
		$this->assertXPathContentContains('//h1', 'Catalogues OPDS');
	}


	/** @test */
	public function shouldHaveAddCatalogButton() {
		$this->assertXPath('//div[contains(@onclick, "opds/add")]');
	}


	/** @test */
	public function catalogEbooksGratuitsShouldBePresent() {
		$this->assertXPathContentContains('//td', 'Ebooks gratuits');
	}


	/** @test */
	public function ebooksgratuitsUrlShouldBePresent() {
		$this->assertXPathContentContains('//td', 'http://www.ebooksgratuits.com/opds/');
	}



	/** @test */
	public function editLinkForEbooksGratuitsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "edit/id/1")]');
	}


	/** @test */
	public function browseLinkForEbooksGratuitsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "opds/browse/id/1")]');
	}



	/** @test */
	public function deleteLinkForEbooksGratuitsShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "delete/id/1")]');
	}


	/** @test */
	public function catalogPragPubMagazineShouldBePresent() {
		$this->assertXPathContentContains('//td', 'PragPub Magazine');
	}


	/** @test */
	public function editLinkForPragPubShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "edit/id/2")]');
	}


	/** @test */
	public function deleteLinkForPragPubShouldBePresent() {
		$this->assertXPath('//a[contains(@href, "delete/id/2")]');
	}
}



class Admin_OpdsControllerAddActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds/add');
	}


	/** @test */
	public function titleShouldBeAjouterUnCatalogueOpds() {
		$this->assertXPathContentContains('//h1', 'Ajouter un catalogue OPDS');
	}


	/** @test */
	public function libelleInputShouldBePresent() {
		$this->assertXPath('//input[@name="libelle"]');
	}


	/** @test */
	public function urlInputShouldBePresent() {
		$this->assertXPath('//input[@name="url"]');
	}


	/** @test */
	public function menuGaucheAdminShouldContainsLinkToOpds() {
		$this->assertXPath('//div[@class="menuGaucheAdmin"]//a[contains(@href, "admin/opds")]');
	}
}




class Admin_OpdsControllerAddPostActionTest extends Admin_OpdsControllerTestCase {
	protected $_wrapper;
	protected $_new_catalog;

	public function setUp() {
		parent::setUp();
		$this->_wrapper = Class_OpdsCatalog::getLoader()
			->whenCalled('save')
			->willDo(function($model) {
					$model->setId(99);
					return true;
				});
		$this->postDispatch('/admin/opds/add', array('libelle' => 'Freebooks',
																								 'url' => 'http://www.freebooks.org/opds'));

		$this->_new_catalog = $this->_wrapper->getFirstAttributeForLastCallOn('save');
	}

	
	/** @test */
	public function newCatalogLibelleShouldBeFreebooks() {
		$this->assertEquals('Freebooks', $this->_new_catalog->getLibelle());
	}


	/** @test */
	public function newCatalogUrlShouldFreebookDotOrg() {
		$this->assertEquals('http://www.freebooks.org/opds', 
												$this->_new_catalog->getUrl());
	}


	/** @test */
	public function responseShouldRedirectToEditCatalogId99() {
		$this->assertRedirectTo('/admin/opds/edit/id/99');
	}

}




class Admin_OpdsControllerInvalidPostActionTest extends Admin_OpdsControllerTestCase {
	protected $_new_catalog;

	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/opds/add', array('libelle' => '',
																								 'url' => 'zork'));
	}

	
	/** @test */
	public function errorsShouldContainsUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 'Une valeur est requise');
	}
	

	/** @test */
	public function errorsShouldContainsUrlNotValid() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', 
																			"'zork' n'est pas une URL valide");
	}


	/** @test */
	public function responsShouldNotRedirect() {
		$this->assertNotRedirect();
	}
}




class Admin_OpdsControllerEditActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_OpdsCatalog::getLoader()->newInstanceWithId(1)
			->setLibelle('Ebooks gratuits')
			->setUrl('http://www.ebooksgratuits.com/opds/');
		$this->dispatch('/admin/opds/edit/id/1');
	}


	/** @test */
	public function titleShouldBeModifierUnCatalogueOpds() {
		$this->assertXPathContentContains('//h1', 'Modifier un catalogue OPDS');
	}


	/** @test */
	public function libelleInputShouldContainEbooksGratuits() {
		$this->assertXPath('//input[@name="libelle"][@value="Ebooks gratuits"]');
	}


	/** @test */
	public function urlInputShouldContainEbookGratuitDotCom() {
		$this->assertXPath('//input[@name="url"][contains(@value, "www.ebooksgratuits.com")]');
	}
}




class Admin_OpdsControllerEditPostActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/opds/edit/id/2', array('libelle' => 'Science et vie',
																											 'url' => 'http://sev.opds'));
	}

	
	/** @test */
	public function libelleShouldBeScienceEtVie() {
		$this->assertEquals('Science et vie', Class_OpdsCatalog::getLoader()->find(2)->getLibelle());
	}

	
	/** @test */
	public function saveShouldHaveBeenCalled() {
		$this->assertTrue(Class_OpdsCatalog::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function shouldRedirectToEdit() {
		$this->assertRedirectTo('/admin/opds/edit/id/2');
	}
}




class Admin_OpdsControllerDeleteActionTest extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds/delete/id/2');
	}


	/** @test */
	public function deleteShouldHaveBeenCalled() {
		$this->assertTrue(Class_OpdsCatalog::getLoader()->methodHasBeenCalled('delete'));
	}


	/** @test */
	public function shouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/opds/index');
	}		
}



class Admin_OpdsControllerUnknownIdsActionErrorsTest extends Admin_OpdsControllerTestCase {
	/** @test */
	public function deleteShouldRedirectToIndex() {
		$this->dispatch('/admin/opds/delete/id/66666');
		$this->assertRedirectTo('/admin/opds/index');
	}


	/** @test */
	public function editShouldRedirectToIndex() {
		$this->dispatch('/admin/opds/edit/id/66666');
		$this->assertRedirectTo('/admin/opds/index');
	}
}




abstract class Admin_OpdsControllerBrowseEbooksGratuitsTestCase extends Admin_OpdsControllerTestCase {
	public function setUp() {
		parent::setUp();
		
		Class_OpdsCatalog::getLoader()
			->find(1)
			->setWebClient(Storm_Test_ObjectWrapper::mock()
										 ->whenCalled('open_url')
										 ->with('http://www.ebooksgratuits.com/opds/')
										 ->answers(OPDSFeedFixtures::ebooksGratuitsStartXml())
										 
										 ->whenCalled('open_url')
										 ->with('http://www.ebooksgratuits.com/opds/opensearch.xml')
										 ->answers(OPDSFeedFixtures::ebooksGratuitsSearchDescriptionXml()));
	}
}




class Admin_OpdsControllerBrowseActionTest extends Admin_OpdsControllerBrowseEbooksGratuitsTestCase  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/opds/browse/id/1');
	}


	/** @test */
	public function listOfCatalogsShouldBePresent() {
		$this->assertXPathContentContains('//h1', 'Catalogues OPDS');
	}


	/** @test */
	public function titleShouldBeParcoursDuCatalogueEbooksGratuits() {
		$this->assertXPathContentContains('//h1', 'Parcours du catalogue "Ebooks gratuits"');
	}


	/** @test */
	public function libelleEbookLibresEtGratuitsShouldBePresent() {
		$this->assertXPathContentContains('//div[@class="panel"]//li', 'Ebooks libres et gratuits');
	}


	/** @test */
	public function lastUpdateDateShouldBeMayFifteen2012() {
		$this->assertXPathContentContains('//div[@class="panel"]//li', '15 mai 2012');
	}


	/** @test */
	public function authorShouldBeEbooksGratuits() {
		$this->assertXPathContentContains('//div[@class="panel"]//li', 'Ebooksgratuits');
	}


	/** @test */
	public function authorUriShouldBeEbooksgratuitsDotCom() {
		$this->assertXPathContentContains('//div[@class="panel"]//li', 'http://www.ebooksgratuits.com');
	}


	/** @test */
	public function authorMailShouldBeSupportAtBbooksgratuitsDotCom() {
		$this->assertXPathContentContains('//div[@class="panel"]//li', 'support@ebooksgratuits.com');
	}


	/** @test */
	public function searchFieldShouldBePresent() {
		$this->assertXPath('//div[@class="panel"]//input[@name="search"]');
	}


	/** @test */
	public function linkToLastPublishedShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/browse/id/1?entry=' . urlencode('http://www.ebooksgratuits.com/opds/feed.php') . '")]', 
																			'Dernieres parutions', $this->_response->getBody());
	}


	/** @test */
	public function linkToAuteursShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/browse/id/1?entry=' . urlencode('http://www.ebooksgratuits.com/opds/authors.php') . '")]', 
																			'Auteurs');
	}
}




class Admin_OpdsControllerBrowseEbooksGratuitsLastUpdatedTest extends Admin_OpdsControllerBrowseEbooksGratuitsTestCase  {
	public function setUp() {
		parent::setUp();

		Class_OpdsCatalog::getLoader()->find(1)
			->getWebClient()
			->whenCalled('open_url')
			->with('http://www.opacsgratuits.com/opds/feed.php?mode=maj')
			->answers(OPDSFeedFixtures::ebooksGratuitsLastUpdatedXml());
		
		$this->dispatch('/admin/opds/browse/id/1?entry=' . urlencode('http://www.opacsgratuits.com/opds/feed.php?mode=maj'));
	}


  /** @test */
  public function draculaShouldBePresent() {
		$this->assertXPathContentContains('//li', 'Dracula');
	}


	/** @test */
	public function bramStokerShouldBePresent() {
			$this->assertXPathContentContains('//li', '(Stoker, Bram)');
	}


	/** @test */
	public function linkToImportDraculaShouldBePresent() {
		$this->assertXPathContentContains(sprintf('//a[contains(@href, "admin/opds/import/id/1?feed=%s&entry=%s")]',
																							urlencode('http://www.opacsgratuits.com/opds/feed.php?mode=maj'),
																							urlencode('http://www.ebooksgratuits.com/details.php?book=592')),
																			'Importer');
	}


	/** @test */
	public function linkToImportLesCompagnonsShouldNotBePresent() {
			$this->assertNotXPath(sprintf('//a[contains(@href, "admin/opds/import/id/1?feed=%s&entry=%s")]',
																		urlencode('http://www.opacsgratuits.com/opds/feed.php?mode=maj'),
																		urlencode('http://www.ebooksgratuits.com/details.php?book=329')));
	}
}



class Admin_OpdsControllerBrowseEbooksGratuitsImportTest extends Admin_OpdsControllerBrowseEbooksGratuitsTestCase  {
	public function setUp() {
		parent::setUp();

		Class_OpdsCatalog::getLoader()->find(1)
			->getWebClient()
			->whenCalled('open_url')
			->with('http://www.opacsgratuits.com/opds/feed.php?mode=maj')
			->answers(OPDSFeedFixtures::ebooksGratuitsLastUpdatedXml());
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('save')
			->willDo(function($model){
					$model->setId(777);
					return true;
				});

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumRessource')
			->whenCalled('save')
			->answers(true);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AlbumCategorie')
			->whenCalled('save')
			->answers(true);


		OPDSEntryFile::defaultDownloader(Storm_Test_ObjectWrapper::mock()
																		 ->whenCalled('downloadFromUrlToDisk')
																		 ->answers(true));

		$this->dispatch(sprintf('/admin/opds/import/id/1?feed=%s&entry=%s',
														urlencode('http://www.opacsgratuits.com/opds/feed.php?mode=maj'),
														urlencode('http://www.ebooksgratuits.com/details.php?book=592')));
	}


	/** @test */
	public function shouldRedirectToAlbumEdit() {
		$this->assertRedirectTo('/admin/album/edit_album/id/777');
	}

}



class Admin_OpdsControllerBrowseSearchPostActionTest extends Admin_OpdsControllerBrowseEbooksGratuitsTestCase {
	public function setUp() {
		parent::setUp();

		

		$this->postDispatch('/admin/opds/browse/id/1', array('search' => 'dracula'));
	}


	/** @test */
	public function shouldRedirectToBrowseWithSearchEntry() {
		$this->assertRedirectTo('/admin/opds/browse/id/1?entry=' . urlencode('http://www.ebooksgratuits.com/opds/feed.php?mode=search&query=dracula'));
	}
}



class OPDSFeedFixtures {
	public static function ebooksGratuitsStartXml() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns:thr="http://purl.org/syndication/thread/1.0" xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" xml:lang="fr" xmlns:app="http://www.w3.org/2007/app" xmlns:opds="http://opds-spec.org/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns="http://www.w3.org/2005/Atom" xmlns:dcterms="http://purl.org/dc/terms/">
  <id>http://www.ebooksgratuits.com/opds/index.php</id>
  <link type="text/html" href="http://www.ebooksgratuits.com/ebooks.php" rel="alternate"/>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/index.php" rel="self"/>
  <title>Ebooks libres et gratuits - Catalogue des livres</title>
  <updated>2012-05-15T06:13:41Z</updated>
  <icon>http://www.ebooksgratuits.com/favicon.png</icon>
  <author>
    <name>Ebooksgratuits</name>
    <uri>http://www.ebooksgratuits.com</uri>
    <email>support@ebooksgratuits.com</email>
  </author>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/index.php" rel="start" title="OPDS Catalog"/>
  <link type="application/opensearchdescription+xml" href="/opds/opensearch.xml" rel="search" title="Rechercher sur Ebooksgratuits"/>
<entry>
    <title>Dernieres parutions</title>
    <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php"/>
    <updated>2012-05-15T06:13:41Z</updated>
    <id>http://www.ebooksgratuits.com/opds/feed.php</id>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/thumbnail"/>
    <content type="text">Dernières parutions</content>
</entry>
<entry>
  <title>Dernières mises à jour</title>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php?mode=maj"/>
  <updated>2012-05-15T06:13:41Z</updated>
  <id>http://www.ebooksgratuits.com/opds/feed.php?mode=maj</id>
  <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/thumbnail"/>
  <content type="text">Dernières mises à jour</content>
</entry>
<entry>
  <title>Les plus populaires</title>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php?mode=rate"/>
  <updated>2012-05-15T06:13:41Z</updated>
  <id>http://www.ebooksgratuits.com/opds/feed.php?mode=rate</id>
  <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/thumbnail"/>
  <content type="text">Nos livres les plus téléchargés</content>
</entry>
<entry>
  <title>Auteurs</title>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/authors.php"/>
  <updated>2012-05-15T06:13:41Z</updated>
  <id>http://www.ebooksgratuits.com/opds/authors.php</id>
  <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/thumbnail"/>
  <content type="text">Nos livres classés par auteurs</content>
</entry>
<entry>
  <title>Catégories et genres</title>
  <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/categories.php"/>
  <updated>2012-05-15T06:13:41Z</updated>
  <id>http://www.ebooksgratuits.com/opds/categories.php</id>
  <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/thumbnail"/>
  <content type="text">Nos livres classés par catégories ou genres</content>
</entry>
</feed>';
	}



	public static function ebooksGratuitsLastUpdatedXml() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns:app="http://www.w3.org/2007/app" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:opds="http://opds-spec.org/" xml:lang="fr" xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:thr="http://purl.org/syndication/thread/1.0" xmlns="http://www.w3.org/2005/Atom">
   <id>http://www.ebooksgratuits.com/opds/feed.php</id>
   <link type="text/html" href="http://www.ebooksgratuits.com/ebooks.php" rel="alternate"/>
   <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php" rel="self"/>
   <link type="application/opensearchdescription+xml" href="/opds/opensearch.xml" rel="search" title="Rechercher sur Ebooksgratuits"/>
   <title>Liste des ebooks mis à jour</title>
  <updated>2012-05-15T08:04:16Z</updated>
  <icon>http://www.ebooksgratuits.com/favicon.png</icon>
  <author>
    <name>Ebooks libres et gratuits - Catalogue des livres</name>
    <uri>http://www.ebooksgratuits.com</uri>
    <email>contact@ebooksgratuits.com</email>
  </author>
  <link type="application/atom+xml" href="/opds/feed.php" rel="start" title="OPDS Catalog"/>

<opensearch:totalResults>895</opensearch:totalResults>
<opensearch:itemsPerPage>100</opensearch:itemsPerPage>
<link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php?mode=maj&amp;page=1" rel="next" title="Next Page"/>
   <entry>
      <title>Dracula</title>
      <id>http://www.ebooksgratuits.com/details.php?book=592</id>
      <author>
         <name>Stoker, Bram</name>
         <uri>http://www.ebooksgratuits.com/ebooks.php?auteur=Stoker_Bram</uri>
      </author>
      <updated>2012-05-09T00:00:00-07:00</updated>
      <dcterms:language>fr</dcterms:language>
      <link type="text/html" href="http://www.ebooksgratuits.com/details.php?book=592" rel="alternate" title="Voir sur ebooksgratuits.com" />
      <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php?mode=author&amp;id=144" rel="related" title="Du même auteur" />
      <category term="Romans" label="Romans"/>
      <category term="Fantastique &amp; SF" label="Fantastique &amp; SF"/>
      <content type="text"><![CDATA[Écrit sous forme d\'extraits de journaux personnels et de lettre, ce roman nous conte les aventures de Jonathan Harker, jeune clerc de notaire envoyé dans une contrée lointaine et mystérieuse, la Transylvanie, pour rencontrer un client étranger, le comte Dracula, qui vient d\'acquérir une maison à Londres. Arrivé au château, lieu sinistre et inquiétant, Jonathan se rend vite compte qu\'il n\'a pas à faire à un client ordinaire... et qu\'il est en réalité retenu prisonnier par son hôte...<br />Inutile de vous en dire plus, chacun sait qui est le terrible comte Dracula, le célèbre vampire... Le pauvre Jonathan, et ses amis, ne sont pas au bout de leurs peines...<br /><br />Édition Ebooks libres et gratuits.<br />Mise à jour: 09/05/2012 : Mise à jour format epub]]></content>
      <dcterms:source>http://www.ebooksgratuits.com/ebooks.php</dcterms:source>
      <link type="application/epub+zip" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=epub" rel="http://opds-spec.org/acquisition" />
      <link type="application/x-mobipocket-ebook" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=mp" rel="http://opds-spec.org/acquisition" />
      <link type="application/pdf" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=pdf" rel="http://opds-spec.org/acquisition" />
      <link type="application/x-palmreader" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=pr" rel="http://opds-spec.org/acquisition" />
      <link type="application/zip" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=so" rel="http://opds-spec.org/acquisition" />
      <link type="application/octet-stream" href="http://www.ebooksgratuits.com/newsendbook.php?id=592&amp;format=lrf" rel="http://opds-spec.org/acquisition" />
   </entry>
   <entry>
      <title>Les Compagnons de Jéhu</title>
      <id>http://www.ebooksgratuits.com/details.php?book=329</id>
      <author>
         <name>Dumas, Alexandre</name>
         <uri>http://www.ebooksgratuits.com/ebooks.php?auteur=Dumas_Alexandre</uri>
      </author>
      <updated>2012-04-10T00:00:00-07:00</updated>
      <dcterms:language>fr</dcterms:language>
      <link type="text/html" href="http://www.ebooksgratuits.com/details.php?book=329" rel="alternate" title="Voir sur ebooksgratuits.com" />
      <link type="application/atom+xml" href="http://www.ebooksgratuits.com/opds/feed.php?mode=author&amp;id=48" rel="related" title="Du même auteur" />
      <category term="Romans" label="Romans"/>
      <category term="Historique" label="Historique"/>
      <content type="text"><![CDATA[En 1799, sous le gouvernement corrompu du Directoire, coups de main et complots se multiplient en France. Les compagnons de Jehu pillent les diligences et remettent leur butin aux généraux royalistes qui veulent rétablir la monarchie. Cette bande est commandée par un gentilhomme masqué que l\'on surnomme Morgan. Mais Bonaparte, revenu incognito d\'Égypte, charge un de ses officiers, Roland de Montrevel, de le démasquer...<br /><br />Édition Ebooks libres et gratuits.<br />Mise à jour: 10/04/2012: Mise à jour de tous les formats - 26/01/2011: Mise à jour du format epub.]]></content>
      <dcterms:source>http://www.ebooksgratuits.com/ebooks.php</dcterms:source>
      <link type="application/x-mobipocket-ebook" href="http://www.ebooksgratuits.com/newsendbook.php?id=329&amp;format=mp" rel="http://opds-spec.org/acquisition" />
      <link type="application/x-palmreader" href="http://www.ebooksgratuits.com/newsendbook.php?id=329&amp;format=pr" rel="http://opds-spec.org/acquisition" />
      <link type="application/zip" href="http://www.ebooksgratuits.com/newsendbook.php?id=329&amp;format=so" rel="http://opds-spec.org/acquisition" />
      <link type="application/octet-stream" href="http://www.ebooksgratuits.com/newsendbook.php?id=329&amp;format=lrf" rel="http://opds-spec.org/acquisition" />
   </entry>
	</feed>';
	}
	


	public static function ebooksGratuitsSearchDescriptionXml() {
		return '<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName>Ebooksgratuits</ShortName>
  <Description>Recherche d\'e-books sur ebooksgratuits</Description>
  <InputEncoding>UTF-8</InputEncoding>
  <OutputEncoding>UTF-8</OutputEncoding>
  <Image type="image/x-icon" width="16" height="16">http://www.ebooksgratuits.com/favicon.ico</Image>

  <Url type="text/html" template="http://www.ebooksgratuits.com/ebooks.php?titre={searchTerms}"/>
  <Url type="application/atom+xml" template="http://www.ebooksgratuits.com/opds/feed.php?mode=search&amp;query={searchTerms}"/>
  <Url type="application/x-suggestions+json" rel="suggestions" template="http://www.ebooksgratuits.com/opds/search.php?mode=json&amp;query={searchTerms}"/>
  <Query role="example" searchTerms="robot" />
</OpenSearchDescription>';
	}
}
?>