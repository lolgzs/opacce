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

/*
 * Helper utilisé pour transformUrlForSaving et transformUrlForEditing dans le CMS
 */
class Class_CmsUrlTransformer {
	/*
	 * Utilisé pour corriger les URL sur les images dans l'édition
	 * du CMS
	 * Si dans le CMS on fait un copier/coller des images, selon le navigateur
	 * l'url d'origine n'est pas conservée.
	 * à l'édition on passe donc on URL absolu.
	 */
	public static function forEditing($content) {
		return preg_replace('/(< *img[^>]*src *= *["\']?)\/([^"\']*)/i',
												'$1http://'.$_SERVER['HTTP_HOST'].'/$2',
												$content);
	}

	/*
	 * A la sauvegarde de l'article, on passe les URL des images
	 * en relatif depuis la racine du site
	 */
	public static function forSaving($content) {
		//voir les tests unitaires pour comprendre la regex
		$re_base_url = str_replace('/', '\/', BASE_URL);
		$rel = preg_replace('/(< *img[^>]*src *= *["\']?)(http:\/\/'.$_SERVER['HTTP_HOST'].')?('.$re_base_url.')+([^"\']*)/i',
												'$1$4',
												$content);
		return preg_replace('/(< *img[^>]*src *= *["\']?)[\.\.\/]+([^"\']*)/i',
												'$1'.BASE_URL.'/$2',
												$rel);
	}
}

?>