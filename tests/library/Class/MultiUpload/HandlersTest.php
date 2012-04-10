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
abstract class MultiUploadHandlerTestCase extends PHPUnit_Framework_TestCase {
	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var Class_MultiUpload_Handler */
	protected $_handler;

	/** @var string */
	protected $_sampleImagePath;


	protected function setUp() {
		parent::setUp();
		$this->_request = new Zend_Controller_Request_Http();
		$this->_sampleImagePath = realpath(dirname(__FILE__))
																								. '/../../../fixtures/add.gif';
	}
}




class MultiUploalHandlerXhrTest extends MultiUploadHandlerTestCase {
	protected function setUp() {
		parent::setUp();
		$this->_handler = new Class_MultiUpload_HandlerXhr($this->_request);
	}


	/** @test */
	public function nameShouldComeFromQqfileParam() {
		$this->_request->setParam('qqfile', 'add.png');
		$this->assertEquals('add.png', $this->_handler->getName());
	}


	/** @test */
	public function sizeShouldComeFromServerContentLength() {
		$_SERVER['CONTENT_LENGTH'] = '111001';
		$this->assertEquals(111001, $this->_handler->getSize());
	}


	/** @test */
	public function whenContentSizeMismatchShouldHaveError() {
		$_SERVER['CONTENT_LENGTH'] = '1';

		$this->assertFalse($this->_handler->save(sys_get_temp_dir() . '/test.gif'));
		$this->assertEquals(
			'Transmitted filesize and declared dont match',
			$this->_handler->getError()
		);
	}
}




class MultiUploalHandlerFormTest extends MultiUploadHandlerTestCase {
	protected function setUp() {
		parent::setUp();
		$this->_handler = new Class_MultiUpload_HandlerForm($this->_request);
	}


	protected function tearDown() {
		if (array_key_exists('qqfile', $_FILES)) {
			unset($_FILES['qqfile']);
		}

		parent::tearDown();
	}


	/** @test */
	public function nameShouldComeFromDollarFilesName() {
		$_FILES['qqfile']['name'] = 'add.png';
		$this->assertEquals('add.png', $this->_handler->getName());
	}


	/** @test */
	public function sizeShouldComeFromDollarFilesSize() {
		$_FILES['qqfile']['size'] = '111001';
		$this->assertEquals(111001, $this->_handler->getSize());
	}


	/** @test */
	public function withSizeZeroShouldHaveError() {
		$_FILES['qqfile']['size'] = '0';
		$this->assertFalse($this->_handler->save(''));
		$this->assertEquals('No file transmitted', $this->_handler->getError());
	}


	/** @test */
	public function whenCannotMoveUploadedFileShouldHaveError() {
		$_FILES['qqfile']['size'] = '111001';
		$_FILES['qqfile']['tmp_name'] = $this->_sampleImagePath;

		$this->assertFalse($this->_handler->save(sys_get_temp_dir() . '/test.gif'));
		$this->assertStringStartsWith('Cannot move uploaded file to',
																									$this->_handler->getError());
	}
}