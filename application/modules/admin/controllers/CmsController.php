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
class Admin_CmsController extends Zend_Controller_Action {
	/** @var Class_Bib */
	private $_bib;

	public function init() {
		$identity = Class_Users::getLoader()->getIdentity();

		if (ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB >= $identity->getRoleLevel()) {
			$this->_bib = $identity->getBib();

		} else {
			$this->_bib = Class_Bib::getLoader()->getPortail();
		}
	}


	public function indexAction()	{
		$identity = Class_Users::getLoader()->getIdentity();

		if (0 != $this->_bib->getId()) {
			$bibs = array($this->_bib);

		} else {
			$bibs = Class_Bib::getLoader()->findAllBy(array('order' => 'libelle'));
			array_unshift($bibs, $this->_bib);
		}

		$categories = array();

		$add_link_label = $this->view->tagImg(URL_ADMIN_IMG . 'ico/add_cat.gif')
			. $this->view->_(' Ajouter une catégorie');

		$add_link_options = array('module' => 'admin',
															'controller' => 'cms',
															'action' => 'catadd');

		foreach ($bibs as $bib) {
			$categories[] = array('bib'=> $bib,
														'containers' => $bib->getArticleCategories(),
														'add_link' => $this->view->tagAnchor($this->view->url(array_merge($add_link_options, array('id_bib' => $bib->getId()))),
																																 $add_link_label));
		}

		$this->view->categories = $categories;

		$this->view->categorieActions = $this->_getTreeViewContainerActions();
		$this->view->articleActions = $this->_getTreeViewItemActions();

		$this->view->titre = 'Mise à jour des articles';

		$this->view->headScript()->appendScript('var treeViewSelectedCategory = '
																			. (int)$this->_getParam('id_cat') . ';');
		$this->view->headScript()->appendFile(URL_ADMIN_JS . 'tree-view.js');

	}


