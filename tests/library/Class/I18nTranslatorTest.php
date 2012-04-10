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
class I18nTranslatorTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$i18nMock = Storm_Test_ObjectWrapper::on(Class_I18n::getInstance())
									->whenCalled('read')->with('fr')->answers(array())->getWrapper()
									->whenCalled('read')->with('en')->answers(array(md5('Médiathèque du plessy valandrey') => 'Plessy valandrey\'s mediatheque'))->getWrapper()
									;

		Class_I18n::setInstance($i18nMock);

		Class_I18nTranslator::setCaching(false);

	}

	/** @test */
	public function translateOfNonExistingShouldReturnPassedValue() {
		$translator = Class_I18nTranslator::getFor('fr');
		$this->assertEquals('Médiathèque du plessy valandrey', $translator->translate('Médiathèque du plessy valandrey'));
	}

	/** @test */
	public function translateOfExistingShouldReturnTranslated() {
		$translator = Class_I18nTranslator::getFor('en');
		$this->assertEquals('Plessy valandrey\'s mediatheque', $translator->translate('Médiathèque du plessy valandrey'));
	}

}
?>