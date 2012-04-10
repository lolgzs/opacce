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
class MultiUploalFactoryTest extends PHPUnit_Framework_TestCase {
	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var Class_MultiUpload_HandlerFactory */
	protected $_factory;


	protected function setUp() {
		parent::setUp();
		$this->_request = new Zend_Controller_Request_Http();
		$this->_factory = new Class_MultiUpload_HandlerFactory();
	}


	protected function tearDown() {
		parent::tearDown();

		if (array_key_exists('qqfile', $_FILES)) {
			unset($_FILES['qqfile']);
		}
	}


	/** @test */
	public function withoutAnyExpectedParamShouldReturnNull() {
		$this->assertNull($this->_factory->getHandlerFor($this->_request));
	}


	/** @test */
	public function withParamQqfileShouldMakeAnXhrHandler() {
		$this->_request->setParam('qqfile', 'add.png');

		$this->assertInstanceOf(
			'Class_MultiUpload_HandlerXhr',
			$this->_factory->getHandlerFor($this->_request)
		);
	}


	/** @test */
	public function withDollarFilesSetShouldMakeAFormHandler() {
		$_FILES['qqfile']['name'] = '';
		$_FILES['qqfile']['size'] = 0;

		$this->assertInstanceOf(
			'Class_MultiUpload_HandlerForm',
			$this->_factory->getHandlerFor($this->_request)
		);
	}
}