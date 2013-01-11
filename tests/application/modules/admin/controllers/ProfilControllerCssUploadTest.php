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


abstract class ProfilControllerCssTestCase extends Admin_AbstractControllerTestCase {
	protected $_file_writer;
	protected $_profil_musique;
	protected $_page_jazz;

	public function setUp() {
		parent::setUp();

		$this->_profil_musique = Class_Profil::newInstanceWithId(5)
			->setLibelle('Profil musique')
			->setSubProfils([$this->_page_jazz = Class_Profil::newInstanceWithId(15)
											 ->setParentId(15)
											 ->setLibelle('Jazz')]);

		$this->_file_writer = Storm_Test_ObjectWrapper::mock();
		Class_Profil::setFileWriter($this->_file_writer);

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true);
	}


	public function tearDown() {
		Class_Profil::setFileWriter(null);
		parent::tearDown();
	}
}




class ProfilControllerCssUploadWithExistingCssTest extends ProfilControllerCssTestCase {
	public function setUp() {
		parent::setUp();

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
	}


	/** @test */
	public function fileWriterShouldHavePutContents() {
		$this->assertTrue($this->_file_writer->methodHasBeenCalled('putContents'));
	}
}




class ProfilControllerCssUploadWithNoCssFileTest extends ProfilControllerCssTestCase {
	public function setUp() {
		parent::setUp();
		
		$this->getRequest()
			->setMethod('PUT')
			->setRawBody('body {font-size: 15px}');

		$this->_file_writer
			->whenCalled('fileExists')
			->with(USERFILESPATH.'/css/profil_5.css')
			->answers(false)

			->whenCalled('putContents')
			->with(USERFILESPATH.'/css/profil_5.css', 'body {font-size: 15px}')
			->willDo(function() {
					$this->_file_writer
						->whenCalled('fileExists')
						->with(USERFILESPATH.'/css/profil_5.css')
						->answers(true);
					return 22;
				})
			->beStrict();

		$this->dispatch('/admin/profil/upload-css/id_profil/5', true);
	}


	/** @test */
	public function fileWriterShouldHavePutContents() {
		$this->assertTrue($this->_file_writer->methodHasBeenCalled('putContents'));
	}

	
	/** @test */
	public function profilMusiqueShouldHaveBeenSaved() {
		$this->assertTrue(Class_Profil::methodHasBeenCalledWithParams('save', [$this->_profil_musique]));		
	}


	/** @test */
	public function profilCssShouldBeProfil5Css() {
		$this->assertEquals(BASE_URL.'/userfiles/css/profil_5.css', $this->_profil_musique->getHeaderCss());		
	}
}




class ProfilControllerCssUploadOnPageJazz extends ProfilControllerCssTestCase {
	public function setUp() {
		parent::setUp();

		$this->getRequest()
			->setMethod('PUT')
			->setRawBody('body {font-size: 15px}');

		$this->_file_writer
			->whenCalled('fileExists')
			->with(USERFILESPATH.'/css/profil_5.css')
			->answers(true)

			->whenCalled('putContents')
			->with(USERFILESPATH.'/css/profil_5.css', 'body {font-size: 15px}')
			->answers(22)
			->beStrict();

		$this->dispatch('/admin/profil/upload-css/id_profil/15', true);
	}


	/** @test */
	public function profilMusiqueShouldHaveBeenSaved() {
		$this->assertTrue(Class_Profil::methodHasBeenCalledWithParams('save', [$this->_profil_musique]));		
	}


	/** @test */
	public function profilCssShouldBeProfil5Css() {
		$this->assertEquals(BASE_URL.'/userfiles/css/profil_5.css', $this->_profil_musique->getHeaderCss());		
	}
}

?>