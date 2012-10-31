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


class PretsTestWithConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$helper = new ZendAfi_View_Helper_Accueil_Prets(2, [
			'type_module'=>'PRETS',
			'division' => '1',
			'preferences' => [
			'titre' => 'Mes documents']]);
		$account = new StdClass();
		$account->ID_USER = '123456';
		ZendAfi_Auth::getInstance()->getStorage()->write($account);
		$user=Class_Users::newInstanceWithId('123456',['nom'=>'Estelle']);
		

		$alice = new Class_WebService_SIGB_Emprunt('13', new Class_WebService_SIGB_Exemplaire(456));
		$alice->getExemplaire()
					->setTitre('Alice')
					->setNoticeOPAC(Class_Notice::newInstanceWithId(1234))
					->setDateRetour('21/10/2021');


		$klein = new Class_WebService_SIGB_Emprunt('13', new Class_WebService_SIGB_Exemplaire(458));
		$klein->getExemplaire()->setTitre('La stratégie du choc');
		$klein->parseExtraAttributes(array(
																			 'Dateretourprevue' => '21/10/2000',
																			 'Section' => 'Essais',
																			 'Auteur' => 'Naomie Klein',
																			 'Bibliotheque' => 'Almont',
																			 'N° de notice' => '5680'));


		$emprunteur = new Class_WebService_SIGB_Emprunteur('1234', 'Estelle');
		$user->setFicheSigb(['fiche'=>$emprunteur]);

		$emprunteur->empruntsAddAll(array( $klein, $alice));
		$this->html = $helper->getBoite();
	}
	

	/** @test  */
	public function h1ShouldContainsMesPrets () {
		$this->assertXPathContentContains($this->html,'//h1','Mes documents');
	}

	
	/** @test */
	public function listShouldDisplayAliceNotice() {
		$this->assertXPathContentContains($this->html,'//ul//li','Alice');
	}


	/** @test */
	public function listShouldDisplayDateRetour () {
		$this->assertXPathContentContains($this->html,'//ul//li','21/10/2021');
	}


	/** @test */
	public function dateRetour2000ShouldBeInClassPretEnRetard () {
		$this->assertXPathContentContains($this->html,'//ul//li[@class="pret_en_retard"]//span[@class="date_retour"]','21/10/2000');
	}



	/** @test */
	public function dateRetour2021ShouldNotBeInClassPretEnRetard () {
		$this->assertNotXPathContentContains($this->html,
																				 '//ul//li[@class="pret_en_retard"]//span[@class="date_retour"]',
																				 '21/10/2021');
	}


	/** @test */
	public function titleAliceShouldBeLinkedToNotice () {
		$this->assertXPath($this->html,'//ul//li//a[contains(@href,"/recherche/viewnotice/clef//id/1234")]',$this->html);
	}


	/** @test */
	public function kleinShouldBeLinkedToRechercheSimple () {
		$this->assertXPath($this->html,'//ul//li//a[contains(@href,"/recherche/simple")]',$this->html);
	}


	/** @test */
	public function titleShouldBeLinkedToAbonnePrets () {
		$this->assertXPath($this->html,'//h1//a[contains(@href,"/abonne/prets")]',$this->html);
	}
	

}




class PretsTestWithNonConnectedUser extends ViewHelperTestCase {	
	public function setUp() {
		parent::setUp();

		$this->helper = new ZendAfi_View_Helper_Accueil_Prets(2, [
			'type_module'=>'PRETS',
			'division' => '1',
			'preferences' => [
			'titre' => 'Mes documents']]);
		$this->html = $this->helper->getBoite();
	}
	

	/** @test */
	public function boitePretsShouldNotBeDisplayed () {
		$this->assertEmpty($this->html);
	}


	/** @test */
	public function boitePretsShouldNotCacheContents () {
		$this->assertFalse($this->helper->shouldCacheContent());
	}

}

?>