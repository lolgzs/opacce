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
require_once ROOT_PATH.'application/modules/opac/controllers/RechercheController.php';

class Telephone_RechercheController extends RechercheController {
	public function viewnoticeAction()  {
		$this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
		$this->view->actions = array($this->view->_('Notice détaillée') =>		  array('action' => 'detail'),
																 $this->view->_('Avis') =>								  array('action' => 'avis'),
																 $this->view->_('Exemplaires') =>					  array('action' => 'exemplaires'),
																 $this->view->_('Résumés, analyses') =>		  array('action' => 'resume'),
																 $this->view->_('Tags') =>								  array('action' => 'tags'),
																 $this->view->_('Biographies') =>					  array('action' => 'biographie'),
																 $this->view->_('Notices similaires') =>	  array('action' => 'similaires'),
																 //																 $this->view->_('Ressources numériques') => array('action' => 'ressourcesnumeriques'),
																 );
	}


	 public function grandeimageAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	 public function exemplairesAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	 public function avisAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	 public function detailAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	 public function reservationAction() {
		 if (!Class_Users::getLoader()->getIdentity()) {
			 $this->_setLastReservationParamsAndGotoLogin();
			 return;
		 }

		 $this->_loadReservationParamsFromSession();

		 if (Class_CosmoVar::isSiteRetraitResaEnabled()
				 && !$this->_getParam('pickup')) {
			 $this->_redirect(sprintf('/recherche/pickup-location/b/%s/e/%s/a/%s',
																urlencode($this->_getParam('b')),
																urlencode($this->_getParam('e')),
																urlencode($this->_getParam('a'))));
			 return;
		 }

		 $ret = Class_CommSigb::getInstance()
			 ->reserverExemplaire($this->_getParam('b'), 
														$this->_getParam('e'), 
														($this->_getParam('pickup')) ? $this->_getParam('pickup') : $this->_getParam('a'));

		 if (isset($ret["erreur"])) {
			 $this->_loadUrlRetourForExemplaire($this->_getParam('e'));
			 $this->view->message = $ret['erreur'];
			 return;
		 }

		 if (isset($ret["popup"]))	{
			 $this->_loadUrlRetourForExemplaire($this->_getParam('e'));
			 $this->view->message = $this->view->_('Réservation en ligne non supportée pour cette bibliothèque.');
			 return;
		 }

		 $this->_redirect('/abonne/fiche');
	 }


	 public function pickupLocationAction() {
		 $this->_loadUrlRetourForExemplaire($this->_getParam('e'));
		 $this->view->annexes = Class_CodifAnnexe::findAllByPickup();
	 }


	 public function resumeAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	 public function tagsAction() {
		 $notice = Class_Notice::getLoader()->find($this->_getParam('id'));
		 $notice_html = new Class_NoticeHtml();
		 $this->view->tags = $notice_html->getTags($notice->getTags(), $notice->getId());
		 $this->view->notice = $notice;
	 }


	 public function biographieAction() {
		 $this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	 }


	public function similairesAction() {
		$notice = Class_Notice::getLoader()->find($this->_getParam('id'));
		$this->view->notices = $notice->getNoticesSimilaires($notice->getId());
		$this->view->preferences = array('liste_codes' => 'TA');
		$this->view->notice = $notice;
	}


	protected function _loadUrlRetourForExemplaire($id_exemplaire) {
		$exemplaire = Class_Exemplaire::getLoader()->find($id_exemplaire);
		$this->view->url_retour = $this->view->url(array('controller' => 'recherche',
																											 'action' => 'exemplaires',
																											 'id' => $exemplaire->getNotice()->getId()),
																								 null, true);
	}


	protected function _setLastReservationParamsAndGotoLogin() {
		$exemplaire = Class_Exemplaire::getLoader()->find($this->_getParam('e'));
		Zend_Registry::get('session')->lastReservationParams = array('b' => $this->_getParam('b'),
																																 'e' => $this->_getParam('e'),
																																 'a' => $this->_getParam('a'));
		$this->_redirect('/auth/login-reservation/id/' . urlencode($exemplaire->getNotice()->getId()));
	}


	protected function _loadReservationParamsFromSession() {
		if (!$params = Zend_Registry::get('session')->lastReservationParams)
			return;

		$this->_request
			->setParam('b', $params['b'])
			->setParam('e', $params['e'])
			->setParam('a', $params['a']);
	}
}
