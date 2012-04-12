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

class ProfileI18nTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var PHPUnit_Framework_MockObject_MockObject
	 */
	protected $_extractor;

	/**
	 * @var Class_I18n
	 */
	protected $_i18n;

	/**
	 * @var string
	 */
	protected $_basePath;

	protected function setUp() {
		Class_I18n::setInstance(null);
		$this->_i18n = Class_I18n::getInstance();

		$this->_basePath = realpath(dirname(__FILE__)). '/../../../userfiles' . Class_I18n::BASE_PATH;

		$this->_ensureNoFile(Class_I18n::MASTER_NAME);

	}

	/** @test */
	public function generateShouldCreateMasterFile() {
		$this->_i18n->generate();
		$this->assertFileExists($this->_getFilePathFor(Class_I18n::MASTER_NAME));
	}

	/** @test */
	public function generateShouldWriteProfileStringsInMaster() {
		$this->_extractor = $this->getMockBuilder('Class_Profil_I18nStringExtractor')
											->disableOriginalConstructor()
											->getMock();

		$this->_extractor->expects($this->once())
							->method('extract')
							->will($this->returnValue(I18nTestFixtures::createProfileStrings()))
							;

		$this->_extractor->expects($this->once())
							->method('setModel')
							->will($this->returnValue($this->_extractor))
							;

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')->whenCalled('findAll')->answers(array(new Class_Profil()));

		$this->_i18n->setProfilExtractor($this->_extractor);
		$this->_i18n->generate();
		$this->assertEquals(I18nTestFixtures::createProfileStringsSerialization(), file_get_contents($this->_getFilePathFor(Class_I18n::MASTER_NAME)));

	}

	/** @test */
	public function generateShouldEscapeSpecialQuotesAndBackslashes() {
		$this->_extractor = $this->getMockBuilder('Class_Profil_I18nStringExtractor')
											->disableOriginalConstructor()
											->getMock();

		$this->_extractor->expects($this->once())
							->method('extract')
							->will($this->returnValue(I18nTestFixtures::createProfileStringsWithQuotesAndSlashes()))
							;

		$this->_extractor->expects($this->once())
							->method('setModel')
							->will($this->returnValue($this->_extractor))
							;

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')->whenCalled('findAll')->answers(array(new Class_Profil()));

		$this->_i18n->setProfilExtractor($this->_extractor);
		$this->_i18n->generate();

		$this->assertEquals(I18nTestFixtures::createProfileStringsSerializationWithQuotesAndSlashes(), file_get_contents($this->_getFilePathFor(Class_I18n::MASTER_NAME)));

	}

	/** @test */
	public function readWithNoLanguageCodeShouldReturnMasterContent() {
		// création du master
		$this->_writeFileFixture(Class_I18n::MASTER_NAME, I18nTestFixtures::createProfileStringsSerialization());

		$this->assertEquals(I18nTestFixtures::createMasterDatas(), $this->_i18n->read());

	}

	/** @test */
	public function readNotExistingEnglishLanguageWithNoMasterShouldReturnEmptyArray() {
		$this->_ensureNoFile('en');
		$this->assertEmpty($this->_i18n->read('en'));

	}

	/** @test */
	public function readNotExistingEnglishLanguageShouldCreateEmpty() {
		$this->_ensureNoFile('en');
		$this->_i18n->read('en');

		$this->assertFileExists($this->_getFilePathFor('en'));
		$this->assertEquals(I18nTestFixtures::createEmptySerialization(), file_get_contents($this->_getFilePathFor('en')));

	}

	/** @test */
	public function readEnglishLanguageShouldReturnEnglishDatas() {
		$this->_ensureNoFile('en');
		$this->_writeFileFixture('en', I18nTestFixtures::createEnglishSerialization());

		$this->assertEquals(I18nTestFixtures::createEnglishDatas(), $this->_i18n->read('en'));

	}

	/** @test */
	public function updateOnNonExistingShouldCreateFile() {
		$this->_ensureNoFile('es');
		$this->_i18n->update('es', '', '');

		$this->assertFileExists($this->_getFilePathFor('es'));

	}

	/** @test */
	public function updateKey666WithNumberOfTheBeastShouldUpdateFile() {
		$this->_ensureNoFile('es');
		$this->_i18n->update('es', '666', 'The number of the beast');
		$datas = $this->_i18n->read('es');

		$this->assertEquals('The number of the beast', $datas['666']);

	}

	/** @test */
	public function updateWithSameDataShouldNotChangeAnything() {
		$this->_ensureNoFile('es');
		$this->_writeFileFixture('es', I18nTestFixtures::createEnglishSerialization());
		$this->_i18n->update('es', md5('Les dernières nouvelles du front'), 'The latest news from the front');

		$this->assertEquals(I18nTestFixtures::createEnglishSerialization(), file_get_contents($this->_getFilePathFor('es')));

	}

	/** @test */
	public function updateAllShouldReplaceFileContent() {
		$this->_ensureNoFile('es');
		$this->_writeFileFixture('es', I18nTestFixtures::createEnglishSerialization());
		$this->_i18n->updateAll('es', I18nTestFixtures::createProfileStrings());

		$this->assertEquals(I18nTestFixtures::createProfileStringsSerialization(), file_get_contents($this->_getFilePathFor('es')));

	}

	/** @test */
	public function updateAllWithEmptyShouldEmptyFileContent() {
		$this->_ensureNoFile('es');
		$this->_writeFileFixture('es', I18nTestFixtures::createEnglishSerialization());
		$this->_i18n->updateAll('es', array());

		$this->assertEquals(I18nTestFixtures::createEmptySerialization(), file_get_contents($this->_getFilePathFor('es')));

	}

	/**
	 * @param string $lang
	 */
	protected function _ensureNoFile($lang) {
		$filename = $this->_getFilePathFor($lang);

		if (file_exists($filename))
			unlink($filename);
	}

	/**
	 * @param string $lang
	 * @param string $content
	 */
	protected function _writeFileFixture($lang, $content) {
		file_put_contents($this->_getFilePathFor($lang), $content);
	}

	/**
	 * @param string $lang
	 * @return string
	 */
	protected function _getFilePathFor($lang) {
		return $this->_basePath . $lang . '.php';
	}
}




