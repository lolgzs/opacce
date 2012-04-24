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

abstract class LieuControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur(1);

		$this->afi_annecy = Class_Lieu::getLoader()
			->newInstanceWithId(3)
			->setLibelle('AFI Annecy')
			->setAdresse('11, boulevard du fier')
			->setCodePostal('74000')
			->setVille('Annecy')
			->setPays('France');


		$this->afi_lognes = Class_Lieu::getLoader()
			->newInstanceWithId(5)
			->setLibelle('AFI Lognes')
			->setAdresse('35, rue de la Maison Rouge')
			->setCodePostal('77185')
			->setVille('Lognes')
			->setPays('France');


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Lieu')
			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array($this->afi_annecy, $this->afi_lognes))

			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(null);
	}
}




class LieuControllerListTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/lieu');
	}

	
	/** @test */
	public function aListItemShouldContainsAnnecy() {
		$this->assertXPathContentContains('//ul//li[1]', 'AFI Annecy');
	}


	/** @test */
	public function aListItemShouldContainsLognes() {
		$this->assertXPathContentContains('//ul//li[2]', 'AFI Lognes');
	}


	/** @test */
	public function annecyShouldHaveLinkToEdit() {
		$this->assertXPath('//ul//li[1]//a[contains(@href, "lieu/edit/id/3")]');
	}


	/** @test */
	public function annecyShouldHaveLinkToDelete() {
		$this->assertXPath('//ul//li[1]//a[contains(@href, "lieu/delete/id/3")]');
	}


	/** @test */
	public function titreShouldBeLieux() {
		$this->assertXPathContentContains('//h1', 'Lieux');
	}


	/** @test */
	public function pageShouldContainsButtonToCreateLieu() {
		$this->assertXPathContentContains('//div[contains(@onclick, "lieu/add")]//td', 'Déclarer un nouveau lieu');
	}


	/** @test */
	function menuGaucheAdminShouldContainsLinkToLieu() {
		$this->assertXPathContentContains('//div[@class="menuGaucheAdmin"]//a[contains(@href,"admin/lieu")]',
																			"Lieux");
	}
}




class LieuControllerAddTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/lieu/add');
	}


	/** @test */
	public function titreShouldBeDeclarerUnNouveauLieu() {
		$this->assertXPathContentContains('//h1', 'un nouveau lieu');
	}


	/** @test */
	public function formShouldContainsInputForLibelle() {
		$this->assertXPath('//form[contains(@action, "add")][@method="post"]//input[@name="libelle"]');
	}


	/** @test */
	public function formShouldContainsTextAreaForAdresse() {
		$this->assertXPath('//form//textarea[@name="adresse"]');
	}


	/** @test */
	public function formShouldContainsInputForCodePostal() {
		$this->assertXPath('//form//input[@name="code_postal"]');
	}


	/** @test */
	public function formShouldContainsInputForVille() {
		$this->assertXPath('//form//input[@name="ville"]');
	}


	/** @test */
	public function formShouldContainsInputForPaysWithDefaultFrance() {
		$this->assertXPath('//form//input[@name="pays"][@value="FRANCE"]');
	}
}




class LieuControllerPostNewLieuTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Lieu::getLoader()
			->whenCalled('save')
			->willDo(function($lieu) {
					$lieu->setId(90);
					return true;
			});

		$this->postDispatch('/admin/lieu/add',
												array('libelle' => 'MJC des Romains',
															'adresse' => '28 avenue du stade',
															'code_postal' => '74014',
															'ville' => 'Annecy',
															'pays' => 'France'));
		$this->new_lieu = Class_Lieu::getLoader()->getFirstAttributeForLastCallOn('save');
	}

	
	/** @test */
	public function lieuShouldBeCreatedWithMJCDesRomains() {
		$this->assertEquals('MJC des Romains', $this->new_lieu->getLibelle());
	}


	/** @test */
	public function adresseShouldBe28AvenueStade() {
		$this->assertEquals('28 avenue du stade', $this->new_lieu->getAdresse());
	}


	/** @test */
	public function codePostalShouldReturn74014() {
		$this->assertEquals('74014', $this->new_lieu->getCodePostal());
	}


	/** @test */
	public function answerShouldRetirectToLieuEditId90() {
		$this->assertRedirectTo('/admin/lieu/edit/id/90');
	}
}




class LieuControllerEditAnnecyTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/lieu/edit/id/3');
	}


	/** @test */
	public function titreShouldBeModificationDuLieuAfiAnnecy() {
		$this->assertXPathContentContains('//h1', 'Modifier le lieu: "AFI Annecy"');
	}


	/** @test */
	public function formInputLibelleShouldContainsAnnecy() {
		$this->assertXPath('//form[contains(@action, "edit")][@method="post"]//input[@name="libelle"][@value="AFI Annecy"]');	
	}


	/** @test */
	public function adresseShouldContains11BoulevardDuFier() {
		$this->assertXPathContentContains('//textarea[@name="adresse"]', '11, boulevard du fier');
	}


	/** @test */
	public function codePostalInputShouldContains74000() {
		$this->assertXPath('//input[@name="code_postal"][@value="74000"]');
	}


	/** @test */
	public function villeShouldContainsAnnecy() {
		$this->assertXPath('//input[@name="ville"][@value="Annecy"]');
	}


	/** @test */
	public function pageShouldContainsStaticGoogleImage() {
		$this->assertXPath('//img[@src="http://maps.googleapis.com/maps/api/staticmap?sensor=false&zoom=15&size=200x200&center=11%2C+boulevard+du+fier%2C74000%2CAnnecy%2CFrance&markers=11%2C+boulevard+du+fier%2C74000%2CAnnecy%2CFrance"]',
											 $this->_response->getBody());
	}
}




class LieuControllerPostLieuAnnecyTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->postDispatch('/admin/lieu/edit/id/3',
												array('libelle' => 'Agence Francaise Informatique'));
	}


	/** @test */
	public function libelleAnnecyShouldBeAgenceFrancaise() {
		$this->assertEquals('Agence Francaise Informatique', $this->afi_annecy->getLibelle());
	}


	/** @test */
	public function lieuAnnecyShouldHaveBeenSaved() {
		$this->assertEquals($this->afi_annecy, Class_Lieu::getLoader()->getFirstAttributeForLastCallOn('save'));
	}
}




class LieuControllerDeleteAnnecyTest extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/admin/lieu/delete/id/3');
	}


	/** @test */
	public function lieuShouldHaveBeenDeleted() {
		$this->assertEquals($this->afi_annecy, Class_Lieu::getLoader()->getFirstAttributeForLastCallOn('delete'));
	}


	/** @test */
	public function answerShouldRetirectToLieuEditId90() {
		$this->assertRedirectTo('/admin/lieu/index');
	}
}




class LieuControllerPostTestErrors extends LieuControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/admin/lieu/add', 
												array('libelle' => ''));
	}


	/** @test */
	function saveShouldNotBeCalledIfLibelleEmpty() {
		$this->assertFalse(Class_Lieu::getLoader()->methodHasBeenCalled('save'));		
	}


	/** @test */
	function responseShouldNotRedirect() {
		$this->assertNotRedirect();
	}

	/** @test */
	function errorShouldDisplayUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]//li', "Une valeur est requise");
	}
}



class LieuControllerErrorsFindTest extends LieuControllerTestCase {
	/** @test */
	public function lieuNotFoundOnEditShouldRedirectToIndex() {
		$this->dispatch('/admin/lieu/edit/id/999999999999');
		$this->assertRedirectTo('/admin/lieu/index');
	}


	/** @test */
	public function lieuNotFoundOnDeleteShouldRedirectToIndex() {
		$this->dispatch('/admin/lieu/delete/id/999999999999');
		$this->assertRedirectTo('/admin/lieu/index');
	}
}

?>