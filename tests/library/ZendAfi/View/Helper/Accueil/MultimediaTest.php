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
	protected $_helper;

	public function setUp() {
		parent::setUp();

		$this->_helper = new ZendAfi_View_Helper_Accueil_Multimedia(2, [
			'type_module'=>'MULTIMEDIA',
			'division' => '1',
			'preferences' => [
			'titre' => 'Postes multimedia']]);

		ZendAfi_Auth::getInstance()->logUser($user = Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']));


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Multimedia_DeviceHold')
			->whenCalled('getFutureHoldsOfUser')
			->with($user)
			->answers([Storm_Test_ObjectWrapper::mock()
								 ->whenCalled('getId')
								 ->answers(25)
								 ->whenCalled('getStart')
								 ->answers(strtotime('2012-12-28 14:00:00'))
								 ->whenCalled('getLibelleBib')
								 ->answers('Melun')
			]);
		
		$this->html = $this->_helper->getBoite();
	}


	/** @test */
	public function titreShouldLinkToActionMultimediaLocation() {
		$this->assertXPathContentContains($this->html,
																			'//h1//a[contains(@href,"/abonne/multimedia-hold-location")]',
																			'Postes multimedia');
	}


	/** @test */
	public function boiteShouldContainsLinkToHoldDevice() {
		$this->assertXPathContentContains($this->html,
																			'//div//a[contains(@href,"/abonne/multimedia-hold-location")]',
																			utf8_encode('Réserver un poste multimedia'));
	}


	/** @test */
	public function boiteShouldContainsReservationAtMelun28_12_2012() {
		$this->assertXPathContentContains($this->html,
																			'//ul//li//a[contains(@href,"abonne/multimedia-hold-view/id/25")]',
																			'[Melun] 28/12/2012 14h00');

	}


	/** @test */
	public function withoutMultimediaKeyBoiteShouldNotBeVisible() {
		Class_AdminVar::newInstanceWithId('MULTIMEDIA_KEY', ['valeur' => '']);
		$this->assertFalse($this->_helper->isBoiteVisible());
	}




}
