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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';


class MultimediaTestWithConnectedUser extends ViewHelperTestCase {	

	public function setUp() {
		parent::setUp();

		$helper = new ZendAfi_View_Helper_Accueil_Multimedia(2, [
			'type_module'=>'MULTIMEDIA',
			'division' => '1',
			'preferences' => [
			'titre' => 'Postes multimedia']]);

		ZendAfi_Auth::getInstance()->logUser(Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']));

		$this->html = $helper->getBoite();
	}

	/** @test */
	public function titreShouldLinkToActionMultimediaLocation() {
		$this->assertXPath($this->html,'//h1//a[contains(@href,"/abonne/multimedia-hold-location")]',$this->html);
		
	}


}
