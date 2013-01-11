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

class TreeViewFixtures {
	/** @return array */
	public static function createItemActions() {
		return array(
			array(
				'url' => 'admin/cms/newsedit/id/%s',
				'icon'			=> 'ico/edit.gif',
				'label'			=> 'Modifier',
			),
			array(
				'url' => 'admin/cms/delete/id/%s',
				'icon'			=> 'ico/del.gif',
				'label'			=> 'Supprimer',
			),
			array(
				'url' => 'admin/cms/makeinvisible/id/%s',
				'icon'			=> 'ico/show.gif',
				'label'			=> 'Rendre cet article invisible',
				'condition' => 'isVisible'
			),
			array(
				'url' => 'admin/cms/makevisible/id/%s',
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
				'url' => '/admin/cms/catedit/id/%s',
				'icon'			=> 'ico/edit.gif',
				'label'			=> 'Modifier'
			),
			array(
				'url' => '/admin/cms/catdel/id/%s',
				'icon'			=> 'ico/del.gif',
				'label'			=> 'Supprimer',
				'condition' => 'hasNoChild',
				'anchorOptions' => array(
					'onclick' => 'return confirm("are you sure ?");'
				)
			),
			array(
				'url' => '/admin/cms/newsadd/id_cat/%s',
				'icon'			=> 'ico/add_news.gif',
				'label'			=> 'Ajouter un article',
			),
			array(
				'url' => '/admin/cms/catadd/id/%s',
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