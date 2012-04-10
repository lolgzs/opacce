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
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Division de la page d'accueil
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Division extends ZendAfi_View_Helper_BaseHelper {
	public function division($profil, $division, $html_id, $content=null) {
		$width = $profil->_get('largeur_division'.$division);
		if ($html_id == "colContenu"  and  $profil->getNbDivisions() == 3)
			$width += $profil->getLargeurDivision3();

		$marge = $profil->_get('marge_division'.$division);
		$style = "float:left;overflow:hidden;width:".$width."px;max-width:".$width."px";
		$style_inner = "padding-left:".$marge."px;padding-right:".$marge."px";

		$template_div = '<div class="%1$s" style="%2$s"><div id="%1$sInner" style="%3$s">%4$s %5$s</div></div>';

		$barre_nav = '';
		if (in_array($html_id, array('colContenu', 'colMilieu'))	and ($profil->getBarreNavOn()))
			$barre_nav = $this->view->barreNav();

		if (null === $content) {
			$boites = $this->view->portail(array('modules' => $profil->getBoitesDivision($division)));
			$content = $boites[$division];
		}

		return sprintf($template_div, $html_id, $style, $style_inner, $barre_nav, $content);
	}
}

?>
