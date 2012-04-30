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
?>