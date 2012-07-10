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
require_once 'ModelTestCase.php';

class CatalogueTestGetRequetesPanier extends ModelTestCase {
	public function setUp() {
		$panier_row = $this->_buildRowset(array(array('ID' => 3,
																									'ID_USER' => 3,
																									'ID_PANIER' => 2,
																									'NOTICES' => ';STARWARS;JAMESBOND;')));

		$this->select_paniers = new Zend_Db_Table_Select(new Storm_Model_Table(array('name' => 'notices_paniers')));

		$tbl_paniers = $this->_buildTableMock('Class_PanierNotice', array('fetchAll', 'select'));
		$tbl_paniers
			->expects($this->once())
			->method('select')
			->will($this->returnValue($this->select_paniers));

		$tbl_paniers
			->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue($panier_row));

		$catalogue = new Class_Catalogue();
		$this->requetes = $catalogue->getRequetesPanier(array('id_panier' => 2,
																													'id_user' => 3,
																													'aleatoire' => 0,
																													'tri' => 0,
																													'only_img' => 1,
																													'avec_avis' => 0,
																													'nb_notices' => 3));
	}

	public function testRequeteListe() {
		$this->assertEquals("select * from notices where notices.clef_alpha in('STARWARS','JAMESBOND') and url_vignette > '' and url_vignette != 'NO'  order by alpha_titre LIMIT 0,3",
												$this->requetes['req_liste']);
	}

	public function testRequeteComptage() {
		$this->assertEquals("select count(*) from notices where notices.clef_alpha in('STARWARS','JAMESBOND') and url_vignette > '' and url_vignette != 'NO' ",
												$this->requetes['req_comptage']);
	}

	public function testRequeteFacettes() {
		$this->assertEquals("select id_notice,type_doc,facettes from notices where notices.clef_alpha in('STARWARS','JAMESBOND')  and url_vignette > '' and url_vignette != 'NO' LIMIT 0,3",
												$this->requetes['req_facettes']);
	}
}


class CatalogueTestGetSelectionFacette extends ModelTestCase {
	protected $_catalogue;

	public function setUp() {
		parent::setUp();
		$this->_catalogue = new Class_Catalogue();
	}


	/** @test */
	public function withoutValuesShouldReturnFalse() {
		$this->assertEquals(false, $this->_catalogue->getSelectionFacette(null, null));
	}


	/** @test */
	public function withEmptyValuesShouldReturnEmptyString() {
		$this->assertEquals('', $this->_catalogue->getSelectionFacette(null, ''));
	}


	/** @test */
	public function withTypeAndValuesShouldReturnThemConcatened() {
		$this->assertEquals('+(A18 A78 A8 A3)', 
												$this->_catalogue->getSelectionFacette('A', '18;78;8;3'));
	}


	/** @test */
	public function withTypeAndValuesAndNoSigneShouldNotAddPlus() {
		$this->assertEquals(' A18 A78 A8 A3', 
												$this->_catalogue->getSelectionFacette('A', '18;78;8;3', false, false));
	}


	/** @test */
	public function withDescendantShouldAddWildCard() {
		$this->assertEquals('+(A18* A78* A8* A3*)', 
												$this->_catalogue->getSelectionFacette('A', '18;78;8;3', true));
	}


	/** @test */
	public function withMatersShouldConcatenateThem() {
		$this->assertEquals('+(M18 M78 M8 M3)', 
												$this->_catalogue->getSelectionFacette('M', '18;78;8;3'));
	}


	/** @test */
	public function withMaterAndDescendantsShouldConcatenateThem() {
		Class_Matiere::getLoader()->newInstanceWithId(18)
			->setLibelle('Parc animalier');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Matiere')
			->whenCalled('findAllBy')
			->with(array('where' => 'libelle LIKE \'Parc animalier : %\''))
			->answers(array(Class_Matiere::getLoader()->newInstanceWithId(78)));

		$this->assertEquals('+(M18 M78)', 
												$this->_catalogue->getSelectionFacette('M', '18', true));
	}
}


class CatalogueTestGetPagedNotices extends ModelTestCase {
	protected $_catalogue;
	protected $_noticeWrapper;

	public function setUp() {
		parent::setUp();
		$this->_catalogue = Class_Catalogue::getLoader()->newInstanceWithId(3);
		$this->_noticeWrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice');
	}


