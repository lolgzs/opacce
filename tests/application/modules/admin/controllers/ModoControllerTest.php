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

class ModoControllerDeleteAvisCmsTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Avis::getLoader()
			->newInstanceWithId(34)
			->setAuteur(Class_Users::getLoader()
				          ->newInstanceWithId(98)
				          ->setPseudo('Mimi'))
			->setDateAvis('2012-02-05')
			->setNote(4)
			->setEntete('Hmmm')
			->setAvis('ça a l\'air bon')
			->beWrittenByAbonne()
      ->setIdCms(28);


	  Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Avis')
			->whenCalled('delete')
			->answers(true);

	
		$this->dispatch('admin/modo/delete-cms-avis/id/34', true);
	}


  /** @test */
	public function avisShouldHaveBeenDeleted() {
		$this->assertEquals(34, Class_Avis::getLoader()->getFirstAttributeForLastCallOn('delete')->getId());
	}


	/** @test */
	public function answersShouldRedirectToArticleId28() {
		$this->assertRedirectTo('/opac/cms/articleview/id/28');
	}
}

?>