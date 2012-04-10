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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  OPAC3: ABONNE
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class AbonneController extends Zend_Controller_Action
{
	private $_user = null;								// Le user connecté

//------------------------------------------------------------------------------------------------------
// Initialisation controller
//------------------------------------------------------------------------------------------------------
	function init()	{
		if ("authenticate" == $this->getRequest()->getActionName())
				return;
		
		$user = Zend_Auth::getInstance();
		if (!$user->hasIdentity()) {
			$this->_redirect('opac/auth/login');
		}	else {
			$this->_user = Zend_Auth::getInstance()->getIdentity();
		}
	}


	public function indexAction() {
		$this->_forward('fiche');
	}


	public function formationsAction() {
		$this->sessions_inscrit = array();
		$this->view->formations_by_year = Class_Formation::indexByYear(Class_Formation::getLoader()->findAll());
		$this->view->user = Class_Users::getLoader()->getIdentity();
	}


	public function inscriresessionAction() {
		if (($session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'))) && 
				!$session->isInscriptionClosed()) {
			$session->addStagiaire(Class_Users::getLoader()->getIdentity());

			if (!$session->save())
				$this->_helper->notify(implode('<br/>', $session->getErrors()));
			else
				$this->_helper->notify(sprintf('Vous êtes inscrit à la session du %s de la formation %s',
																			 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																			 $session->getLibelleFormation()));
		} else {
			$this->_helper->notify('L\'inscription à cette session est fermée');
		}
		
		$this->_redirect('/abonne/formations');
	}


	public function desinscriresessionAction() {
		if (!$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_helper->notify('Session non trouvée');
			$this->_redirect('/abonne/formations');
			return;
		}

		$user = Class_Users::getLoader()->getIdentity();
		$user->removeSessionFormation($session);
		if ($user->save()) {
			$this->_helper->notify(sprintf('Vous n\'êtes plus inscrit à la session du %s de la formation %s',
																		 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																		 $session->getLibelleFormation()));
		};

		$this->_redirect('/abonne/formations');
	}


	public function detailsessionAction() {
		if (!$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id')))
			$this->_redirect('/abonne/formations');
		$this->view->retour_action  = $this->_getParam('retour', 'formations');
		$this->view->session = $session;
	}

//------------------------------------------------------------------------------------------------------
// Voir ses avis
//------------------------------------------------------------------------------------------------------
	public function viewavisAction(){
		$this->_redirect('blog/viewauteur/id/'.$this->_user->ID_USER);
	}

//------------------------------------------------------------------------------------------------------
// Donner son avis
//------------------------------------------------------------------------------------------------------

	private function handleAvis($readSourceMethod, $writeAvisMethod)
	{
		$cls_user= new Class_Users();

		$avis = new Class_Avis();

		// Validation du formulaire
		if ($this->_request->isPost())
		{
			// Bornage du texte
			$longueur_min = Class_AdminVar::get("AVIS_MIN_SAISIE");
			$longueur_max = Class_AdminVar::get("AVIS_MAX_SAISIE");
			if(!$longueur_min) $longueur_min=10;
			if(!$longueur_max) $longueur_max=250;


			$filter = new Zend_Filter_StripTags();
			$avisSignature = trim($filter->filter($this->_request->getPost('avisSignature')));
			$avisEntete = trim($filter->filter($this->_request->getPost('avisEntete')));
			$avisTexte = trim($filter->filter($this->_request->getPost('avisTexte')));
			$avisNote = trim($filter->filter($this->_request->getPost('avisNote')));
			$id = trim($filter->filter($this->_request->getPost('id')));

			if ($avisEntete != '' and (strlen($avisTexte)>= $longueur_min and strlen($avisTexte)<= $longueur_max ) and $avisSignature != '')
			{
				$avis->$writeAvisMethod($this->_user->ID_USER,$this->_user->ROLE_LEVEL,$id,$avisNote,$avisEntete,$avisTexte);
				$cls_user->updatePseudo($this->_user, $avisSignature);

				$this->_renderRefreshOnglet();
			}
			else
			{
				if(strlen($avisTexte)< $longueur_min or strlen($avisTexte) > $longueur_max)
					$this->view->message = $this->view->_("L'avis doit avoir une longueur comprise entre %d et %d caractères", $longueur_min, $longueur_max);
				else
					$this->view->message = $this->view->_('Il faut compléter tous les champs.');
				$this->view->avisSignature = $avisSignature;
				$this->view->avisEntete = $avisEntete;
				$this->view->avisTexte = $avisTexte;
				$this->view->avisNote = $avisNote;
				$this->view->id = $id;

				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setLayoutScript('subModal.phtml');
			}
		}
		// Saisie du formulaire
		else
		{
			$id = $this->_request->getParam('id', 0);
			$this->view->message = '';
			$this->view->id = $id;
			$this->view->avisSignature = $cls_user->getNomAff($this->_user->ID_USER);
			$data = $avis->$readSourceMethod($this->_user->ID_USER, $id);

			$this->view->avisEntete = $data[0]["ENTETE"];
			$this->view->avisTexte = $data[0]["AVIS"];
			if(!$data[0]["NOTE"]) $data[0]["NOTE"]="0";
			$this->view->avisNote = $data[0]["NOTE"];
			if($this->view->avisEntete) $this->view->mode_modif=true;
			else $this->view->mode_modif=false;

			$viewRenderer = $this->getHelper('ViewRenderer');
			$viewRenderer->setLayoutScript('subModal.phtml');
		}
	}


	protected function _renderRefreshOnglet() {
		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		if (array_key_exists('onglets', $_SESSION))
			$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.refreshOnglet('".$_SESSION["onglets"]["avis"]."');</script>");
		else
			$this->getResponse()->setBody("<script>window.top.hidePopWin(false); window.top.location.reload();</script>");
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}


	function avisAction()	{
		$id_notice = $this->_request->getParam('id_notice', 0);
		$this
			->getHelper('ViewRenderer')
			->setLayoutScript('subModal.phtml');

		$user = Class_Users::getLoader()->find($this->_user->ID_USER);
		$notice = Class_Notice::getLoader()->find($id_notice);
		$avis = $user->getFirstAvisByIdNotice($id_notice);

		if ($this->_request->isPost()) {
			if ($avis == null)
				$avis = new Class_AvisNotice();

			$avis
				->setEntete($this->_request->getParam('avisEntete'))
				->setAvis($this->_request->getParam('avisTexte'))
				->setNote($this->_request->getParam('avisNote'))
				->setUser($user)
				->setClefOeuvre($notice->getClefOeuvre())
				->setStatut(0);


			if ($avis->save()) {
				$user
					->setPseudo($this->_request->getParam('avisSignature'))
					->save();
				$this->_renderRefreshOnglet();
			}

			$this->view->message = implode('.', $avis->getErrors());
		}


		if ($avis != null) {
			$this->view->id = $avis->getId();
			$this->view->avisEntete = $avis->getEntete();
			$this->view->avisTexte = $avis->getAvis();
			$this->view->avisNote = $avis->getNote();
		}
		$this->view->avisSignature = $user->getNomAff();
		$this->view->id_notice = $id_notice;
	}


	function avissupprimerAction()
	{
		$id_notice = $this->_request->getParam('id', 0);
		$id_user=$this->_user->ID_USER;
		$avis = new Class_Avis();
		$avis->supprimerAvis($id_user,$id_notice);

		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.refreshOnglet('".$_SESSION["onglets"]["avis"]."');</script>");
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}


//------------------------------------------------------------------------------------------------------
// AVIS CMS
//------------------------------------------------------------------------------------------------------
	function cmsavisAction()
	{
		$this->handleAvis('getCmsAvisById', 'ecrireCmsAvis');
	}


	function aviscmssupprimerAction()
	{
		$id_notice = $this->_request->getParam('id', 0);
		$id_user=$this->_user->ID_USER;
		$avis = new Class_Avis();
		$avis->supprimerCmsAvis($id_user,$id_notice);

		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.refreshOnglet('".$_SESSION["onglets"]["avis"]."');</script>");
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}

//------------------------------------------------------------------------------------------------------
// Proposer des tags
//------------------------------------------------------------------------------------------------------
	function tagnoticeAction()
	{

		if ($this->_request->isPost())
		{
			$filter = new Zend_Filter_StripTags();
			$abonneTag1 = trim($filter->filter($this->_request->getPost('abonneTag1')));
			$abonneTag2 = trim($filter->filter($this->_request->getPost('abonneTag2')));
			$abonneTag3 = trim($filter->filter($this->_request->getPost('abonneTag3')));
			$id = trim($filter->filter($this->_request->getPost('id')));

			if ($abonneTag1 == '' && $abonneTag2 == '' && $abonneTag3 == '')
			{
				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);</script>");

				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setNoRender();
			}
			else
			{
				$tag= new Class_TagNotice();
				$tag->creer_tag($abonneTag1,$id);
				$tag->creer_tag($abonneTag2,$id);
				$tag->creer_tag($abonneTag3,$id);

				$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
				$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top.refreshOnglet('".$_SESSION["onglets"]["tags"]."');</script>");

				$viewRenderer = $this->getHelper('ViewRenderer');
				$viewRenderer->setNoRender();
			}
		}
		else
		{
			$this->view->id = $this->_request->getParam('id_notice', 0);
			$viewRenderer = $this->getHelper('ViewRenderer');
			$viewRenderer->setLayoutScript('subModal.phtml');
		}
	}

//------------------------------------------------------------------------------------------------------
// Fiche abonné
//------------------------------------------------------------------------------------------------------
	function ficheAction()
	{
		$user = Class_Users::getLoader()->find($this->_user->ID_USER);
		$abonnement = '';
		$nb_prets = '';
		$nb_resas = '';
		$nb_retards = '';
		$nb_paniers = '';
		$user_info_popup_url = null;
		$error = '';

		// Dates d'abonnement
		if ($user->isAbonne()) {
			$date_fin=formatDate($user->getDateFin(),"1");
			if($user->isAbonnementValid())
				$abonnement = $this->view->_("Votre abonnement est valide jusqu'au %s.", $date_fin);
			else
				$abonnement = $this->view->_("Votre abonnement est terminé depuis le %s.", $date_fin);

		}
		// Fiche abonné sigb
		$fiche_sigb = $user->getFicheSigb();
		if(array_key_exists("fiche", $fiche_sigb)) {
			$nb_retards = $fiche_sigb["fiche"]->getNbPretsEnRetard();
			$str_retards = $nb_retards ? $this->view->_('(%d en retard)', $nb_retards) : '';

			$nb_prets = $fiche_sigb["fiche"]->getNbEmprunts();
			$nb_prets = $this->view->_plural($nb_prets,
																			 "Vous n'avez aucun prêt en cours.",
																			 "Vous avez %d prêt en cours",
																			 "Vous avez %d prêts en cours",
																			 $nb_prets);
			$nb_prets = sprintf("<a href='%s/abonne/prets'>%s %s</a>", BASE_URL, $nb_prets, $str_retards);

			$nb_resas = $fiche_sigb["fiche"]->getNbReservations();
			$nb_resas = $this->view->_plural($nb_resas,
																			 "Vous n'avez aucune réservation en cours.",
																			 "Vous avez %d réservation en cours",
																			 "Vous avez %d réservations en cours",
																			 $nb_resas);
			$nb_resas = sprintf("<a href='%s/abonne/reservations'>%s</a>", BASE_URL, $nb_resas);

			try {
				$user_info_popup_url = $fiche_sigb["fiche"]->getUserInformationsPopupUrl($user);
			} catch (Exception $e) {
				$error = sprintf('Erreur VSmart: %s', $e->getMessage());
			}
		}

		if(array_key_exists("erreur", $fiche_sigb))
			$error = $fiche_sigb["erreur"];
			

		// Paniers
		$nb_paniers=count($user->getPaniers());
		$nb_paniers = $this->view->_plural($nb_paniers,
																			 "Vous n'avez aucun panier de notices.",
																			 "Vous avez %d panier de notices",
																			 "Vous avez %d paniers de notices",
																			 $nb_paniers);
		$nb_paniers = sprintf("<a href='%s/panier'>%s</a>", BASE_URL, $nb_paniers);

		// Variables de vue
		$this->view->user = $user;
		$this->view->fiche = $fiche_sigb;
		$this->view->abonnement = $abonnement;
		$this->view->nb_prets = $nb_prets;
		$this->view->nb_resas = $nb_resas;
		$this->view->nb_paniers = $nb_paniers;
		$this->view->user_info_popup_url = $user_info_popup_url;
		$this->view->error = $error;
	}

//------------------------------------------------------------------------------------------------------
// Liste des prets en cours
//------------------------------------------------------------------------------------------------------
	function pretsAction()
	{
		$user = Class_Users::getLoader()->find($this->_user->ID_USER);

		$this->view->fiche = $user->getFicheSigb();
	}


	function prolongerpretAction() {
		$user = Class_Users::getLoader()->find($this->_user->ID_USER);

		$id_pret = $this->_request->getParam('id_pret');
		$cls_comm = new Class_CommSigb();

		$result = $cls_comm->prolongerPret($user, $id_pret);

		$this->view->fiche = $user->getFicheSigb();

		if ($result['statut'] == 1) {
			$this->view->fiche['message'] = $this->view->_('Prêt prolongé');
		} else {
			$this->view->fiche['erreur'] = $result['erreur'];
		}

		$this->renderScript('abonne/prets.phtml');
	}

//------------------------------------------------------------------------------------------------------
// Liste des reservations en cours
//------------------------------------------------------------------------------------------------------
	function reservationsAction()	{
		// Communication sigb
		$user = Class_Users::getLoader()->find($this->_user->ID_USER);

		// Mode Suppression
		if (null !== ($delete = $this->_getParam('id_delete'))) {
			$cls_comm = new Class_CommSigb();
			$statut_suppr = $cls_comm->supprimerReservation($this->_user, $delete);
		}

		$this->view->fiche = $user->getFicheSigb();
	}



	protected function _userForm($user) {
		$form = new Zend_Form;
		$form
			->setAction($this->view->url(array('action' => 'edit',
																				 'id' => $user->getId())))
			->setMethod('post')
			->setAttrib('id', 'user')
			->setAttrib('autocomplete', 'off');

		$textfields = array('nom' => $this->view->_('Nom'),
												'prenom' => $this->view->_('Prénom'),
												'pseudo' => $this->view->_('Pseudo'),
												'mail' => $this->view->_('E-Mail'));

		foreach($textfields	as $field => $label) {
			$element = $form
				->createElement('text', $field)
				->setLabel($label)
				->setAttrib('size', 30);
			$form->addElement($element);
		}

		$form
			->getElement('mail')
			->addValidator(new Zend_Validate_EmailAddress());

		$new_password = new Zend_Form_Element_Password('password');
		$new_password
			->setLabel($this->view->_('Nouveau mot de passe'))
			->addValidator('Identical',
										 false,
										 array('token' => $this->_request->getParam('confirm_password'),
													 'messages' => array('missingToken' => $this->view->_('Vous devez confirmer le mot de passe'),
																							 'notSame' => $this->view->_('Les mots de passe ne correspondent pas'))))
			->addValidator('StringLength', false, array(4,24));

		$confirm_password = new Zend_Form_Element_Password('confirm_password');
		$confirm_password
			->setLabel($this->view->_('Confirmez le mot de passe'))
			->addValidator('Identical',
										 false,
										 array('token' => $this->_request->getParam('password'),
													 'messages' => array('missingToken' => $this->view->_('Vous devez saisir un mot de passe'),
																							 'notSame' => $this->view->_('Les mots de passe ne correspondent pas'))))
			->setValue($user->getPassword());


		$form
			->addElement($new_password)
			->addElement($confirm_password);

		/* Abonnements aux newsletters*/
		$subscriptions = new Zend_Form_Element_MultiCheckbox('subscriptions');
		$subscriptions->setLabel($this->view->_("Abonnement aux lettres d'information"));


		$newsletters = Class_Newsletter::getLoader()->findAll();
		if (count($newsletters)>0) {
			foreach($newsletters as $nl)
				$subscriptions->addMultiOption($nl->getId(), $nl->getTitre());

			$checked_subscriptions = array();
			foreach($user->getNewsletters() as $nl)
				$checked_subscriptions []= $nl->getId();

			$subscriptions->setValue($checked_subscriptions);
			$form->addElement($subscriptions);
		}


		$form
			->addElement('submit', 'submit', array('label' => $this->view->_('Enregistrer')))
			->populate($user->toArray());

		return $form;
	}


	function editAction() {
		$user = Class_Users::getLoader()->find($this->_user->ID_USER);
		$form = $this->_userForm($user);

		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$newsletters = array();

			$newsletters_id = $this->_request->getParam('subscriptions', array());
			foreach($newsletters_id as $nl_id)
				$newsletters []= Class_Newsletter::getLoader()->find($nl_id);

			try {
				$password = $this->_request->getParam('password');
				if (empty($password))
					$password = $user->getPassword();

				$user
					->updateSIGBOnSave()
					->setNom($this->_request->getParam('nom'))
					->setPrenom($this->_request->getParam('prenom'))
					->setMail($this->_request->getParam('mail'))
					->setPseudo($this->_request->getParam('pseudo'))
					->setPassword($password)
					->setNewsletters($newsletters)
					->save();

				$this->_redirect('/abonne/fiche');
			} catch(Exception $e) {
				$form->addError($e->getMessage());
				$form->addDecorator('Errors');
			}
		}

		$this->view->form = $form;
		$this->view->help = nl2br(Class_AdminVar::get('AIDE_FICHE_ABONNE'));
	}
	
	public function authenticateAction(){
		$this->getHelper('ViewRenderer')->setNoRender();
		$response = new StdClass();
		
		$login = $this->_getParam('login');
		$password = $this->_getParam('password');
		
		$user = Class_Users::getLoader()->findFirstBy(array('login' => $login));
		
		if(!$user )
			$response->error = 'UserNotFound';
		else if (($user->getPassword() !== $password)) 
			$response->error = 'PasswordIsWrong';
		else if (!$user->isAbonnementValid()) 
			$response->error='SubscriptionExpired';
		else {
			foreach(array('id', 'login', 'password', 'nom', 'prenom') as $attribute) {
				$response->$attribute = $user->$attribute;
			}
			$response->groupes=$user->getGroupes();
			$response->date_naissance=$user->getDateNaissanceIso8601();
		}
				
		$this->_response->setBody(json_encode($response));
	}
}