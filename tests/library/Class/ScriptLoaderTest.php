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

abstract class AbstractScriptLoaderTestCase extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->_old_cfg = Zend_Registry::get('cfg');
		$this->cfg = new Zend_Config($this->_old_cfg->toArray(), true);
		Zend_Registry::set('cfg', $this->cfg);
		Class_ScriptLoader::resetInstance();
	}


	public function tearDown() {
		Zend_Registry::set('cfg', $this->_old_cfg);
	}
}


class ScriptLoaderAmberTest extends AbstractScriptLoaderTestCase {
	/** @test */
	function withoutConfigAmberShouldLoadDeploy() {
		$this->cfg->amber = null;
		Class_ScriptLoader::getInstance()->loadAmber();
		$this->assertContains('"deploy":true',
													Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	function callingLoadAmberTwiceShouldResultInOneLoad() {
		$this->cfg->amber = null;
		Class_ScriptLoader::getInstance()->loadAmber()->loadAmber();
		$this->assertEquals(1, substr_count(Class_ScriptLoader::getInstance()->html(), 'amber.js'));
	}


	/** @test */
	function withConfigDeployFalseShouldLoadDevelopment() {
		foreach (array(false, 'false', 0, '0') as $value) {
			$this->cfg->amber = new Zend_Config(array('deploy' => $value));

			Class_ScriptLoader::resetInstance();
			Class_ScriptLoader::getInstance()->loadAmber();

			$this->assertContains('"deploy":false',
														Class_ScriptLoader::getInstance()->html(),
														"should load development for amber.deploy=".$value);
		}
	}


	/** @test */
	function withConfigDeployTrueShouldLoadDeploy() {
		foreach (array(true, 'true', 1, 'zork') as $value) {
			$this->cfg->amber = new Zend_Config(array('deploy' => $value));

			Class_ScriptLoader::resetInstance();
			Class_ScriptLoader::getInstance()->loadAmber();

			$this->assertContains('"deploy":true',
														Class_ScriptLoader::getInstance()->html(),
														"should not load development for amber.deploy=".$value);
		}
	}


	/** @test */
	function withoutCallingLoadAmberShouldNotLoadIt() {
			$this->assertNotContains('loadAmber',
															 Class_ScriptLoader::getInstance()->html());
	}
}



class ScriptLoaderAmberDevelopmentModeTest extends AbstractScriptLoaderTestCase {
	public function setUp() {
		parent::setUp();
		$this->cfg->amber = new Zend_Config(array('deploy' => false));
		Class_ScriptLoader::resetInstance();
		Class_ScriptLoader::getInstance()->loadAmber();
	}


	/** @test */
	function commitPathJsShouldBeAmberCommitJs() {
		$this->assertContains('"' . BASE_URL . '/admin/amber/commitJs"',
													Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	function commitPathStShouldBeAmberCommitSt() {
		$this->assertContains('"' . BASE_URL . '/admin/amber/commitSt"',
													Class_ScriptLoader::getInstance()->html());
	}
}



class ScriptLoaderJsAndCssTest extends PHPUnit_Framework_TestCase {
	protected $html;

	public function setUp() {
		Class_ScriptLoader::resetInstance();
		$this->html = Class_ScriptLoader::getInstance()
			->addOPACStyleSheet('blanc_sur_noir', array('rel' => 'alternate stylesheet', 
																									'title' => 'Blanc sur noir'))
			->addAdminScript('toolbar')
			->addOPACStyleSheets(array('global', 'print'))
			->addInlineStyle('body {font-size: 10px}')
			->addStyleSheet('public/css/nuages.css')
			->addScript('opac/cycle.min')
			->addScript('opac/slides.min.js')
			->html();
	}


	public function assertXpath($path, $message = '') {
		$constraint = new Zend_Test_PHPUnit_Constraint_DomQuery($path);
		if (!$constraint->evaluate($this->html, __FUNCTION__)) {
            $constraint->fail($path, $message);
		}
	}


	/** @test */
	function shouldContainCSSBlancSurNoir() {
		$this->assertXPath('//link[@type="text/css"][contains(@href, "public/opac/css/blanc_sur_noir.css")][@rel="alternate stylesheet"]');
	}


	/** @test */
	function shouldContainCSSGlobal() {
		$this->assertXPath('//link[@type="text/css"][contains(@href, "public/opac/css/global.css")][@rel="stylesheet"]');
	}


	/** @test */
	function shouldNotContainNuagesCssCss() {
		$this->assertXPath('//link[@type="text/css"][contains(@href, "public/css/nuages.css")]', $this->html);
	}


	/** @test */
	function shouldContainInlineStyle() {
		$this->assertContains('<style type="text/css">body {font-size: 10px}</style>', $this->html);
	}


	/** @test */
	function javascriptsShouldBeAfterCss() {
		$this->assertContains('<style type="text/css">body {font-size: 10px}</style>', $this->html);
	}


	/** @test */
	function jsShouldContainsCylcleDotJs() {
		$this->assertContains('opac/cycle.min.js', $this->html);
	}


	/** @test */
	function jsShouldContainsSlidesDotJs() {
		$this->assertContains('opac/slides.min.js', $this->html);
	}
}



class ScriptLoaderVersionHashTest extends PHPUnit_Framework_TestCase {
	protected $_html;
	protected $_versionHash;

	public function setUp() {
		Class_ScriptLoader::resetInstance();
		$this->_html = Class_ScriptLoader::getInstance()
			->addStyleSheet('public/css/nuages.css')
			->addStyleSheet('normal.css?param=value')
			->addScript('opac/cycle.min')
			->html();

		$this->_versionHash = md5(VERSION_PERGAME);
	}


	/** @test */
	public function hashShouldBeAppendedToCss() {
		$this->assertContains('nuages.css?v=' . $this->_versionHash, $this->_html);
	}


	/** @test */
	public function hashShouldBeAppendedToCssWithAmperstand() {
		$this->assertContains('normal.css?param=value&v=' . $this->_versionHash, $this->_html);
	}


	/** @test */
	public function hashShouldBeAppendedToJs() {
		$this->assertContains('cycle.min.js?v=' . $this->_versionHash, $this->_html);
	}	
}
?>