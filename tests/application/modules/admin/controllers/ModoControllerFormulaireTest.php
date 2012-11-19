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

abstract class ModoControllerFormulaireForArticleTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$article = Class_Article::newInstanceWithId(12, ['titre' => 'Inscrivez vous au Hackaton']);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
			->whenCalled('delete')
			->answers(true)

			->whenCalled('save')
			->answers(true)

			->whenCalled('findAllBy')
			->with([ 'role' => 'article',
							 'model' => $article,
							 'order' => 'date_creation desc'])

			->answers([
				Class_Formulaire::newInstanceWithId(3, ['data' => serialize(['nom' => 'Tinguette',
																																		 'prenom' => 'Quentine']),
																								'date_creation' => '2012-12-05 12:00:23',
																								'article' => $article,
																								'validated' => true]),

				Class_Formulaire::newInstanceWithId(5, ['data' => serialize(['nom' => 'Bougie',
																																		 'Prenom' => 'Mireille']),
																								'date_creation' => '2012-12-06 10:00:01',
																								'article' => $article,
																								'user' => Class_Users::newInstanceWithId(34, 
																																												 [
																																													 'login' => 'zork',
																																													 'bib' => Class_Bib::newInstanceWithId(4, ['libelle' => 'Annecy'])
																																												 ])]),

				Class_Formulaire::newInstanceWithId(6, ['data' => serialize(['name' => 'Lefort',
																																		 'prenom' => 'Nono',
																																		 'age' => 12]),
																								'date_creation' => '2012-11-06 17:00:01',
																								'article' => $article])
			]);
	}
}




class ModoControllerFormulaireForArticleListTest extends ModoControllerFormulaireForArticleTestCase {
  public function setUp() {
    parent::setUp();

    $this->dispatch('admin/modo/formulaires/id_article/12', true);
  }


  /** @test */
  public function h1ShouldContainsFormulairesAndArticleTitle() {
    $this->assertXPathContentContains('//h1', 'Modération des formulaires: Inscrivez vous au Hackaton');
	}


	/** @test */
	public function aTDShouldContainsTinguette() {
		$this->assertXPathContentContains('//td', 'Tinguette');
	}


	/** @test */
	public function aTDShouldContainsBougie() {
		$this->assertXPathContentContains('//td', 'Bougie');
	}


	/** @test */
	public function aLastTDShouldContainsAge12() {
		$this->assertXPathContentContains('//td', '12');
	}


	/** @test */
	public function aTDShouldContainsPrenomMireille() {
		$this->assertXPathContentContains('//tr[2]//td', 'Mireille');
	}


	/** @test */
	public function formulaireQuentineShouldNotHaveLinkToValidate() {
		$this->assertNotXPath('//a[contains(@href, "validate-formulaire/id_article/12/id/3")]');
	}


	/** @test */
	public function mireilleRowShouldContainsUserZork() {
		$this->assertXPathContentContains('//tr[2]//td', 'zork');
	}


	/** @test */
	public function mireilleRowShouldContainsBibAnnecy () {
		$this->assertXPathContentContains('//tr[2]//td', 'Annecy');
	}


	/** @test */
	public function mireilleRowShouldContainsDate06_12_2012 () {
		$this->assertXPathContentContains('//tr[2]//td', '06/12/2012', $this->_response->getBody());
	}


	/** @test */
	public function aTDShouldContainsActionToDeleteFormulaireMireille() {
		$this->assertXPath('//tr[2]//td/a[contains(@href, "admin/modo/delete-formulaire/id_article/12/id/5")]');
	}


	/** @test */
	public function aTDShouldContainsActionToValidateFormulaireMireille() {
		$this->assertXPath('//tr[2]//td/a[contains(@href, "admin/modo/validate-formulaire/id_article/12/id/5")]');
	}


	/** @test */
	public function linkToExportCsvShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "admin/modo/export-csv-formulaire/id_article/12")]', 
																			'Export CSV');
	}
}




class ModoControllerFormulaireForArticleValidateFormulaireMireilleTest extends ModoControllerFormulaireForArticleTestCase {
  public function setUp() {
    parent::setUp();

    $this->dispatch('admin/modo/validate-formulaire/id_article/12/id/5', true);
  }


	/** @test */
	public function formulaireShouldBeValidated() {
		$this->assertTrue(Class_Formulaire::find(5)->isValidated());
	}


	/** @test */
	public function formulaireShouldHaveBeenSaved() {
		$this->assertEquals(5, Class_Formulaire::getFirstAttributeForLastCallOn('save')->getId());
	}


	/** @test */
	public function responseShouldRedirectToFormulairesIdArticle12() {
		$this->assertRedirectTo('/admin/modo/formulaires/id_article/12');
	}


