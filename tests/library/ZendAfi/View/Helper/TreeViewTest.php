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
require_once realpath(dirname(__FILE__)) . '/ViewHelperTestCase.php';

abstract class TreeViewTestCase extends ViewHelperTestCase {
	/** @var ZendAfi_View_Helper_TreeView */
	protected $_helper;

	/** @var string */
	protected $_html;

	public function setUp() {
		parent::setUp();
		$this->_helper = new ZendAfi_View_Helper_TreeView();
		$this->_helper->setView(new ZendAfi_Controller_Action_Helper_View());
	}
}




abstract class TreeViewContainersTestCase extends TreeViewTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('findAllBy')
			->answers(array());
	}
}




class TreeViewContainersTest extends TreeViewContainersTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
			->whenCalled('findAllBy')
			->answers(array());

	}


	/** @test */
	public function withNoContainersShouldReturnEmptyString() {
		$this->assertEquals('', $this->_helper->treeView(array()));
	}


	/** @test */
	public function withAContainerShouldReturnUlWithContainerLabel() {
		$html = $this->_helper->treeView(TreeViewFixtures::createOneCategoryWithoutItems());

		$this->assertXpathContentContains($html, '//ul//li', utf8_encode('Actualités'), $html);

	}


	/** @test */
	public function withTwoContainerShouldReturnUlWithTwoLi() {
		$html = $this->_helper->treeView(TreeViewFixtures::createTwoCategoriesWithoutItems());

		$this->assertQueryCount($html, '//ul//li', 2, $html);
	}
}




class TreeViewContainersWithoutItemsActionsTest extends TreeViewContainersTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
											TreeViewFixtures::createOneCategoryWithoutItems(),
											TreeViewFixtures::createContainerActions()
										);
	}


	/** @test */
	public function accordionLabelShouldBePortail() {
		$this->assertXpathContentContains($this->_html, '//div[@class="tree"]/h3/a[@href="#"]', 'Portail');
	}


	/** @test */
	public function addToRootLinkShouldBePresent() {
		$this->assertXpath($this->_html, '//a[contains(@href, "admin/cms/catadd/id_bib/0")]');
	}


	/** @test */
	public function editActionShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/catedit/id/1")]');
	}


	/** @test */
	public function deleteActionShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/catdel/id/1")]');
	}

	/** @test */
	public function deleteActionShouldHaveConfirmationJavascript() {
		$this->assertXpath($this->_html,
				'//a[contains(@href, "admin/cms/catdel/id/1")][@onclick]', $this->_html);
	}


	/** @test */
	public function addItemActionShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/newsadd/id_cat/1")]');
	}


	/** @test */
	public function addSubContainerActionShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/catadd/id/1")]');
	}


	/** @test */
	public function filterInputShouldBePresent() {
		$this->assertXpath($this->_html, '//input[@class="treeViewSearch"]');
	}
}




class TreeViewTwoNestedContainersTest extends TreeViewContainersTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
										TreeViewFixtures::createTwoNestedCategoriesWithoutItems()
									);
	}


	/** @test */
	public function htmlShouldContainsTwoNestedUl() {
		$this->assertXpath($this->_html, '//ul/li/ul/li');
	}


	/** @test */
	public function firstLiShouldContainActualites() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul[1]/li',
			utf8_encode('Actualités')
		);
	}


	/** @test */
	public function secondLiShouldContainAnimations() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul[1]/li',
			'Animations'
		);
	}
}




class TreeViewFiveNestedContainersTest extends TreeViewContainersTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
										TreeViewFixtures::createFiveNestedCategoriesWithoutItems()
									);
	}


	/** @test */
	public function htmlShouldContainFiveNestedUl() {
		$this->assertXpath($this->_html, '//ul/li/ul/li/ul/li/ul/li/ul/li');
	}


	/** @test */
	public function lastLiShouldContainDeLOuest() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li/ul/li/ul/li/ul/li',
			'De l\'ouest'
		);
	}
}