class I18nTestFixtures {
	public static function createEnglishDatas() {
		return array(
			md5('Toutes nos collections de papillons') => 'All our collections of butterflies',
			md5('Les dernières nouvelles du front') => 'The latest news from the front',
		);
	}

		/**
	 * @return string
	 */
	public static function createEnglishSerialization() {
		return '<?php
return array(
\'' . md5('Toutes nos collections de papillons') . '\' => \'All our collections of butterflies\',
\'' . md5('Les dernières nouvelles du front') . '\' => \'The latest news from the front\',

);
?>';
	}

	public static function createMasterDatas() {
		return self::createProfileStrings();
	}

	/**
	 * @return array
	 */
	public static function createProfileStrings() {
		return array(
			md5('Toutes nos collections de papillons') => 'Toutes nos collections de papillons',
			md5('Les dernières nouvelles du front') => 'Les dernières nouvelles du front',
		);
	}

	/**
	 * @return string
	 */
	public static function createProfileStringsSerialization() {
		return '<?php
return array(
\'' . md5('Toutes nos collections de papillons') . '\' => \'Toutes nos collections de papillons\',
\'' . md5('Les dernières nouvelles du front') . '\' => \'Les dernières nouvelles du front\',

);
?>';
	}

	/**
	 * @return array
	 */
	public static function createProfileStringsWithQuotesAndSlashes() {
		return array(
			md5('La lecture c\'est de la bombe') => 'La lecture c\'est de la bombe',
			md5('Et si on passait par un back\\slash ?') => 'Et si on passait par un back\\slash ?',
		);
	}

	/**
	 * @return string
	 */
	public static function createProfileStringsSerializationWithQuotesAndSlashes() {
		return '<?php
return array(
\'' . md5('La lecture c\'est de la bombe') . '\' => \'La lecture c\\\'est de la bombe\',
\'' . md5('Et si on passait par un back\\slash ?') . '\' => \'Et si on passait par un back\\\\slash ?\',

);
?>';
	}

	public static function createEmptySerialization() {
		return '<?php
return array(

);
?>';
	}

}

?>