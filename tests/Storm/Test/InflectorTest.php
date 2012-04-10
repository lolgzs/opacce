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

class InflectorFrenchTest extends PHPUnit_Framework_TestCase {
	/** @test */
	function pluralizeChevalShouldReturnChevaux() {
		$this->assertEquals('chevaux', Storm_Inflector::pluralize('cheval'));
	}


	/** @test */
	function pluralizeLieuShouldReturnLieux() {
		$this->assertEquals('Lieux', Storm_Inflector::pluralize('Lieu'));
	}


	/** @test */
	function pluralizeCategorieShouldReturnCategories() {
		$this->assertEquals('categories', Storm_Inflector::pluralize('categorie'));
	}


	/** @test */
	function pluralizeAvisShouldReturnAvis() {
		$this->assertEquals('avis', Storm_Inflector::pluralize('avis'));
	}


	/** @test */
	function singuralizeChevauxShouldReturnCheval() {
		$this->assertEquals('cheval', Storm_Inflector::singularize('chevaux'));
	}


	/** @test */
	function singuralizeZorkShouldReturnZork() {
		$this->assertEquals('zork', Storm_Inflector::singularize('zork'));
	}


	/** @test */
	function singularizeCategoriesShouldReturnCategorie() {
		$this->assertEquals('categorie', Storm_Inflector::singularize('categories'));
	}


	/** @test */
	function singularizeAvisShouldReturnAvis() {
		$this->assertEquals('avis', Storm_Inflector::singularize('avis'));
	}


	/** @test */
	function camelizeNEWSShouldReturnNews() {
		$this->assertEquals('News', Storm_Inflector::camelize('NEWS'));
	}


	/** @test */
	function camelizeID_NEWSShouldReturnIdNews() {
		$this->assertEquals('IdNews', Storm_Inflector::camelize('ID_NEWS'));
	}


	/** @test */
	function underscorizeIdNewsShouldReturnID_NEWS() {
		$this->assertEquals('id_news', Storm_Inflector::underscorize('IdNews'));
	}

	/** @test */
	function underscorizeNewsShouldReturnNEWS() {
		$this->assertEquals('news', Storm_Inflector::underscorize('News'));
	}


}

?>