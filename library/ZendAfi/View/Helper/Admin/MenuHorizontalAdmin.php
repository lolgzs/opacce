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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Menu admin horizontal
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_MenuHorizontalAdmin extends ZendAfi_View_Helper_BaseHelper {
	public function menuHorizontalAdmin() {
		$menus = array(array("icon" => "icon_home.gif",
												 "label" => $this->translate()->_("Accueil"),
												 "url" => "/admin"),
									 array("icon" => "go_back.png",
												 "label" => $this->translate()->_("Retour au site"),
												 "url" => "?id_profil=".Class_Profil::getCurrentProfil()->getId()),
									 array("icon" => "deconnexion.png",
												 "label" => $this->translate()->_("Se déconnecter"),
												 "url" => "/admin/auth/logout"));

		return $this->generateMenu($menus);
	}


	public function generateMenu($menus) {
		$content = '<ul>';
		foreach ($menus as $entry) {
			$icon_url = URL_ADMIN_IMG.'picto/'.$entry['icon'];
			$target_url = BASE_URL.$entry['url'];
			$label = $entry['label'];

			$count_info = '';
			if (array_key_exists('count', $entry))
				$count_info = "<span class='menu_info'>".$entry['count']."</span>";

			$class_selected = '';
			if (array_key_exists('REQUEST_URI', $_SERVER)
				  and (0 === strpos($_SERVER['REQUEST_URI'], $target_url))) {
				$class_selected = 'class="selected"';
			}


			$content .= 
				"<li $class_selected>".
				  "<img src='$icon_url' alt='$label' />".
				  "<a href='$target_url'>$label</a>".$count_info.
				"</li>";
		}
		$content .= "</ul>";

		return $content;
	}
}

