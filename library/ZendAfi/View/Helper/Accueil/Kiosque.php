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
class ZendAfi_View_Helper_Accueil_Kiosque extends ZendAfi_View_Helper_Accueil_Base {
	protected function _renderHeadScriptsOn($script_loader) {
		$script_loader->addJQueryReady('$(".embedcode>div:first-child").click(function(){$(this).next().toggle("fast")})');
	}


	public function getHtml() {
		extract($this->preferences);
		
		// Proprietes en fonction du type de liste
		$args = array("id_module" => $this->id_module, 
									'id_profil' => Class_Profil::getCurrentProfil()->getId());
		$action = "kiosque";
		switch($style_liste) {
			case "slide_show":
				$controler = "java";
				$args["vue"]="slide_show";
				$hauteur = $op_hauteur_img + 7;
				if ($hauteur == 7) 
					$hauteur = 117;
				break;
			case "protoflow":
				$controler = "java";
				$args["vue"] = "protoflow";
				$hauteur = 350;
				break;
			case "cube":
				$controler = "java";
				$args["vue"] = "cube";
				$hauteur = $op_hauteur_img + 20;
				break;
			case "diaporama":
				$controler = "java";
				$args["vue"] = "diaporama";
				$hauteur = $op_hauteur_boite;
				break;
			case "jcarousel":
				$controler = "java";
				$args["vue"] = "jcarousel";
				$hauteur = $op_hauteur_img + 10;
				break;
		case "mycarousel_horizontal":
				$controler = "java";
				$args["vue"] = "mycarousel_horizontal";
				$hauteur = $op_hauteur_img + 5;
				break;
		case "mycarousel_vertical":
				$controler = "java";
				$args["vue"] = "mycarousel_vertical";
				$hauteur = ($op_visible * $op_hauteur_img) + 15;
				break;
			case "coverflow":
				$controler = "flash";
				$action = "coverflow";
				$hauteur = 270;
				break;
			case "carrousel_horizontal":
				$controler = "flash";
				$action = "carrouselhorizontal";
				$hauteur = 310;
				break;
			case "carrousel_vertical":
				$controler = "flash";
				$action = "carrouselvertical";
				$hauteur = 500;
				break;
			case "dockmenu_horizontal":
				$controler = "flash";
				$action = "dockmenuh";
				$hauteur = 170;
				break;
			case "dockmenu_vertical":
				$controler = "flash";
				$action = "dockmenuv";
				$hauteur = 300;
				break;
			case "pyramid_gallery":
				$controler = "flash";
				$action = "pyramidhorizontal";
				$hauteur = 290;
				break;
		}

		// Iframe
		$iframe = new ZendAfi_View_Helper_IframeContainer();
		$iframe->iframeContainer("100%",
														 $hauteur,
														 array_merge(['controller' => $controler,
																					'action' => $action],
																				 $args));
		if ($this->shouldCacheContent())
			$iframe->setCacheKey($this->getCacheKey());

		$this->contenu = sprintf('<div class="embedcode">'.
														 '<div>&lt;&gt;</div>'.
														 '<div style="display:none">%s<textarea cols="20" rows="10" readonly="readonly">%s</textarea></div>'.
														 '</div>',
														 $this->view->_("Copier le code suivant sur le site où vous voulez afficher le kiosque"),
														 htmlspecialchars($iframe->getHtml()));

		$this->contenu .= $iframe->getHtml();

		$this->titre = $titre;

		if ($this->preferences['rss_avis'])
			$this->rss_interne = $this->_getRSSurl('rss', 'kiosque');

		return $this->getHtmlArray();
	}
}