	/** @test */
	public function dateCreationShouldNotChange() {
		$this->assertEquals('2012-12-06 10:00:01', Class_Formulaire::find(5)->getDateCreation());
	}
}




class ModoControllerFormulaireForArticleDeleteTest extends ModoControllerFormulaireForArticleTestCase {
  public function setUp() {
    parent::setUp();

    $this->dispatch('admin/modo/delete-formulaire/id_article/12/id/5', true);
  }

	
	/** @test */
	public function formulaireShouldHaveBeenDeleted() {
		$this->assertEquals(5, Class_Formulaire::getFirstAttributeForLastCallOn('delete')->getId());
	}


	/** @test */
	public function responsShouldRedirectToFormulairesIdArticle12() {
		$this->assertRedirectTo('/admin/modo/formulaires/id_article/12');
	}
}




class ModoControllerFormulaireExportCSVForArticlTest extends ModoControllerFormulaireForArticleTestCase {
  public function setUp() {
    parent::setUp();

    $this->dispatch('admin/modo/export-csv-formulaire/id_article/12', true);
  }

	
	/** @test */
	public function secondFormulaireShouldBeCSV() {
		$this->assertContains('"2012-12-06 10:00:01",zork,Annecy,Bougie,Mireille',
													$this->_response->getBody());
	}


	/** @test */
	public function csvShouldContainsAttributeNames() {
		$this->assertContains('date_creation,compte,libelle_bib,nom,prenom,name,age',
													$this->_response->getBody());
	}


	/** @test */
	public function headerShouldContainsFileAttachment() {
		$this->assertHeaderContains('Content-Disposition', 'attachment; filename="formulaire_12.csv"');
	}


	/** @test */
	public function headerShouldContainsContentTypeCSV() {
		$this->assertHeaderContains('Content-Type', 'text/csv; name="formulaire_12.csv"');
	}
}




class ModoControllerFormulaireListTest extends Admin_AbstractControllerTestCase {
  public function setUp() {
    parent::setUp();
		$hackaton =		Class_Article::newInstanceWithId(4, ['titre' => 'Inscrivez vous au Hackaton']);
		$preinscription = Class_Article::newInstanceWithId(2, ['titre' => 'Formulaire de préinscription']);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('findAll')
			->with('select id_article,titre from cms_article where id_article in (select distinct id_article from formulaires)')
			->answers([
				$hackaton,
				$preinscription
			]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
			->whenCalled('countBy')
			->with(['model' => $hackaton,
							'role' => 'article'])
			->answers(2)

			->whenCalled('countBy')
			->with(['model' => $hackaton,
							'role' => 'article',
							'scope' => ['validated' => false]])
			->answers(2)

			->whenCalled('countBy')
			->with(['model' => $preinscription,
							'role' => 'article'])
			->answers(4)

			->whenCalled('countBy')
			->with(['model' => $preinscription,
							'role' => 'article',
							'scope' => ['validated' => false]])
			->answers(1)

			->whenCalled('countNotValidated')
			->answers(3)

			->beStrict();

    $this->dispatch('admin/modo/formulaires/', true);
  }


	/** @test */
	public function liShouldContainsLinkToFormulaireForHackaton() {
		$this->assertXPathContentContains('//li[1]/a[contains(@href,"admin/modo/formulaires/id_article/4")]', 'Inscrivez vous au Hackaton [2/2]', $this->_response->getBody());
	}


	/** @test */
	public function liShouldContainsLinkToEditFormulaireHackaton() {
		$this->assertXPath('//li[1]/a[contains(@href,"admin/cms/newsedit/id/4")]');
	}


	/** @test */
	public function liShouldContainsLinkToFormulaireForPreinscription() {
		$this->assertXPathContentContains('//li[2]/a[contains(@href,"admin/modo/formulaires/id_article/2")]', 'Formulaire de préinscription [1/4]',$this->_response->getBody());
	}
}




class ModoControllerFormulaireIndexWithOptionActivatedTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('CMS_FORMULAIRES')->setValeur(1);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
			->whenCalled('countBy')
			->with(['validated' => false])
			->answers(2);

		$this->dispatch('admin/modo/', true);
	}


	/** @test */
	public function linkToModerateFormulairesShouldBePresent() {
		$this->assertXPathContentContains('//a[contains(@href, "/admin/modo/formulaires")]/following-sibling::span', '2');
	}
}




class ModoControllerFormulaireIndexWithOptionDesactivatedTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_AdminVar::newInstanceWithId('CMS_FORMULAIRES')->setValeur(0);
		$this->dispatch('admin/modo/', true);
	}


	/** @test */
	public function linkToModerateFormulairesShouldNotBePresent() {
		$this->assertNotXPath('//a[contains(@href, "/admin/modo/formulaires")]');
	}
}

?>

