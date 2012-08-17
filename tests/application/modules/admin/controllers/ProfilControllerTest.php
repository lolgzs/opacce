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


abstract class Admin_ProfilControllerProfilJeunesseTestCase extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$cfg_site = array('header_img' => "/public/jeunesse.png",
											'header_img_cycle' => true,
											'liens_sortants_off' => true,
											'accessibilite_on' => 0,
											'couleur_texte_bandeau' => '#222',
											'couleur_lien_bandeau' => '#55F',
											'access_level' => 3,
											'favicon' => '/userfiles/favicon.ico',
											'logo_gauche_img' => '/userfiles/mabib.png',
											'logo_gauche_link' => 'http://mabib.fr',
											'logo_droite_img' => '/userfiles/macommune.png',
											'logo_droite_link' => 'http://macommune.fr',
											'header_social_network' => true);

		$cfg_notice = array('exemplaires' => array('grouper' => 1,
																							 'section' => 1,
																							 'emplacement' => 0,
																							 'bib' => 1,
																							 'annexe' => 0,
																							 'dispo' => 1,
																							 'date_retour' => 0,
																							 'localisation' => 0,
																							 'plan' => 0,
																							 'resa' => 2));


		$this->profil_jeunesse = Class_Profil::getLoader()
			->newInstanceWithId(5)
			->setBrowser('opac')
			->setTitreSite('Médiathèque de Melun')
			->setLibelle('Profil Jeunesse')
			->setCommentaire('Pour les jeunes')
			->setSkin('modele')
			->setCfgSite(ZendAfi_Filters_Serialize::serialize($cfg_site))
			->setCfgNotice($cfg_notice)
			->setMailSite('tintin@herge.be')
			->setHauteurBanniere(150)
			->setBoiteLoginInBanniere(true);


		$this->profil_wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('save')->answers(true)->getWrapper();

		Zend_Auth::getInstance()->getIdentity()->ROLE_LEVEL = 7;
	}
}



