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

class MultimediaTest extends PHPUnit_Framework_TestCase {
	/** @test */
	public function validHashShouldBeValid() {
		Class_Multimedia::setInstance(new Class_MultimediaTesting);

		$this->assertTrue(Class_Multimedia::isValidHash(
				'3dd16c4df840a92cc0a361456cc23f59',
				'{{test-test}}'));
	}


	/** @test */
	public function invalidHashShouldNotBeValid() {
		Class_Multimedia::setInstance(new Class_MultimediaTesting);
		$this->assertFalse(Class_Multimedia::isValidHash(
				'3dd16uiessseruiest8973iue98759',
				'{{test-test}}'));
	}
}


class Class_MultimediaTesting extends Class_Multimedia {
	public function getKey() {return 'passwordOfThisTest';}
	public function getDate() {return '2012-07-05';}
}