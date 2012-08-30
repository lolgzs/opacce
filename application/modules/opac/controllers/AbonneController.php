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
class AbonneController extends Zend_Controller_Action {
	use Trait_Translator;

	protected $_user = null;								// Le user connecté

	public function init()	{
		if ("authenticate" == $this->getRequest()->getActionName())
				return;
		
		if (!$this->_user = Class_Users::getLoader()->getIdentity()) {
			$this->_redirect('auth/login');
			return;
		}	
			
		$this->clearEmprunteurCache();
	}


	protected function clearEmprunteurCache() {
		if (in_array($this->getRequest()->getActionName(), array('prets', 'reservations', 'fiche')))
			Class_WebService_SIGB_EmprunteurCache::newInstance()->remove($this->_user);
	}


	public function indexAction() {
		$this->_forward('fiche');
	}


	public function formationsAction() {
		$this->sessions_inscrit = array();
		$this->view->formations_by_year = Class_Formation::indexByYear(Class_Formation::getLoader()->findAll());
		$this->view->user = $this->_user;
	}


	public function inscriresessionAction() {
		if (($session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'))) && 
				!$session->isInscriptionClosed()) {
			$session->addStagiaire($this->_user);

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

		$this->_user->removeSessionFormation($session);
		if ($this->_user->save()) {
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


	public function viewavisAction(){
		$this->_redirect('blog/viewauteur/id/'.$this->_user->getId());
	}


	private function handleAvis($readSourceMethod, $writeAvisMethod) {
		$cls_user= new Class_Users();

		$avis = new Class_Avis();

		// Validation du formulaire
		if ($this->_request->isPost()) {
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
					$this->view->message = $this->_("L'avis doit avoir une longueur comprise entre %d et %d caractères", $longueur_min, $longueur_max);
				else
					$this->view->message = $this->_('Il faut compléter tous les champs.');
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
		$js = 'location.reload()';
		if (array_key_exists('onglets', $_SESSION))
			$js = "refreshOnglet('" . $_SESSION["onglets"]["avis"] . "')";
		$this->getResponse()->setBody("<script>window.top.hidePopWin(false);window.top." . $js. ";</script>");
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
	}


	public function avisAction()	{
		$id_notice = $this->_request->getParam('id_notice', 0);
		$this
			->getHelper('ViewRenderer')
			->setLayoutScript('subModal.phtml');

		$notice = Class_Notice::getLoader()->find($id_notice);
		$avis = $this->_user->getFirstAvisByIdNotice($id_notice);

		if ($this->_request->isPost()) {
			if ($avis == null)
				$avis = new Class_AvisNotice();

			$avis
				->setEntete($this->_request->getParam('avisEntete'))
				->setAvis($this->_request->getParam('avisTexte'))
				->setNote($this->_request->getParam('avisNote'))
				->setUser($this->_user)
				->setClefOeuvre($notice->getClefOeuvre())
				->setStatut(0);


			if ($avis->save()) {
				$this->_user
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
		$this->view->avisSignature = $this->_user->getNomAff();
		$this->view->id_notice = $id_notice;
	}


	public function cmsavisAction()	{
		$this->handleAvis('getCmsAvisById', 'ecrireCmsAvis');
	}


	public function tagnoticeAction() {
		if ($this->_request->isPost()) {
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


	public function ficheAction() {
		$fiche_sigb = $this->_user->getFicheSigb();

		$this->view->error = isset($fiche_sigb['erreur']) ? $fiche_sigb["erreur"] : '';
		$this->view->user = $this->_user;
	}


	public function pretsAction()	{
		$this->view->fiche = $this->_user->getFicheSigb();
		$this->view->user = $this->_user;
	}


	public function prolongerpretAction() {
		$id_pret = $this->_request->getParam('id_pret');
		$cls_comm = new Class_CommSigb();

		$result = $cls_comm->prolongerPret($this->_user, $id_pret);

		$this->view->fiche = $this->_user->getFicheSigb();

		if ($result['statut'] == 1) {
			$this->view->fiche['message'] = $this->_('Prêt prolongé');
		} else {
			$this->view->fiche['erreur'] = $result['erreur'];
		}

		$this->renderScript('abonne/prets.phtml');
	}


	public function reservationsAction()	{
		// Mode Suppression
		if (null !== ($delete = $this->_getParam('id_delete'))) {
			$cls_comm = new Class_CommSigb();
			$statut_suppr = $cls_comm->supprimerReservation($this->_user, $delete);
		}

		$this->view->fiche = $this->_user->getFicheSigb();
		$this->view->user = $this->_user;
	}



	protected function _userForm($user) {
		$form = new Zend_Form;
		$form
			->setAction($this->view->url(array('action' => 'edit',
																				 'id' => $user->getId())))
			->setMethod('post')
			->setAttrib('id', 'user')
			->setAttrib('autocomplete', 'off');

		$textfields = array('nom' => $this->_('Nom'),
												'prenom' => $this->_('Prénom'),
												'pseudo' => $this->_('Pseudo'),
												'mail' => $this->_('E-Mail'));

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
			->setLabel($this->_('Nouveau mot de passe'))
			->addValidator('Identical',
										 false,
										 array('token' => $this->_request->getParam('confirm_password'),
													 'messages' => array('missingToken' => $this->_('Vous devez confirmer le mot de passe'),
																							 'notSame' => $this->_('Les mots de passe ne correspondent pas'))))
			->addValidator('StringLength', false, array(4,24));

		$confirm_password = new Zend_Form_Element_Password('confirm_password');
		$confirm_password
			->setLabel($this->_('Confirmez le mot de passe'))
			->addValidator('Identical',
										 false,
										 array('token' => $this->_request->getParam('password'),
													 'messages' => array('missingToken' => $this->_('Vous devez saisir un mot de passe'),
																							 'notSame' => $this->_('Les mots de passe ne correspondent pas'))))
			->setValue($user->getPassword());


		$form
			->addElement($new_password)
			->addElement($confirm_password);

		/* Abonnements aux newsletters*/
		$subscriptions = new Zend_Form_Element_MultiCheckbox('subscriptions');
		$subscriptions->setLabel($this->_("Abonnement aux lettres d'information"));


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
			->addElement('submit', 'submit', array('label' => $this->_('Enregistrer')))
			->populate($user->toArray());

		return $form;
	}


	public function editAction() {
		$form = $this->_userForm($this->_user);

		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$newsletters = array();

			$newsletters_id = $this->_request->getParam('subscriptions', array());
			foreach($newsletters_id as $nl_id)
				$newsletters []= Class_Newsletter::getLoader()->find($nl_id);

			try {
				$password = $this->_request->getParam('password');
				if (empty($password))
					$password = $this->_user->getPassword();

				$this->_user
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
	

	public function authenticateAction() {
		$this->getHelper('ViewRenderer')->setNoRender();
		
		$response = new StdClass();
		$response->auth = 0;
		$response->until = '';

		$request = Class_Multimedia_AuthenticateRequest::newWithRequest($this->_request);

		if ($user = $request->getUser()) {
			foreach (array('id', 'login', 'password', 'nom', 'prenom') as $attribute)
				$response->$attribute = $user->$attribute;

			$response->groupes = $user->getUserGroupsLabels();
			$response->date_naissance = $user->getDateNaissanceIso8601();
		}

		if ($request->isValid()) {
			$response->auth = 1;
			$response->until = date('c', $request->getCurrentHoldEnd());
		} else {
			$response->error = $request->getError();
		} 


		$this->_response->setBody(json_encode($response));
	}


	public function multimediaHoldLocationAction() {
		$bean = Class_Multimedia_ReservationBean::newInSession();

		if (null != $this->_getParam('location')) {
			$bean->location = $this->_getParam('location');
			$this->_redirect('/abonne/multimedia-hold-day');
			return;
		}

		$this->view->locations = array_filter(Class_Multimedia_Location::findAllBy(['order' => 'libelle']),
																					function($location) {return $location->numberOfOuvertures() > 0;});
		$this->view->timelineActions = $this->_getTimelineActions('location');
	}


	public function multimediaHoldDayAction() {
		$bean = Class_Multimedia_ReservationBean::current();

		/* Vérification du quota sur le jour choisi */
		$day = $this->_getParam('day');
		$quotaErrorType = null;
		if (null != $day) {
			$quotaErrorType = $this->_user->getMultimediaQuotaErrorForDay($day);
			switch ($quotaErrorType) {
			  case Class_Multimedia_DeviceHold::QUOTA_NONE:
					$this->view->quotaError = $this->_('Vous n\'êtes pas autorisé à effectuer une réservation');
					break;
			  case Class_Multimedia_DeviceHold::QUOTA_DAY:
					$this->view->quotaError = $this->_('Quota déjà atteint ce jour, choisissez un autre jour.');
					break;
			  case Class_Multimedia_DeviceHold::QUOTA_WEEK:
					$this->view->quotaError = $this->_('Quota déjà atteint cette semaine, choisissez une autre semaine.');
					break;
			  case Class_Multimedia_DeviceHold::QUOTA_MONTH:
					$this->view->quotaError = $this->_('Quota déjà atteint ce mois, choisissez un autre mois.');
					break;
			}
		}
				
		/* Choix valide, passage à l'écran suivant */
		if (null != $day && null == $quotaErrorType) {
			$bean->day = $day;
			$this->_redirect('/abonne/multimedia-hold-hours');
			return;
		}
		
		$this->getRequest()->setParam('day', 0);
		if (!$bean->isCurrentStateValidForRequest($this->getRequest())) {
			$this->_redirect('/abonne/'.$bean->currentState());
			return;
		}

		/* Rendu du calendrier avec les jours sélectionnables */
		$location = $bean->getLocation();
		$this->view->minDate = $location->getMinDate();
		$this->view->maxDate = $location->getMaxDate();
		$holidayStamps = array_map(
			function($item) {return $item * 1000;},
			array_merge(
				Class_Date_Holiday::getTimestampsForYear(),
				Class_Date_Holiday::getTimestampsForYear(date('Y') + 1)
			)
		);

		$js_opened_days = implode(',', array_map(
																						 function ($day) { return '"'.$day.'"'; }, 
																						 $location->getHoldableDays()));


		$beforeShowDay = 'var result = [true, \'\'];
		var stamps = [' . implode(', ', $holidayStamps) . '];
		$.each(stamps, function(i, stamp) {
			var holiday = new Date();
			holiday.setTime(stamp);
			if (date.getDate() == holiday.getDate()
				&& date.getMonth() == holiday.getMonth()
				&& date.getFullYear() == holiday.getFullYear()) {
			  result[0] = false;
			  return result;
			}
		});
		if (-1 == $.inArray($.datepicker.formatDate(\'yy-mm-dd\', date), 
                        [' . $js_opened_days . '])) {
			result[0] = false;
		}
	  return result;';

		$this->view->beforeShowDay = $beforeShowDay;
		$this->view->timelineActions = $this->_getTimelineActions('day');
	}


	public function multimediaHoldHoursAction() {
		if (!$bean = $this->_getDeviceHoldBean())
			return;

		$location = $bean->getLocation();
		if ($this->_getParam('time') && $this->_getParam('duration')) {
			$holdLoader = Class_Multimedia_DeviceHold::getLoader();
			$start = $holdLoader->getTimeFromDayAndTime($bean->day, $this->_getParam('time'));
			$end = $holdLoader->getTimeFromStartAndDuration($start, $this->_getParam('duration'));

			if (0 < $holdLoader->countBetweenTimesForUser($start, $end, $this->_user)) {
				$this->view->error = $this->_('Vous avez déjà une réservation dans ce créneau horaire');
			}

			if ($start < $location->getMinTimeForDate($bean->day)
				|| $end > $location->getMaxTimeForDate($bean->day)) {
				$this->view->error = $this->_('Ce créneau n\'est pas dans les heures d\'ouverture.');
			}

			if (!$this->view->error) {
				$bean->time = $this->_getParam('time');
				$bean->duration = (int)$this->_getParam('duration');
				$this->_redirect('/abonne/multimedia-hold-group');
				return;
			}
		}
		
		$this->view->timelineActions = $this->_getTimelineActions('hours');
		$this->view->form = $this->multimediaHoldHoursForm($bean, $location);
	}


	public function multimediaHoldHoursForm($bean, $location) {
		return $this->view
			->newForm(['id' => 'hold-hours'])
			->setMethod('get')
			->addElement('select', 'time', ['label' => $this->_('À partir de quelle heure ?'),
																			'multiOptions' => $location->getStartTimesForDate($bean->day)])
			->addElement('select', 'duration', ['label' => $this->_('Pour quelle durée ?'),
																					'multiOptions' => $location->getDurations()])
			->addDisplayGroup(['time', 'duration'], 'choix', ['legend' => ''])
			->addElement('submit', 'submit', ['label' => $this->_('Choisir')]);
	}


	public function multimediaHoldGroupAction() {
		if (!$bean = $this->_getDeviceHoldBean())
			return;

		$this->view->groups = $bean->getGroups();
		$this->view->timelineActions = $this->_getTimelineActions('group');
	}


	public function multimediaHoldDeviceAction() {
		if (!$bean = $this->_getDeviceHoldBean())
			return;

		$this->view->timelineActions = $this->_getTimelineActions('device');
		$this->view->devices = $bean->getGroup()->getHoldableDevicesForDateTimeAndDuration($bean->day,
																																											 $bean->time,
																																											 $bean->duration);
	}


	public function multimediaHoldConfirmAction() {
		if (!$bean = $this->_getDeviceHoldBean())
			return;

		if ($this->_getParam('validate')) {
			$hold = Class_Multimedia_DeviceHold::getLoader()->newFromBean($bean);
			$hold->save();
			$this->_redirect('/abonne/multimedia-hold-view/id/' . $hold->getId());
			return;
		}

		$this->view->timelineActions = $this->_getTimelineActions('confirm');
		$this->view->location = $bean->getLocation()->getLibelleBib();
		$this->view->day = strftime('%d %B %Y', strtotime($bean->day));
		$this->view->time = str_replace(':', 'h', $bean->time);
		$this->view->duration = $bean->duration . 'mn';

		$device = $bean->getDevice();
		$this->view->device = $device->getLibelle() . ' - ' . $device->getOs();
	}


	public function multimediaHoldViewAction() {
		if (null == ($hold = Class_Multimedia_DeviceHold::find((int)$this->_getParam('id')))) {
			$this->_redirect('/abonne/fiche');
			return;
		}

		if ($this->_user != $hold->getUser()) {
			$this->_redirect('/abonne/fiche');
			return;
		}

		if ($this->_getParam('delete')) {
			$hold->delete();
			$this->_redirect('/abonne/fiche');
			return;
		}
			
		$this->view->location = $hold->getLibelleBib();
		$this->view->day = strftime('%d %B %Y', $hold->getStart());
		$this->view->time = strftime('%Hh%M', $hold->getStart());
		$this->view->duration = (($hold->getEnd() - $hold->getStart()) / 60)  . 'mn';
		$this->view->device = $hold->getLibelleDevice() . ' - ' . $hold->getOs();
	}
		

	/**
	 * @param $current string
	 * @return array
	 */
	protected function _getTimelineActions($current) {
		$knownActions = ['location' => $this->_('Lieu'),
										 'day' => $this->_('Jour'),
										 'hours' => $this->_('Horaires'),
										 'group' => $this->_('Section'),
										 'device' => $this->_('Poste'),
										 'confirm' => $this->_('Confirmation')];

		$actions = array();
		foreach ($knownActions as $knownAction => $label) {
			$action = $this->_getTimelineActionWithNameAndAction($label, $knownAction);
			if ($current == $knownAction)
				$action[ZendAfi_View_Helper_Timeline::CURRENT] = true;
			$actions[] = $action;
		}
		return $actions;
	}


	/** @return array */
	protected function _getTimelineActionWithNameAndAction($name, $action) {
		return array(ZendAfi_View_Helper_Timeline::LABEL => $name,
			           ZendAfi_View_Helper_Timeline::CURRENT => false,
			           ZendAfi_View_Helper_Timeline::URL => $this->view->url(array('controller' => 'abonne',
										                                                         'action' => 'multimedia-hold-' . $action),
									                                                     null, true));
	}


	/** @return stdClass */
	protected function _getDeviceHoldBean() {
		$bean = Class_Multimedia_ReservationBean::current();
		if (!$bean->isCurrentStateValidForRequest($this->getRequest())) {
			$this->_redirect('/abonne/'.$bean->currentState());
			return null;
		}

		return $bean;
	}


	public function suggestionAchatAction() {
		if (Class_SuggestionAchat::find($this->_getParam('id'))) {
			$this->_forward('suggestion-achat-ok');
			return;
		}
			
		$form = new ZendAfi_Form_SuggestionAchat();

		if ($this->_request->isPost()) {
			$post = $this->_request->getPost();
			unset($post['submit']);
			$suggestion = (new Class_SuggestionAchat())
				->updateAttributes($post)
				->setUserId(Class_Users::currentUserId());

			if ($form->isValid($suggestion)) {
				$suggestion->save();
				$suggestion->sendMail('noreply@'.$this->_request->getHttpHost());
				$this->_redirect('/opac/abonne/suggestion-achat/id/'.$suggestion->getId());
			}
		}

		$this->view->form = $form;
	}


	public function suggestionAchatOkAction() {	}
}