abstract class TreeViewItemsTestCase extends TreeViewTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_ArticleCategorie')
			->whenCalled('findAllBy')
			->answers(array());
	}
}




class TreeViewItemsWithoutWorkflowTest extends TreeViewItemsTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')
															->setValeur(0);

		$this->_html = $this->_helper->treeView(
											TreeViewFixtures::createOneCategoryWithItems()
									);
	}


	/** @test */
	public function twoItemsLiShouldBePresent() {
		$this->assertQueryCount($this->_html, '//ul/li/ul/li', 2);
	}


	/** @test */
	public function feteDeLaBiereShouldBePresent() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li',
			utf8_encode('La fête de la bière')
		);
	}


	/** @test */
	public function feteDeLaBiereStatusClassShouldBeValidated() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li[contains(@class, "status-3")]',
			utf8_encode('La fête de la bière')
		);
	}


	/** @test */
	public function harlockShouldBePresent() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li',
			utf8_encode('Avant-première Captain Harlock 3D')
		);
	}


	/** @test */
	public function harlockStatusClassShouldBeValidated() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li[contains(@class, "status-3")]',
			utf8_encode('Avant-première Captain Harlock 3D')
		);
	}


	/** @test */
	public function filterByStatusShouldNotBePresent() {
		$this->assertNotXpath($this->_html, '//div[@class="treeViewSearchStatus"]');
	}
}




class TreeViewItemsWithWorkflowTest extends TreeViewItemsTestCase {
	public function setUp() {
		parent::setUp();

		Class_AdminVar::getLoader()->newInstanceWithId('WORKFLOW')
															->setValeur(1);

		$fixture = TreeViewFixtures::createOneCategoryWithItems();

		Class_Article::getLoader()->find(1)->beDraft();
		Class_Article::getLoader()->find(2)->beArchived();

		$this->_html = $this->_helper->treeView($fixture);
	}


	/** @test */
	public function feteDeLaBiereStatusClassShouldBeDraft() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li[contains(@class, "status-'
																				. Class_Article::STATUS_DRAFT . '")]',
			utf8_encode('La fête de la bière')
		);
	}


	/** @test */
	public function harlockStatusClassShouldBeArchived() {
		$this->assertXpathContentContains(
			$this->_html,
			'//ul/li/ul/li[contains(@class, "status-'
																				. Class_Article::STATUS_ARCHIVED . '")]',
			utf8_encode('Avant-première Captain Harlock 3D')
		);
	}


	/** @test */
	public function filterByStatusShouldBePresent() {
		$this->assertXpath($this->_html, '//div[@class="treeViewSearchStatus"]');
	}


	/** @test */
	public function filterByStatusShouldContainDraftLink() {
		$this->assertXpath(
			$this->_html,
			'//div[@class="treeViewSearchStatus"]/a[@rel="status-'
																						. Class_Article::STATUS_DRAFT . '"]'
		);
	}


	/** @test */
	public function filterByStatusShouldContainValidationPendingLink() {
		$this->assertXpath(
			$this->_html,
			'//div[@class="treeViewSearchStatus"]/a[@rel="status-'
															. Class_Article::STATUS_VALIDATION_PENDING . '"]'
		);
	}


	/** @test */
	public function filterByStatusShouldContainValidatedLink() {
		$this->assertXpath(
			$this->_html,
			'//div[@class="treeViewSearchStatus"]/a[@rel="status-'
																				. Class_Article::STATUS_VALIDATED . '"]'
		);
	}


	/** @test */
	public function filterByStatusShouldContainArchivedLink() {
		$this->assertXpath(
			$this->_html,
			'//div[@class="treeViewSearchStatus"]/a[@rel="status-'
																				. Class_Article::STATUS_ARCHIVED . '"]'
		);
	}

	/** @test */
	public function filterByStatusShouldContainAllLink() {
		$this->assertXpath(
			$this->_html,
			'//div[@class="treeViewSearchStatus"]/a[@rel="status-all"]'
		);
	}
}




