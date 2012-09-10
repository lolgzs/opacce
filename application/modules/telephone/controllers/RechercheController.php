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
  use Trait_Translator;

  public function viewnoticeAction()  {
    $notice = Class_Notice::find($this->_getParam('id'));

    $actions = [$this->_('Description du document') =>	['action' => 'detail'],
		$this->_('Où le trouver ?') => ['action' => 'exemplaires'],
		$this->_('Critiques') => ['action' => 'avis'],
		$this->_('Résumé') =>  ['action' => 'resume'],
		$this->_('Vidéos associées') =>	 ['action' => 'videos'],
		$this->_('Rebondir dans le catalogue') =>  ['action' => 'tags'],
		$this->_('Biographie de l\'auteur') => ['action' => 'biographie'],
		$this->_('Documents similaires') => ['action' => 'similaires'],
		$this->_('Notices liées') => ['action' => 'frbr']
    ];

    if ($notice->isLivreNumerique())
      $actions[$this->_('Feuilleter le livre')] = ['action' => 'ressourcesnumeriques',
						   'attribs' => ['data-ajax' => 'false']];

    if ($notice->isArteVOD())
      $actions[$this->_('Bande-annonce')] = ['action' => 'ressourcesnumeriques',
					     'attribs' => ['data-ajax' => 'false']];

    $this->view->notice = $notice;
    $this->view->actions = $actions;
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


  public function ressourcesnumeriquesAction() {
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

    if (isset($ret["erreur"]) && '' != $ret['erreur']) {
      $this->_loadUrlRetourForExemplaire($this->_getParam('e'));
      $this->view->message = $ret['erreur'];
      return;
    }

    if (isset($ret["popup"]) && '' != $ret['popup'])	{
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


  public function videosAction() {
    $notice = Class_Notice::getLoader()->find($this->_getParam('id'));
    $video = Class_WebService_AllServices::runServiceAfiVideo(array('titre' => $notice->getTitrePrincipal(),
								    'auteur' => $notice->getAuteurPrincipal()));
    $video_id = null;
    if ($html = $video['video']) {
      if (1==preg_match('/value=\"([^\"\&]+)/', $html, $matches)) {
	$parts = explode('/', $matches[1]);
	$video_id = end($parts);
      }
    }

    $this->view->notice = $notice;
    $this->view->video_id = $video_id;
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


  public function bibliothequeAction() {
    $this->view->bib = Class_Bib::getLoader()->find($this->_getParam('id'));
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
