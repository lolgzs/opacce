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
		parent::setUp();
		$this->dispatch(sprintf('recherche/reseau/id_notice/%d/type_doc/1', 
														$this->notice->getId()));
	}
	

	/** @test */
	public function getResauShouldReturnTwitterLink() {
		$this->assertXPath('//img[contains(@src, "twitter.gif")]', $this->_response->getBody());
	}
}




class RechercheControllerViewNoticeTest extends RechercheControllerNoticeTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()));
	}


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

?>