class Admin_ProfilControllerEditProfilJeunesseTest extends Admin_ProfilControllerProfilJeunesseTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/profil/edit/id_profil/5');
	}

	/**
	 * @test
	 */
	public function pageTitleShouldBeModifierLeProfilJeunesse() {
		$this->assertQueryContentContains("head title", "Modifier le profil: Profil Jeunesse");
	}


	/** @test */
	public function textInputNamedHauteurBanniereShouldBeDisplayed() {
		$this->assertXPath("//input[@type='text'][@name='hauteur_banniere'][@value='150']");
	}


	/** @test */
	public function formActionShouldBeEdit() {
		$this->assertXPath("//form[contains(@action,'/admin/profil/edit/id_profil/5')]");
	}


	/** @test */
	public function shouldDisplayBibSelection() {
		$this->assertXPath('//select[@name="id_site"]');
	}


	/** @test */
	public function textInputHeaderImgShouldContainsPathToJeunessePng() {
		$this->assertXPath("//input[@type='text'][@name='header_img'][@value='/public/jeunesse.png']");
	}


	/** @test */
	public function textInputFaviconShouldContainsAFIFavicon() {
		$this->assertXPath("//input[@type='text'][@name='favicon'][@value='/userfiles/favicon.ico']");
	}


	/** @test */
	public function textInputLogoGaucheImgShouldContainsMaBibDotPng() {
		$this->assertXPath("//input[@type='text'][@name='logo_gauche_img'][@value='/userfiles/mabib.png']");
	}


	/** @test */
	public function textInputLogoGaucheLinkShouldContainsMaBibDotFr() {
		$this->assertXPath("//input[@type='text'][@name='logo_gauche_link'][@value='http://mabib.fr']");
	}


	/** @test */
	public function textInputLogoDroiteImgShouldContainsMaCommuneDotPng() {
		$this->assertXPath("//input[@type='text'][@name='logo_droite_img'][@value='/userfiles/macommune.png']");
	}


	/** @test */
	public function textInputLogoDroiteLinkShouldContainsMaCommuneDotFr() {
		$this->assertXPath("//input[@type='text'][@name='logo_droite_link'][@value='http://macommune.fr']");
	}


	/** @test */
	public function textInputLibelleShouldContainsProfilJeunesse() {
		$this->assertXPath("//input[@type='text'][@name='libelle'][@value='Profil Jeunesse']");
	}

	/** @test */
	public function textInputCouleurTexteBandeauShouldContainsSharp222() {
		$this->assertXPath("//input[@type='text'][@name='couleur_texte_bandeau'][@value='#222']");
	}


	/** @test */
	public function textInputCouleurLienBandeauShouldContainsSharp55F() {
		$this->assertXPath("//input[@type='text'][@name='couleur_lien_bandeau'][@value='#55F']");
	}

	/** @test */
	public function checkBoxAccessibiliteOnShouldNotBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='accessibilite_on'][not(@checked)]");
	}

	/** @test */
	public function checkBoxLiensSortantsOffShouldBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='liens_sortants_off'][@checked='checked']");
	}

	/** @test */
	public function checkBoxHeaderSocialNetworkBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='header_social_network'][@checked='checked']");
	}

	/** @test */
	public function skinListShouldContainOriginal() {
		$this->assertXPath("//select[@name='skin']/option[@value='original'][not(@selected)]");
	}

	/** @test */
	public function skinAstrolabeShouldBeSelected() {
		$this->assertXPath("//select[@name='skin']/option[@value='modele'][@selected='selected']");
	}

	/** @test */
	public function mailSiteShouldContainsTintinAtHergeDotBe() {
		$this->assertXPath("//input[@type='text'][@name='mail_site'][@value='tintin@herge.be']");
	}


	/** @test */
	public function shouldNiveauAccesRedacteurBibliothequeShouldBeSelected() {
		$this->assertXPathContentContains("//select[@name='access_level']//option[@value='3'][@selected='selected']",
																			"rédacteur bibliothèque");
	}


	/** @test */
	public function checkBoxBoiteLoginInBanniereShouldBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='boite_login_in_banniere'][@checked='checked']");
	}


	/** @test */
	public function checkBoxBoiteRechercheSimpleInBanniereShouldNotBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='boite_recherche_simple_in_banniere'][not(@checked)]");
	}


	/** @test */
	public function postingLiensOff_BibTwoRedirectsToProfilIndex() {
		$data = array(	'liens_sortants_off' => 0,
										'id_site' => 2);
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/profil/edit/id_profil/5');

		$this->assertController('profil');
		$this->assertAction('edit');

		return $this->profil_jeunesse;
	}


	/**
	 * @test
	 * @depends postingLiensOff_BibTwoRedirectsToProfilIndex
	 */
	public function liensSortantsShouldBeFalse($profil_jeunesse) {
		$this->assertEquals(0, $profil_jeunesse->getLiensSortantsOff());
	}


	/**
	* @test
	* @depends postingLiensOff_BibTwoRedirectsToProfilIndex
	*/
	public function idSiteShouldBeTwo($profil_jeunesse) {
		$this->assertEquals(2, $profil_jeunesse->getIdSite());
	}


	public function _postData($data) {
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/profil/edit/id_profil/5');
	}


	/** @test */
	public function postingLibelleJeunesseTitrePourLesJeunesShouldUpdateRightFields() {
		$this->_postData(array(	'libelle' => "Jeunesse",
														'titre_site' => "Pour les jeunes"));

		$this->assertEquals("Jeunesse", $this->profil_jeunesse->getLibelle());
		$this->assertEquals("Pour les jeunes", $this->profil_jeunesse->_get('titre_site'));
		return $this->profil_jeunesse;
	}


	/** @test */
	public function postingBoitesBanniereAndLoginShouldUpdateCreateThem() {
		$this->_postData(array(	'boite_login_in_banniere' => false,
														'boite_recherche_simple_in_banniere' => true));

		$this->assertFalse($this->profil_jeunesse->getBoiteLoginInBanniere());
		$this->assertTrue($this->profil_jeunesse->getBoiteRechercheSimpleInBanniere());
	}


	/** @test */
	public function profilShouldNotBeSavedIfLibelleEmpty() {
		$this->_postData(array('libelle' => ""));
		$this->assertAction('edit');

		$this->assertFalse($this->profil_wrapper->methodHasBeenCalled('save'));
		$this->assertXPathContentContains('//ul[@class="errors"]//li',
																			'est obligatoire');
	}


	/** @test */
	public function deleteActionShouldCallDeleteOnLoader() {
		$this->profil_wrapper
			->whenCalled('delete')
			->answers(null);

		$this->dispatch('/admin/profil/delete/id_profil/5');

		$this->assertTrue($this->profil_wrapper->methodHasBeenCalled('delete'));
		$this->assertEquals($this->profil_jeunesse,
												$this->profil_wrapper->getFirstAttributeForLastCallOn('delete'));
		$this->assertRedirect('admin/profil');
	}


	/** @test */
	function checkboxHeaderImgCycleShouldBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='header_img_cycle'][@checked]");
	}
}



