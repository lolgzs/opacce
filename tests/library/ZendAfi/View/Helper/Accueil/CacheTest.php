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
require_once 'library/ZendAfi/View/Helper/ViewHelperTestCase.php';

class CacheWithCritiquesTest extends ViewHelperTestCase {
	private $_cache_actif;

	public function setUp() {
		parent::setUp();

		$params = array('type_module' => 'CRITIQUES',
										'division' => 2,
										'preferences' => array('boite' => 'boite_vide',
																					 'titre' => 'Critiques',
																					 'rss_avis' => false,
																					 'display_order' => 'Random',
																					 'only_img' => true));

		$this->avis_loader = $this->getMock('MockLoader', array('getAvisFromPreferences'));
		Storm_Model_Abstract::setLoaderFor('Class_AvisNotice', $this->avis_loader);

		$this->critiques_helper = new ZendAfi_View_Helper_Accueil_Critiques(2, $params);

		$this->_cache_actif = Class_AdminVar::getLoader()->newInstanceWithId('CACHE_ACTIF');

	}

	/** @test */
	function withDisableCacheTwiceCallsShouldCallLoaderTwice() {
		$this->_cache_actif->setValeur('0');
		$this->avis_loader
			->expects($this->exactly(2))
			->method('getAvisFromPreferences')
			->will($this->returnValue(array()));

		$this->critiques_helper->getBoite();
		$this->critiques_helper->getBoite();
	}


	/** @test */
	function withEnableCacheTwiceCallsShouldCallLoaderOnce() {
		$this->_cache_actif->setValeur('1');
		$this->avis_loader
			->expects($this->exactly(1))
			->method('getAvisFromPreferences')
			->will($this->returnValue(array()));

		$this->critiques_helper->getBoite();
		$this->critiques_helper->getBoite();
	}

}

?>