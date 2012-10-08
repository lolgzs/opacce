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
require_once 'AdminAbstractControllerTestCase.php';

class ProfilControllerCssUploadTest extends Admin_AbstractControllerTestCase {
	protected $_file_writer;
	protected $_profil_musique;

	public function setUp() {
		parent::setUp();

		$this->_profil_musique = Class_Profil::getLoader()
			->newInstanceWithId(5)
			->setLibelle('Profil musique');

		$this->_file_writer = Storm_Test_ObjectWrapper::mock();
		Class_Profil::setFileWriter($this->_file_writer);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true);

	}


	/** @test */
	public function uploadShoulPutContentInCssFileWhenDefined() {
		$this->_profil_musique->setCfgSite([ 'header_css' => USERFILESURL.'css/my.css' ] );

		$this->getRequest()
			->setMethod('PUT')
			->setRawBody('body {font-size: 15px}');

		$this->_file_writer
			->whenCalled('putContents')
			->with(USERFILESPATH.'/css/my.css', 'body {font-size: 15px}')
			->answers(22)
			->beStrict();

		$this->dispatch('/admin/profil/upload-css/id_profil/5', true);
		
		$this->assertTrue($this->_file_writer->methodHasBeenCalled('putContents'));
	}


	/** @test */
	public function uploadShoulPutContentInANewCssFileWhenProfilHasNoCss() {
		$this->getRequest()
			->setMethod('PUT')
			->setRawBody('body {font-size: 15px}');

		$this->_file_writer
			->whenCalled('putContents')
			->with(USERFILESPATH.'/css/profil_5.css', 'body {font-size: 15px}')
			->answers(22)
			->beStrict();

		$this->dispatch('/admin/profil/upload-css/id_profil/5', true);
		
		$this->assertTrue($this->_file_writer->methodHasBeenCalled('putContents'));
		return Class_Profil::getLoader();
	}


	/**
	 * @depends uploadShoulPutContentInANewCssFileWhenProfilHasNoCss
	 * @test 
	 */
	public function profilShouldBeSaved($profil_loader) {
		$this->assertTrue($profil_loader->methodHasBeenCalled('save'));		
	}


	/**
	 * @depends uploadShoulPutContentInANewCssFileWhenProfilHasNoCss
	 * @test 
	 */
	public function profilCssShouldBeProfil5Css($profil_loader) {
		$this->assertEquals(BASE_URL.'/userfiles/css/profil_5.css', $profil_loader->find(5)->getHeaderCss());		
	}
}

?>