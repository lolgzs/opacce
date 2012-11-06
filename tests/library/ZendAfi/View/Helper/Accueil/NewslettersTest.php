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


class NewslettersTestWithConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$helper = new ZendAfi_View_Helper_Accueil_Newsletters(2, [
			'type_module'=>'NEWSLETTERS',
			'division' => '1',
			'preferences' => [
			'titre' => 'Newsletters']]);
		$account = new StdClass();
		$account->ID_USER = '123456';
		ZendAfi_Auth::getInstance()->getStorage()->write($account);
		$user=Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']);
		$nouveautes_musique = Class_Newsletter::newInstanceWithId(2,['titre' =>'Nouveautes Musique']);

		$user->setNewsletters([$nouveautes_musique]);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Newsletter') 
		->whenCalled('findAll')
		->answers( [ $nouveautes_musique,
									Class_Newsletter::newInstanceWithId(3,['titre' =>'Animations'])
		]);

		$this->html = $helper->getBoite();
	}
	

	/** @test  */
	public function h1ShouldContainsMesNewsletters () {
		$this->assertXPathContentContains($this->html,'//h1','Newsletters');
	}

	
	/** @test */
	public function listShouldDisplayNouveautesMusique() {
		$this->assertXPathContentContains($this->html,'//ul//li','Nouveautes Musique');
	}

	/** @test */
	public function listShouldDisplayAnimations() {
		$this->assertXPathContentContains($this->html,'//ul//li','Animations');
	}


	/** @test */
	public function listShouldDisplayButtonSubscribe() {
		$this->assertXPath($this->html,'//ul//li//a[contains(@href,"/subscribe-newsletter")]',$this->html);

	}


	/** @test */
	public function listShouldDisplayButtonUnSubscribe() {
		$this->assertXPath($this->html,'//ul//li//a[contains(@href,"/unsubscribe-newsletter")]',$this->html);

	}


}

class NewslettersTestWithConnectedUserWithoutNewsletters extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$this->helper = new ZendAfi_View_Helper_Accueil_Newsletters(2, [
			'type_module'=>'NEWSLETTERS',
			'division' => '1',
			'preferences' => [
			'titre' => 'Newsletters']]);

		$user=Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']);
		ZendAfi_Auth::getInstance()->logUser($user);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Newsletter') 
		->whenCalled('findAll')
		->answers([])
		->whenCalled('count')
		->answers(0);

		$this->html = $this->helper->getBoite();
	}
	

	/** @test */
	public function boiteNewslettersShouldNotBeDisplayed () {
		$this->assertEmpty($this->html);
	}


	/** @test */
	public function boiteNewslettersShouldNotCacheContents () {
		$this->assertFalse($this->helper->shouldCacheContent());
	}

}


class NewslettersTestWithNonConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$this->helper = new ZendAfi_View_Helper_Accueil_Newsletters(2, [
			'type_module'=>'NEWSLETTERS',
			'division' => '1',
			'preferences' => 	['titre' => 'Mes newsletters']]
		);
		$this->html = $this->helper->getBoite();
	}
	

	/** @test */
	public function boiteNewslettersShouldNotBeDisplayed () {
		$this->assertEmpty($this->html);
	}


	/** @test */
	public function boiteNewslettersShouldNotCacheContents () {
		$this->assertFalse($this->helper->shouldCacheContent());
	}

}

?>