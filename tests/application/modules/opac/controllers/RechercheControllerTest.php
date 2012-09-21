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

abstract class RechercheControllerNoticeTestCase extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->notice = Class_Notice::getLoader()->findFirstBy(array());
	}
}



class RechercheControllerReseauTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		Class_WebService_ReseauxSociaux::setDefaultWebClient(Storm_Test_ObjectWrapper::mock()
																												 ->whenCalled('open_url')
																												 ->answers(false));
		parent::setUp();
		$this->dispatch(sprintf('recherche/reseau/id_notice/%d/type_doc/1', 
														$this->notice->getId()));
	}
	

	public function tearDown() {
		Class_WebService_ReseauxSociaux::setDefaultWebClient(null);
	}

	/** @test */
	public function getResauShouldReturnTwitterLink() {
		$this->assertXPath('//img[contains(@src, "twitter.gif")]', $this->_response->getBody());
	}
}




class RechercheControllerViewNoticeBabelthequeTest extends RechercheControllerNoticeTestCase {
	/** @test */
	public function withoutBabelthequeJSShouldNotBeLoaded() {
		Class_AdminVar::newInstanceWithId('BABELTHEQUE_JS')->setValeur('');
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertNotXpath('//script[contains(@src, "babeltheque.js")]');
	}


	/** @test */
	public function withBabelthequeJSShouldBeLoadedWithRightId() {
		Class_AdminVar::newInstanceWithId('BABELTHEQUE_JS')->setValeur('http://www.babeltheque.com/bw_666.js');
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertXpath('//script[contains(@src, "babeltheque.js?bwid=666")]');
	}
}




abstract class RechercheControllerViewNoticeTestCase extends RechercheControllerNoticeTestCase {
	/** @test */
	public function titleShouldBeDisplayed() {
		$this->assertXPathContentContains('//h1',
																			array_first(explode('<br />', $this->notice->getTitrePrincipal())),
																			$this->_response->getBody());
	}


	/** @test */
	public function tagReseauSociauxShouldBePresent() {
		$this->assertXPath('//div[@id="reseaux-sociaux"]');
	}


	/** @test */
	public function headShouldContainsRechercheJS() {
		$this->assertXPath('//head//script[contains(@src,"public/opac/js/recherche.js")]');
	}
}




abstract class RechercheControllerViewNoticeTest extends RechercheControllerViewNoticeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()));
	}
}




class RechercheControllerViewNoticeClefAlphaTest extends RechercheControllerViewNoticeTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->with(['clef_alpha' => 'TESTINGALPHAKEY---101'])
			->answers([$this->notice]);

		$this->dispatch('recherche/viewnotice/clef/TESTINGALPHAKEY---101', true);
	}
}




class RechercheControllerViewNoticeClefAlphaWithDoublonsTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('findAllBy')
			->with(['clef_alpha' => 'TWILIGHT--SLADED-3-M6VIDEO-2010-4'])
			->answers([Class_Notice::newInstanceWithId($this->notice->getId())
								 ->setTitrePrincipal('Twilight 1')
								 ->setClefAlpha('TWILIGHT--SLADED-3-M6VIDEO-2010-4'),
								 
								 Class_Notice::newInstanceWithId(1)
								 ->setTitrePrincipal('Twilight 2')
								 ->setClefAlpha('TWILIGHT--SLADED-3-M6VIDEO-2010-4')
								 ]);
	}

	
	/** @test */
	public function withOnlyClefResponseShouldRedirectToRechercheTWILIGHT_SLADED() {
		$this->dispatch('recherche/viewnotice/clef/'.urlencode('TWILIGHT--SLADED-3-M6VIDEO-2010-4'), true);
		$this->assertRedirectTo('/opac/recherche?q=TWILIGHT+SLADED');
	}


	/** @test */
	public function withClefAndIdResponseShouldNotRedirectTo() {
		$this->dispatch('recherche/viewnotice/clef/'.urlencode('TWILIGHT--SLADED-3-M6VIDEO-2010-4').'/id/'.$this->notice->getId(), true);
		$this->assertNotRedirect();
	}
}