class Admin_ProfilControllerProfilJeunesseTestMenusMaj extends Admin_ProfilControllerProfilJeunesseTestCase {
	/** @test */
	public function withModuleFormationEnabledComboMenuShouldContainsFormations() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('1');
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertXPathContentContains('//option', 'Formations');
	}


	/** @test */
	public function withModuleFormationDisabledComboMenuShouldNotContainsFormations() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('0');
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertNotXPathContentContains('//option', 'Formations');
	}


	/** @test */
	public function withBibNumberiqueEnabledComboMenuShouldContainsLienVersUnAlbum() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('BIBNUM')
			->setValeur('1');
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertXPathContentContains('//option', 'Lien vers un album');
	}


	/** @test */
	public function withBibNumberiqueDisabledComboMenuShouldNotContainsLienVersUnAlbum() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('BIBNUM')
			->setValeur('0');
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertNotXPathContentContains('//option', 'Lien vers un album');
	}


	/** @test */
	public function withMultimediaEnabledComboMenuShouldContainsReserverPosteMultimedia() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('MULTIMEDIA_KEY')
			->setValeur('I love multimedia');
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertXPathContentContains('//option', 'Réserver un poste multimédia');
	}


	/** @test */
	public function withMultimediaDisabledComboMenuShouldNotContainsReserverPosteMultimedia() {
		Class_AdminVar::getLoader()
			->newInstanceWithId('MULTIMEDIA_KEY')
			->setValeur(null);
		$this->dispatch('admin/profil/menusmaj/id_profil/5/id_menu/H/mode/edit');
		$this->assertNotXPathContentContains('//option', 'Réserver un poste multimédia');
	}
}



class Admin_ProfilControllerNewPageTest extends Admin_ProfilControllerProfilJeunesseTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/profil/newpage/id_profil/5');

		$this->new_page = Class_Profil::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function assertRedirectToAccueilConfig() {
		$this->assertRedirect('admin/profil/accueil/');
	}


	/** @test */
	public function parentShouldBeProfilJeunesse() {
		$this->assertEquals(5, $this->new_page->getParentId());
	}


	
}



class Admin_ProfilControllerEditProfilVideTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->profil_vide = new Class_Profil();
		$this->profil_vide->setId(34);

		Class_Profil::getLoader()->cacheInstance($this->profil_vide);
		$this->dispatch('/admin/profil/edit/id_profil/34');
	}

	/** @test */
	public function textInputNamedHauteurBanniereShouldBeDisplayed() {
		$this->assertXPath("//input[@type='text'][@name='hauteur_banniere'][@value='100']");
	}

	/** @test */
	public function checkBoxAccessibiliteOnShouldBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='accessibilite_on'][@checked='checked']");
	}

	/** @test */
	public function checkBoxLiensSortantsOffShouldBeNotChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='liens_sortants_off'][not(@checked)]");
	}


	/** @test */
	function checkboxHeaderImgCycleShouldNotBeChecked() {
		$this->assertXPath("//input[@type='checkbox'][@name='header_img_cycle'][not(@checked)]");
	}


	/** @test */
	public function shouldDisplayNiveauDAcces() {
		$this->assertXPathContentContains("//select[@name='access_level']//option[@value='-1'][@selected='selected']",
																			"public");

		foreach(array('0' => 'invité',
									'1' => 'abonné',
									'2' => 'abonné identifié SIGB',
									'3' => 'rédacteur bibliothèque',
									'4' => 'administrateur bibliothèque',
									'5' => 'rédacteur portail',
									'6' => 'administrateur portail') as $level => $label)
		$this->assertXPathContentContains("//select[@name='access_level']//option[@value='$level']",
																			$label);

		$this->assertNotXPath("//select[@name='access_level']//option[@value='7']");
	}



	/** @test */
	public function postingDataWithWrongData() {
		$data = array('libelle' => '',
									'largeur_site' => 200,
									'largeur_division1' => 100,
									'largeur_division2' => 500);
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/profil/edit/id_profil/34');
		return $this->profil_vide;
	}


	/**
	 * @test
	 * @depends postingDataWithWrongData
	 */
	public function errorsShouldContainsLargeurDuSiteError($profil_vide) {
		$this->assertContains("La largeur du site doit être comprise entre 800 et 2000 pixels.",
													$profil_vide->getErrors());
	}


	/**
	 * @test
	 * @depends postingDataWithWrongData
	 */
	public function errorsShouldContainsSommeLargeursDivisionsError($profil_vide) {
		$this->assertContains("La somme des largeurs des divisions ne doit pas excéder la largeur du site.",
												$profil_vide->getErrors());
	}
}



class Admin_ProfilControllerProfilJeunesseModuleNoticeTest extends Admin_ProfilControllerProfilJeunesseTestCase {
	/** @test */
	public function viewShouldDisplayConfig() {
		$this->dispatch('/admin/modulesnotice/exemplaires?id_profil=5');
		$this->assertXPathContentContains("//select[@name='grouper']//option[@value='1'][@selected='selected']",
																			"Afficher une ligne par exemplaire");
		$this->assertXPathContentContains("//select[@name='bib']//option[@value='1'][@selected='selected']",
																			"oui");

	}


	/** @test */
	public function postShouldUpdateConfig() {
		$data = array('grouper' => 0,
									'section' => 0,
									'emplacement' => 0,
									'bib' => 1,
									'annexe' => 0,
									'dispo' => 1,
									'date_retour' => 1,
									'localisation' => 0,
									'plan' => 1,
									'resa' => 1);
		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/modulesnotice/exemplaires?id_profil=5');

		$this->assertEquals(array('exemplaires' => $data),
												$this->profil_jeunesse->getCfgNoticeAsArray());
	}
}



class Admin_ProfilControllerProfilJeunesseTestMenusIndex extends Admin_ProfilControllerProfilJeunesseTestCase {
	public function setUp() {
		parent::setUp();


		$profil_portail = new Class_Profil();
		$profil_portail
			->setId(1)
			->setLibelle('Portail');


		$profil_vide = new Class_Profil();
		$profil_vide
			->setId(34)
			->setLibelle('Vide');

		$this->profil_wrapper
			->whenCalled('findAllByZoneAndBib')
			->answers(array($profil_portail, $this->profil_jeunesse, $profil_vide));

		$this->dispatch('/admin/profil/menusindex?id_profil=5');
	}


