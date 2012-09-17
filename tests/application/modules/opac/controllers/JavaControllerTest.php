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

class JavaControllerTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$cfg_accueil = ['modules' => ['1' => ['division' => 1,
																					'type_module' => 'KIOSQUE']], 
										'options' => 	[]];


		
		$this->profil_cache = Class_Profil::getLoader()->newInstanceWithId(5345)
			->setBrowser('opac')
			->setLibelle('Profil cache')
			->setCfgAccueil($cfg_accueil);

		Class_Profil::setCurrentProfil(Class_Profil::getLoader()->newInstanceWithId(2)
																	 ->setLibelle('Accueil'));
		$_SESSION['id_profil'] = 2;

		$this->dispatch('java/kiosque?id_module=1&id_profil=5345');
	}


	/** @test */
	public function sessionIdProfilShouldBeProfil2() {
		$this->assertEquals(2, $_SESSION['id_profil']);
	}


	/** @test */
	public function vueShouldDefaultsToDiaporama() {
		$this->assertXPath('//script[contains(@src, "diaporama")]');
	}
}


?>