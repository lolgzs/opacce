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

class IsbnTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->_validator = new ZendAfi_Validate_Isbn();
	}


	public function validIsbns() {
		return [
						['99921-58-10-7'],
						['9971-5-0210-0'],
						['960-425-059-0'],
						['80-902734-1-6'],
						['85-359-0277-5'],
						['1-84356-028-3'],
						['0-684-84328-5'],
						['0-8044-2957-X'],
						['0-85131-041-9'],
						['0-943396-04-2'],
						['0-9752298-0-X'],

						['0 9752298 0 X'],
						['097522980X'],
						['0-9752298-0-X'],

						['978-1-4028-9462-6']
						];
	}


	public function invalidIsbns() {
		return [ ['234'], 
						 ['A-9?52298-0-X'],
						 ['978-1-?028-9462-6'] ];
	}


	/**
	 * @test
	 * @dataProvider validIsbns
	 */
	public function testisbnShouldBeValid($isbn)    {
		$this->assertTrue($this->_validator->isValid($isbn));
	}


	/**
	 * @test
	 * @dataProvider invalidIsbns
	 */
	public function isbnShouldNotBeValid($isbn)    {
		$this->assertFalse($this->_validator->isValid($isbn));
	}
}

?>