	/** @test */
	public function withoutConditionsShouldReturnEmptyResult() {
		$this->assertEquals(0, count(Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue)));
	}


	/** @test */
	public function withBibliothequeShouldQueryNoticeWithFacet() {
		$lambda = function($catalogue) {$catalogue->setBibliotheque('23;88');};
		$this->_prepareAndLoadForFacet($lambda, 'B23 B88');
	}


	/** @test */
	public function withSectionShouldQueryNoticeWithFacet() {
		$lambda = function($catalogue) {$catalogue->setSection('66');};
		$this->_prepareAndLoadForFacet($lambda, 'S66');
	}


	/** @test */
	public function withGenreShouldQueryNoticeWithFacet() {
		$lambda = function($catalogue) {$catalogue->setGenre('43');};
		$this->_prepareAndLoadForFacet($lambda, 'G43');
	}


	/** @test */
	public function withLangueShouldQueryNoticeWithFacet() {
		$lambda = function($catalogue) {$catalogue->setLangue('4');};
		$this->_prepareAndLoadForFacet($lambda, 'L4');
	}


	/** @test */
	public function withAnnexeShouldQueryNoticeWithFacet() {
		$lambda = function($catalogue) {$catalogue->setAnnexe('67');};
		$this->_prepareAndLoadForFacet($lambda, 'Y67');
	}


	/** @test */
	public function withEmplacementShouldQueryNoticeWithFacet() {
		$lambda = function ($catalogue) {$catalogue->setEmplacement('12');};
		$this->_prepareAndLoadForFacet($lambda, 'E12');
	}


	/** @test */
	public function withTypeDocShouldQueryOnColumn() {
		$this->_catalogue->setTypeDoc(7);
		$this->_expectNoticeFindAllBy('type_doc=7');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withSeveralTypeDocShouldQueryOnColumn() {
		$this->_catalogue->setTypeDoc('7;10;12');
		$this->_expectNoticeFindAllBy('type_doc IN (7, 10, 12)');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withYearShouldQueryOnColumn() {
		$this->_catalogue
			->setAnneeDebut('1936')
			->setAnneeFin('1965');
		$this->_expectNoticeFindAllBy('annee >= \'1936\' and annee <= \'1965\'');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withCoteShouldQueryOnColumn() {
		$this->_catalogue
			->setCoteDebut('A')
			->setCoteFin('Z');
		$this->_expectNoticeFindAllBy('cote >= \'A\' and cote <= \'Z\'');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withNouveauteShouldQueryOnColumn() {
		$this->_catalogue->setNouveaute(1);
		$this->_expectNoticeFindAllBy('date_creation >= \'' . date('Y-m-d') . '\'');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withFromShouldQueryOnDateMaj() {
		$this->_catalogue->setFrom('2011-03-05');
		$this->_expectNoticeFindAllBy('left(date_maj, 10) >= \'2011-03-05\'');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function withUntilShouldQueryOnDateMaj() {
		$this->_catalogue->setUntil('2011-03-05');
		$this->_expectNoticeFindAllBy('left(date_maj, 10) <= \'2011-03-05\'');
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	/** @test */
	public function forFirstPageShouldLimitFromZero() {
		$this->_catalogue->setBibliotheque('77');
		$this->_noticeWrapper
			->whenCalled('findAllBy')
			->with(array('where' => $this->_facetsClauseWith('B77'),
									 'limitPage' => array(1, 5)))
			->answers(array())
			->beStrict();

		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue, 5);
	}


	/** @test */
	public function forThirdPageAndFiveItemsByPageShouldLimitFromTen() {
		$this->_catalogue->setBibliotheque('77');
		$this->_noticeWrapper
			->whenCalled('findAllBy')
			->with(array('where' => $this->_facetsClauseWith('B77'),
									 'limitPage' => array(3, 5)))
			->answers(array())
			->beStrict();

		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue, 5, 3);
	}

	
	/** @test */
	public function withCatalogForAllShouldQueryOneEgalOne() {
		$this->_expectNoticeFindAllBy('1=1');
		Class_Catalogue::getLoader()->loadNoticesFor(Class_Catalogue::newCatalogueForAll());
	}


	protected function _prepareAndLoadForFacet($lambda, $facet) {
		$lambda($this->_catalogue);
		$this->_expectNoticeFindAllBy($this->_facetsClauseWith($facet));
		Class_Catalogue::getLoader()->loadNoticesFor($this->_catalogue);
	}


	protected function _expectNoticeFindAllBy($where, $limit = array()) {
		if (0 == count($limit))
			$limit = array(1, CatalogueLoader::DEFAULT_ITEMS_BY_PAGE);

 		$this->_noticeWrapper
			->whenCalled('findAllBy')
			->with(array('where' => $where,
									 'limitPage' => $limit))
			->answers(array())
			->beStrict();
	}


  protected function _facetsClauseWith($clauses) {
    return sprintf('MATCH(facettes) AGAINST(\'+(%s)\' IN BOOLEAN MODE)', $clauses);
  }
}




class CatalogueTestGetRequetesWithFacettesAndNoCatalogue extends ModelTestCase {
	protected $_catalogue;

	public function setUp() {
		parent::setUp();
		$catalogue = new Class_Catalogue();
		$this->_requetes = $catalogue->getRequetes(array('id_catalogue' => 0,
																										 'facettes' => 'T1, Y1'));
	}


	/** @test */
	public function requeteListeShouldEqualsSelectStarWhereFacettesFromNotices() {
		$this->assertEquals('select * from notices  where MATCH(facettes) AGAINST(\'+(T1, Y1)\' IN BOOLEAN MODE) order by alpha_titre  LIMIT 5000', $this->_requetes['req_liste']);
	}


	/** @test */
	public function requeteComptageShouldBeSelectCount() {
		$this->assertEquals('select count(*) from notices  where MATCH(facettes) AGAINST(\'+(T1, Y1)\' IN BOOLEAN MODE)', $this->_requetes['req_comptage']);
	}


	/** @test */
	public function requeteFacettesShouldBeSelectIdNoticeTypeDocFacet() {
		$this->assertEquals('select notices.id_notice,type_doc,facettes from notices  where MATCH(facettes) AGAINST(\'+(T1, Y1)\' IN BOOLEAN MODE) LIMIT 5000', $this->_requetes['req_facettes']);
	}
}




class CatalogueTestOAISpec extends ModelTestCase {
	protected $_catalogue;

	public function setUp() {
		parent::setUp();
		$this->_catalogue = Class_Catalogue::getLoader()->newInstanceWithId(3)->setLibelle('zork');
	}


	/** @test */
	public function oaiSpecEmptyShouldBeValid() {
		$this->assertTrue($this->_catalogue->isValid());
	}


	/** @test */
	public function oaiSpecValidShouldBeValid() {
		$this->_catalogue->setOaiSpec('bd-Adultes_1.');
		$this->assertTrue($this->_catalogue->isValid());
	}


	/** @test */
	public function oaiSpecWithUnknownCharsShouldBeInvalid() {
		$this->_catalogue->setOaiSpec('+@*/');
		$this->assertFalse($this->_catalogue->isValid());
	}


	/** @test */
	public function oaiSpecWithColonShouldBeInvalidHasHierarchyNotSupported() {
		$this->_catalogue->setOaiSpec('bd:adultes');
		$this->assertFalse($this->_catalogue->isValid());
	}
}




class CatalogueTestGetNoticesByPreferences extends ModelTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('CACHE_ACTIF')
			->setValeur(1);

		$this->old_cache = Zend_Registry::get('cache');
		$this->mock_cache = Storm_Test_ObjectWrapper::mock();
		Zend_Registry::set('cache', $this->mock_cache);
		$this->mock_cache
			->whenCalled('save')
			->answers(true)
			->whenCalled('test')
			->answers(false);

		$this->old_sql = Zend_Registry::get('sql');
		$this->mock_sql = Storm_Test_ObjectWrapper::mock();
		Zend_Registry::set('sql', $this->mock_sql);


		$this->mock_sql
			->whenCalled('fetchAll')
			->with("select notices.id_notice, notices.editeur, notices.annee, notices.date_creation, notices.facettes, notices.clef_oeuvre from notices  order by alpha_titre  LIMIT 0,25",
						 false)
			->answers(array(array('id_notice' => 23,
														'editeur' => 'dargaud',
														'annee' => 1975,
														'date_creation' => '2011-02-23',
														'facettes' => '',
														'clef_oeuvre' => 'JEUNE FILLE')))

			->whenCalled('fetchEnreg')
			->with("select type_doc,facettes,isbn,ean,annee,tome_alpha,unimarc from notices where id_notice=23",
						 false)
			->answers(array('type_doc' => 2,
											'facettes' => 'T2',
											'isbn' => '123456789',
											'ean' => '',
											'annee' => 1974,
											'tome_alpha' => '',
											'unimarc' => "01328ngm0 2200265   450 0010007000001000041000071010013000481020007000611150025000682000071000932100022001642150053001863000035002393000045002743300454003193450027007735100018008006060027008186060039008457000042008847020043009267020033009697020032010028010028010342247456  a20021213i20041975u  y0frey0103    ba0 abamjfre  aFR  ac086baz|zba    zz  c1 aLa jeune fillebDVDdDen MusofSouleymane Cisse, réal., scénario  cPathédcop. 2004  a1 DVD vidéo monoface zone 2 (1 h 26 min)ccoul.  aDate de sortie du film : 1975.  aFilm en bambara sous-titré en français  aSékou est renvoyé de l'usine parce qu'il a osé demander une augmentation. Chômeur, il sort avec Ténin, une jeune fille muette ; il ignore qu'elle est la fille de son ancien patron. Ténin, qui sera violée par Sékou lors d'une sortie entre jeunes, se retrouve enceinte et subit la colère de ses parents. Elle se trouve alors confrontée brutalement à la morale de sa famille et à la lâcheté de Sékou, qui refuse de reconnaiîre l'enfant.  b3388334509824d14.00 ?1 aDen Musozbam| 31070135aCinémayMali| 32243367aCinéma30076549yAfrique 131070144aCissébSouleymane43704690 132247457aCoulibalibDounamba Dani4590 132247458aDiabatebFanta4590 132247459aDiarrabOumou4590 0aFRbBNc20011120gAFNOR"))
			->beStrict();


		$this->_catalogue = Class_Catalogue::getLoader()->newInstanceWithId(666);
		$this->_notices = $this->_catalogue->getNoticesByPreferences(array('id_catalogue' => 666,
																																			 'aleatoire' => 1,
																																			 'nb_analyse' => 25,
																																			 'nb_notices' => 40));
	}


	public function tearDown() {
		Zend_Registry::set('sql', $this->old_sql);
		Zend_Registry::set('cache', $this->old_cache);
		
		parent::tearDown();
	}


	/** @test */
	public function firstNoticeIdShouldBe23() {
		$this->assertEquals(23, 
												$this->_notices[0]["id_notice"]);
	}


	/** @test */
	public function saveInCacheShouldHaveBeenCalledWithSerializedNotices() {
		$this->assertTrue($this->mock_cache->methodHasBeenCalled('save'));
	}


	/** @test */
	public function getNoticesWithCachePresentShouldNotCallThem() {
		$this->mock_cache 
			->whenCalled('test')
			->with('51f61478ed7c755ae2f6a15283526f65')
			->answers(true)

			->whenCalled('load')
			->with('51f61478ed7c755ae2f6a15283526f65')
			->answers(serialize(array('test')))

			->beStrict();

		$notices = $this->_catalogue->getNoticesByPreferences(array('id_catalogue' => 666,
																																'aleatoire' => 1,
																																'nb_analyse' => 25,
																																'nb_notices' => 40));

		$this->assertEquals(array('test'), $notices);
	}



	/** @test */
	public function getNoticesWithCachePresentButUserAdminShouldCallThem() {
		$account = new stdClass();
		$account->username     = 'AutoTest' . time();
		$account->password     = md5( 'password' );		
		$account->ID_USER      = 2;
		$account->ROLE_LEVEL   = ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
		$account->confirmed    = true;
		$account->enabled      = true;
		Zend_Auth::getInstance()->getStorage()->write($account);
		
		Class_Users::getLoader()->newInstanceWithId($account->ID_USER)->setRoleLevel($account->ROLE_LEVEL);

		$notices = $this->_catalogue->getNoticesByPreferences(array('id_catalogue' => 666,
																																'aleatoire' => 1,
																																'nb_analyse' => 25,
																																'nb_notices' => 40));
		$this->assertEquals(23, $notices[0]["id_notice"]);
	}
}

?>