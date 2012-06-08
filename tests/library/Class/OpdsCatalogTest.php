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
require_once 'ModelTestCase.php';

class OpdsCatalogTest extends ModelTestCase {
	protected $_catalog;

	public function setUp() {
		parent::setUp();
		$this->_catalog = Class_OpdsCatalog::getLoader()->newInstance()
			->setLibelle('gutenberg')
			->setUrl('http://m.gutenberg.org/ebooks/?format=opds');
	}


	/** @test */
	public function newForAbsoluteUrlShouldAppendSchemeAndDomain() {
		$new = $this->_catalog->newForEntry('/ebooks/search.opds/?sort_order=downloads');
		$this->assertEquals('http://m.gutenberg.org/ebooks/search.opds/?sort_order=downloads',
												$new->getUrl());
	}

}