	/** @test */
	public function menusindexPageShouldDisplayMenuVerticalAndHorizontal() {
		$this->assertQueryContentContains("tr.second td", "Menu horizontal");
		$this->assertQueryContentContains("tr.first td", "Menu");
	}


	/** @test */
	public function profilJeunessePanelShouldBeVisible() {
		$this->assertXPathContentContains("//div[contains(@class,'profils')]//ul//li//div",
																			"Profil Jeunesse",
																			$this->_response->getBody());
	}


	/** @test */
	public function profilJeunessePanelActionEditShouldBeVisible() {
		$this->assertXPath("//div[contains(@class,'profils')]//li//div[@class='actions']//a[contains(@href,'profil/edit/id_profil/5')]");
	}

	/** @test */
	public function editMenuHorizontalLink() {
		$this->assertXPath("//tr[@class='second']//td//a[contains(@href, 'menusmaj/id_profil/5/id_menu/H/mode/edit')]");
	}


	/** @test */
	public function cannotDeleteMenuHorizontalLink() {
		$this->assertNotXPath("//tr[@class='second']//td//a[contains(@href, 'menusmaj/id_profil/5/id_menu/H/mode/delete')]");
	}

	/** @test */
	public function editMenuVerticalLink() {
		$this->assertXPath("//tr[@class='first']//td//a[contains(@href, 'menusmaj/id_profil/5/id_menu/V/mode/edit')]");
	}


	/** @test */
	public function deleteMenuHorizontalLink() {
		$this->assertXPath("//tr[@class='first']//td//a[contains(@href, 'menusmaj/id_profil/5/id_menu/V/mode/delete')]");
	}


	/** @test */
	public function addMenuLink() {
		$this->assertXPath("//div[contains(@onclick, 'menusmaj/id_profil/5/mode/add')]");
	}
}




class Admin_ProfilControllerAddProfilHistoireTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		$this->profil_portail = Class_Profil::getLoader()
			->newInstanceWithId(1)
			->setBrowser('opac')
			->setCommentaire('Commentaire du portail')
			->setTitreSite('Médiathèque de Melun');

		$this->dispatch('/admin/profil/add');
	}


	/**
	 * @test
	 */
	public function shouldRenderActionAdd() {
		$this->assertAction('add');
	}


	/**
	 * @test
	 */
	public function pageTitleShouldBeAjouterUnProfil() {
		$this->assertQueryContentContains("head title", "Ajouter un profil");
	}


	/** @test */
	public function formActionShouldBeAdd() {
		$this->assertXPath("//form[contains(@action,'/admin/profil/add')]");
	}



	/** @test */
	public function commentaireShouldBeSameAsPortail() {
		$this->assertXPathContentContains("//textarea[@name='commentaire']",
																			'Commentaire du portail');

	}


	/** @test */
	public function profilPanelShouldNotBeVisible() {
		$this->assertNotXPathContentContains("//div[contains(@class,'profils')]//ul//li//div", "** Nouveau Profil **");
	}


	/** @test */
	public function postingValidDataShouldResultInProfilToBeValid() {
		$wrapper = Storm_Test_ObjectWrapper
			::onLoaderOfModel('Class_Profil')
			->whenCalled('save')
			->answers(true)
			->getWrapper();

		$data = array(	'libelle' => "Histoire",
										'id_site' => 1,
										'nb_divisions' => 2,
										'largeur_division1' => 400,
										'marge_division1' => 5,
										'largeur_division2' => 500,
										'marge_division2' => 8,
										'largeur_site' => 900,
										'access_level' => 6);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/profil/add');

		$new_profil = $wrapper->getFirstAttributeForLastCallOn('save');
		$this->assertTrue($new_profil->isValid());

		$this->assertRedirectTo('/admin/profil/edit/id_profil/'.$new_profil->getId()); // id_site=1 => par défaut

		return $new_profil;
	}


	/**
	 * @depends postingValidDataShouldResultInProfilToBeValid
	 * @test
	 */
	public function getNbDivisionsShouldReturnTwo($profil) {
		$this->assertEquals(2, $profil->getNbDivisions());
	}


	/**
	 * @depends postingValidDataShouldResultInProfilToBeValid
	 * @test
	 */
	public function getLibelleShouldReturnHistoire($profil) {
		$this->assertEquals("Histoire", $profil->getLibelle());
	}


	/**
	 * @depends postingValidDataShouldResultInProfilToBeValid
	 * @test
	 */
	public function getAccessLevelShouldReturnSix($profil) {
		$this->assertEquals(6, $profil->getAccessLevel());
	}


	/**
	 * @depends postingValidDataShouldResultInProfilToBeValid
	 * @test
	 */
	public function getLargeurSiteShouldReturnNineHundred($profil) {
		$this->assertEquals(900, $profil->getLargeurSite());
	}



	/** @test */
	public function profilShouldNotBeSavedIfPostingLargeurTooLow() {
		$wrapper = Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil');

		$data = array(	'libelle' => "Histoire",
										'id_site' => 1,
										'largeur_site' => 100);

		$this
			->getRequest()
			->setMethod('POST')
			->setPost($data);
		$this->dispatch('/admin/profil/add');
		$this->assertAction('add');

		$this->assertFalse($wrapper->methodHasBeenCalled('save'));
		$this->assertXPathContentContains('//ul[@class="errors"]//li',
																			'La largeur du site doit' /*être comprise entre 800 et 2000 pixels.*/,
																			$this->_response->getBody());
	}

}