class RechercheControllerUploadVignetteTest extends RechercheControllerNoticeTestCase {
	/** @test */
	public function linkToUploadVignetteShouldNotBePresentForInvite() {
		Class_Users::getIdentity()->beInvite();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertNotXPathContentContains('//a', 'Modifier la vignette');
	}

	/** @test */
	public function linkToUploadVignetteShouldBePresentForAdmin() {
		Class_Users::getIdentity()->beAdminPortail();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()), true);
		$this->assertXPathContentContains('//a', 'Modifier la vignette');
	}
}




class RechercheControllerReservationPickupAjaxActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_CodifAnnexe')
			->whenCalled('findAllBy')
			->with(array('no_pickup' => '0',
									 'order' => 'libelle'))
			->answers(array(Class_CodifAnnexe::getLoader()->newInstanceWithId(2)
											->setLibelle('Annecy')
											->setCode('ANN'),
											Class_CodifAnnexe::getLoader()->newInstanceWithId(3)
											->setLibelle('Cran')
											->setCode('CRN')));

		$this->dispatch('recherche/reservation-pickup-ajax?id_bib=2&id_origine=12&code_annexe=ANN');
	}


	/** @test */
	public function shouldRenderAnnecyCheckedRadio() {
		$this->assertXPath('//input[@name="code_annexe"][@value="ANN"][@checked="checked"]');
	}


	/** @test */
	public function shouldRenderCranRadio() {
		$this->assertXPath('//input[@name="code_annexe"][@value="CRN"]');
	}


	/** @test */
	public function layoutShouldBeEmpty() {
		$this->assertNotXPath('//div[@id="banniere"]');
	}
}




class RechercheControllerSimpleActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/recherche/simple', array('expressionRecherche' => 'pomme'));
	}

	
	/** @test */
	public function pommeShouldBePresent() {
		$this->assertXPathContentContains('//div', 'pomme');
	}
}




class RechercheControllerSimpleByISBNActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('/recherche/simple', ['expressionRecherche' => '2-203-00119-4'], true);
	}

	
	/** @test */
	public function pageResultatRechecheShouldBeDisplayed() {
		$this->assertXPathContentContains('//div', 'Recherche : 2-203-00119-4');
	}
}




class RechercheControllerAvanceeActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/recherche/avancee', true);
	}

	
	/** @test */
	public function pageRechercheAvanceeShouldBeDisplayed() {
		$this->assertXPathContentContains('//h1', 'recherche');
	}
}




class RechercheControllerPostReservationAction extends AbstractControllerTestCase {
	protected $_sent_mails;

	public function setUp() {
		parent::setUp();

		$_SESSION["captcha_code"] = '1234';

		$mock_transport = new MockMailTransport();
		Zend_Mail::setDefaultTransport($mock_transport);


		Class_Bib::getLoader()->newInstanceWithId(4)->setLibelle('Astrolabe');

		$this->postDispatch('/recherche/reservation',
												array('id_notice' => 4,
															'id_bib' => 4,
															'mail_bib' => 'zork@gloub.fr',
															'user_name' => 'nanuk',
															'demande' => 'je veux le livre',
															'user_mail' => 'nanuk@gloub.com',
															'code_saisi' => '1234',
															'cote' => 'XYZ'),
												true);
		$this->_sent_mails = $mock_transport->getSentMails();
	}


	/** @test */
	public function twoMailsShouldHaveBeenSent() {
		$this->assertEquals(2, count($this->_sent_mails));
	}


	/** @test */
	public function firstMailFromShouldBeNanuk() {
		$this->assertEquals('nanuk@gloub.com', 
												array_first($this->_sent_mails)->getFrom());
	}


	/** @test */
	public function firstMailToShouldBeZork() {
		$this->assertContains('zork@gloub.fr', 
													array_first($this->_sent_mails)->getRecipients());
	}


	/** @test */
	public function secondMailFromShouldBeNobody() {
		$this->assertEquals('nobody@noreply.fr', 
												array_last($this->_sent_mails)->getFrom());
	}


	/** @test */
	public function secondMailToShouldBeNanuk() {
		$this->assertContains('nanuk@gloub.com', 
													array_last($this->_sent_mails)->getRecipients());
	}

}

?>