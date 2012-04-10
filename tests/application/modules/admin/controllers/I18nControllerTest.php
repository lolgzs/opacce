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

class Admin_I18nControllerIndexActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('LANGUES')
			->setValeur('fr;en');

		$i18nMock = Storm_Test_ObjectWrapper::on(Class_I18n::getInstance());

		$i18nMock->whenCalled('setProfilExtractor')->answers($i18nMock)->getWrapper()
						->whenCalled('read')->with()->answers(array('key1' => 'value1', 'key2' => 'value2', 'key3' => "value\n3"))->getWrapper()
						->whenCalled('read')->with('en')->answers(array())->getWrapper()
						->whenCalled('read')->with('fr')->answers(array())
						;

		Class_I18n::setInstance($i18nMock);

		$this->dispatch('/admin/i18n');

	}

	/** @test */
	public function oneLanguageNavigationsShouldBePresent() {
		$this->assertXpathCount("//a[@class='content_triggerer']", 1);
	}

	/** @test */
	public function twoInputsForEnShouldBePresent() {
		$this->assertXpathCount("//input[@type='text'][@class='i18n_field'][contains(@name, 'en_')]", 2);
	}

	/** @test */
	public function oneTextareaForEnShouldBePresent() {
		$this->assertXpathCount("//textarea[@class='i18n_field'][contains(@name, 'en_')]", 1);
	}

}

class Admin_I18nControllerIndexActionPostingTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$varMock = new Class_AdminVar();
		$varMock->setValeur('fr;en');

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_AdminVar')
			->whenCalled('find')->with('LANGUES')->answers($varMock);

		$i18nMock = $this->getMockBuilder('Class_I18n')
											->disableOriginalConstructor()
											->getMock();

		$i18nMock->expects($this->once())->method('setProfilExtractor')->will($this->returnValue($i18nMock));
		$i18nMock->expects($this->once())->method('generate');
		$i18nMock->expects($this->exactly(2))->method('read')->will($this->returnValue(array('key1' => 'value1', 'key2' => 'value2', 'key3' => "value\n3")));
		$i18nMock->expects($this->exactly(1))
						->method('updateAll')
						;

		Class_I18n::setInstance($i18nMock);

		$datas = array(
			'i18nFormId' => 1,
			'fr_key1' => 'fr1',
			'fr_key2' => 'fr2',
			'fr_key3' => 'fr3',
			'en_key1' => 'en1',
			'en_key2' => 'en2',
			'en_key3' => 'en3',
		);

		$this->_request->setMethod('POST')
									->setPost($datas);

		$this->dispatch('/admin/i18n');

	}

	/** @test */
	public function submissionShouldRedirectToIndex() {
		$this->assertRedirectTo('/admin/i18n/index');
	}

}

class Admin_I18nControllerUpdateActionTest extends Admin_AbstractControllerTestCase {
	/** @test */
	public function withoutLangShouldSetHttpStatus500() {
		$this->_request->setParam('field', 'key1');
		$this->dispatch('/admin/i18n/update');

		$this->assertEquals(500, $this->_response->getHttpResponseCode());
	}

	/** @test */
	public function withoutFieldShouldSetHttpStatus500() {
		$this->_request->setParam('lang', 'fr');
		$this->dispatch('/admin/i18n/update');

		$this->assertEquals(500, $this->_response->getHttpResponseCode());

	}

	/** @test */
	public function withLangAndFieldShouldCallModelUpdate() {
		$i18nMock = $this->getMockBuilder('Class_I18n')
											->disableOriginalConstructor()
											->getMock();

		$i18nMock->expects($this->once())
						->method('update')
						;

		Class_I18n::setInstance($i18nMock);

		$this->_request->setParam('lang', 'fr')
									->setParam('field', 'key1');

		$this->dispatch('/admin/i18n/update');

	}

}
?>