abstract class Admin_ProfilControllerProfilJeunesseWithPagesTestCase extends Admin_ProfilControllerProfilJeunesseTestCase {
	public function setUp() {
		parent::setUp();


		$this->page_jeux = Class_Profil::getLoader()
			->newInstanceWithId(12)
			->setParentId($this->profil_jeunesse->getId())
			->setLibelle('Jeux');


		$this->page_musique = Class_Profil::getLoader()
			->newInstanceWithId(23)
			->setParentId($this->profil_jeunesse->getId())
			->setLibelle('Musique');

		$this->profil_jeunesse->setSubProfils(array($this->page_jeux,
																								$this->page_musique));

		Class_Profil::getLoader()
			->whenCalled('findAllByZoneAndBib')
			->answers(array($this->profil_jeunesse,
											$this->page_jeux,
											$this->page_musique));
	}
}


abstract class Admin_ProfilControllerProfilPanelTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	/** @test	 */
	public function profilsPanelShouldIncludeProfilJeunesse() {
		$this->assertXPathContentContains("//div[contains(@class, 'profils')]//div", "Jeunesse");
	}


	/** @test */
	public function profilsPanelShouldIncludePageAccueil() {
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[1]//img[contains(@src, 'home')]");
		$this->assertXPathContentContains("//div[contains(@class, 'profils')]//ul[1]//div", "Accueil");
		$this->assertXPath("//div[contains(@class, 'profils')]//ul[1]//a[contains(@href, 'profil/accueil/id_profil/5')]");
		$this->assertXPath("//div[contains(@class, 'profils')]//ul[1]//a[contains(@href, 'profil/copy/id_profil/5')]");
		$this->assertNotXPath("//div[contains(@class, 'profils')]//li//ul[1]//a[contains(@href, 'profil/delete/id_profil/5')]");
	}


	/** @test */
	public function profilsPanelShouldIncludePageJeux() {
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[2]//li[1]//img[contains(@src, 'page')]");
		$this->assertXPathContentContains("//div[contains(@class, 'profils')]//ul[2]//li[1]//div", "Jeux");
		$this->assertXPath("//div[contains(@class, 'profils')]//ul[2]//li[1]//a[contains(@href, 'profil/accueil/id_profil/12')]");
		$this->assertXPath("//div[contains(@class, 'profils')]//ul[2]//li[1]//a[contains(@href, 'profil/copy/id_profil/12')]");
		$this->assertXPath("//div[contains(@class, 'profils')]//ul[2]//li[1]//a[contains(@href, 'profil/delete/id_profil/12')]");
	}


	/** @test */
	public function profilsPanelShouldIncludePageMusique() {
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[2]//li[2]//img[contains(@src, 'page')]");
		$this->assertXPathContentContains("//div[contains(@class, 'profils')]//ul[2]//li[2]//div", "Musique");
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[2]//li[2]//a[contains(@href, 'profil/accueil/id_profil/23')]");
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[2]//li[2]//a[contains(@href, 'profil/copy/id_profil/23')]");
		$this->assertXPath("//div[contains(@class, 'profils')]//li//ul[2]//li[2]//a[contains(@href, 'profil/delete/id_profil/23')]");
	}
}



