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
class ZendAfi_View_Helper_TagEditArticle extends Zend_View_Helper_HtmlElement {
	/**
	 * @param Class_Article $article
	 * @return string
	 */
	public function tagEditArticle($article) {
		if (Class_Users::getLoader()->isCurrentUserCanEditArticle($article))
			return $this->view->tagAnchor($this->view->url(array('module' => 'admin',
																													 'controller' => 'cms',
																													 'action' => 'newsedit',
																													 'id' => $article->getId())),
																		$this->view->tagImg(URL_ADMIN_IMG.'ico/edit.gif',
																												array('class' => 'article_edit',
																															'target' => '_blank',
																															'alt' => $this->view->translate("Modifier l'article"),
																															'title' => $this->view->translate("Modifier l'article"))));
		return '';
	}
}
?>