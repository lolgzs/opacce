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

class RechercheControllerViewNoticeTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->notice = Class_Notice::getLoader()->findFirstBy(array());
		$this->assertNotEmpty($this->notice, "Pas de notice dans la base. TODO => faire des mocks");
	}

	protected function dispatchViewNotice() {
		$this->dispatch(sprintf('recherche/viewnotice/id/%d', $this->notice->getId()));
	}

	/** @test */
	function titleShouldBeDisplayed() {
		$this->dispatchViewNotice();
		$this->assertXPathContentContains('//h1',
																			array_first(explode('<br />', $this->notice->getTitrePrincipal())),
																			$this->_response->getBody());
	}


	/** @test */
	function tagReseauSociauxShouldBePresent() {
		$this->dispatchViewNotice();
		$this->assertXPath('//div[@id="reseaux-sociaux"]');
	}


	/** @test */
	function getResauShouldReturnTwitterLink() {
		$this->dispatch(sprintf('recherche/reseau/id_notice/%d/type_doc/1', 
														$this->notice->getId()));
		$this->assertXPath('//img[contains(@src, "twitter.gif")]', $this->_response->getBody());
	}


	/** @test */
	function headShouldContainsRechercheJS() {
		$this->dispatchViewNotice();
		$this->assertXPath('//head//script[contains(@src,"public/opac/js/recherche.js")]');
	}
}





?>