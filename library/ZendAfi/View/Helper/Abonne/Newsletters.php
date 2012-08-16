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
class ZendAfi_View_Helper_Abonne_Newsletters extends ZendAfi_View_Helper_Abonne_Abstract {
	public function abonne_newsletters($user) {
		if (count(Class_Newsletter::getLoader()->findAll()) == 0)
			return '';

		$newsletter_info = $this->view->_("Vous n'êtes abonné à aucune lettre d'information");

		if (count($user->getNewsletters()) > 0) {
			$titres = $user->getTitresNewsletters();

			$newsletter_info = $this->view->_('Vous êtes abonné');
			$newsletter_info .= count($titres) > 1 ? $this->view->_(" aux lettres d'information: ") : $this->view->_(" à la lettre d'information: ");
			$newsletter_info .= implode(', ', $titres);
		}

		return $this->tagFicheAbonne(
																 '<p>'.$newsletter_info.'</p>'.
																 $this->view->tagAnchor(['controller' => 'abonne', 'action' => 'edit'],
																												$this->view->_('Modifier mes abonnements')),
																 'newsletter');
	}
}

?>