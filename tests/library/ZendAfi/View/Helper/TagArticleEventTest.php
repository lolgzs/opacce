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
require_once realpath(dirname(__FILE__)) . '/ViewHelperTestCase.php';

class TagArticleEventTest extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_TagArtivcleEvent */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();
		Zend_Registry::get('translate')->setLocale('fr');

		$this->_helper = new ZendAfi_View_Helper_TagArticleEvent();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());
		
		$this->article = Class_Article::getLoader()
			->newInstanceWithId(2)
			->setTitre('Mediévales Andilly')
			->setContenu('Prochainement');
	}


	/** @test */
	function withNoEventShouldAnswerEmptyString() {
		$this->assertEmpty($this->_helper->tagArticleEvent($this->article));
	}


	/** @test */
	function withEventDebutShouldAnswerLe05Sept() {
		$this->article->setEventsDebut('2011-09-05');
		$this->assertTagContains('Le 05 septembre 2011');
	}


	/** @test */
	function withEventDebutAndFinShouldAnswerDu05SeptAu10Oct() {
		$this->article
			->setEventsDebut('2011-09-05')
			->setEventsFin('2011-10-10');
		$this->assertTagContains('Du 05 septembre au 10 octobre 2011');
	}


	protected function assertTagContains($expected) {
		$this->assertEquals(sprintf('<span class="calendar_event_date">%s</span>', $expected), 
												$this->_helper->tagArticleEvent($this->article));
	}
}


