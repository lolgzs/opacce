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
require_once realpath(dirname(__FILE__)) . '/DeleteButtonTesting.php';

class ZendAfi_Form_Decorator_DeleteButtonTest extends PHPUnit_Framework_TestCase {
	/** @var ZendAfi_Form_Decorator_DeleteButtonTesting */
	protected $_decorator;


	protected function setUp() {
		$this->_decorator = new ZendAfi_Form_Decorator_DeleteButtonTesting();
	}


	/** @test */
	public function withoutActionUrlShouldRenderNothing() {
		$this->_decorator->setElement(
			Storm_Test_ObjectWrapper::on(new Zend_Form_Element('name'))
				->whenCalled('getActionUrl')->answers('')
			  ->whenCalled('getValue')->answers('xxx.pdf')->getWrapper()
		);

		$this->assertEquals('', $this->_decorator->render(''));
	}


	/** @test */
	public function withoutValueShouldRenderNothing() {
		$this->_decorator->setElement(
			Storm_Test_ObjectWrapper::on(new Zend_Form_Element('name'))
				->whenCalled('getActionUrl')->answers('controlle/action')
				->whenCalled('getActionUrl')->answers('')->getWrapper()
		);

		$this->assertEquals('', $this->_decorator->render(''));
	}


	/** @test */
	public function withActionUrlShouldRenderDeleteLink() {
		$this->_decorator->setElement(
			Storm_Test_ObjectWrapper::on(new Zend_Form_Element('name'))
				->whenCalled('getActionUrl')->answers('/controller/delete/id/999')
				->whenCalled('getValue')->answers('xxx.pdf')
			  ->getWrapper()
		);

		$this->assertRegExp('#<a href="/controller/delete/id/999"#', $this->_decorator->render(''));
	}
}