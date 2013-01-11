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

class UploadFichierTest extends PHPUnit_Framework_TestCase {
	/** @var Class_Upload */
	protected $_upload;

	/** @var Class_Folder_Manager */
	protected $_folderManager;


	protected function setUp() {
		parent::setUp();
		$this->_folderManager = $this->getMock('Class_Folder_Manager');
		$this->_upload = Class_Upload::newInstanceFor('fichier')
			->setFolderManager($this->_folderManager);
	}


	protected function tearDown() {
		$_FILES = array();
		parent::tearDown();
	}


	/**
	 * @param string $error
	 */
	protected function _assertError($error = '') {
		$this->assertFalse($this->_upload->receive());

		$actual = $this->_upload->getError();

		('' == $error) ? $this->assertNotEmpty($actual)
										: $this->assertEquals($error, $actual);
	}


	/** @test */
	public function withoutInputNameShouldHaveError() {
		$upload = Class_Upload::newInstanceFor('');
		$this->assertFalse($upload->receive());
		$this->assertNotEmpty($upload->getError());
	}


	/** @test */
	public function whenNoFileTransmittedShouldHaveError() {
		$this->_assertError();
	}


	/** @test */
	public function whenFileSizeIsZeroAndShouldHaveError() {
		$_FILES['fichier'] = array('size' => 0);
		$this->_assertError();
	}


	/** @test */
	public function whenCannotCreateFolderShouldHaveError() {
		$_FILES['fichier'] = array(
													'size' => 1,
													'tmp_name' => '',
													'name' => 'test.xml'
												);

		$this->_folderManager
			->expects($this->once())
			->method('ensure')
			->will($this->returnValue(false));

		$this->_assertError();
	}


	/** @test */
	public function whenFileExtensionIsNotAllowedShouldHaveError() {
		$this->_folderManager
			->expects($this->once())
			->method('ensure')
			->will($this->returnValue(true));

		$this->_upload->setAllowedExtensions(array('gif'));
		$_FILES['fichier'] = array(
													'size' => 1,
													'tmp_name' => '',
													'name' => 'test.xml'
												);

		$this->_assertError('Le fichier n\'est pas de type gif');
	}


	/** @test */
	public function whenCannotMoveUploadedShouldHaveError() {
		$this->_folderManager
			->expects($this->once())
			->method('ensure')
			->will($this->returnValue(true));

		$this->_upload->setUploadMover(
			Storm_Test_ObjectWrapper::on(new Class_UploadMover_HttpPost())
				->whenCalled('moveTo')->answers(false)->getWrapper()
				->whenCalled('getError')->answers('Move error')->getWrapper()
		);

		$_FILES['fichier'] = array(
													'size' => 1,
													'tmp_name' => '',
													'name' => 'test.xml'
												);

		$this->_assertError('Move error');
	}
}