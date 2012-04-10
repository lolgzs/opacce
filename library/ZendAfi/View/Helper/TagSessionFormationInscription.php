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
class ZendAfi_View_Helper_TagSessionFormationInscription extends Zend_View_Helper_HtmlElement {
	/**
	 * @param string $url
	 * @param string $text
	 * @param array $attribs
	 * @return string
	 */
	public function tagSessionFormationInscription($session) {
		if ($session->isInscriptionClosed())
			return '';

		$user = Class_Users::getLoader()->getIdentity();
		if (!$user->hasRightSuivreFormation())
			return '';

		$content = '';
		$sessions_inscrit = $user->getSessionFormations();
		$is_inscrit = in_array($session, $sessions_inscrit);


		if ($is_inscrit)
			$content = $this->view->tagAnchor(array('action' => 'desinscrire_session',
																							 'id' => $session->getId()),
																				$this->view->_("Se désinscrire"));

		if ($session->isFull())
			return $content.sprintf('<span class="error">%s</span>', 
															$this->view->_('Effectif maximum atteint'));

		if ($is_inscrit)
			return $content;

		$link_inscription =  $this->view->tagAnchor(array('action' => 'inscrire_session',
																											'id' => $session->getId()),
																								$this->view->_("S'inscrire"));

		if (!$session->hasDateLimiteInscription())
			return $link_inscription;

		return sprintf('%s %s: %s',
									 $link_inscription,
									 $this->view->_('Limite'),
									 $this->view->humanDate($session->getDateLimiteInscription(), 'd MMMM YYYY'));
	}
}

?>