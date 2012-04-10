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
class RssItemDescriptionTest extends PHPUnit_Framework_TestCase {
	/** @var Class_RssItem */
	protected $_item;

	/** @var Storm_Test_ObjectWrapper */
	protected $_wrapper;


	protected function setUp() {
		parent::setUp();
		$this->_wrapper = Storm_Test_ObjectWrapper::on(new stdClass());
		$this->_item = $item = new Class_RssItem($this->_wrapper);
	}


	/**
	 * @category regression
	 * @test
	 */
	public function withTwoDescriptionsShouldReturnFirstDescription() {
		$this->_wrapper
				->whenCalled('description')
				->answers(array(
						new DOMElement('description', 'Premiere description trouvee'),
						new DOMElement('description', 'Seconde description trouvee')));

		$this->assertEquals('Premiere description trouvee', $this->_item->getDescription());
	}


	/** @test */
	public function withOneDescriptionShouldReturnIt() {
		$this->_wrapper
				->whenCalled('description')
				->answers('Premiere description trouvee');
		$this->assertEquals('Premiere description trouvee', $this->_item->getDescription());
	}


	/** @test */
	public function descriptionShouldBeStripTagged() {
		$this->_wrapper
				->whenCalled('description')
				->answers('Premiere <span>description<span> trouvee<img src="#" />');
		$this->assertEquals('Premiere description trouvee', $this->_item->getDescription());
	}
}