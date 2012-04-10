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

abstract class Admin_UserGroupControllerTestCase extends Admin_AbstractControllerTestCase {
	protected $_spiderman;

	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()
			->newInstanceWithId('FORMATIONS')
			->setValeur('1'); //pour l'instant juste pour Camélia
		
		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_UserGroup')
			->whenCalled('save')->answers(true)
			->whenCalled('delete')->answers(true)

			->whenCalled('findAllBy')
			->with(array('order' => 'libelle'))
			->answers(array(Class_UserGroup::getLoader()
											->newInstanceWithId(3)
											->setLibelle('Stagiaires')
											->setRights(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION))
											->setUsers(array( Class_Users::getLoader()
																				->newInstanceWithId(31)
																				->setLogin('batman')
																				->setNom('Wayne')
																				->setPrenom('Bruce'),
																				
																				$this->_spiderman = Class_Users::getLoader()
																				->newInstanceWithId(32)
																				->setLogin('spiderman')
																				->setNom('Parker')
																				->setPrenom('Peeter'))),
											
											Class_UserGroup::getLoader()
											->newInstanceWithId(5)
											->setLibelle('Chercheurs')
											->setUsers(array())
											->setRights(array())
											));
	}
}




class Admin_UserGroupControllerIndexTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/usergroup');
	}


	/** @test */
	public function aTDInFirstRowShouldContainsStagiaires() {
		$this->assertXPathContentContains('//tr[1][@class="first"]//td', 'Stagiaires');
	}


	/** @test */
	public function firstTRShouldHaveALinkToEditGroupStagiaires() {
		$this->assertXPath('//tr[1][@class="first"]//td//a[contains(@href, "admin/usergroup/edit/id/3")]');
	}


	/** @test */
	public function firstTRShouldHaveALinkToDeleteGroupStagiaires() {
		$this->assertXPath('//tr[1][@class="first"]//td//a[contains(@href, "admin/usergroup/delete/id/3")]');
	}


	/** @test */
	public function firstTRShouldHaveALinkToEditMembersGroupStagiaires() {
		$this->assertXPath('//tr[1][@class="first"]//td//a[contains(@href, "admin/usergroup/editmembers/id/3")]');
	}


	/** @test */
	public function firstTRShouldContainsRightSuivreUneFormation() {
		$this->assertXPathContentContains('//tr[1][@class="first"]//td//a[contains(@href, "admin/usergroup/edit/id/3")]', 
																			'Suivre une formation');
	}


	/** @test */
	public function firstTRShouldIndicatesTwoMembers() {
		$this->assertXPathContentContains('//tr[1][@class="first"]//td//a', '2');
	}


	/** @test */
	public function aTDInSecondRowShouldContainsChercheursShouldExpectation() {
		$this->assertXPathContentContains('//tr[2][@class="second"]//td', 'Chercheurs');
	}


	/** @test */
	public function aButtonShouldLinkToAddUserGroup() {
		$this->assertXPath('//div[contains(@onclick, "admin/usergroup/add")]');
	}


	/** @test */
	public function menuGaucheAdminShouldHaveAnEntryForGroups() {
		$this->assertXPathContentContains('//ul[@class="menuAdmin"]//a[contains(@href, "admin/usergroup")]', 
																			'Groupes');
	}


	/** @test */
	public function titleShouldBeGestionDesGroupes() {
		$this->assertXPathContentContains('//h1', "Gestion des groupes d'utilisateurs");
	}
}



class Admin_UserGroupControllerAddTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/usergroup/add');
	}


	/** @test */
	public function formShouldContainsAnInputForLibelle() {
		$this->assertXPath('//input[@name="libelle"]');
	}


	/** @test */
	public function formActionShouldBeAddUserGroup() {
		$this->assertXPath('//form[contains(@action,"admin/usergroup/add")]');
	}


	/** @test */
	public function titreShouldBeAjouterUnGroupeDUtilisateurs() {
		$this->assertXPathContentContains('//h1', "Ajouter un groupe d'utilisateurs");
	}


	/** @test */
	public function aCheckBoxShouldContainsRightSuivreAUneFormation() {
		$this->assertXPath('//input[@name="rights[]"][@value="1"]');	
		$this->assertXPathContentContains('//label', 'Suivre une formation');	
	}


	/** @test */
	public function aCheckBoxShouldContainsRightDirigerUneFormation() {
		$this->assertXPath('//input[@name="rights[]"][@value="2"]');	
		$this->assertXPathContentContains('//label', 'Diriger une formation');	
	}
}



class Admin_UserGroupControllerAddPostTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('admin/usergroup/add',
												array('libelle' => 'Intervenants'));
	}


	/** @test */
	public function newGroupShouldBeCreatedWithLibelleIntervenants() {
		$this->assertEquals('Intervenants', 
												Class_UserGroup::getLoader()->getFirstAttributeForLastCallOn('save')->getLibelle());
	}


	/** @test */
	public function responseShouldRedirectToDefaultAction() {
		$this->assertRedirectTo('/admin/usergroup');
	}
}


class Admin_UserGroupControllerAddPostInvalidDataTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->postDispatch('admin/usergroup/add',
												array('libelle' => ''));
	}


	/** @test */
	public function newGroupShouldNotBeCreated() {
		$this->assertFalse(Class_UserGroup::getLoader()->methodHasBeenCalled('save'));
	}


	/** @test */
	public function errorsShouldContainsUneValeurEstRequise() {
		$this->assertXPathContentContains('//ul[@class="errors"]', 'Une valeur est requise');
	}
}



class Admin_UserGroupControllerEditGroupStagiairesTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/usergroup/edit/id/3');
	}


	/** @test */
	public function inputLibelleShouldContainsStagiaires() {
		$this->assertXPath('//input[@name="libelle"][@value="Stagiaires"]');
	}


	/** @test */
	public function formActionShouldBeEditUserGroupIdThree() {
		$this->assertXPath('//form[contains(@action,"admin/usergroup/edit/id/3")]');
	}


	/** @test */
	public function titreShouldBeModifierLeGroupeStagiaires() {
		$this->assertXPathContentContains('//h1', "Modifier le groupe d'utilisateurs: Stagiaires");
	}


	/** @test */
	public function rightSuivreFormationShouldBeChecked() {
		$this->assertXPath('//input[@name="rights[]"][@value="1"][@checked="checked"]');
	}
}



class Admin_UserGroupControllerEditMembersGroupStagiairesTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Users')
			->whenCalled('findAll')
			->with("select bib_admin_users.* from bib_admin_users ".
						 "where (nom like '%spid%' or prenom like '%spid%' or login like '%spid%') ".
 						 "order by nom, prenom, login limit 500")
			->answers(array($this->_spiderman));

		$this->dispatch('admin/usergroup/editmembers/id/3/search/Spid');
	}


	/** @test */
	public function titleShouldBeMembresDuGroupeStagiaires() {
		$this->assertXPathContentContains("//h1", 'Membres du groupe: Stagiaires');
	}


	/** @test */
	public function tdShouldContainsBatman() {
		$this->assertXPathContentContains('//td', 'batman');
	}


	/** @test */
	public function libelleSearchFormShouldBeRechercherDesUtilisateurs() {
		$this->assertXPathContentContains('//label[@for="search"]', 'Rechercher des utilisateurs');
	}


	/** @test */
	public function searchInputShouldContainsSpid() {
		$this->assertXPath('//input[@name="search"][@value="Spid"]');
	}


	/** @test */
	public function inputSubmitSearchFormShouldBeAjouterLesUtilisateursSelectionnes() {
		$this->assertXPath('//form[@id="user_subscribe"]//input[@value="Ajouter les utilisateurs sélectionnés"]');
	}

	
	/** @test */
	public function userSubcribeFormShouldContainsCheckBoxWithSpiderman() {
		$this->assertXPathContentContains('//form[@id="user_subscribe"]//label', 'Parker Peeter - spiderman',
																			Class_Users::getLoader()->getFirstAttributeForLastCallOn('findAll'));	
	}
}




