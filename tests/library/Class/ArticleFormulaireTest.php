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


class ArticleFormulaireInternalTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		$this->_article = Class_Article::newInstanceWithId(2,['titre' => 'Contactez-nous !',
																				'contenu' => '<form id="idform" action="form" method="post" name="form" target="_blank">
	<p>		Donnee 1 :<br /><input name="champs texte" type="text" value="champtxt" />
  <input value="champ2"  name=\'champs texte\' type="text"/></p>
	<p>	&nbsp;</p>
	<p>	<input name="click" type="submit" value="click !" /></p>
	<input name="envoi" value="envoi" type="submit"/>
	<input type="submit" value="send" name="send"/>

  </form>
  <form method="POST">
  
    <input type="button" value="likebutton" />
  </form>
  ']);
	}

	/** @test */
	public function formIdFormActionShouldBeFormulaireAdd() {
			$this->assertContains('<form action="'.BASE_URL.'/formulaire/add/id_article/2" id="idform"', 
														$this->_article->getContenu());
	}


	/** @test */
	public function emptyFormActionShouldBeFormulaireAdd() {
			$this->assertContains('<form action="'.BASE_URL.'/formulaire/add/id_article/2" method="POST">', 
														$this->_article->getContenu());
	}

 
	/** @test */
	public function formSubmitButtonShouldHaveNoName() {
		$this->assertContains('<input   value="click !" type="submit"/>',
													$this->_article->getContenu());
	}



	/** @test */
	public function formNotSubmitButtonShouldNotBeChanged() {
		$this->assertContains(
			'<input name="champs texte" type="text" value="champtxt" />',
			$this->_article->getContenu());
	}


	/** @test */
	public function formSubmitButtonNamedEnvoiShouldHaveNoName() {
		$this->assertContains('<input  value="envoi" type="submit"/>',
													$this->_article->getContenu());
	}

	/** @test */
	public function formSubmitButtonNamedSendShouldHaveNoName() {
		$this->assertContains('<input  value="send" type="submit"/>',
													$this->_article->getContenu());
	}


	/** @test */
	public function formTypeButtonShouldBeTransformedToSubmit() {
		$this->assertContains('<input  value="likebutton" type="submit"/>',
													$this->_article->getContenu());
	}
}


class ArticleFormulaireExternalTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();
		$this->_article = Class_Article::newInstanceWithId(2,['titre' => 'Contactez-nous !',
																				'contenu' => '
  <form id="extern" action="http://monserveur/post" >
	<input name="extenvoi" value="extenvoi" type="submit"/>
  </form>
  ']);
	}

 
	/** @test */
	public function formWithExternalUrlShouldNotChange() {
			$this->assertContains('<form id="extern" action="http://monserveur/post" >', 
														$this->_article->getContenu());
	}

	/** @test */
	public function formSubmitWithExternalUrlShouldNotChange() {
		$this->assertContains('	<input name="extenvoi" value="extenvoi" type="submit"/>',
														$this->_article->getContenu());
	}

}