	public function cataddAction() {
		$category = Class_ArticleCategorie::getLoader()
										->newInstance()
										->setLibelle('');
		$parent = Class_ArticleCategorie::getLoader()->find((int)$this->_getParam('id'));

		if (null !== $parent) {
			$category->setParentCategorie($parent);
			// j'essaye d'avoir la même bib que mon parent
			$category->setBib($parent->getBib());
		} else {
			$category->setBib(
				(0 === $this->_bib->getId())
					? Class_Bib::getLoader()->find((int)$this->_getParam('id_bib'))
					: $this->_bib
			);
		}

		if ($this->_isCategorySaved($category)) {
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $category->getIdCatMere()));
			return;
		}

		$this->view->category = $category;
		$this->view->titre = "Ajouter une catégorie d'articles";
	}

	public function cateditAction() {
		$category = Class_ArticleCategorie::getLoader()->find((int)$this->_getParam('id'));

		if ((null == $category) || ($this->_isCategorySaved($category))) {
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $category->getId()));
			return;
		}

		if (null === $category->getBib()) {
			$category->setBib($this->_bib);
		}

		$this->view->category = $category;
		$this->view->combo_cat = $this->_getParentCategoryInput($category);

		$this->view->titre = "Modifier une catégorie d'articles";
	}

	/**
	 * @param Class_ArticleCategorie $category
	 * @return bool
	 */
	protected function _isCategorySaved($category) {
		if ($this->_request->isPost()) {
			$post = $this->_request->getPost();
			$filter = new Zend_Filter_StripTags();
			$post['libelle'] = trim($filter->filter($this->_request->getPost('libelle')));

			return $category
				->updateAttributes($post)
				->save();
		}

		return false;
	}


	public function catdelAction() {
		$categorie = Class_ArticleCategorie::getLoader()->find((int)$this->_getParam('id'));
		if (null !== $categorie) {
			$categorie->delete();
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $categorie->getIdCatMere()));
			return;
		}

		$this->_redirect('admin/cms');
	}


	public function newsaddAction() {
		$category = Class_ArticleCategorie::getLoader()->find($this->_getParam('id_cat'));

		// pas d'article non catégorisé
		if (null === $category) {
			$this->_redirect('admin/cms');
			return;
		}

		if (null === ($category->getBib())) {
			$category->setBib($this->_bib);
		}

		$article = Class_Article::getLoader()->newInstance()
																				->setCategorie($category);

		if ($this->_isArticleSaved($article)) {
			$this->_redirect(sprintf('admin/cms/newsedit/id/%d', $article->getId()));
			return;
		}

		$this->view->article		= $article;
		$this->view->titre			= 'Ajouter un article';
		$this->view->combo_cat	= $this->_getArticleCategoryInput($category);
	}


	public function newseditAction() {
		$article = Class_Article::getLoader()->find((int)$this->_getParam('id'));

		if (null === ($article->getCategorie()->getBib())) {
			$article->getCategorie()->setBib($this->_bib);
		}

		if ($lang = $this->_getParam('lang')) {
			$article = $article->getOrCreateTraductionLangue($lang);
		}

		if ($this->_isArticleSaved($article)) {
			$this->view->message = "Article sauvegardé";
		}

		$this->view->article = $article;
		$this->view->combo_cat = $this->_getArticleCategoryInput($article->getCategorie());

		if ($article->isTraduction()) {
			$this->view->titre = 'Traduire un article';
			$this->render('traductionform');
		}	else {
			$this->view->titre = 'Modifier un article';
			$this->render('newsform');
		}
	}


	/**
	 * @param Class_Article $article
	 * @return bool
	 */
	protected function _isArticleSaved($article) {
		if ($this->_request->isPost()) {
			$post = $this->_request->getPost();

			foreach(array('debut', 'fin', 'events_debut', 'events_fin') as $date_field) {
				if (array_key_exists($date_field, $post))
					$post[$date_field] = $this->_toDate($post[$date_field]);
			}

			foreach(array('description', 'contenu') as $content_field)
				$post[$content_field] = Class_CmsUrlTransformer::forSaving($post[$content_field]);

			return $article
				->updateAttributes($post)
				->save();
		}

		foreach(array('description', 'contenu') as $content_field)
			$article->_set($content_field,
										Class_CmsUrlTransformer::forEditing($article->_get($content_field)));

		return false;
	}


	public function deleteAction() {
		if (
			null !== (
				$article = Class_Article::getLoader()->find((int)$this->_getParam('id'))
			)
		) {
			$article->delete();
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $article->getIdCat()));
			return false;
		}

		$this->_redirect('admin/cms');
	}


	public function viewcmsAction() {
		$this->view->article = Class_Article::getLoader()->find((int)$this->_getParam('id'));
		$this->view->title = 'Afficher un article';
	}


	public function makevisibleAction() {
		if ($article = Class_Article::getLoader()->find((int)$this->_getParam('id'))) {
			$article->beVisible();
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $article->getIdCat()));
			return;
		}

		$this->_redirect('admin/cms');
	}


	public function makeinvisibleAction()	{
		if ($article = Class_Article::getLoader()->find((int)$this->_getParam('id'))) {
			$article->beInvisible();
			$this->_redirect(sprintf('admin/cms/index/id_cat/%d', $article->getIdCat()));
			return;
		}

		$this->_redirect('admin/cms');
	}


	/**
	 * @return array
	 */
	private function _getTreeViewContainerActions() {
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
					'onclick' => "return confirm('Etes-vous sûr de vouloir supprimer cette catégorie ?')"
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


	private function _getTreeViewItemActions() {
		return array(
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
			),
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
				'anchorOptions' => array(
					'onclick' => "return confirm('Etes-vous sûr de vouloir supprimer cet article ?')"
				),
			)
		);
	}


	private function _toDate($str) {
		if ($str!==null && $str!=='') {
			$date = new Zend_Date($str, null, Zend_Registry::get('locale'));
			return $date->getIso();
		}

		return null;
	}


	/**
	 * @param Class_ArticleCategorie $category
	 * @return string
	 */
	protected function _getArticleCategoryInput($category) {
		if (Class_Users::getLoader()->isCurrentUserCanAccessAllBibs())
			$bibs = Class_Bib::getLoader()->findAllWithPortail();
		else {
			$bib = $category->getBib();
			if (0 == $bib->getId())
				$bib = $this->_bib;
			$bibs = array($bib);
		}

		return sprintf('<select name="id_cat" id="id_cat">%s</select>', 
									 $this->_getAllBibArticleCategories($bibs, $category));
	}


	protected function _getAllBibArticleCategories($bibs, $category) {
		$html = '';

		foreach($bibs as $bib)
			$html .=  $this->_getArticleCategorySelectGroupForBib($bib, $category);

		return $html;
	}


	/**
	 * @param Class_Bib bib
	 * @return string
	 */
	protected function _getArticleCategorySelectGroupForBib($bib, $category) {
		$html = sprintf('<optgroup label="%s">', $bib->getLibelle());
		$rootCategories = $bib->getArticleCategories();
		foreach ($rootCategories as $rootCategory) {
			$html .= $this->_getLeveledArticleCategoryOption($rootCategory, $category);
		}
		$html .= '</optgroup>';
		return $html;
	}


	/**
	 * @param Class_ArticleCategorie $category
	 * @param Class_ArticleCategorie $origin
	 * @param int $level
	 * @return string
	 */
	protected function _getLeveledArticleCategoryOption($category, $origin, $level = 0) {
		$prefix = str_repeat('&nbsp;&nbsp;', $level*2);
		$html = '<option value="' . $category->getId() . '"'
							. (($category->getId() == $origin->getId()) ? ' selected="selected"' : '') . '>'
							. $prefix . $category->getLibelle() . '</option>';

		foreach ($category->getSousCategories() as $subCategory) {
			$html .= $this->_getLeveledArticleCategoryOption($subCategory, $origin, $level+1);
		}

		return $html;
	}

	/**
	 * @param Class_ArticleCategorie $category
	 * @return string
	 */
	protected function _getParentCategoryInput($category) {
		$html = '<select name="id_cat_mere" id="id_cat_mere" style="width:100%">';
		$html .= '<option value="0">Aucune</option>';

		$rootCategories = $category->getBib()->getArticleCategories();

		foreach ($rootCategories as $rootCategory) {
			$html .= $this->_getLeveledParentCategoryOption($rootCategory, $category);
		}

		return $html . '</select>';
	}


	/**
	 * @param Class_ArticleCategorie $category
	 * @param Class_ArticleCategorie $origin
	 * @param int $level
	 * @return string
	 */
	protected function _getLeveledParentCategoryOption($category, $origin, $level = 0) {
		// on exclut la categorie courante => impossible de se déplacer sous
		// soi-même ou un de ses descendants
		if ($category->getId() == $origin->getId()) {
			return '';
		}

		$prefix = str_repeat('&nbsp;&nbsp;', $level);
		$html = '<option value="' . $category->getId() . '"'
							. (($category->getId() == $origin->getIdCatMere()) ? ' selected="selected"' : '') . '>'
							. $prefix . $category->getLibelle() . '</option>';

		foreach ($category->getSousCategories() as $subCategory) {
			$html .= $this->_getLeveledParentCategoryOption($subCategory, $origin, $level+1);
		}

		return $html;
	}
}