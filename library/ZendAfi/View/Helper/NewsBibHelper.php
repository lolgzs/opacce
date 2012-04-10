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
//--------------------------------------------------------------------------------------
// OPAC 3 
//--------------------------------------------------------------------------------------
class ZendAfi_View_Helper_NewsBibHelper extends ZendAfi_View_Helper_BaseHelper
{
	public function newsBibHelper($id_bib){
		$html = '';

		$art_loader = Class_Article::getLoader();
		$articles = $art_loader->getArticlesByPreferences(array('id_bib' => $id_bib));
		$articles = $art_loader->filterByLocaleAndWorkflow($articles);
		$articles_by_cat = $art_loader->groupByLibelleCategorie($articles);

		foreach($articles_by_cat as $libelle_cat => $cat_articles) {
			$html .= sprintf('<h1>%s</h1>', $libelle_cat);
			foreach($cat_articles as $article) {
				$html .= sprintf('<h2>%s</h2>', $article->getTitre());
				$html .= sprintf($article->getSummary());
			}
		}

		return $html;
	}
}
