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

class ArticleCategorieTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->annecy = Class_Bib::getLoader()
			->newInstanceWithId(21)
			->setLibelle('Annecy');

		$this->cat_jeunesse = Class_ArticleCategorie::getLoader()
			->newInstanceWithId(3)
			->setLibelle('Jeunesse')
			->setIdCatMere(null)
			->setBib($this->annecy);

		$this->annecy->setArticleCategories(array($this->cat_jeunesse));

		$this->cran = Class_Bib::getLoader()
			->newInstanceWithId(32)
			->setLibelle("Cran");

		$this->cat_adulte = Class_ArticleCategorie::getLoader()
			->newInstanceWithid(5)
			->setLibelle('Adulte')
			->setIdCatMere(null)
			->setBib($this->cran);

		$this->cran->setArticleCategories(array($this->cat_adulte));


		$this->cat_concerts = Class_ArticleCategorie::getLoader()
			->newInstanceWithId(34)
			->setLibelle('Concerts')
			->setIdCatMere(5);

		$this->cat_concerts_jazz = Class_ArticleCategorie::getLoader()
			->newInstanceWithId(73)
			->setLibelle('Jazz')
			->setIdCatMere(34);


		$this->cat_actu = Class_ArticleCategorie::getLoader()
			->newInstanceWithId(12)
			->setLibelle('Actu')
			->setIdCatMere(5);


		$this->cat_wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
			->whenCalled('findAllBy')
			->with(array('role' => 'parent_categorie', 'model' => $this->cat_adulte, 'order' => 'libelle'))
			->answers(array($this->cat_concerts, $this->cat_actu))
			->getWrapper()

			->whenCalled('findAllBy')
			->with(array('role' => 'parent_categorie', 'model' => $this->cat_concerts, 'order' => 'libelle'))
			->answers(array($this->cat_concerts_jazz))
			->getWrapper()

			->whenCalled('findAllBy')
			->with(array('role' => 'parent_categorie', 'model' => $this->cat_jeunesse, 'order' => 'libelle'))
			->answers(array())
			->getWrapper()


			->whenCalled('delete')
			->answers(true)
			->getWrapper();



		$this->concert_truffaz = Class_Article::getLoader()
			->newInstanceWithId(23)
			->setIdCat(34)
			->setTitre('Concert Truffaz');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_concerts, 'order' => 'titre'))
			->answers(array($this->concert_truffaz))
			->getWrapper()

			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_jeunesse, 'order' => 'titre'))
			->answers(array())
			->getWrapper()

			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_adulte, 'order' => 'titre'))
			->answers(array())
			->getWrapper()

			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_actu, 'order' => 'titre'))
			->answers(array())
			->getWrapper()


			->whenCalled('findAllBy')
			->with(array('role' => 'categorie', 'model' => $this->cat_concerts_jazz, 'order' => 'titre'))
			->answers(array())
			->getWrapper()


			->whenCalled('delete')
			->answers(true);
	}


	/** @test */
	public function concertsParentCategorieShouldBeJAdulte() {
		$this->assertEquals($this->cat_adulte, $this->cat_concerts->getParentCategorie());
	}


	/** @test */
	public function jeunesseSousCategoriesShouldAnswerEmptyArray() {
		$this->assertEquals(array(), $this->cat_jeunesse->getSousCategories());
	}


	/** @test */
	public function jeunesseToJSONShouldNoHaveItemNorCategorie() {
		$expected = '{ "id": 3,'.
									'"label": "Jeunesse",'.
									'"items": [],'.
									'"categories": [] }';
		$this->assertJSONEquals($expected, $this->cat_jeunesse->toJSON());
	}


	/** @test */
	public function annecyToJSONShouldHaveCategorieJeunesse() {
		$expected = '{ "id": 21,'.
			            '"label": "Annecy",'.
                  '"items": [],'.
			            '"categories": [{ "id": 3,'.
			                             '"label": "Jeunesse",'.
									                 '"items": [],'.
									                 '"categories": [] }]}';
		$this->assertJSONEquals($expected, $this->annecy->articlesToJSON());
	}


	/** 
	 * Régression vue sur Pontault
	 * @test 
	 */
	public function annecyWithCarriageReturnToJSONShouldRemoveCR() {
		$this->annecy->setLibelle("Annecy\n");

		$expected = '{ "id": 21,'.
			            '"label": "Annecy",'.
                  '"items": [],'.
			            '"categories": [{ "id": 3,'.
			                             '"label": "Jeunesse",'.
									                 '"items": [],'.
									                 '"categories": [] }]}';
		$this->assertJSONEquals($expected, $this->annecy->articlesToJSON());
	}


	/** @test */
	public function aduteSousCategoriesShouldAnswerArrayWithConcertsAndActu() {
		$this->assertEquals(array($this->cat_concerts,
															$this->cat_actu),
												$this->cat_adulte->getSousCategories());
	}


	/** @test */
	public function adulteToJSONShouldHaveCategoriesConcertsAndActu() {
		$expected = <<<JSON
			{"id":5,
			 "label": "Adulte",
			 "categories": [
					 {"id":34,
						"label": "Concerts",
						"categories": [{"id":73,
														"label": "Jazz",
														"categories": [],
														"items": []}],
						"items": [{"id":23,
											 "label":"Concert Truffaz"}]},
					 {"id":12,
						"label": "Actu",
						"categories": [],
						"items": []}],
			 "items": []}
JSON;

		$this->assertJSONEquals($expected, $this->cat_adulte->toJSON());
	}


	/** @test */
	public function adulteToJSONWithoutArticlesShouldHaveCategoriesOnly() {
		$expected = <<<JSON
			{"id":5,
			 "label": "Adulte",
			 "categories": [
					 {"id":34,
						"label": "Concerts",
						"categories": [{"id":73,
														"label": "Jazz",
														"categories": [],
														"items": []}],
						"items": []},
					 {"id":12,
						"label": "Actu",
						"categories": [],
						"items": []}],
			 "items": []}
JSON;

		$this->assertJSONEquals($expected, $this->cat_adulte->toJSON(false));
	}


	/** @test */
	public function aduteRecursiveSousCategoriesShouldAnswerArrayWithConcertsJazzAndActu() {
		$this->assertEquals(array($this->cat_concerts,
															$this->cat_actu,
															$this->cat_concerts_jazz),
												$this->cat_adulte->getRecursiveSousCategories());
	}


	/** @test */
	public function concerTruffazCategorieShouldBeConcerts() {
		$this->assertEquals($this->cat_concerts, $this->concert_truffaz->getCategorie());
	}


	/** @test */
	public function concertsGetArticlesShouldAnswerArrayWithConcertTruffaz() {
		$this->assertEquals(array($this->concert_truffaz),
												$this->cat_concerts->getArticles());
	}


	/** @test */
	public function deleteCategorieShouldCascadeDelete() {
		$this->cat_adulte->delete();

		$wrapper = Class_ArticleCategorie::getLoader();

		foreach(array($this->cat_adulte, $this->cat_concerts, $this->cat_actu) as $categorie)
			$this->assertTrue($wrapper->methodHasBeenCalledWithParams('delete', array($categorie)));


		$article_loader = Class_ArticleCategorie::getLoader();
		$this->assertTrue($article_loader->methodHasBeenCalled('delete',
																													 array($this->concert_truffaz)));
	}


	/** @test */
	public function adulteShouldHaveChildren() {
		$this->assertTrue($this->cat_adulte->hasChildren());
		$this->assertFalse($this->cat_adulte->hasNoChild());
	}


	/** @test */
	public function jeunesseShouldNotHaveChildren() {
		$this->assertFalse($this->cat_jeunesse->hasChildren());
		$this->assertTrue($this->cat_jeunesse->hasNoChild());
	}


	/** @test */
	function categorieConcertsJazzBibShouldBeCran() {
		$this->assertEquals($this->cran, $this->cat_concerts_jazz->getBib());
	}


	/** @test */
	function withoutBibShouldShouldReturnPortail() {
		$this->cat_adulte->setBib(null);
		$this->assertEquals('Portail', $this->cat_concerts_jazz->getBib()->getLibelle());
	}
}

?>