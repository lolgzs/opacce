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

abstract class DublinCoreVisitorTestCase extends Storm_Test_ModelTestCase { 
	protected $_xpath;
	protected $_dublin_core_visitor;

	public function setUp() {
		parent::setUp();
		$this->_xpath = TestXPathFactory::newOaiDc();
		$this->_dublin_core_visitor = new Class_Notice_DublinCoreVisitor();
	}
}


class DublinCoreVisitorPotterTest extends DublinCoreVisitorTestCase { 
	protected $_summary;

	public function setUp() {
		parent::setUp();
		$this->_summary = 'Apres la mort < tragique de Lily et James Potter, Harry est recueilli par sa tante Petunia, la soeur de Lily et son oncle Vernon. Son oncle et sa tante, possedant une haine feroce envers les parents d\'Harry, le maltraitent et laissent leur fils Dudley l\'humilier. Harry ne sait rien sur ses parents. On lui a toujours dit qu\'ils etaient morts dans un accident de voiture.';

		$potter = Class_Notice::getLoader()
			->newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setTitrePrincipal('Harry Potter a l\'ecole des sorciers')
			->setAuteurPrincipal('Joanne Kathleen Rowling')
			->setDateMaj('2012-04-23')
			->setAnnee('2012')
			->setResume($this->_summary)
			->setMatieres(array('Potions', 'Etude des runes'))
			->setEditeur('Bloomsbury Publishing')
			->setLangueCodes(array('fre', 'eng'))
			->setIsbn('978-2-07-054127-0')
			->setEan('')
			->setTypeDoc(1);
		$this->_dublin_core_visitor->visit($potter);
	}


	/**
	 * @group integration
	 * @test 
	 */
	public function xmlShouldBeValid() {
		$dom = new DOMDocument();
		$dom->loadXml($this->_dublin_core_visitor->xml());
		$dom->schemaValidate('tests/library/Class/Notice/oai_dc.xsd');
	}


	/** @test */
	public function identifierShouldBeHarryPotterSorciers() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:identifier',
																							sprintf('http://localhost%s/recherche/viewnotice/clef/harrypotter-sorciers',
																											BASE_URL));
	}


	/** @test */
	public function titleShouldBeHarryPotterEcoleSorciers() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:title',
																							'Harry Potter a l\'ecole des sorciers');
	}


	/** @test */
	public function creatorShouldBeJKRowling() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:creator',
																							'Joanne Kathleen Rowling');
	}


	/** @test */
	public function dateShouldBe2012() {
		$this->_xpath->assertXpathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:date',
																							'2012');
	}


	/** @test */
	public function descriptionShouldBeApresLaMortTragiqueEtc() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:description',
																							$this->_summary);
	}


	/** @test */
	public function subjectEtudeDesRuneShouldBePresent() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:subject',
																							'Etude des runes');
	}


	/** @test */
	public function subjectPotionsShouldBePresent() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:subject',
																							'Potions');
	}


	/** @test */
	public function namespaceShouldBeOAIDC() {
		$this->_xpath->assertXpath($this->_dublin_core_visitor->xml(),
															 '//oai_dc:dc');
	}


	/** @test */
	public function publisherShouldBeBloomsbury() {
		$this->_xpath->assertXpathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:publisher',
																							'Bloomsbury Publishing');
	}


	/** @test */
	public function shouldHaveTwoLanguages() {
		$this->_xpath->assertXpathCount($this->_dublin_core_visitor->xml(),
																		'//oai_dc:dc/dc:language', 2);
	}


	/** @test */
	public function shouldHaveFrenchLanguage() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:language',
																							'fre');
	}


	/** @test */
	public function shouldHaveEnglishLanguage() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:language',
																							'eng');
	}


	/** @test */
	public function shouldHaveIdentifierWithIsbn() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:identifier',
																							'978-2-07-054127-0');
	}


	/** @test */
	public function shouldHaveEmptyRights() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:rights',
																							'');
	}

}


class DublinCoreVisitorSouvignyTest extends DublinCoreVisitorTestCase { 
	public function setUp() {
		parent::setUp();

		$souvigny = Class_Notice::getLoader()
			->newInstanceWithId(5)
			->setClefAlpha('souvigny-bible-11eme')
			->setDateMaj('2012-04-23')
			->setUrlVignette('http://server.fr/vignette.png')
			->setIsbn('')
			->setEan('4719-5120-0288-9')
			->beLivreNumerique();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_TypeDoc')
			->whenCalled('find')
			->with(Class_TypeDoc::LIVRE_NUM)
			->answers(Class_TypeDoc::getLoader()->newInstanceWithId(Class_TypeDoc::LIVRE_NUM)
								->setLabel('livre numerise'));


		$oldServerName = $_SERVER['SERVER_NAME'];
		$_SERVER['SERVER_NAME'] = 'moulins.fr';
		$this->_dublin_core_visitor->visit($souvigny);
		$_SERVER['SERVER_NAME'] = $oldServerName;
	}


	/** @test */
	public function identifierShouldBeSouvignyBible11eme() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:identifier',
																							sprintf('http://moulins.fr%s/recherche/viewnotice/clef/souvigny-bible-11eme',
																											BASE_URL));
	}


	/** @test */
	public function shouldNotHaveDate() {
		$this->_xpath->assertNotXPath($this->_dublin_core_visitor->xml(),
																	'//oai_dc:dc/dc:date');
	}


	/** @test */
	public function shouldNotHavePublisher() {
		$this->_xpath->assertNotXPath($this->_dublin_core_visitor->xml(),
																	'//oai_dc:dc/dc:publisher');
	}


	/** @test */
	public function shouldHaveJpegFormat() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:format',
																							'image/jpeg');
	}


	/** @test */
	public function shouldHaveTypeLivreNumerise() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:type',
																							'livre numerise');
	}


	/** @test */
	public function shouldHaveRelationWithThumbnail() {
			$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																								'//oai_dc:dc/dc:relation',
																								'vignette : http://server.fr/vignette.png');
	}


	/** @test */
	public function shouldHaveIdentifierWithEan() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:identifier',
																							'4719-5120-0288-9');
	}


	/** @test */
	public function shouldHavePublicDomainRights() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:rights',
																							'domaine public');
	}
}
?>