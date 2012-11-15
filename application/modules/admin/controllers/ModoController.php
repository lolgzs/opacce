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
class Admin_ModoController extends Zend_Controller_Action {
	private $_count = 10;

	public function indexAction()	{
		$this->view->titre = $this->view->_('Modération');
	}


	public function avisnoticeAction() {
		$this->view->subview = $this->view->partial('modo/avisnotice.phtml',
																								array('list_avis' => Class_AvisNotice::getLoader()->findAllBy(array('statut' => 0,'limit'=>100)),
																											'title' => 'Avis'));
		$this->_forward('index');
	}


	public function delavisnoticeAction() {
		$id = $this->_request->getParam('id');
		Class_AvisNotice::find($id)->delete();
		$this->_redirect('admin/modo/avisnotice');
	}


	public function validateavisnoticeAction() {
		$id = $this->_request->getParam('id');
		Class_AvisNotice::find($id)
			->setModerationOK()
			->save();
		$this->_redirect('admin/modo/avisnotice');
	}


	public function deleteCmsAvisAction() {
		$avis = Class_Avis::find($this->_getParam('id'));
		$avis->delete();
		$avis->maj_note_cms($avis->getIdCms(), $avis->getAbonOuBib());
		$this->_redirect('opac/cms/articleview/id/'.$avis->getIdCms());
		
		$this->_helper->notify($this->view->_('Avis %s supprimé', $avis->getEntete()));
	}


	public function updateavisnoticeAction() {
		$class_modo = new Class_Moderer();
		$avis = $this->_getParam('avis', []);
		if (0 == count($avis)) {
			$this->_redirect('admin/modo/avisnotice');
			return;
		}
				
		foreach ($avis as $item) {
			$elems = explode('_', $item);
			$class_modo->modererAvis($elems[0], $elems[1], $elems[2]);
		}

		$this->_redirect('admin/modo/avisnotice');
	}


	public function tagnoticeAction() {
		$view_liste_tags=array();
		$class_modo = new Class_Moderer();
		$liste_tags = $class_modo->getAllTagsAModerer();

		// Completer les infos
		$cls_notice = new Class_Notice();
		for ($i = 0; $i < count($liste_tags); $i++) {
			$notice = $cls_notice->getNotice($liste_tags[$i]['id_notice'], 'TA');
			if (!$notice)
				continue;
			
			$liste_tags[$i]["NOTICE"] = '<a href="'.BASE_URL.'/recherche/viewnotice/id/'.$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_blank">';
			$liste_tags[$i]["NOTICE"] .= '<img src="'.URL_ADMIN_IMG.'supports/support_'.$notice["type_doc"].'.gif" border="0" style="float:left">&nbsp;<b>'.trim($notice["T"]);
			if ($notice["A"] > '')
				$liste_tags[$i]["NOTICE"] .= " / ".$notice["A"];
			$liste_tags[$i]["NOTICE"] .= '</b></a>';
			$view_liste_tags[] = $liste_tags[$i];
		}

		$this->view->subview = $this->view->partial('modo/tagnotice.phtml',
																								array('title' => 'Tags',
																											'liste_tags' => $view_liste_tags));
		$this->_forward('index');
	}
	

	public function updatetagnoticeAction() {
		$class_modo = new Class_Moderer();
		$items = $this->_getParam('tag', []);
		if (0 == count($items)) {
			$this->_redirect('admin/modo/tagnotice');
			return;
		}
		
		foreach ($items as $item) {
			$elems = explode('_', $item);
			$class_modo->modererTag($elems[0], $elems[1], $elems[2]);
		}
		
		$this->_redirect('admin/modo/tagnotice');
	}
	

	public function aviscmsAction() {
		$class_modo = new Class_Moderer();
		$this->view->subview = $this->view->partial('modo/aviscms.phtml',
																								array('liste_avis_abo' => $class_modo->getAllAvisCmsAModerer(0),
																											'liste_avis_bib' => $class_modo->getAllAvisCmsAModerer(1)));
		$this->_forward('index');
	}


