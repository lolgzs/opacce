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
class FolderManagerCreateTest extends PHPUnit_Framework_TestCase {
	/** @var string */
	protected $_path;

	/** @var Class_Folder_Manager */
	protected $_manager;


	protected function setUp() {
		parent::setUp();
		$temp_dir = sys_get_temp_dir();
		$this->_path = $temp_dir . '/' . uniqid() . '/' . uniqid();
		$this->_manager = Class_Folder_Manager::newInstanceLimitedTo($temp_dir);
	}


	protected function tearDown() {
		$base = dirname($this->_path);
		if (file_exists($this->_path)) {
			rmdir($this->_path);
		}
		if (file_exists($base)) {
			rmdir($base);
		}

		parent::tearDown();
	}


	/** @test */
	public function whenFolderDontExistShouldCreateIt() {
		$this->_manager->ensure($this->_path);
		$this->assertFileExists($this->_path);
	}


	/** @test */
	public function whenFolderAlreadyExistShouldDoNothing() {
		$this->_manager->ensure($this->_path);
		$this->assertTrue($this->_manager->ensure($this->_path));
	}


	/** @test */
	public function whenBasePathNotAllowedShouldReturnFalse() {
		$this->_manager->setAllowedBasePath('/impossible/to/allow/');
		$this->assertFalse($this->_manager->ensure($this->_path));
	}
}