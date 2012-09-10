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

class FRBR_LinkWrongAttributesTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		parent::setUp();

		$this->link = Class_FRBR_Link::newInstanceWithId(34)
			->setSource('zork')
			->setTarget('grouik')
			->setSourceType(Class_FRBR_Link::TYPE_NOTICE)
			->setTargetType('http://localhost/notice/view/clef/TINTIN')
			->setLinkType(Class_FRBR_LinkType::newInstanceWithId(4)
										->setLibelle('synonyme')
										->setFromSource('est un synonyme de')
										->setFromTarget('est un synonyme de'));

	}


	/** @test */
	public function getSourceNoticeShouldReturnNullWithInvalidScheme() {
		$this->assertEquals(null, $this->link->getSourceNotice());
	}


	/** @test */
	public function getTargetNoticeShouldReturnNullWithInvalidType() {
		$this->assertEquals(null, $this->link->getTargetNotice());		
	}
}

?>