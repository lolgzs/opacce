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
require_once 'Class/AvisNotice.php';
require_once 'ModelTestCase.php';

class NoticeHtmlDonnezVotreAvisTest extends ModelTestCase {
	public function setUp() {
		parent::setUp();

		$_REQUEST["cherche"] = null;
		$_REQUEST["onglet"] = '';

		Class_Profil::setCurrentProfil(new Class_Profil());
		Zend_Registry::get('locale')->setLocale('fr');
		Zend_Registry::get('translate')->setLocale('fr');

		$this->millenium = new Class_Notice();
		$this->millenium
			->setId(25);

		$this->avis = array("bib" => array("nombre" => 0),
												"abonne" => array("nombre" => 0));

		$this->notice_html = new Class_NoticeHtml();

		$this->avis_bib_seulement = new Class_AdminVar();
		$this
			->_generateLoaderFor('Class_AdminVar', array('find'))
			->expects($this->atLeastOnce())
			->method('find')
			->with('AVIS_BIB_SEULEMENT')
			->will($this->returnValue($this->avis_bib_seulement));


		$account = new stdClass();
		$account->username     = 'AutoTest' . time();
		$account->password     = md5( 'password' );		
		$account->ID_USER      = 0;
		$account->ROLE_LEVEL   = 4;
		$account->confirmed    = true;
		$account->enabled      = true;
		ZendAfi_Auth::getInstance()->getStorage()->write($account);
	}

	public function testVisibleWithAdminAndAvisReaderAllowed() {
		ZendAfi_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 5;
		$this->avis_bib_seulement->setValeur('0');

		$html = $this->notice_html->getAvis($this->millenium, $this->avis);
		$this->assertTrue(strpos($html, 'Donnez ou modifiez votre avis') != false, $html);
	}


	public function testVisibleWithReaderAndAvisReaderAllowed() {
		ZendAfi_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 1;
		$this->avis_bib_seulement->setValeur('0');

		$html = $this->notice_html->getAvis($this->millenium, $this->avis);
		$this->assertTrue(strpos($html, 'Donnez ou modifiez votre avis') != false);
	}


	public function testVisibleWithAdminAndAvisReaderForbidden() {
		ZendAfi_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 5;
		$this->avis_bib_seulement->setValeur('1');

		$html = $this->notice_html->getAvis($this->millenium, $this->avis);
		$this->assertTrue(strpos($html, 'Donnez ou modifiez votre avis') != false);
	}


	public function testInvisibleWithReaderAndAvisReaderForbidden() {
		ZendAfi_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 1;
		$this->avis_bib_seulement->setValeur('1');

		$html = $this->notice_html->getAvis($this->millenium, $this->avis);
		$this->assertFalse(strpos($html, 'Donnez ou modifiez votre avis'));
	}
}


class NoticeHtmlGetExemplairesEmptyTest extends ModelTestCase {
	/** @test */
	function noticeHTMLShouldReturnEmptyString() {
		$this->notice_html = new Class_NoticeHtml();
		$this->assertEquals('', $this->notice_html->getExemplaires(array()));
	}

}


class NoticeHtmlGetExemplairesWithOneExemplaireNoWebServiceTest extends ModelTestCase {
	public function setUp() {
		parent::setUp();
		Class_Profil::setCurrentProfil(new Class_Profil());
		$exemplaire = array('id_bib' => -1,
												'id_notice' => '24765',
												'annexe' => 'MOUL',
												'count(*)' => 2, //???
												'cote' => 'DSEM',
												'dispo' => "Disponible",
												'code_barres' => "12345"); 
		$notice_html = new Class_NoticeHtml();
		$this->html = $notice_html->getExemplaires(array($exemplaire));
	}


	/** @test */
	function noticeHTMLShouldDisplayTableHeader() {
		$this->assertContains('table', $this->html);
	}


	/** @test */
	public function reservationAjaxShouldNotBeVisible() {
		$this->assertNotContains('reservationAjax', $this->html);
	}
}



abstract class NoticeHtmlGetExemplairesWithOneExemplaireAndWebServiceTestCase extends ModelTestCase {
	protected $exemplaire;

	public function setUp() {
		parent::setUp();
		$_SESSION['id_profil'] = 4;
		Class_Profil::setCurrentProfil(
																	 Class_Profil::getLoader()
																	 ->newInstanceWithId(4)
																	 ->setCfgNotice(array('exemplaires' => array('grouper' => 1,
																																							 'bib' => 1,
																																							 'annexe' => 1,
																																							 'section' => 1,
																																							 'emplacement' => 1,
																																							 'dispo' => 1,
																																							 'date_retour' => 1,
																																							 'localisation' => 1,
																																							 'plan' => 1,
																																							 'resa' => 1))));


		Class_IntBib::getLoader()
			->newInstanceWithId(1)
			->setCommSigb(Class_IntBib::COM_MICROBIB)
			->setCommParams('');


		Class_WebService_SIGB_Microbib::setService(Storm_Test_ObjectWrapper::on(new StdClass())
																							 ->whenCalled('getExemplaire')
																							 ->answers(Class_WebService_SIGB_Exemplaire::newInstance()
																												 ->setId(5)
																												 ->setDisponibiliteEnPret()
																												 ->setDateRetour('20/03/2012')
																												 ->beReservable())
																							 ->whenCalled('isConnected')
																							 ->answers(true)
																							 ->getWrapper());

		$this->exemplaire = array('id' => 12,
												'id_bib' => 1,
												'id_notice' => '24765',
												'id_origine' => '666',
												'annexe' => 'MOUL',
												'count(*)' => 2, //???
												'cote' => 'DSEM',
												'dispo' => "Disponible",
												'code_barres' => "12345",
												'section' => 3,
												'emplacement' => 2);
	}
}




class NoticeHtmlGetExemplairesWithOneExemplaireAndWebServiceTest 
  extends NoticeHtmlGetExemplairesWithOneExemplaireAndWebServiceTestCase {

	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()->newInstanceWithId('site_retrait_resa')
			->setValeur('0');
		$notice_html = new Class_NoticeHtml();
		$this->html = $notice_html->getExemplaires(array($this->exemplaire));
	}


	/** @test */
	public function reservationAjaxShouldBeVisible() {
		$this->assertContains("reservationAjax(this,'1','12', 'MOUL')", $this->html);
	}
}



class NoticeHtmlGetExemplairesWithOneExemplaireAndWebServiceAndPickupActiveTest
  extends NoticeHtmlGetExemplairesWithOneExemplaireAndWebServiceTestCase {
	

	public function setUp() {
		parent::setUp();
		Class_CosmoVar::getLoader()->newInstanceWithId('site_retrait_resa')
			->setValeur('1');

		$notice_html = new Class_NoticeHtml();
		$this->html = $notice_html->getExemplaires(array($this->exemplaire));
	}


	/** @test */
	public function shouldRenderReservationPickup() {
		$this->assertContains("reservationPickupAjax(this,'1','12', 'MOUL')", $this->html);
	}
}
?>