class Admin_UserGroupControllerAddMemberSupermanGroupStagiairesTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		Class_Users::getLoader()
			->newInstanceWithId(56)
			->setLogin('superman')
			->setNom('Kent')
			->setPrenom('Clark');

		$this->postDispatch('/admin/usergroup/editmembers/id/3',
												array('users' => array(56)));
	}

	/** @test */
	public function groupStagiairesShouldIncludeSuperman() {
		$this->assertEquals('superman',
												array_last(Class_UserGroup::getLoader()->find(3)->getUsers())->getLogin());
	}


	/** @test */
	public function groupShouldHaveBeenSaved() {
		$this->assertEquals('Stagiaires', 
												Class_UserGroup::getLoader()->getFirstAttributeForLastCallOn('save')->getLibelle());
	}
}




class Admin_UserGroupControllerDeleteMemberSpidermanFromGroupStagiairesTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('/admin/usergroup/editmembers/id/3/delete/32?search=Spid');
	}


	/** @test */
	public function spidermanShouldNotBeInGroupStagiaires() {
		$this->assertNotContains($this->_spiderman, Class_UserGroup::getLoader()->find(3)->getUsers());
	}


	/** @test */
	public function responseShouldRedirectToEditMembersIdThree() {
		$this->assertRedirectTo('/admin/usergroup/editmembers/id/3?search=Spid');
	}
}




class Admin_UserGroupControllerEditStagiairesPostDataTest extends Admin_UserGroupControllerTestCase {
	protected $_group;
	public function setUp() {
		parent::setUp();
		$this->postDispatch('admin/usergroup/edit/id/3',
												array('libelle' => 'étudiants',
															'rights' => array(Class_UserGroup::RIGHT_SUIVRE_FORMATION,
																								Class_UserGroup::RIGHT_DIRIGER_FORMATION)));
		$this->_group = Class_UserGroup::getLoader()->find(3);
	}


	/** @test */
	public function groupeNewLibelleShouldEtudiants() {
		$this->assertEquals('étudiants', $this->_group->getLibelle());
	}


	/** @test */
	public function groupRightsShouldContainsSuivreAndDiriger() {
		$this->assertEquals(array(Class_UserGroup::RIGHT_SUIVRE_FORMATION,
															Class_UserGroup::RIGHT_DIRIGER_FORMATION),
												$this->_group->getRights());
	}


	/** @test */
	public function responseShouldRedirectToDefaultAction() {
		$this->assertRedirectTo('/admin/usergroup');
	}
}




class Admin_UserGroupControllerDeleteStagiairesTest extends Admin_UserGroupControllerTestCase {
	public function setUp() {
		parent::setUp();
		$this->dispatch('admin/usergroup/delete/id/3');
	}


	/** @test */
	public function loaderShouldHaveDeletedTheGroup() {
		$this->assertEquals('Stagiaires', 
												Class_UserGroup::getLoader()
												->getFirstAttributeForLastCallOn('delete')
												->getLibelle());
	}


	/** @test */
	public function responseShouldRedirectToDefaultAction() {
		$this->assertRedirectTo('/admin/usergroup');		
	}
}



class Admin_UserGroupControllerWrongIdsTest extends Admin_UserGroupControllerTestCase {
	/** @test */
	public function editInexistentGroupShouldRedirectToIndex() {
		$this->dispatch('/admin/usergroup/edit/id/999999');
		$this->assertRedirectTo('/admin/usergroup');		
	}


	/** @test */
	public function editMembersInexistentGroupShouldRedirectToIndex() {
		$this->dispatch('/admin/usergroup/editmembers/id/999999');
		$this->assertRedirectTo('/admin/usergroup');		
	}


	/** @test */
	public function deleteInexistentGroupShouldRedirectToIndex() {
		$this->dispatch('/admin/usergroup/delete/id/999999');
		$this->assertRedirectTo('/admin/usergroup');		
	}
}


?>