class TreeViewItemsActionsTest extends TreeViewItemsTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
										TreeViewFixtures::createOneCategoryWithItems(),
										array(),
										TreeViewFixtures::createItemActions()
									);
	}


	/** @test */
	public function editActionOfFirstItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/newsedit/id/1")]');
	}


	/** @test */
	public function deleteActionOfFirstItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/delete/id/1")]');
	}


	/** @test */
	public function makeInvisibleActionOfFirstItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/makeinvisible/id/1")]');
	}


	/** @test */
	public function makeVisibleActionOfFirstItemShouldNotBePresent() {
		$this->assertNotXpath($this->_html,
												'//a[contains(@href, "admin/cms/makevisible/id/1")]');
	}


	/** @test */
	public function editActionOfSecondItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/newsedit/id/2")]');
	}


	/** @test */
	public function deleteActionOfSecondItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/delete/id/2")]');
	}


	/** @test */
	public function makeInvisibleActionOfSecondItemShouldNotBePresent() {
		$this->assertNotXpath($this->_html,
												'//a[contains(@href, "admin/cms/makeinvisible/id/2")]');
	}


	/** @test */
	public function makeVisibleActionOfSecondItemShouldBePresent() {
		$this->assertXpath($this->_html,
												'//a[contains(@href, "admin/cms/makevisible/id/2")]');
	}
}




abstract class TreeViewNestedContainersWithItemsTestCase
extends TreeViewTestCase {
	public function setUp() {
		parent::setUp();

		Storm_Test_ObjectWrapper::onLoaderOfModel('Class_Article')
			->whenCalled('findAllBy')
			->answers(array());
	}
}




class TreeViewNestedContainersWithItemsTest
extends TreeViewNestedContainersWithItemsTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
									TreeViewFixtures::createNestedCategoriesWithItems()
								);
	}


	/** @test */
	public function nineLiShouldBePresent() {
		$this->assertQueryCount($this->_html, '//li', 9, $this->_html);
	}
}




class TreeViewNestedContainersWithItemsActionsTest
extends TreeViewNestedContainersWithItemsTestCase {
	public function setUp() {
		parent::setUp();

		$this->_html = $this->_helper->treeView(
										TreeViewFixtures::createNestedCategoriesWithItems(),
										TreeViewFixtures::createContainerActions(),
										TreeViewFixtures::createItemActions()
									);

	}


	/** @test */
	public function firstContainerDeleteActionShouldBeAbsent() {
		$this->assertNotXpath(
			$this->_html,
			'//a[contains(@href, "/catdel/id/1")]',
			$this->_html
		);
	}
}




