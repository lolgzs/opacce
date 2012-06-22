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

class AvisCmsOnArticleConcertTest extends Storm_Test_ModelTestCase {
	protected 
		$_concert, 
		$_laurent, 
		$_avis_laurent;


	public function setUp() {
		parent::setUp();
		$this->_concert = Class_Article::getLoader()
			->newInstanceWithId(2)
			->setTitre('Concert');

		$this->_laurent = Class_Users::getLoader()
			->newInstanceWithId(22)
			->setPseudo('laurent');

		$this->_avis_laurent = Class_Avis::getLoader()
			->newInstanceWithId(43)
			->setIdUser(22)
			->setIdCms(2);


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Avis')
			->whenCalled('findAllBy')
			->with(array('model' => $this->_laurent, 
									 'role' => 'auteur', 
									 'order' => 'date_avis desc'))
			->answers(array($this->_avis_laurent))

			->whenCalled('findAllBy')
			->with(array('model' => $this->_concert, 
									 'role' => 'article', 
									 'order' => 'date_avis desc'))
			->answers(array($this->_avis_laurent));

	}


	/** @test */
	public function avisAuteurShouldBeLaurent() {
		$this->assertEquals($this->_laurent, $this->_avis_laurent->getAuteur());
	}


	/** @test */
	public function avisArticleShouldBeConcert() {
		$this->assertEquals($this->_concert, $this->_avis_laurent->getArticle());
	}

	
	/** @test */
	public function laurentGetAvisArticleShouldAnswersArrayWithAvisLaurent() {
		$this->assertEquals(array($this->_avis_laurent), $this->_laurent->getAvisArticles());
	}


	/** @test */
	public function concertGetAvisShouldAnswersArrayWithAvisLaurent() {
		$this->assertEquals(array($this->_avis_laurent), $this->_concert->getAvis());
	}
}

?>