	public function updateaviscmsAction() {
		$class_modo = new Class_Moderer();
		$items = $this->_getParam('avis', []);
		if (0 == count($items)) {
			$this->_redirect('admin/modo/aviscms');
			return;
		}
		
		foreach ($items as $item) {
			$elems = explode('_', $item);
			$class_modo->modererAvisCms($elems[0], $elems[1], $elems[2]);
		}
		$this->_redirect('admin/modo/aviscms');
	}
	

	public function membreviewAction() {
		$class_user = new Class_Users();
		$liste_user = $class_user->getUsersNonValid();

		$this->view->liste_user = $liste_user;
		$this->view->titre = "Demandes d'inscription";
	}


	public function updatemembreAction() {
		$class_modo = new Class_Moderer();
		$items = $this->_getParam('user', []);
		if (0 == count($items)) {
			$this->_redirect('admin/modo/tagnotice');
			return;
		}
		
		foreach ($items as $item) {
			$elems = explode('_', $item);
			$class_modo->modererUserNonValid($elems[0], $elems[1]);
		}
		
		$this->_redirect('admin/modo/membreview');
	}


	public function suggestionAchatAction() {
		$this->view->subview = $this->view->partial('modo/suggestion-achat.phtml',
			                                          ['suggestions' => Class_SuggestionAchat::findAllBy(['order' => 'date_creation'])]);
		$this->_forward('index');
	}


	public function suggestionAchatEditAction() {
		if (!$model = Class_SuggestionAchat::find((int)$this->_getParam('id'))) {
			$this->_redirect('/admin/modo/suggestion-achat');
			return;
		}
		
		$form = (new ZendAfi_Form_SuggestionAchat())->removeSubmitButton()
				->populate($this->_request->getParams())
				->populate($model->toArray())
				->setAttrib('data-backurl', $this->view->url(['action' => 'suggestion-achat']));

		if ($this->_request->isPost()) {
			$model->updateAttributes($this->_request->getPost());
			if ($form->isValid($model) && $model->save()) {
				$this->_helper->notify($this->view->_('Suggestion d\'achat sauvegardée'));
				$this->_redirect('/admin/modo/suggestion-achat');
				return;
			}
		}
				
		$this->view->subview = $this->view->partial('modo/suggestion-achat-edit.phtml',
			                                          ['form' => $form]);
		$this->_forward('index');
	}


	public function suggestionAchatDeleteAction() {
		if (!$model = Class_SuggestionAchat::find((int)$this->_getParam('id'))) {
			$this->_redirect('/admin/modo/suggestion-achat');
			return;
		}

		$model->delete();
		$this->_helper->notify($this->view->_('Suggestion d\'achat supprimée'));
		$this->_redirect('/admin/modo/suggestion-achat');
	}


	/**
	 *
	 * "Hell isn't bad place,
	 * Hell is from here to eternity"
	 * (Bruce Dickinson)
	 *
	 *
	 *
	 */
	
	public function avisindexAction() {
		$moderer = new Class_Moderer();

		$this->view->title = "Avis";

		$count = $this->_count; //number of rows to return at a time
		$totalUnModRows = $moderer->countBibAbonAvisStatut0();
		if (is_string($totalUnModRows) ){
			$this->_redirect('admin/error/database');
		}

		$page = (int)$this->_request->getParam('page', 1);
		if ( (($page -1) * $count) >= $totalUnModRows){
			$page = $page -1;
		}

		$checkall = $this->_request->getParam('checkall', 0);
		if ($checkall === 'v'){
			$this->view->deletechecked = "";
			$this->view->validatechecked = "checked";
		}elseif($checkall === 'd'){
			$this->view->deletechecked = "checked";
			$this->view->validatechecked = "";
		}else{
			$this->view->deletechecked = "";
			$this->view->validatechecked = "";
		}

		$offset = ($page - 1)*$count;

		if ($offset < 0) {
			$offset = 0;
		}elseif($offset > $totalUnModRows){
			$offset = $totalUnModRows;
		}

		$this->view->moderer = $moderer;
		$fetch = $moderer->fetchLimitBibAbonAvisByStatut0($count, $offset);
		if ( is_string($fetch) ){
			$this->_redirect('admin/error/database');
		}

		$pages = (int)($totalUnModRows / $count);
		$mod = $totalUnModRows % $count;
		if ($mod > 0){
			$pages++;
		}

		$this->view->dateClass = new Class_Date();
		$this->view->numPage = $page;
		$this->view->nb_pages = $pages;
		$this->view->url = "/admin/moderer/avisindex?page=";

		$this->view->pages = $pages;
		$this->view->bibAvisRows = $fetch;
		$this->view->offset = $offset;
		$this->view->count = $count;
		$this->view->total = $totalUnModRows;
	}

