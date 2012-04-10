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

class ZendAfi_View_Helper_FicheAbonneLinksTestTest extends ViewHelperTestCase {
	public function setUp() {
		parent::setUp();
		$this->_helper = new ZendAfi_View_Helper_FicheAbonneLinks();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());
	}

	/** @test */
	public function withNoPretAndNoRetardShouldAnswerEmptyString() {
		$this->assertEquals('', $this->_helper->ficheAbonneLinks(0,0,0));
	}


	/** @test */
	public function withOnePretShouldAnswerOnePretEnCours() {
		$html = $this->_helper->ficheAbonneLinks(1,0,0);
		$this->assertContains('1 prêt en cours', $html);
		$this->assertNotContains('error', $html);
		$this->assertNotContains('retard', $html);
		$this->assertNotContains('reservation', $html);
	}


	/** @test */
	public function withTwoPretAndOneRetardShouldAnswerTwoPretsEnCoursOneEnRetard() {
		$html = $this->_helper->ficheAbonneLinks(2,1,0);
		$this->assertContains('class="pret_en_retard"', $html);
		$this->assertContains('2 prêts en cours, 1 en retard', $html);
		$this->assertNotContains('reservation', $html);
	}


	/** @test */
	public function withTwoRetardAndTwoReservationsShouldAnswerTwoPretsEnCoursTwoReservations() {
		$html = $this->_helper->ficheAbonneLinks(2,2,2);
		$this->assertContains('2 prêts en cours, 2 en retard', $html);
		$this->assertContains('2 réservations', $html);
	}
}

?>