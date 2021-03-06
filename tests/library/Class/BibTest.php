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

class BibTest extends Storm_Test_ModelTestCase {
	/** @test */
	function loaderFindAllWithPortailShouldIncludePortail() {
		$this->assertEquals(0, 
												array_first(Class_Bib::getLoader()->findAllWithPortail())->getId());
	}

	/** @test */
	function loaderFindAllByWithPortailShouldIncludePortail() {
		$this->assertEquals(0, 
												array_first(Class_Bib::getLoader()->findAllByWithPortail(array()))->getId());
	}


	/** 
	 * Non régression bug n'affiche aucune bib sur sélection territoire "toutes" admin bib
	 * @test 
	 */
	public function findAllByIdZoneShouldForceIntForRequest() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Bib')
			->whenCalled('findAllBy')
			->with(['order' => 'ville'])
			->answers('ALL')

			->whenCalled('findAllBy')
			->with(['id_zone' => 3, 
							'order' => 'ville'])
			->answers('ZONE');

		$this->assertEquals('ALL', Class_Bib::findAllByIdZone('ALL'));
		$this->assertEquals('ZONE', Class_Bib::findAllByIdZone(3));

	}
}

?>