	public function modifieravisAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');

		$moderer = new Class_Moderer();

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$id_abon = $this->_request->getPost('id_abon');
			$ordre_abon = $this->_request->getPost('ordre_abon');
			$id_notice = $this->_request->getPost('id_notice');

			$entete = trim($filter->filter($this->_request->getPost('entete')));
			$avis = trim($filter->filter($this->_request->getPost('avis')));

			$data = array(
			'ENTETE' => $entete,
			'AVIS' => $avis,
			);

			// try to update avis
			$errorMessage = $moderer->updateBibAbonAvis($id_abon, $ordre_abon, $id_notice, $data);
			if ($errorMessage == ''){
				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setNoRender();

				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/avisindex';</script>");

			}else{
				$avis = $moderer->fetchRowBibAbonAvis($id_abon, $ordre_abon, $id_notice);
				/*
				 * if $avis is a string, we have encountered a problem with the database
				 */
				if ( is_string($avis) ){
					$this->view->avis = new stdClass();
					$this->view->avis->ID_ABON = $id_abon;
					$this->view->avis->ORDRE_ABON = $ordre_abon;
					$this->view->avis->ID_NOTICE = $id_notice;
					$this->view->avis->ENTETE = '';
					$this->view->avis->AVIS = '';
					$this->view->message = $avis;
				}elseif($avis == null){
					$viewRenderer = $this->getHelper('ViewRenderer');
					$viewRenderer->setNoRender();

					$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
					$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/avisindex';</script>");
				}else{
					$this->view->avis = $avis;
					$this->view->message = $errorMessage;
				}
			}
		} else {
			$id_abon = $this->_request->getParam('id_abon', 0);
			$ordre_abon = $this->_request->getParam('ordre_abon', 0);
			$id_notice = $this->_request->getParam('id_notice', 0);
			if ( ( $id_abon > 0 ) && ( $id_abon > 0 ) && ( $id_abon > 0 ) ) {
				$avis = $moderer->fetchRowBibAbonAvis($id_abon, $ordre_abon, $id_notice);
				/*
				 * if $avis is a string, we have encountered a problem with the database
				 * if $avis is null, the categorie does not exist in the database
				 */
				if ( is_string($avis) ){
					$this->view->avis = new stdClass();
					$this->view->avis->ID_ABON = $id_abon;
					$this->view->avis->ORDRE_ABON = $ordre_abon;
					$this->view->avis->ID_NOTICE = $id_notice;
					$this->view->avis->ENTETE = '';
					$this->view->avis->AVIS = '';
					$this->view->message = $avis;
				}elseif($avis == null){
					$viewRenderer = $this->getHelper('ViewRenderer');
					$viewRenderer->setNoRender();

					$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
					$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/avisindex';</script>");
				}
				else{
					$this->view->avis = $avis;
				}
			}else{
				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setNoRender();

				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/avisindex';</script>");
			}
		}

	}


	public function cmsavisindexAction() {
		$moderer = new Class_Moderer();

		$this->view->title = "CMS Avis";

		$count = $this->_count; //number of rows to return at a time
		$totalUnModRows = $moderer->countBibCmsAvisStatut0();
		if (is_string($totalUnModRows)){
			$this->_redirect('admin/error/database');
		}

		$page = (int)$this->_request->getParam('page', 1);
		if ( (($page -1) * $count) >= $totalUnModRows){
			$page = $page -1;
		}

		$checkall = $this->_request->getParam('checkall', 0);
		if ($checkall === 'v'){
			$this->view->deletechecked = "";
			$this->view->validatechecked = "checked";
		}elseif($checkall === 'd'){
			$this->view->deletechecked = "checked";
			$this->view->validatechecked = "";
		}else{
			$this->view->deletechecked = "";
			$this->view->validatechecked = "";
		}

		$offset = ($page - 1)*$count;

		if ($offset < 0) {
			$offset = 0;
		}elseif($offset > $totalUnModRows){
			$offset = $totalUnModRows;
		}

		$this->view->moderer = $moderer;
		$fetch = $moderer->fetchLimitBibCmsAvisByStatut0($count, $offset);
		if ( is_string($fetch) ){
			$this->_redirect('admin/error/database');
		}

		$pages = (int)($totalUnModRows / $count);
		$mod = $totalUnModRows % $count;
		if ($mod > 0){
			$pages++;
		}

		$this->view->dateClass = new Class_Date();
		$this->view->numPage = $page;
		$this->view->nb_pages = $pages;
		$this->view->url = "/admin/moderer/cmsavisindex?page=";

		$this->view->pages = $pages;
		$this->view->bibAvisRows = $fetch;
		$this->view->offset = $offset;
		$this->view->count = $count;
		$this->view->total = $totalUnModRows;
	}

	public function modifiercmsavisAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');

		$moderer = new Class_Moderer();

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$id_abon = $this->_request->getPost('id_abon');
			$ordre_abon = $this->_request->getPost('ordre_abon');
			$id_cms_article = $this->_request->getPost('id_cms_article');

			$entete = trim($filter->filter($this->_request->getPost('entete')));
			$avis = trim($filter->filter($this->_request->getPost('avis')));

			$data = array(
			'ENTETE' => $entete,
			'AVIS' => $avis,
			);

			// try to update avis
			$errorMessage = $moderer->updateBibCmsAvis($id_abon, $ordre_abon, $id_cms_article, $data);
			if ($errorMessage == ''){
				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setNoRender();

				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/cmsavisindex';</script>");

			}else{
				$avis = $moderer->fetchRowBibCmsAvis($id_abon, $ordre_abon, $id_cms_article);
				/*
				 * if $avis is a string, we have encountered a problem with the database
				 */
				if ( is_string($avis) ){
					$this->view->avis = new stdClass();
					$this->view->avis->ID_ABON = $id_abon;
					$this->view->avis->ORDRE_ABON = $ordre_abon;
					$this->view->avis->ID_CMS_ARTICLE = $id_cms_article;
					$this->view->avis->ENTETE = '';
					$this->view->avis->AVIS = '';
					$this->view->message = $avis;
				}elseif($avis == null){
					$viewRenderer = $this->getHelper('ViewRenderer');
					$viewRenderer->setNoRender();

					$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
					$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/cmsavisindex';</script>");
				}else{
					$this->view->avis = $avis;
					$this->view->message = $errorMessage;
				}
			}
		} else {
			$id_abon = $this->_request->getParam('id_abon', 0);
			$ordre_abon = $this->_request->getParam('ordre_abon', 0);
			$id_cms_article = $this->_request->getParam('id_cms_article', 0);
			if ( ( $id_abon > 0 ) && ( $id_abon > 0 ) && ( $id_abon > 0 ) ) {
				$avis = $moderer->fetchRowBibCmsAvis($id_abon, $ordre_abon, $id_cms_article);
				/*
				 * if $avis is a string, we have encountered a problem with the database
				 * if $avis is null, the categorie does not exist in the database
				 */
				if ( is_string($avis) ){
					$this->view->avis = new stdClass();
					$this->view->avis->ID_ABON = $id_abon;
					$this->view->avis->ORDRE_ABON = $ordre_abon;
					$this->view->avis->ID_CMS_ARTICLE = $id_cms_article;
					$this->view->avis->ENTETE = '';
					$this->view->avis->AVIS = '';
					$this->view->message = $avis;
				}elseif($avis == null){
					$viewRenderer = $this->getHelper('ViewRenderer');
					$viewRenderer->setNoRender();

					$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
					$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/cmsavisindex';</script>");
				}
				else{
					$this->view->avis = $avis;
				}
			}else{
							
				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.location='" . BASE_URL . "/admin/moderer/cmsavisindex';</script>");
				
			}
		}

	}


	public function updatecmsavisAction() {
		$id_abonArray = $this->_request->getParam('id_abon', 0);
		$ordre_abonArray = $this->_request->getParam('ordre_abon', 0);
		$id_cms_articleArray = $this->_request->getParam('id_cms_article', 0);
		$validateArray = $this->_request->getParam('validate', 0);

		if (!$validateArray){
			$validateArray = array();
		}

		$avis = new Class_Avis();
		$moderer = new Class_Moderer();

		foreach($validateArray as $key => $val)
		{
			$id_abon = $id_abonArray[$key];
			$ordre_abon = $ordre_abonArray[$key];
			$id_cms_article = $id_cms_articleArray[$key];

			if ($val){
				$error = $moderer->updateBibCmsAvisStatut($id_abon, $ordre_abon, $id_cms_article);
				if ($error != ''){
					$this->_redirect('admin/error/database');
				}
			}else{
				$error = $moderer->deleteBibCmsAvis($id_abon, $ordre_abon, $id_cms_article);
				$avis->maj_note_cms($id_cms_article);
				if ($error != ''){
					$this->_redirect('admin/error/database');
				}
			}

		}

		$this->_redirect('admin/moderer/cmsavisindex');
	}


	public function tagsindexAction() {
		$moderer = new Class_Moderer();

		$this->view->title = "Tags";

		$checkall = $this->_request->getParam('checkall', 0);
		if ($checkall === 'v'){
			$this->view->deletechecked = "";
			$this->view->validatechecked = "checked";
		}elseif($checkall === 'd'){
			$this->view->deletechecked = "checked";
			$this->view->validatechecked = "";
		}else{
			$this->view->deletechecked = "";
			$this->view->validatechecked = "";
		}

		$count = $this->_count; //number of rows to return at a time
		$totalUnModRows = $moderer->countBibRechTagsStatut0();
		if ( is_string($totalUnModRows) ){
			$this->_redirect('admin/error/database');
		}

		$page = (int)$this->_request->getParam('page', 1);
		if ( (($page -1) * $count) >= $totalUnModRows){
			$page = $page -1;
		}

		$offset = ($page - 1)*$count;

		if ($offset < 0) {
			$offset = 0;
		}elseif($offset > $totalUnModRows){
			$offset = $totalUnModRows;
		}

		$this->view->moderer = $moderer;
		$fetch = $moderer->fetchLimitBibRechTagsByStatut0($count, $offset);
		if ( is_string($fetch) ){
			$this->_redirect('admin/error/database');
		}

		$pages = (int)($totalUnModRows / $count);
		$mod = $totalUnModRows % $count;
		if ($mod > 0){
			$pages++;
		}

		$this->view->dateClass = new Class_Date();
		$this->view->numPage = $page;
		$this->view->nb_pages = $pages;
		$this->view->url = "/admin/moderer/tagsindex?page=";

		$this->view->pages = $pages;
		$this->view->bibTagRows = $fetch;
		$this->view->offset = $offset;
		$this->view->count = $count;
		$this->view->total = $totalUnModRows;
	}

	
	public function updatetagAction() {
		$idArray = $this->_request->getParam('id', 0);
		$validateArray = $this->_request->getParam('validate', 0);

		if (!$validateArray){
			$validateArray = array();
		}

		$moderer = new Class_Moderer();

		foreach($validateArray as $key => $val)
		{
			$id = (int)$idArray[$key];

			if ($val){
				$error = $moderer->updateBibRechTagStatut($id);
				if ($error != ''){
					$this->_redirect('admin/error/database');
				}
			}else{
				$error = $moderer->deleteBibRechTag($id);
				if ($error != ''){
					$this->_redirect('admin/error/database');
				}
			}

		}

		$this->_redirect('admin/moderer/tagsindex');

	}


	public function rechercheabonAction()	{
		$this->view->title = "Rechercher Abonnés";

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$nom = trim($filter->filter($this->_request->getPost('nom')));
			$id_abon = trim($filter->filter($this->_request->getPost('id_abon')));

			if ( ($nom != '') || ($id_abon != '') ){
				$moderer = new Class_Moderer();
				$fetch = $moderer->fetchAbonByIdNom($id_abon, $nom);
				if (is_string($fetch)){
					$this->_redirect('admin/error/database');
				}else{
					$this->view->abonFetch = $fetch;
					$this->view->message = '';
				}
			}else{
				$this->view->abonFetch = array();
				$this->view->message = "Vous devez compléter le champ 'Nom' ou le champ 'Id'";
			}


		}

		if (!$this->_request->isPost()){
			$this->view->recherche = new stdClass();
			$this->view->recherche->nom = '';
			$this->view->recherche->id_abon = '';
			$this->view->abonFetch = array();
		}else{
			// use the info that has already been posted
			$this->view->recherche = new stdClass();
			$this->view->recherche->nom = $nom;
			$this->view->recherche->id_abon = $id_abon;
		}

	}


	public function showallabonmessageAction() {
		$this->view->title = "Abonnés avec un message";

		$count = $this->_count;

		$moderer = new Class_Moderer();

		$totalAbonMessage = $moderer->countBibAbonMessage();

		$page = (int)$this->_request->getParam('page', 1);
		if ( (($page -1) * $count) >= $totalAbonMessage){
			$page = $page -1;
		}

		$offset = ($page - 1)*$count;

		if ($offset < 0) {
			$offset = 0;
		}elseif($offset > $totalAbonMessage){
			$offset = $totalAbonMessage;
		}

		$fetch = $moderer->fetchLimitBibAbonMessage($count, $offset);
		if (is_string($fetch)){
			$this->_redirect('admin/error/database');
		}else{
			$this->view->abonMessage = $fetch;
		}


		$pages = (int)($totalAbonMessage / $count);
		$mod = $totalAbonMessage % $count;
		if ($mod > 0){
			$pages++;
		}

		$this->view->numPage = $page;
		$this->view->nb_pages = $pages;
		$this->view->url = "/admin/moderer/showallabonmessage?page=";

		$this->view->pages = $pages;
		$this->view->abonCount = $totalAbonMessage;
		$this->view->moderer = $moderer;
		$this->view->offset = $offset;
		$this->view->count = $count;

	}

		
	public function addabonmessageAction() {
		$this->view->title = "Ajouter Abonné Message";
		$moderer = new Class_Moderer();
		$this->view->moderer = $moderer;
		$page = (int)$this->_request->getParam('page', 1);
		$this->view->numPage = $page;

		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$message = trim($filter->filter($this->_request->getPost('message')));
			$id_abon = trim($filter->filter($this->_request->getPost('id_abon')));
			$ordre_abon = trim($filter->filter($this->_request->getPost('ordre_abon')));

			$data = array(
			'ID_ABON' => $id_abon,
			'ORDRE_ABON' => $ordre_abon,
			'MESSAGE' => $message,
			);

			// check to see if this abon already has a message
			$hasMessage = $moderer->hasBibAbonMessage($id_abon, $ordre_abon);
			if (is_string($hasMessage)){
				$this->_redirect('admin/error/database');
			}elseif ($hasMessage){
				$error = $moderer->updateBibAbonMessage($data);
			}else{
				$error = $moderer->addBibAbonMessage($data);
			}

			if ($error != ''){
				$this->view->message = $error;
			}else{
				$this->_redirect('admin/moderer/showallabonmessage?page=' . $page);
			}

		}

		if (!$this->_request->isPost()){
			$id_abon = (int)$this->_request->getParam('id_abon', 0);
			$ordre_abon = (int)$this->_request->getParam('ordre_abon', 0);
			$this->view->abonMessage = new stdClass();
			$bibAbonMessage = $moderer->fetchBibAbonMessageByID($id_abon, $ordre_abon);
			if (is_string($bibAbonMessage)){
				$this->_redirect('admin/error/database');
			}elseif (isset($bibAbonMessage->MESSAGE)){
				$this->view->abonMessage->message = $bibAbonMessage->MESSAGE;
			}else{
				$this->view->abonMessage->message = '';
			}
			$this->view->abonMessage->id_abon = $id_abon;
			$this->view->abonMessage->ordre_abon = $ordre_abon;
		}else{
			// use the info that has already been posted
			$this->view->abonMessage = new stdClass();
			$this->view->abonMessage->message = $message;
			$this->view->abonMessage->id_abon = $id_abon;
			$this->view->abonMessage->ordre_abon = $ordre_abon;
		}

	}


	public function deleteabonmessageAction() {
		$page = (int)$this->_request->getParam('page', 1);
		$id_abon = (int)$this->_request->getParam('id_abon', 0);
		$ordre_abon = (int)$this->_request->getParam('ordre_abon', 0);
		$moderer = new Class_Moderer();

		$error = $moderer->deleteBibAbonMessage($id_abon, $ordre_abon);
		if ($error != ''){
			$this->_redirect('admin/error/database');
		}else{
			$this->_redirect('admin/moderer/showallabonmessage?page=' . $page);
		}
	}


	public function suggestionindexAction() {
		$this->view->title = 'Analyse des suggestions de commandes';

		$moderer = new Class_Moderer();

		$count = $this->_count; //number of rows to return at a time
		$totalUnModRows = $moderer->countBibOpacSuggestByNoResponse();
		if ( is_string($totalUnModRows) ){
			$this->_redirect('admin/error/database');
		}

		$page = (int)$this->_request->getParam('page', 1);
		if ( (($page -1) * $count) >= $totalUnModRows){
			$page = $page -1;
		}

		$offset = ($page - 1)*$count;

		if ($offset < 0) {
			$offset = 0;
		}elseif($offset > $totalUnModRows){
			$offset = $totalUnModRows;
		}

		$this->view->moderer = $moderer;
		$fetch = $moderer->fetchLimitBibOpacSuggestByNoResponse($count, $offset);
		if ( is_string($fetch) ){
			$this->_redirect('admin/error/database');
		}

		$this->view->bibOpacSugestRows = $fetch;

		$pages = (int)($totalUnModRows / $count);
		$mod = $totalUnModRows % $count;
		if ($mod > 0){
			$pages++;
		}

		$this->view->numPage = $page;
		$this->view->nb_pages = $pages;
		$this->view->url = "/admin/moderer/suggestionindex?page=";

		$this->view->pages = $pages;

		$this->view->offset = $offset;
		$this->view->previous = $offset - $count;
		$this->view->next = $offset + $count;
		$this->view->count = $count;
		$this->view->total = $totalUnModRows;
		$this->view->locale = Zend_Registry::get('locale');

	}


	public function updatesuggestionAction() {
		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			$reponseArray = $this->_request->getPost('reponse');
			$idArray = $this->_request->getPost('id_enreg');

			$moderer = new Class_Moderer();

			foreach($reponseArray as $key=>$reponse)
			{
				if ($reponse != ''){
					$date = new Zend_Date();
					$id_enreg = $idArray[$key];
					$data = array(
					'ID_ENREG' => $id_enreg,
					'REPONSE' => trim($filter->filter($reponse)),
					'DATE_REPONSE' => $date->get('yyyy-MM-dd HH:mm:ss'),
					);

					$error = $moderer->updateBibOpacSuggestResponse($data);

					if ($error != ''){
						$this->_redirect('admin/error/database');
					}else{
						$fetch = $moderer->fetchBibOpacSuggestByIDEnreg($id_enreg);
						if ( is_string($fetch) ){
							$this->_redirect('admin/error/database');
						}
						$enreg["TEXTE"]=($fetch->TEXTE);
						$enreg["ID_ABON"]=$fetch->ID_ABON;
						$enreg["ORDRE_ABON"]=$fetch->ORDRE_ABON;
						$enreg["DATE_REPONSE"]=$fetch->DATE_REPONSE;
						$enreg["REPONSE"]=$fetch->REPONSE;
						$mvt=new Class_Mouvement($sql);
						$error = $mvt->insereMouvement(6,$enreg);
						if ($error != ''){
							$this->_redirect('admin/error/database');
						}
					}
				}

			}
		}

		$this->_redirect('admin/moderer/suggestionindex');
	}


	public function alertAction() {
		$class_blog = new Class_Blog();
		$this->view->subview = $this->view->partial('modo/alert.phtml',
																								array('liste_alert' => $class_blog->getAllAlertes()));
		$this->_forward('index');
	}


	public function updatealerteAction() {
		$class_blog = new Class_Blog();
		$i = 1;
		foreach($_POST["avis"] as $item) {
			$elems=explode("_",$item);
			$contenu = $_POST["cmt"][$i];
			$class_blog->modererAlertCommentaire($elems[0],$elems[1],$elems[2],$elems[3],$contenu);
			$i++;
		}
		$this->_redirect('admin/modo/alert');
  }


	public function formulairesAction() {
		$this->view->formulaires = Class_Formulaire::findAll();
	}
}