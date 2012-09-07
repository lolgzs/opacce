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

class ZendAfi_View_Helper_TagVideoTest extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_TagVideo */
	protected $_helper;

	/** @var Class_Album */
	protected $_album;

	/** @var string */
	protected $_html;

	/** @var Class_Users */
	protected $_james_bond;

	public function setUp() {
		parent::setUp();

		$this->_james_bond = Class_Users::newInstanceWithId(45)
			->setIdabon(45)
			->setPrenom('James')
			->setNom('Bond')
			->setMail('jbond@007.fr');

		$identity = new StdClass();
		$identity->ID_USER = 45;

		ZendAfi_Auth::getInstance()->getStorage()->write($identity);


		$this->_album = Class_Album::getLoader()
			->newInstanceWithId(102)
			->setTitre('Mulholland drive')
			->beArteVOD()
			->setExternalUri('http://www.mediatheque-numerique.com/films/mulholland-drive')
			->setNotes(array(
				array('field' => '856',
							'data' => array('x' => 'trailer',
															'a' => 'http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.mp4')),
				array('field' => '856',
							'data' => array('x' => 'trailer',
															'a' => 'http://media.universcine.com/7e/5b/7e5bece6-7d56-11e1-9d5b-6b449667e8b8.flv')),
											 
				array('field' => '856',
							'data' => array('x' => 'poster',
															'a' => 'http://media.universcine.com/7e/5c/7e5c210a-b4ad-11e1-b992-959e1ee6d61d.jpg'))));

		$view = new ZendAfi_Controller_Action_Helper_View();
		$this->_helper = new ZendAfi_View_Helper_TagVideo();
		$this->_helper->setView($view);
	}


	/** @test */
	public function withCurrentUserAbonneSigbShouldDisplayLinkFullPlay() {
		$this->_james_bond
			->setDateDebut('1999-09-12')
			->setDateFin('2023-09-12')
			->beAbonneSIGB();

		$this->assertXPathContentContains($this->_helper->tagVideo($this->_album), 
																			'//a[contains(@href, "mulholland-drive?sso_id=afi")]',
																			'Visionner le film');
	}


	/** @test */
	public function withCurrentUserNotAbonneShouldNotDisplayLinkArteVod() {
		$this->_james_bond->beInvite();

		$this->assertNotXPathContentContains($this->_helper->tagVideo($this->_album), 
																				 '//a', 'Visionner le film');
		
	}


	/** @test */
	public function withCurrentUserNotAbonneShouldDisplayErrorMessage() {
		$this->_james_bond->beInvite();

		$this->assertXPathContentContains($this->_helper->tagVideo($this->_album), 
																			'//p', 'abonnement valide');
		
	}


	/** @test */
	public function withNoCurrentUserShouldDisplayErrorMessage() {
		ZendAfi_Auth::getInstance()->getStorage()->clear();

		$this->assertXPathContentContains($this->_helper->tagVideo($this->_album), 
																			'//p', 'abonnement valide');
	}

}

?>