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

		Class_CosmoVar::newInstanceWithId('nature_docs', 
																			['liste' =>  "1:Collection\r\n20:Manuscrit"]);

	}
}


class DublinCoreVisitorPotterTest extends DublinCoreVisitorTestCase { 
	protected $_summary;

	public function setUp() {
		parent::setUp();
		$this->_summary = 'Apres la mort < tragique de Lily et <span>James P&eacute;tter</span>, Harry est recueilli par sa tante Petunia, la soeur de Lily et son oncle Vernon. Son oncle et sa tante, possedant une haine feroce envers les parents d\'Harry, le maltraitent et laissent leur fils Dudley l\'humilier. Harry ne sait rien sur ses parents. On lui a toujours dit qu\'ils etaient morts dans un accident de voiture.';

		$potter = Class_Notice::newInstanceWithId(4)
			->setClefAlpha('harrypotter-sorciers')
			->setDateMaj('2012-04-23')
			->setAnnee('2012')
			->setResume($this->_summary)
			->setMatieres(array('Potions', 'Etude des runes'))
			->setLangueCodes(array('fre', 'eng'))
			->setIsbn('978-2-07-054127-0')
			->setEan('')
			->setTypeDoc(1)
			->setNoticeUnimarc(DublinCoreNoticeUnimarcTesting::newInstance()
				->subfieldWillReturn(['215'], [' 1a636 p.d22 cm'])
				->subfieldWillReturn(['200'], ['1 aHarry Potter a l\'ecole des sorciersePotter est un coquinfJoanne K. RowlinghT.4'])
				->subfieldWillReturn(['700'], [' 1aRowlingbJoanne Kathleen',
						                           ' 1aRowlingbBebop a lula'])
				->subfieldWillReturn(['702'], [' 1aCoutonbPatrick'])
				->subfieldWillReturn(['210', 'c'], ['Bloomsbury Publishing'])
				->subfieldWillReturn(['210', 'a'], ['Londres'])
				->subfieldWillReturn(['200', 'b'], ['Manuscrit',
																						'Collection',
																						'Parchemin'])
				->subfieldWillReturn(['801', 'b'], ['Castagnera'])
				->subfieldWillReturn(['852', 'k'], ['LV/R ROW']));
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
		$xml = $this->_dublin_core_visitor->xml();
		$this->_xpath->assertXPathContentContains(
				$xml,
				'//oai_dc:dc/dc:identifier',
				sprintf('http://localhost%s/recherche/viewnotice/clef/harrypotter-sorciers',
								BASE_URL)
		);
	}


	/** @test */
	public function titleShouldBeHarryPotterEcoleSorciersWithComplements() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:title',
																							'Harry Potter a l\'ecole des sorciers. Potter est un coquin');
	}


	/** @test */
	public function creatorJKRowlingShouldBePresent() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:creator',
																							'Rowling, Joanne Kathleen');
	}


	/** @test */
	public function creatorBebopRowlingShouldBePresent() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:creator',
																							'Rowling, Bebop a lula');
	}


	/** @test */
	public function contributorPatrickCoutonShouldBePresent() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:contributor',
																							'Couton, Patrick');
	}
	

	/** @test */
	public function dateShouldBe2012() {
		$this->_xpath->assertXpathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:date',
																							'2012');
	}


	/** @test */
	public function descriptionShouldBeApresLaMortTragiqueEtcWithoutTags() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:description',
																							'tragique de Lily et James Pétter');
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
																							'Bloomsbury Publishing (Londres)');
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


	/** @test */
	public function shouldHave636PagesFormat() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:format',
																							'636 p.');
	}


	/** @test */
	public function shouldHave22CmFormat() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:format',
																							'22 cm');
	}


	/** @test */
	public function shouldHaveCastagneraSource() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:source',
																							'Castagnera, LV/R ROW');
	}


	/** @test */
	public function shouldHaveTypeManuscrit() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:type[@xml:lang="fre"]',
																							'Manuscrit');
	}



	/** @test */
	public function shouldHaveTypeParchemin() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:type[@xml:lang="fre"]',
																							'Parchemin');
	}



	/** @test */
	public function shouldHaveTypeCollection() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:type[@xml:lang="eng"]',
																							'Collection');
	}



}


class DublinCoreVisitorSouvignyTest extends DublinCoreVisitorTestCase { 
	public function setUp() {
		parent::setUp();

		$souvigny = Class_Notice::newInstanceWithId(5)
			->setClefAlpha('souvigny-bible-11eme')
			->setDateMaj('2012-04-23')
			->setUrlVignette('http://server.fr/vignette.png')
			->setIsbn('')
			->setExemplaires([$exemplaire = Class_Exemplaire::newInstanceWithId(22, ['id_origine' => 33])])
			->setEan('4719-5120-0288-9')
			->beLivreNumerique();


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Exemplaire')
			->whenCalled('findFirstBy')
			->with(['id_notice' => 5])
			->answers($exemplaire);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_TypeDoc')
			->whenCalled('find')
			->with(Class_TypeDoc::LIVRE_NUM)
				->answers(Class_TypeDoc::newInstanceWithId(Class_TypeDoc::LIVRE_NUM)
					           ->setLabel('livre numerise'));
		
		Class_Album::newInstanceWithId(33, ['droits' => 'domaine public',
				                                'description' => '']);

		$oldServerName = $_SERVER['SERVER_NAME'];
		$_SERVER['SERVER_NAME'] = 'moulins.fr';
		$this->_dublin_core_visitor->visit($souvigny);
		$_SERVER['SERVER_NAME'] = $oldServerName;
	}


	/** @test */
	public function identifierShouldBeSouvignyBible11eme() {
		$this->_xpath->assertXPathContentContains($this->_dublin_core_visitor->xml(),
																							'//oai_dc:dc/dc:identifier',
																							sprintf('http://moulins.fr%s/recherche/viewnotice/clef/souvigny-bible-11eme', BASE_URL));
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
	public function shouldNotHaveSource() {
		$this->_xpath->assertNotXPath($this->_dublin_core_visitor->xml(),
																	'//oai_dc:dc/dc:source');
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


class DublinCoreNoticeUnimarcTesting extends Class_NoticeUnimarc {
	protected $_subfields = [];

	public static function newInstance() {
		return new self();
	}


	public function subfieldWillReturn($call_params, $return) {
		$this->_subfields[$this->_getKeyForParams($call_params)] = $return;
		return $this;
	}


	public function get_subfield() {
		if (array_key_exists($key = $this->_getKeyForParams(func_get_args()), $this->_subfields))
			return $this->_subfields[$key];
		return call_user_func_array(['parent', 'get_subfield'], func_get_args());
	}


	private function _getKeyForParams($params) {
		return md5(serialize($params));
	}
}
?>