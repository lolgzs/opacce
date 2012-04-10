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
require_once 'AdminAbstractControllerTestCase.php';

class AdminIndexControllerTestBabelio extends Admin_AbstractControllerTestCase {
	protected function _setExpiration($expire_at) {
		Zend_Registry::set('cfg', new Zend_Config(array('babelio' => array('expire_at' => $expire_at))));
	}


	public function testDefaultBabelioDisabled() {
		$this->_setExpiration(null);

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Désactivé');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}

	public function testActivatedNoExpiration() {
		$this->_setExpiration('never');

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Activé');
		$this->assertNotQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}

	public function testExpirated() {
		$yesterday = new Zend_Date();
		$yesterday->addDay(-1);
		$this->_setExpiration($yesterday->toString('yyyy/MM/dd'));

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Désactivé, expiration le');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}

	public function testNotExpirated() {
		$tomorrow = new Zend_Date();
		$tomorrow->addDay(1);
		$this->_setExpiration($tomorrow->toString('yyyy/MM/dd'));

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Activé, expiration le');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}
}




class AdminIndexControllerIndexActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('');

		$this->dispatch('/admin/index');
	}

	/** @test */
	function menuGaucheTraductionsShouldBeHidden() {
		$this->assertNotXPathContentContains('//a', 'Traductions');
	}


	/** @test */
	public function titreShouldBeAccueil() {
		$this->assertXPathContentContains('//div[@class="modules"]/h1', 'Accueil');
	}


	/** @test */
	public function helpLinkShouldBePresent() {
    $this->assertXPath("//a[@href='https://akm.ardans.fr/AFI2/invite/listerFiche.do?idFiche=4037']//img");
	}


	/** @test */
	public function PATH_JAVAShouldExists() {
		$this->assertTrue(file_exists(PATH_JAVA));
	}


	/** @test */
	public function PATH_FLASHShouldExists() {
		$this->assertTrue(file_exists(PATH_FLASH));
	}
}

class AdminIndexControllerAdminVarActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/index/adminvar');
	}

	/** @test */
	public function titleShouldBeGestionDesVariables() {
		$this->assertXpathContentContains('//h1', 'Gestion des variables');
	}

	/** @test */
	public function avisMaxSaisieShouldBePresent() {
		$this->assertXpathContentContains('//td', 'AVIS_MAX_SAISIE');
	}

	/** @test */
	public function avisMaxSaisieEditLinkShouldBePresent() {
		$this->assertXpath('//a[contains(@href, "adminvaredit/cle/AVIS_MAX_SAISIE")]');
	}
}

class AdminIndexControllerAdminVarEditActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->modo_blog = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AdminVar')
			->whenCalled('save')
			->answers('true')
			->getWrapper()
			->newInstanceWithId('MODO_BLOG')
			->setValeur('1');
	}


	/** @test */
	public function editMODO_BLOG() {
		$this->dispatch('/admin/index/adminvaredit/cle/MODO_BLOG');
		$this->assertQueryContentContains('td.droite', 'MODO_BLOG');
	}


	/** @test */
	public function postTwoToMODO_BLOG() {
		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('cle' => 'MODO_BLOG',
											'valeur' => "<b>2  \n</b>"));
		$this->dispatch('/admin/index/adminvaredit/cle/MODO_BLOG');
		$this->assertEquals(2, $this->modo_blog->getValeur());
	}
}

?>