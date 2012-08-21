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

abstract class AbonneVSmartTestCase extends AbstractControllerTestCase {
	protected function _loginHook($account) {
		$account->ROLE = "abonne_sigb";
		$account->ROLE_LEVEL = ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB;
		$account->ID_USER = 34;
		$account->PSEUDO = "Marcus";
	}


	public function setUp() {
		parent::setUp();

		$emprunteur = $this->getMockBuilder('Class_WebService_SIGB_Emprunteur')
			                 ->disableOriginalConstructor()
			                 ->getMock();

		$emprunteur
			->expects($this->once())
			->method('getNbPretsEnRetard')
			->will($this->returnValue(1));

		$emprunteur
			->expects($this->once())
			->method('getNbEmprunts')
			->will($this->returnValue(2));

		$emprunteur
			->expects($this->once())
			->method('getNbReservations')
			->will($this->returnValue(3));

		$emprunteur
			->expects($this->once())
			->method('getUserInformationsPopupUrl')
			->will($this->returnValue('http://12.34.56.78/moulins/LoginWebSSo.csp'));

		$this->manon = Class_Users::getLoader()
			->newInstanceWithId(34)
			->setId(10)
			->setPrenom('Manon')
			->setNom('Laffont')
			->setLogin('mlaffont')
			->setMail('mlaffont@gmail.com')
			->setPseudo('manoune')
			->setDateDebut(null)
			->setPassword('gaga')
			->setFicheSIGB(array('type_comm' => Class_IntBib::COM_VSMART, 
													 'nom_aff' => 'Marcus',
													 'fiche' => $emprunteur))
			->setRole('abonne_sigb')
			->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB)
			->setIdSite(2)
			->setIdabon('00123')
			->setNewsletters(array())
			->setDateDebut('2001-02-16')
			->setDateFin('2045-02-16');


		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_PanierNotice')
			->whenCalled('findAllBy')
			->with(array('role' => 'user', 'model' => $this->manon))
			->answers(array(1, 2));
	}
}




class AbonneVSmartTest extends AbonneVSmartTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/opac/abonne/fiche/id/34', true);
	}


	/** @test */
	function actionShouldBeFiche() {
		$this->assertAction('fiche');
	}


	/** @test */
	function aDivAbonneFicheShouldContainsVousAvezDeuxPretsEnCoursUnEnRetard() {
		$this->assertXPathContentContains('//div/a[contains(@href, "abonne/prets")]', 
																			'Vous avez 2 prêts en cours (1 en retard)');
	}


	/** @test */
	function aDivAbonneFicheShouldContainsVousAvezTroisReservationsEnCours() {
		$this->assertXPathContentContains('//div/a[contains(@href, "abonne/reservations")]', 
																			'Vous avez 3 réservations en cours');
	}


	/** @test */
	function aDivAbonneFicheShouldContainsVousAvezDeuxPaniersDeNotice() {
		$this->assertXPathContentContains('//div/a[contains(@href, "panier")]', 
																			'Vous avez 2 paniers de notices');
	}


	/** @test */
	function modifierMaFicheShouldBeALinkToPopup() {
		$this->assertXPathContentContains('//a[@onclick="openIFrameDialog(\'http://12.34.56.78/moulins/LoginWebSSo.csp\');"]',
																		  'Modifier ma fiche');
	}

	/** @test */
	function abonnementShouldBeDisplayedValid() {
		$this->assertXPathContentContains('//div', "Votre abonnement est valide jusqu'au 16-02-2045",
																			$this->_response->getBody());
	}
}




class AbonneWithExpiredSubscriptionVSmartTest extends AbonneVSmartTestCase {
	public function setUp() {
		parent::setUp();
		$this->manon->setDateFin('2001-12-25');
		$this->dispatch('/opac/abonne/fiche/id/34');
	}


	/** @test */
	function abonnementShouldBeDisplayedInvalid() {
		$this->assertXPathContentContains('//div', "Votre abonnement est terminé depuis le 25-12-2001",
																			$this->_response->getBody());
	}
}

?>