class Admin_ProfilControllerListProfilsJeunesseWithPagesTest extends Admin_ProfilControllerProfilPanelTest {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/profil/');
	}


	/** @test */
	public function shouldHaveOnlyOneProfilJeunessePanel() {
		$this->assertXPathCount("//div[contains(@class,'profils')]//li//div[@class='actions']//a[contains(@href,'profil/edit/id_profil/5')]",
														1);
	}
}



class Admin_ProfilControllerEditProfilJeunesseWithPagesTest extends Admin_ProfilControllerProfilPanelTest {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/profil/edit/id_profil/5');
	}
}



class Admin_ProfilControllerEditAccueilPageMusiqueTest extends Admin_ProfilControllerProfilPanelTest  {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/profil/accueil/id_profil/23');
	}

	/** @test */
	public function libelleFieldShouldBeVisible() {
		$this->assertXPath("//input[@type='text'][@name='libelle'][@value='Musique']");
	}

	/** @test */
	public function pageMusiqueShouldHaveClassSelected() {
		$this->assertXPathContentContains("//div[contains(@class, 'profils')]//li[2][@class='selected']//div", "Musique");
	}

	/** @test */
	public function pageJeuxShouldNotHaveClassSelected() {
		$this->assertNotXPath("//div[contains(@class, 'profils')]//li[1][@class='selected']//div");
	}
}



class Admin_ProfilControllerPostAccueilPageMusiqueTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		$this->profil_jeunesse->setCfgAccueil(array('modules' => array('1' => array('division' => 4,
																																								'type_module' => 'RECH_SIMPLE',
																																								'preferences' => array()))));



		$cfg_module = 'box1|new|KIOSQUE|nb_notices=12/nb_analyse=36/only_img=1/;box2|new|CRITIQUES|';

		$this
			->getRequest()
			->setMethod('POST')
			->setPost(array('saveContent' => $cfg_module,
											'libelle' => 'Bonne Musique'));
		$this->dispatch('/admin/profil/accueil/id_profil/23');
	}


	/** @test */
	public function libelleShouldBeBonneMusique() {
		$this->assertEquals('Bonne Musique', $this->page_musique->getLibelle());
	}


	/** @test */
	public function moduleKiosqueShouldBeAdded() {
		$kiosque = array_first($this->page_musique->getBoitesDivision(1));
		$this->assertEquals('KIOSQUE', $kiosque['type_module']);
	}


	/** @test */
	public function moduleCritiquesShouldBeAdded() {
		$critiques = array_first($this->page_musique->getBoitesDivision(2));
		$this->assertEquals('CRITIQUES', $critiques['type_module']);
	}


	/** @test */
	public function cfgAccueilShouldHaveModulesSizeOfTwo() {
		$cfg = $this->page_musique->getCfgAccueilAsArray();
		$this->assertEquals(2, count($cfg['modules']));
	}


	/** @test */
	public function boitesBanniereShouldReturnBoiteRecherche() {
		$boites_banniere = $this->page_musique->getBoitesDivision(Class_Profil::DIV_BANNIERE);
		$boite_rech = array_first($boites_banniere);
		$this->assertEquals('RECH_SIMPLE', $boite_rech['type_module']);
	}

}



