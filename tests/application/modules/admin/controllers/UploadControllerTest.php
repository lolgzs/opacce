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

class UploadControllerMultipleActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/admin/upload/multiple');
	}

	/** @test */
	public function multipleContainerShouldBePresent() {
		$this->assertXpath('//div[@id="file_uploader"]');
	}

	/** @test */
	public function multipleJavascriptLibraryShouldBePresent() {
		$this->assertXpath('//head/script[contains(@src, "multi_upload/fileuploader.js")]');
	}
}




class UploadControllerMultipleProcessPostAction extends AbstractControllerTestCase {
	/** @test */
	public function withEmptyRequestResponseShouldContainError() {
		$this->dispatch('/admin/upload/multiple-process');
		$this->assertEquals(
			json_decode('{"success":"false"}'),
			json_decode($this->_response->getBody())
		);
	}


	/** @test */
	public function withNonExistingModelClassResponseShouldContainBadModelError() {
		$this->getRequest()->setMethod('POST')
										->setPost(array(
											'qqfile' => 'test.png',
											'modelClass' => 'AbsolutelyNonExisting_Model_InThis_system',
											'modelId'	=> 999
										));
		$this->dispatch('/admin/upload/multiple-process');
		$this->assertEquals(
			json_decode('{"success":"false","error":"Bad model loader"}'),
			json_decode($this->_response->getBody())
		);
	}


	/** @test */
	public function withNonExistingModelInstanceResponseShouldContainNoModelError() {
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
						->whenCalled('find')
						->with(999)
						->answers(null);

		$this->getRequest()->setMethod('POST')
										->setPost(array(
											'qqfile' => 'test.png',
											'modelClass' => 'Class_Album',
											'modelId'	=> 999
										));

		$this->dispatch('/admin/upload/multiple-process');

		$this->assertEquals(
			json_decode('{"success":"false","error":"No model"}'),
			json_decode($this->_response->getBody())
		);
	}


	/** @test */
	public function withModelShouldCallFileProcessingMethod() {
		$album = Storm_Test_ObjectWrapper::on(Class_Album::getLoader()->newInstance())
			->whenCalled('addFile')->answers(array('success' => 'true'))
			->getWrapper();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Album')
			->whenCalled('find')->with(999)->answers($album)
			->whenCalled('save')->answers(true);

		$this->getRequest()->setMethod('POST')
										->setPost(array(
											'qqfile' => 'test.png',
											'modelClass' => 'Class_Album',
											'modelId'	=> 999
										));

		$this->dispatch('/admin/upload/multiple-process');

		$this->assertTrue($album->methodHasBeenCalled('addFile'));

		$this->assertEquals(
			json_decode('{"success":"true"}'),
			json_decode($this->_response->getBody())
		);

		return $album;
	}


	/** 
	 * @depends withModelShouldCallFileProcessingMethod
	 * @test 
	 */
	public function modelShouldBeSavedAfterAddFile($album) {
		$this->assertTrue($album->methodHasBeenCalled('save'));
	}


	/** 
	 * @depends withModelShouldCallFileProcessingMethod
	 * @test 
	 */
	public function albumDateMajShouldBeNow($album) {
		$today = new Zend_Date();
		$this->assertContains($today->toString('yyyy-MM-dd'),	$album->getDateMaj());
	}
}




class UploadControllerVignetteNoticeActionTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->dispatch('/admin/upload/vignette-notice/id/12345', true);
	}


	/** @test */
	public function formShouldHaveInputUrlVignette() {
		$this->assertXPath('//form//input[@type="url"][@name="url_vignette"][contains(@placeholder, "ex: http://upload")]');
	}


	/** @test */
	public function formShouldHaveSubmitButtonEnvoyer() {
		$this->assertXPath('//form//input[@type="submit"][@value="Envoyer"]');
	}
}




class UploadControllerVignetteNoticeActionPostValidUrlTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Notice')
			->whenCalled('save')
			->answers(true);


		Class_Notice::newInstanceWithId(12345)
			->setIsbn('0123456789')
			->setTypeDoc(Class_TypeDoc::LIVRE)
			->setTitrePrincipal('Harry Potter')
			->setAuteurPrincipal('J.K.Rowling');

		Class_CosmoVar::newInstanceWithId('url_services')->setValeur('http://cache.org');

		Class_WebService_AllServices::setHttpClient($http_client = Storm_Test_ObjectWrapper::mock()
																								->whenCalled('open_url')
																								->with('http://cache.org?isbn=0123456789'
																											 .'&type_doc=1'
																											 .'&titre='.urlencode('Harry Potter')
																											 .'&auteur='.urlencode('J.K.Rowling')
																											 .'&image='.urlencode('http://upload.wikimedia.org/potter.jpg')
																											 .'&src='.Class_WebService_AllServices::createSecurityKey()
																											 .'&action=12'
																								)
																								->answers(json_encode(['vignette' => 'http://cache.org/potter_thumb.jpg',
																																			 'image' => 'http://cache.org/potter.jpg',
																																			 'statut' => 'ok'])
																								)
																								->beStrict()
		);

		$this->postDispatch('/admin/upload/vignette-notice/id/12345', 
												['url_vignette' => 'http://upload.wikimedia.org/potter.jpg'],
												true);
	}

	
	public function tearDown() {
		Class_WebService_AllServices::setHttpClient(null);
	}


	/** @test */
	public function noticeShouldHaveBeenSaved() {
		$this->assertEquals('0123456789', Class_Notice::getFirstAttributeForLastCallOn('save')->getIsbn());
	}


	/** @test */
	public function noticeUrlVignetteShouldBePotterThumbDotJpg() {
		$this->assertEquals('http://cache.org/potter_thumb.jpg', Class_Notice::find(12345)->getUrlVignette());
	}

}

?>