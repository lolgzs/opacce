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

class EmprunteurCacheTest extends Storm_Test_ModelTestCase {
	protected $_old_zend_cache;

	public function setUp() {
		parent::setUp();
		
		$this->emprunteur_johnny = Class_WebService_SIGB_Emprunteur::newInstance(1, 'johnny');
		$this->user_johnny = Class_Users::getLoader()->newInstanceWithId(23);

		$this->emprunteur_steven = Class_WebService_SIGB_Emprunteur::newInstance(2, 'steven');
		$this->user_steven = Class_Users::getLoader()->newInstanceWithId(666);

		$this->sigb = Storm_Test_ObjectWrapper::mock();
		$this->sigb
			->whenCalled('getEmprunteur')
			->willDo(function() {throw new Class_WebService_Exception('erreur communication');})

			->whenCalled('getEmprunteur')
			->with($this->user_steven)
			->answers($this->emprunteur_steven);

		$this->_old_zend_cache = Zend_Registry::get('cache');
		Zend_Registry::set('cache', $this->zend_cache = Storm_Test_ObjectWrapper::mock());
		$this->zend_cache
			->whenCalled('test')->answers(false)
			->whenCalled('load')->answers(false)
			->whenCalled('remove')->answers(false)
			->whenCalled('save')->answers(true)

			->whenCalled('test')->with(md5('emprunteur_23'))->answers(true)
			->whenCalled('load')->with(md5('emprunteur_23'))->answers(serialize($this->emprunteur_johnny))
			->whenCalled('remove')->with(md5('emprunteur_23'))->answers(true);

		$this->emprunteur_cache = Class_WebService_SIGB_EmprunteurCache::newInstance();
	}


	public function tearDown() {
		Zend_Registry::set('cache', $this->_old_zend_cache);
		parent::tearDown();
	}


	/** @test */
	public function isInCacheShouldReturnFalseForSteven() {
		$this->assertFalse($this->emprunteur_cache->isCached($this->user_steven));
	}


	/** @test */
	public function loadShouldReturnFalseForSteven() {
		$this->assertFalse($this->emprunteur_cache->load($this->user_steven));
	}


	/** @test */
	public function loadFromCacheOrSigbStevenShouldReturnEmprunteurStevenAndCacheIt() {
		$this->assertEquals($this->emprunteur_steven,
												$this->emprunteur_cache->loadFromCacheOrSIGB($this->user_steven, $this->sigb));

		$this->assertEquals(array(serialize($this->emprunteur_steven), md5('emprunteur_666')),
												$this->zend_cache->getAttributesForLastCallOn('save'));
		
	}


	/** @test */
	public function isInCacheShouldReturnTrueShouldForJohnny() {
		$this->assertTrue($this->emprunteur_cache->isCached($this->user_johnny));
	}


	/** @test */
	public function loadJohnnyShouldReturnEmprunteur() {
		$this->emprunteur_johnny->setService(null);
		$this->assertEquals($this->emprunteur_johnny, $this->emprunteur_cache->load($this->user_johnny));
	}


	/** @test */
	public function removeJohnnyShouldCallRemoveOnCache() {
		$this->emprunteur_cache->remove($this->user_johnny);
		$this->assertTrue($this->zend_cache->methodHasBeenCalled('remove'));
	}


	/** @test */
	public function loadFromCacheOrSigbJohnnyShouldNotCallSIGB() {
		$this->emprunteur_johnny->setService(null);
		$this->assertEquals($this->emprunteur_johnny,
												$this->emprunteur_cache->loadFromCacheOrSIGB($this->user_johnny, $this->sigb));

		$this->assertFalse($this->zend_cache->methodHasBeenCalled('save'));
	}

}


?>