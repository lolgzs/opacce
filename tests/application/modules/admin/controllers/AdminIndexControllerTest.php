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

abstract class AdminIndexControllerTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('getIdentity')
			->answers(Class_Users::getLoader()->newInstanceWithId(777)
								->setLogin('sysadmin')
								->setRoleLevel(ZendAfi_Acl_AdminControllerRoles::SUPER_ADMIN)
								->setPseudo('admin'));
	}
}




class AdminIndexControllerTestBabelio extends AdminIndexControllerTestCase {
	protected $_old_cfg;

	public function setUp() {
		parent::setUp();
		$this->_old_cfg = Zend_Registry::get('cfg');
	}


	public function tearDown() {
		Zend_Registry::set('cfg', $this->_old_cfg);
		parent::tearDown();
	}


	protected function _setExpiration($expire_at) {
		Zend_Registry::set('cfg', new Zend_Config(array('babelio' => array('expire_at' => $expire_at))));
	}


	/** @test */
	public function withNullExpirationShouldBeDisabled() {
		$this->_setExpiration(null);

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Désactivé');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}


	/** @test */
	public function withExpirationNeverShouldBeActivated() {
		$this->_setExpiration('never');

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Activé');
		$this->assertNotQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}


  /** @test */
  public function withPastExpirationDateShouldBeDisabled() {
		$yesterday = new Zend_Date();
		$yesterday->addDay(-1);
		$this->_setExpiration($yesterday->toString('yyyy/MM/dd'));

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Désactivé, expiration le');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}


  /** @test */
  public function withFutureExpirationDateShouldBeActivated() {
		$tomorrow = new Zend_Date();
		$tomorrow->addDay(1);
		$this->_setExpiration($tomorrow->toString('yyyy/MM/dd'));

		$this->dispatch('/admin/index');
		$this->assertQueryContentContains('div.ligne_info b', 'Activé, expiration le');
		$this->assertQueryContentContains('div.ligne_info', 'souscrire à un abonnement');
	}
}




class AdminIndexControllerIndexActionTest extends AdminIndexControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('');

		$this->dispatch('/admin/index', true);
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




class AdminIndexControllerAdminVarEditModoBlogActionTest extends Admin_AbstractControllerTestCase {
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




class AdminIndexControllerAdminVarEditResaConditionActionTest extends Admin_AbstractControllerTestCase {
	protected $_resa_condition;

	public function setUp() {
		parent::setUp();
		
		$this->_resa_condition = Class_AdminVar::getLoader()
			->newInstanceWithId('RESA_CONDITION')
			->setValeur('Mes+conditions+de+reservation');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AdminVar')
			->whenCalled('save')
			->answers(true);
	}


	/** @test */
	public function editResaConditionShouldDecodeItsValeur() {
		$this->dispatch('/admin/index/adminvaredit/cle/RESA_CONDITION');
		$this->assertXPathContentContains('//textarea', 'Mes conditions de reservation');
	}


	/** @test */
	public function postResaConditionShouldEncodeItsValeur() {
		$this->postDispatch('/admin/index/adminvaredit/cle/RESA_CONDITION', 
												array('valeur' => 'Il faut demander'));
		
		$this->assertEquals('Il+faut+demander', $this->_resa_condition->getValeur());
	}
}
?>