class Admin_ProfilControllerCopyPageMusiqeTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		$this
			->page_musique
			->setCfgAccueil(array('modules' => array(
																							 '6' => array('division' => 2,
																														'type_module' => 'CRITIQUES',
																														'preferences' => array()))));

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$_SERVER['HTTP_REFERER'] = BASE_URL.'admin/profil/edit/id_profil/23';
		$this->dispatch('/admin/profil/copy/id_profil/23');

		$this->new_page = Class_Profil::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function assertRedirectToAccueilConfig() {
		$this->assertRedirect('admin/profil/accueil/');
	}


	/** @test */
	public function parentShouldBeProfilJeunesse() {
		$this->assertEquals(5, $this->new_page->getParentId());
	}


	/** @test */
	public function cfgAccueilShouldBeSameAsPageMusique() {
		$this->assertEquals($this->page_musique->cfg_accueil,
												$this->new_page->cfg_accueil);
	}

	/** @test */
	public function libelleShouldBeMusiqueCopie() {
		$this->assertEquals('Musique - copie', $this->new_page->getLibelle());
	}
}



class Admin_ProfilControllerCopyPageJeuxTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$_SERVER['HTTP_REFERER'] = BASE_URL.'admin/profil';
		$this->dispatch('/admin/profil/copy/id_profil/12');

		$this->new_page = Class_Profil::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function assertRedirectToAccueilConfig() {
		$this->assertRedirect('admin/profil');
	}


	/** @test */
	public function parentShouldBeProfilJeunesse() {
		$this->assertEquals(5, $this->new_page->getParentId());
	}
}



class Admin_ProfilControllerCopyProfilJeunesseTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$_SERVER['HTTP_REFERER'] = BASE_URL.'admin/profil';
		$this->dispatch('/admin/profil/copy/id_profil/5');

		$this->new_page = Class_Profil::getLoader()->getFirstAttributeForLastCallOn('save');
	}


	/** @test */
	public function assertRedirectToAccueilConfig() {
		$this->assertRedirect('admin/profil');
	}


	/** @test */
	public function parentShouldBeProfilJeunesse() {
		$this->assertEquals(5, $this->new_page->getParentId());
	}

	/** test */
	public function libelleShouldBeAccueilCopie() {
		$this->assertEquals('Accueil - copie', $this->new_page->getLibelle());
	}

	/** @nontest */
	public function libelleShouldBeProfilJeunesse() {
		$this->assertEquals('Profil Jeunesse', $this->new_page->getLibelle());
	}
}




class Admin_ProfilControllerMovePageMusiqueToPageJeuxTest extends Admin_ProfilControllerProfilJeunesseWithPagesTestCase {
	public function setUp() {
		parent::setUp();

		Class_Profil::getLoader()
			->whenCalled('save')
			->answers(true);

		$this->dispatch('/admin/profil/move/id_profil/23/to/12');
	}


	/** @test */
	public function testPageMusiqueParentIsPageJeux() {
		$this->assertEquals($this->page_jeux,
												$this->page_musique->getParentProfil());
	}
}




class Admin_ProfilControllerNonExistingProfileTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Profil')
			->whenCalled('find')->with(999)->answers(null);
	}


	/** @test */
	public function proprieteShouldRedirectToIndex() {
		$this->dispatch('/admin/profil/proprietes/id_profil/999');
		$this->assertRedirectTo('/admin/profil');
	}
}



class Admin_ProfilControllerGenresActionTest extends Admin_AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/profil/genres');
	}

	/** @test */
	function actionShouldBeGenres() {
		$this->assertAction('genres');
	}
}