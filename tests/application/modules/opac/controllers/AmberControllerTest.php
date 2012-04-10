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
require_once 'AbstractControllerTestCase.php';
require_once 'application/modules/opac/controllers/AmberController.php';


class AmberControllerTest extends AbstractControllerTestCase {
	protected $_old_cfg, $cfg;

	public function setUp() {
		parent::setUp();

		$this->assertInstanceOf('Class_FileWriter', AmberController::getFileWriter());

		$this->mock_filewriter = $this->getMock('Class_FileWriter', array('putContents'));
		AmberController::setFileWriter($this->mock_filewriter);

		$this->_old_cfg = Zend_Registry::get('cfg');
		$this->cfg = new Zend_Config($this->_old_cfg->toArray(), true);
		Zend_Registry::set('cfg', $this->cfg);
		$this->cfg->amber = new Zend_Config(array('deploy' => false));

		Class_ScriptLoader::resetInstance();
	}


	public function tearDown() {
		Zend_Registry::set('cfg', $this->_old_cfg);
		
		parent::tearDown();
	}


	public function putDispatch($url, $body) {
		$this->getRequest()
			->setMethod('PUT')
			->setRawBody($body);

		return $this->dispatch($url);
	}



	/** @test */
	function commitKernelJsShouldCallPutContents() {
		$this
			->mock_filewriter
			->expects($this->once())
			->method('putContents')
			->with('./amber/src/js/Kernel.js', 'somejs');
		
		$this->putDispatch('amber/commitJs/Kernel.js', 'somejs');
	}


	/** @test */
	function commitAFICoreJsShouldCallPutContentsOnDirectoryAFI() {
		$this
			->mock_filewriter
			->expects($this->once())
			->method('putContents')
			->with('./amber/afi/js/AFI-Core.js', 'somejs');
		
		$this->putDispatch('amber/commitJs/AFI-Core.js', 'somejs');
	}



	/** @test */
	function commitKernelStShouldCallPutContents() {
		$this
			->mock_filewriter
			->expects($this->once())
			->method('putContents')
			->with('./amber/src/st/Kernel.st', 'some smalltalk');
		
		$this->putDispatch('amber/commitSt/Kernel.st', 'some smalltalk');
	}


	/** @test */
	function commitKernelJsShouldNotCallPutContentsInDeploymentMode() {
		$this->cfg->amber = new Zend_Config(array('deploy' => true));

		$this
			->mock_filewriter
			->expects($this->never())
			->method('putContents');
		
		$this->putDispatch('amber/commitJs/Kernel.js', 'somejs');
	}
}

?>