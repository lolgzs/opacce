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
abstract class MultiUploadTestCase extends PHPUnit_Framework_TestCase {
	/** @var Zend_Controller_Request_Http */
	protected $_request;

	/** @var string */
	protected $_tmpBasePath;

	protected function setUp() {
		parent::setUp();
		$this->_request = new Zend_Controller_Request_Http();
		$this->_tmpBasePath = realpath(sys_get_temp_dir()) . '/testAlbum';
	}

}




class MultiUploadInitializationTest extends MultiUploadTestCase {
	/** @test */
	public function withIncompatibleSettingsShouldHaveError() {
		$upload = Class_MultiUpload::newInstanceWith($this->_request)
								->setSettingsReader(
									Storm_Test_ObjectWrapper::on(new MultiUploadSettingReader())
										->whenCalled('get')->with('post_max_size')
										->answers('1K')->getWrapper()
										->whenCalled('get')->with('upload_max_filesize')
										->answers('1K')->getWrapper()
								);

		$this->assertFalse($upload->handleUpload('', ''));
		$this->assertRegExp('/monter le post/', $upload->getError());
	}
}



class MultiUploadXHRTest extends MultiUploadTestCase {
	/** @var Class_MultiUpload */
	protected $_upload;


	protected function setUp() {
		parent::setUp();

		$this->_upload = Class_MultiUpload::newInstanceWith($this->_request)
											->setSettingsReader(
																					MultiUploadSettingReader::newInstance()
																					->set('post_max_size', '500M')
																					->set('upload_max_filesize', '500M'))
											->setFolderManager(
																				 Class_Folder_Manager::newInstanceLimitedTo(realpath(sys_get_temp_dir()))
											);
	}


	protected function tearDown() {
		if (file_exists($this->_tmpBasePath)) {
			if (file_exists($this->_tmpBasePath . '/thumbs'))
				rmdir($this->_tmpBasePath . '/thumbs');

			if (file_exists($this->_tmpBasePath . '/big'))
				rmdir($this->_tmpBasePath . '/big');

			rmdir($this->_tmpBasePath);
		}

		parent::tearDown();
	}


	/** @test */
	public function withoutHandlerShouldHaveError() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(null)->getWrapper();

		$this->_upload->setHandlerFactory($factoryWrapper);

		$this->assertFalse($this->_upload->handleUpload('', ''));
		$this->assertEquals('No handler set', $this->_upload->getError());
	}


	/** @test */
	public function withSizeZeroShouldHaveError() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(new Class_MultiUpload_Handler(null))
												->getWrapper();

		$this->_upload->setHandlerFactory($factoryWrapper);

		$this->assertFalse($this->_upload->handleUpload('', ''));
		$this->assertEquals('Aucun fichier transmis', $this->_upload->getError());
	}


	/** @test */
	public function withSizeTooBigShouldHaveError() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(
													Storm_Test_ObjectWrapper::on(new Class_MultiUpload_Handler(null))
														->whenCalled('getSize')->answers(100 * 1024 * 1024)
														->getWrapper()
												)
												->getWrapper();

		$this->_upload->setHandlerFactory($factoryWrapper);

		$this->assertFalse($this->_upload->handleUpload('', ''));
		$this->assertEquals('Fichier trop volumineux', $this->_upload->getError());
	}


	/** @test */
	public function whenFolderManagerHasErrorShouldHaveError() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(
													Storm_Test_ObjectWrapper::on(new Class_MultiUpload_Handler(null))
														->whenCalled('getSize')->answers(100 * 1024)->getWrapper()
														->whenCalled('getName')->answers('add.png')->getWrapper()
														->whenCalled('save')->answers(true)->getWrapper()
												)
												->getWrapper();

		$folderWrapper = Storm_Test_ObjectWrapper::on(new Class_Folder_Manager())
											->whenCalled('ensure')->answers(false)->getWrapper();

		$this->assertFalse(
			$this->_upload->setHandlerFactory($factoryWrapper)
										->setFolderManager($folderWrapper)
										->handleUpload($this->_tmpBasePath, '5')
		);

		$this->assertEquals('Le dossier "/tmp/testAlbum" n\'est pas accessible en écriture', 
												$this->_upload->getError());
	}


	/** @test */
	public function withNonExistingDestinationPathShouldCreateIt() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(
													Storm_Test_ObjectWrapper::on(new Class_MultiUpload_Handler(null))
														->whenCalled('getSize')->answers(100 * 1024)->getWrapper()
														->whenCalled('getName')->answers('add.png')->getWrapper()
														->whenCalled('save')->answers(true)->getWrapper()
												)
												->getWrapper();

		$this->_upload->setHandlerFactory($factoryWrapper)
									->handleUpload($this->_tmpBasePath . '/big', '5');

		$this->assertNull($this->_upload->getError());
		$this->assertFileExists($this->_tmpBasePath);
		$this->assertFileExists($this->_tmpBasePath . '/big');
	}


	/** @test */
	public function withPngFileAndRessourceIdFiveSavedFileNameShouldBeFiveDotPng() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(
													Storm_Test_ObjectWrapper::on(new Class_MultiUpload_Handler(null))
														->whenCalled('getSize')->answers(100 * 1024)->getWrapper()
														->whenCalled('getName')->answers('add.png')->getWrapper()
														->whenCalled('save')->answers(true)->getWrapper()
												)
												->getWrapper();

		$this->_upload->setHandlerFactory($factoryWrapper)
									->handleUpload($this->_tmpBasePath, '5');

		$this->assertEquals('5_add.png', $this->_upload->getSavedFileName());
	}


	/** @test */
	public function whenHandlerSaveHasErrorShouldHaveError() {
		$factoryWrapper = Storm_Test_ObjectWrapper::on(new Class_MultiUpload_HandlerFactory())
												->whenCalled('getHandlerFor')
												->answers(
													Storm_Test_ObjectWrapper::on(new Class_MultiUpload_Handler(null))
														->whenCalled('getSize')->answers(100 * 1024)->getWrapper()
														->whenCalled('getName')->answers('add.png')->getWrapper()
														->whenCalled('save')->answers(false)->getWrapper()
														->whenCalled('getError')->answers('A save error')->getWrapper()
												)
												->getWrapper();

		$this->assertFalse(
			$this->_upload->setHandlerFactory($factoryWrapper)
										->handleUpload($this->_tmpBasePath, '5')
		);

		$this->assertEquals('A save error', $this->_upload->getError());
	}
}