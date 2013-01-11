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

abstract class ZendAfi_View_Helper_RenderFormTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_RenderForm */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();
		Class_ScriptLoader::resetInstance();

		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->_helper = new ZendAfi_View_Helper_RenderForm();
		$this->_helper->setView($view);

		$this->_form = $view->newForm(array('id' => 'form_personne'))
			->addElement('text', 'nom', array('label' => 'nom')) 
			->addElement('text', 'prenom', array('label' => 'prenom'))
			->addDisplayGroup(array('nom', 'prenom'), 
												'personne',
												array('legend' => 'Personne'));
	}
}


class ZendAfi_View_Helper_RenderFormWithoutSubmitButtonsTest extends  ZendAfi_View_Helper_RenderFormTestCase {
	public function setUp() {
		parent::setUp();
		$this->_html = $this->_helper->renderForm($this->_form);
	}


	/** @test */
	public function labelNomShouldBeInFirstTD() {
		$this->assertXPathContentContains($this->_html, '//table/tr[1]/td[1]', 'nom');
	}


	/** @test */
	public function textFieldNomShouldBeInSecondTD() {
		$this->assertXPath($this->_html, '//table/tr[1]/td[2][@class="gauche"]/input[@name="nom"]');
	}


	/** @test */
	public function labelPrenomShouldBeInFirstTDOfSecondTR() {
		$this->assertXPathContentContains($this->_html, '//table/tr[2]/td[1]', 'prenom');
	}


	/** @test */
	public function buttonValiderShouldBePresent() {
		$this->assertXPath($this->_html, 
											 '//div[@class="bouton"][contains(@onclick, "submit")][contains(@onclick, "setFlagMaj(false)")]//img[@alt="valider"]',
											 $this->_html);
	}


	/** @test */
	public function buttonAnnulerShouldBePresent() {
		$this->assertXPath($this->_html, 
											 '//div[@class="bouton"][not(contains(@onclick, "submit"))]',
											 $this->_html);
	}


	/** @test */
	public function scriptLoaderShouldSetFlagMaj() {
		$this->assertContains('setFlagMaj(true)', Class_ScriptLoader::getInstance()->html());
	}
}



class ZendAfi_View_Helper_RenderFormWithSubmitButtonsTest extends  ZendAfi_View_Helper_RenderFormTestCase {
	public function setUp() {
		parent::setUp();
		$this->_form
			->addElement('Submit', 'Valider')
			->addElement('Submit', 'Annuler');
		$this->_html = $this->_helper->renderForm($this->_form);
	}


	/** @test */
	public function customButtonHelpersShouldNotBePresent() {
		$this->assertNotXPath($this->_html, '//div[@class="bouton"]');
	}


	/** @test */
	public function scriptLoaderShouldNotSetFlagMaj() {
		$this->assertNotContains('setFlagMaj', Class_ScriptLoader::getInstance()->html());
	}


	/** @test */
	public function inputShouldNotBeInTD() {
		$this->assertNotXPath($this->_html, '//td//submit');
	}
}

?>