class TreeViewFixtures {
	/** @return array */
	public static function createItemActions() {
		return array(
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'newsedit',
				'icon'			=> 'ico/edit.gif',
				'label'			=> 'Modifier',
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'delete',
				'icon'			=> 'ico/del.gif',
				'label'			=> 'Supprimer',
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'makeinvisible',
				'icon'			=> 'ico/show.gif',
				'label'			=> 'Rendre cet article invisible',
				'condition' => 'isVisible'
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'makevisible',
				'icon'			=> 'ico/hide.gif',
				'label'			=> 'Rendre cet article visible',
				'condition' => 'isNotVisible'
			)
		);
	}

	/** @return array */
	public static function createContainerActions() {
		return array(
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'catedit',
				'icon'			=> 'ico/edit.gif',
				'label'			=> 'Modifier'
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'catdel',
				'icon'			=> 'ico/del.gif',
				'label'			=> 'Supprimer',
				'condition' => 'hasNoChild',
				'anchorOptions' => array(
					'onclick' => 'return confirm("are you sure ?");'
				)
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'newsadd',
				'idName'		=> 'id_cat',
				'icon'			=> 'ico/add_news.gif',
				'label'			=> 'Ajouter un article',
			),
			array(
				'module'		=> 'admin',
				'controller'=> 'cms',
				'action'		=> 'catadd',
				'icon'			=> 'ico/add_cat.gif',
				'label'			=> 'Ajouter une sous-catégorie'
			),
		);
	}


	/** @return array */
	public static function createNestedCategoriesWithItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(1)
					->setLibelle('Actualités')
					->setArticles(array(
						Class_Article::getLoader()
							->newInstanceWithId(1)
							->setTitre('La fête de la bière')
					))
					->setSousCategories(array(
						Class_ArticleCategorie::getLoader()
							->newInstanceWithId(2)
							->setLibelle('Animations')
							->setArticles(array(
								Class_Article::getLoader()
									->newInstanceWithId(2)
									->setTitre('La fête de la frite'),
								Class_Article::getLoader()
									->newInstanceWithId(3)
									->setTitre('Avant-première Captain Harlock 3D')
							))
							->setSousCategories(array(
								Class_ArticleCategorie::getLoader()
									->newInstanceWithId(3)
									->setLibelle('Folklore')
									->setSousCategories(array(
										Class_ArticleCategorie::getLoader()
											->newInstanceWithId(4)
											->setLibelle('Occitan')
											->setArticles(array(
												Class_Article::getLoader()
													->newInstanceWithId(4)
													->setTitre('Sinsemilia en concert')
											))
											->setSousCategories(array(
												Class_ArticleCategorie::getLoader()
													->newInstanceWithId(5)
													->setLibelle('De l\'ouest')
													->setSousCategories(array())
											))
									))
							))
					))
				)
		));
	}

	/** @return array */
	public static function createFiveNestedCategoriesWithoutItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(1)
					->setLibelle('Actualités')
					->setSousCategories(array(
						Class_ArticleCategorie::getLoader()
							->newInstanceWithId(2)
							->setLibelle('Animations')
							->setSousCategories(array(
								Class_ArticleCategorie::getLoader()
									->newInstanceWithId(3)
									->setLibelle('Folklore')
									->setSousCategories(array(
										Class_ArticleCategorie::getLoader()
											->newInstanceWithId(4)
											->setLibelle('Occitan')
											->setSousCategories(array(
												Class_ArticleCategorie::getLoader()
													->newInstanceWithId(5)
													->setLibelle('De l\'ouest')
											))
									))
							))
					))
				)
		));
	}

	/** @return array */
	public static function createTwoNestedCategoriesWithoutItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(1)
					->setLibelle('Actualités')
					->setSousCategories(array(
						Class_ArticleCategorie::getLoader()
							->newInstanceWithId(2)
							->setLibelle('Animations')
					))
			)
		));
	}

	/** @return array */
	public static function createOneCategoryWithItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(1)
					->setLibelle('Actualités')
					->setArticles(array(
						Class_Article::getLoader()
							->newInstanceWithId(1)
							->setTitre('La fête de la bière')
							->setDebut('')
							->setFin(''),
						Class_Article::getLoader()
							->newInstanceWithId(2)
							->setTitre('Avant-première Captain Harlock 3D')
							->setDebut('')
							->setFin('2010-10-10')
					))
			)
		));
	}

	/** @return array */
	public static function createOneCategoryWithoutItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(Class_ArticleCategorie::getLoader()
														->newInstanceWithId(1)
														->setLibelle('Actualités')
														->setSousCategories(array())
														->setArticles(array())),
			'add_link' => '<a href="admin/cms/catadd/id_bib/0">Ajouter une categorie</a>'
		));
	}

	/** @return array */
	public static function createTwoCategoriesWithoutItems() {
		return array(array(
			'bib' => Class_Bib::getLoader()->newInstanceWithId(0)->setLibelle('Portail'),
			'containers' => array(
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(1)
					->setLibelle('Actualités'),
				Class_ArticleCategorie::getLoader()
					->newInstanceWithId(2)
					->setLibelle('Animations')
			)
		));
	}
}