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

abstract class FormulaireControllerPostActionTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();


		Class_Article::newInstanceWithId(45, ['titre' => 'Contactez nous']);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Formulaire')
		->whenCalled('save')
		->willDo(function ($formulaire) {
				$formulaire->setId(2)->cache();
				return true;
			});


		$timesource = new TimeSourceForTest();
		$timesource->setTime(strtotime('2012-10-23 12:32:00'));
		Class_Formulaire::setTimeSource($timesource);
	}
}




class FormulaireControllerPostActionTest extends FormulaireControllerPostActionTestCase {
	protected $_user;

  public function setUp() {
    parent::setUp();

		$user = Class_Users::newInstanceWithId(23, ['nom' => 'Mas', 
																								'prenom' => 'Fanto', 
																								'login' => 'fantomas']);
		ZendAfi_Auth::getInstance()->logUser($user);


    $this->postDispatch('/formulaire/add/id_article/45', 
												['nom' => 'Tinguette' ,
												 'prenom' => 'Quentin' ]
												,true);

		$this->new_formulaire = Class_Formulaire::find(2);
 	}

	
	/** @test */
	public function saveFormulaireShouldHaveNomTinguette() {
		$this->assertEquals('Tinguette', $this->new_formulaire->getNom());
	}

	
	/** @test */
	public function dateCreationShouldBeNow() {
		$this->assertEquals('2012-10-23 12:32:00', $this->new_formulaire->getDateCreation());
	}


	/** @test */
	public function getDataShouldAnswerSerializedNomPrenom() {
		$this->assertEquals(serialize(['nom' => 'Tinguette' ,
																	 'prenom' => 'Quentin']),
																	$this->new_formulaire->getData());
	}


	/** @test */
	public function saveFormulaireShouldSaveUserIdIfConnected() {
		$this->assertEquals(23, $this->new_formulaire->getIdUser());
	}


	/** @test */
	public function articleShouldBeContactezNous() {
		$this->assertEquals('Contactez nous', $this->new_formulaire->getArticle()->getTitre());
	}


	/** @test */
	public function articleTitreContactezNousShouldBeDisplayed() {
		$this->assertXPathContentContains('//h1', 'Contactez nous');
	}


	/** @test */
	public function postFormulaireShouldReturnMessage() {
		$this->assertXpathContentContains('//div','Merci.',true );
	}


	/** @test */
	public function postFormulaireShouldReturnPostValues() {
		$this->assertXpathContentContains('//div','Tinguette',true );
	}
}




class FormulaireControllerWithoutConnectedUserPostActionTest extends FormulaireControllerPostActionTestCase {

  public function setUp() {
    parent::setUp();

		ZendAfi_Auth::getInstance()->clearIdentity();

    $this->postDispatch('/formulaire/add', 
												['nom' => 'Tinguette' ,
												 'prenom' => 'Quentin' ]
												,true);

		$this->new_formulaire = Class_Formulaire::find(2);

	}

	/** @test */
	public function saveFormulaireShouldNotHaveAnyUsers() {
		$this->assertEmpty($this->new_formulaire->getUser());
		
	}

}

?>