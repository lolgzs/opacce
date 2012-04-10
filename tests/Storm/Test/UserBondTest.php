<?php
/*
STORM is under the MIT License (MIT)

Copyright (c) 2010-2011 Agence Française Informatique http://www.afi-sa.fr

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class Storm_Test_UserBondTest extends Storm_Test_ModelTestCase {
	public function setUp() {
		$this->bond = new Storm_Test_Mock_User();
		$this->bond
			->setName('Bond')
			->setMatriculation('007')
			->setFirstName('James')
			->setFavoriteCocktail('Martini Dry');
	}

	public function testGetNameReturnsBond() {
		$this->assertEquals('Bond', $this->bond->getName());
	}

	public function testGetMatriculationReturns007() {
		$this->assertEquals('007', $this->bond->getMatriculation());
	}

	public function testToArrayReturnsOneWordAttributes() {
		$attributes = $this->bond->toArray();
		$this->assertEquals('007', $attributes['matriculation']);
		$this->assertEquals('Bond', $attributes['name']);
	}

	public function testGetFirstNameReturnsJames() {
		$this->assertEquals('James', $this->bond->getFirstName());
	}

	public function testGetFavoriteCocktailReturnsMartiniDry() {
		$this->assertEquals('Martini Dry', $this->bond->getFavoriteCocktail());
	}

	public function testToArrayReturnsUnderscoredAttributes() {
		$attributes = $this->bond->toArray();
		$this->assertEquals('James', $attributes['first_name']);
		$this->assertEquals('Martini Dry', $attributes['favorite_cocktail']);
	}
}

?>