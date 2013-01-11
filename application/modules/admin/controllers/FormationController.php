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
class Admin_FormationController extends Zend_Controller_Action {
	public function indexAction() {
		$this->view->titre = 'Mise à jour des formations';
		$this->view->formations_by_year = Class_Formation::indexByYear(Class_Formation::getLoader()->findAllBy(array('order' => 'id desc')));
	}


	public function addAction() {
		$this->view->titre = "Ajouter une formation";
		$formation = new Class_Formation();
		if ($this->_setupFormationFormAndSave($formation, 'add'))
			$this->_redirect('admin/formation/session_add/formation_id/'.$formation->getId());
	}


	public function editAction() {
		if (!$formation = Class_Formation::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_redirect('admin/formation');
			return;
		}
			
		$this->view->titre = "Modifier la formation: ".$formation->getLibelle();
		$this->view->formation = $formation;
		if ($this->_setupFormationFormAndSave($formation, 'edit'))
			$this->_redirect('admin/formation');
	}


	public function deleteAction() {
		if ($formation = Class_Formation::getLoader()->find((int)$this->_getParam('id')))
			$formation->delete();
		$this->_redirect('admin/formation');
	}
	

	public function modeleimpressionAction() {
		$modele = Class_ModeleFusion::getLoader()->find($this->_getParam('id'));
		$modele
			->setContenu($this->_request->getPost($modele->getNom()))
			->save();
		$this->_helper->notify('Modèle sauvegardé');
		$this->_redirect('admin/formation/session_impressions/id/'.$this->_getParam('session_id'));
	}


	protected function _setupFormationFormAndSave($formation, $action) {
		$saved = false;

		$form = $this->_formationForm($formation, $action);
		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			$formation
				->updateAttributes($this->_request->getPost())
				->save();
			return true;
		}

		$this->view->form = $form;
		$this->renderScript('formation/formation_form.phtml');
		return false;
	}


	protected function _formationForm($formation, $action) {
		return $this->view
			->newForm(array('id' => 'formationForm'))
			->setMethod('post')
			->setAction($this->view->url(array('action' => $action)))
			->addElement('text', 'libelle', array(
																						'label' => 'Libellé *',
																						'size'	=> 50,
																						'required' => true,
																						'allowEmpty' => false	))
			->addElement('ckeditor', 'description', array(
																										'label' => 'Description',
																										'allowEmpty' => true))
			->addDisplayGroup(
												array('libelle', 'description'), 
												'formation',
												array('legend' => 'Formation'))
			->populate($formation->toArray());
	}


	protected function _readPostDate($date) {
		return implode('-', array_reverse(explode('/', $date)));
	}


	public function sessiondeleteAction() {
		if ($session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id')))
			$session->delete();
		$this->_redirect('admin/formation');
	}


	public function sessioneditAction() {
		if (!$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_redirect('admin/formation');
			return;
		}

		$this->view->titre = sprintf('Modifier la session du %s de la formation "%s"',
																 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																 $session->getLibelleFormation());

		$this->view->formation = $session->getFormation();
		$this->_setupSessionFormAndSave($session, 'sessionedit');
	}


	public function presencesAction() {
		$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'));

		if ($this->_request->isPost()) {
			$user_ids = $this->_request->getPost('user_ids');
			foreach ($session->getSessionFormationInscriptions() as $inscription) {
				$inscription
					->setPresence(in_array($inscription->getStagiaire()->getId(), $user_ids))
					->save();
			}

			$this->_helper->notify('Présences sauvegardées');
			$this->_redirect('admin/formation/presences/id/'.$session->getId());
			return;
		}

		$this->view->titre = sprintf('Personnes présentes à la session du %s de la formation "%s"',
																 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																 $session->getLibelleFormation());
		$this->view->formation = $session->getFormation();
		$this->view->form = $this->_presencesForm($session);
	}


	public function ficheemargementAction() {
		$this->_renderLettreFusion(new SessionOneLetterPerDayFusionStrategy('FORMATION_EMARGEMENT'));
	}


	public function listestagiairesAction() {
		$this->_renderLettreFusion(new SessionFusionStrategy('FORMATION_LISTE_STAGIAIRES'));
	}


	public function convocationsAction() {
		$this->_renderLettreFusion(new SessionStagiairesFusionStrategy('FORMATION_CONVOCATION'));
	}


	public function attestationsAction() {
		$this->_renderLettreFusion(new SessionStagiairesFusionStrategy('FORMATION_ATTESTATION'));
	}


	public function refusAction() {
		$this->_renderLettreFusion(new SessionStagiairesFusionStrategy('FORMATION_REFUS'));
	}


	public function _renderLettreFusion($fusion_strategy) {
		$this->_helper->getHelper('viewRenderer')->setLayoutScript('empty.phtml');
		$session_formation = Class_SessionFormation::getLoader()->find($this->_getParam('id'));

		$this->view->lettre = $fusion_strategy->getContenuFusionne($session_formation);

		$this->renderScript('formation/lettre-fusion.phtml');
	}


	public function sessionimpressionsAction() {
		$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'));

		$this->view->titre = sprintf('Impressions pour la session du %s de la formation "%s"',
																 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																 $session->getLibelleFormation());
		$this->view->session = $session;

		$this->view->impressions = array(
					 'Liste des stagiaires' => array('action' => 'liste_stagiaires', 
																					 'form' => $this->_modeleFusionForm('FORMATION_LISTE_STAGIAIRES', 
																																							$session)),

					 'Fiche d\'émargement' => array('action' => 'fiche_emargement', 
																					'form' => $this->_modeleFusionForm('FORMATION_EMARGEMENT', 
																																						 $session)),
					 'Convocations' => array('action' => 'convocations',
																	 'form' => $this->_modeleFusionForm('FORMATION_CONVOCATION', 
																																			$session)),

					 'Attestations' => array('action' => 'attestations',
																	 'form' => $this->_modeleFusionForm('FORMATION_ATTESTATION', 
																																			$session)),

					 'Refus' => array('action' => 'refus',
														'form' => $this->_modeleFusionForm('FORMATION_REFUS', 
																															 $session)));
	}


	protected function _presencesForm($session) {
		$user_checkboxes = new Zend_Form_Element_MultiCheckbox('user_ids');
		$ids = array();
		foreach($session->getSessionFormationInscriptions() as $inscription) {
			$user = $inscription->getStagiaire();
			if ($inscription->isPresent())
				$ids[]=$user->getId();
			$user_checkboxes->addMultiOption($user->getId(),
																			 sprintf('%s %s - %s',
																							 $user->getNom(),
																							 $user->getPrenom(),
																							 $user->getLogin()));
		}
		$user_checkboxes->setValue($ids);

		return $this->view
			->newForm(array('id' => 'presencesForm'))
			->setMethod('post')
			->addElement($user_checkboxes)
			->addDisplayGroup(array('user_ids'),
												'presences',
												array('legend' => 'Stagiaires présents'));
	}


	public function exportinscriptionsAction() {
		$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('id'));

		$this->_helper->getHelper('ViewRenderer')->setNoRender(true);
		
		// on va devoir modifier les entetes HTTP
		$response = Zend_Controller_Front::getInstance()->getResponse();
		$response->canSendHeaders(true);
		$response->setHeader('Content-Type', 
												 'text/csv;charset=utf-8', 
												 //												 sprintf('text/csv; name="session_%d.csv"', $session->getId()), 
												 true);
		$response->setHeader('Content-Disposition', 
												 sprintf('attachment; filename="session_%d.csv"', $session->getId()), 
												 true);

		$content = sprintf("Formation;%s;\n".
											 "Session;%s;\n".
											 "Durée;%dh;\n".
											 "Effectif;%d-%d;\n",
											 $session->getLibelleFormation(),
											 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
											 $session->getDuree(),
											 $session->getEffectifMin(),
											 $session->getEffectifMax());

		$content .= sprintf("\nNom;Prénom;Identifiant\n");

		foreach ($session->getStagiairesSortedByNom() as $stagiaire) {
			$content .= sprintf("%s;%s;%s\n",
													$stagiaire->getNom(),
													$stagiaire->getPrenom(),
													$stagiaire->getLogin());
		}

		$response->setBody($content);
	}


	public function sessionaddAction() {
		if (!$formation = Class_Formation::getLoader()->find((int)$this->_getParam('formation_id'))) {
			$this->_redirect('admin/formation');
			return;
		}

		$this->view->titre = sprintf('Nouvelle session de la formation "%s"',
																 $formation->getLibelle());

		$session = Class_SessionFormation::getLoader()
			->newInstance()
			->setFormation($formation);

		$this->view->formation = $formation;
		$this->_setupSessionFormAndSave($session, 'sessionadd');
	}


	public function inscriptionsAction() {
		$session = Class_SessionFormation::getLoader()->find((int)$this->_getParam('session_id'));

		if ($id_user_to_delete = $this->_getParam('delete')) {
			$user_to_delete = Class_Users::getLoader()->find($id_user_to_delete);
			$session
				->removeStagiaire($user_to_delete)
				->saveWithoutValidation();

			$redirect_url = '/admin/formation/inscriptions/session_id/'.$session->getId();
			if ($_GET)
				$redirect_url .= '?'.http_build_query($_GET);

			$this->_redirect($redirect_url);
			return;
		}

		if ($this->_request->isPost() 
				&& ($ids_users_to_subscribe = $this->_request->getPost('users'))) {
			foreach($ids_users_to_subscribe as $id)
				$session->addStagiaire(Class_Users::getLoader()->find($id));
			$session->save();
		}
		
		$this->view->session = $session;

		$this->view->getHelper('SubscribeUsers')
			->setUsers($session->getStagiairesSortedByNom())
			->setSearch($this->_getParam('search'))
			->filterByRight(Class_UserGroup::RIGHT_SUIVRE_FORMATION)
			->setSearchLabel($this->view->_('Rechercher des stagiaires'))
			->setSubmitLabel($this->view->_('Inscrire les stagiaires sélectionnés'));

		$this->view->titre = sprintf('Liste des participants à la session du %s de la formation "%s"',
																 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY'),
																 $session->getFormation()->getLibelle());
	}


	protected function _setupSessionFormAndSave($session, $action) {
		$form = $this->_sessionForm($session, $action);

		if ($this->_request->isPost()) {
			$intervenants = array();
			$post = $this->_request->getPost();

			if (is_array($intervenant_ids = $this->_request->getPost('intervenant_ids'))) {
				foreach($intervenant_ids as $intervenant_id)
					$intervenants []= Class_Users::getLoader()->find($intervenant_id);
				unset($post['intervenant_ids']);
			}

			$session
				->updateAttributes($post)
				->setDateDebut($this->_readPostDate($this->_request->getPost('date_debut')))
				->setDateFin($this->_readPostDate($this->_request->getPost('date_fin')))
				->setDateLimiteInscription($this->_readPostDate($this->_request->getPost('date_limite_inscription')))
				->setIntervenants($intervenants);
			
			if ($form->isValid($session)) {
				$session->save();
				$this->_helper->notify(sprintf('Session du %s sauvegardée', 
																			 $this->view->humanDate($session->getDateDebut(), 'd MMMM YYYY')));
				$this->_redirect('admin/formation/session_edit/id/'.$session->getId());
				return true;
			}
		}

		$this->view->form = $form;
		$this->renderScript('formation/formation_form.phtml');		
		return false;
	}


	protected function _sessionForm($session, $action) {
		$intervenants = Class_Users::getLoader()->findAllByRightDirigerFormation();
		$intervenants_checkboxes = new Zend_Form_Element_MultiCheckbox('intervenant_ids');
		foreach($intervenants as $user) 
			$intervenants_checkboxes->addMultiOption($user->getId(),
																							 sprintf('%s %s - %s',
																											 $user->getNom(),
																											 $user->getPrenom(),
																											 $user->getLogin()));
		$ids = array();
		foreach($session->getIntervenants() as $intervenant)
			$ids []= $intervenant->getId();
		$intervenants_checkboxes->setValue($ids);
			
		return $this->view
			->newForm(array('id' => 'sessionForm'))
			->setMethod('post')
			->setAction($this->view->url(array('action' => $action)))
			->addElement('datePicker', 'date_debut', array(
																										 'label' => 'Date début *',
																										 'size'	=> 10,
																										 'required' => true,
																										 'allowEmpty' => false	))
			->addElement('datePicker', 'date_fin', array(
																										 'label' => 'Date fin',
																										 'size'	=> 10	))
			->addElement('datePicker', 'date_limite_inscription', array(
																																	'label' => 'Date limite d\'inscription *',
																																	'size'	=> 10	))
			->addElement('text', 'effectif_min', array(
																								 'label' => 'Effectif minimum *',
																								 'size'	=> 2,
																								 'required' => true,
																								 'allowEmpty' => false,
																								 'validators' => array('int')))

			->addElement('text', 'effectif_max', array(
																								 'label' => 'Effectif maximum *',
																								 'size'	=> 2,
																								 'required' => true,
																								 'allowEmpty' => false,
																								 'validators' => array('int')))
			->addElement('text', 'duree', array(
																					'label' => 'Durée (h)',
																					'size'	=> 2,
																					'validators' => array('int')))

			->addElement('text', 'horaires', array(
																						 'label' => 'Horaires *',
																						 'size'	=> 50,
																						 'required' => true,
																						 'allowEmpty' => false))

			->addElement('select', 'lieu_id', array('label' => 'Lieu',
																							'multiOptions' => Class_Lieu::getAllLibelles()))

			->addElement('text', 'cout', array(
																				 'label' => 'Coût',
																				 'size'	=> 6,
																				 'validators' => array('int')))

			->addElement('checkbox', 'is_annule', array('label' => 'Session annulée'))

			->addElement($intervenants_checkboxes)

			->addElement('ckeditor', 'contenu', array(
																										 'label' => '',
																										 'required' => true,
																										 'allowEmpty' => false))
			->addElement('ckeditor', 'compte_rendu', array('label' => ''))

			->addDisplayGroup(
												array('date_debut',
															'date_fin',
															'date_limite_inscription',
															'effectif_min',
															'effectif_max',
															'lieu_id',
															'horaires',
															'duree',
															'cout',
															'is_annule'), 
												'session',
												array('legend' => 'Session'))
			->addDisplayGroup(
												array('intervenant_ids'),
												'intervenants',
												array('legend' => 'Intervenants'))
			->addDisplayGroup(
												array('contenu'), 
												'programme',
												array('legend' => 'Contenu *'))
			->addDisplayGroup(
												array('compte_rendu'),
												'bilan',
												array('legend' => 'Compte-rendu'))
			->populate($session->toArray());
	}


	protected function _modeleFusionForm($modele_fusion_name, $session) {
		$modele_fusion = Class_ModeleFusion::get($modele_fusion_name);
		return $this->view
			->newForm(array('id' => $modele_fusion->getNom().'_FORM'))
			->setMethod('post')
			->setAction($this->view->url(array('action' => 'modele_impression',
																				 'id' => $modele_fusion->getId(),
																				 'session_id' => $session->getId())))
			->addElement('ckeditor', 
									 $modele_fusion->getNom(), 
									 array(
												 'label' => '',
												 'value' => $modele_fusion->getContenu(),
												 'required' => true,
												 'allowEmpty' => false));
	}
}



class AbstractSessionFusionStrategy {
	protected $_modele_fusion;

	public function __construct($nom) {
		$this->_modele_fusion = Class_ModeleFusion::get($nom);
	}
}


class SessionFusionStrategy extends AbstractSessionFusionStrategy{
	public function getContenuFusionne($session_formation) {
		return $this->_modele_fusion
			->setDataSource(array("session_formation" => $session_formation,
														"date_jour" => new FusionDateContext()))
			->getContenuFusionne();
	}
}


class SessionOneLetterPerDayFusionStrategy extends AbstractSessionFusionStrategy{
	public function getContenuFusionne($session_formation) {
		$date_context = new FusionDateContext($session_formation->getDateDebut());

		$nb_jours = $date_context->numberOfDaysTo($session_formation->getDateFin());

		$lettres = array();
		for ($i=0; $i<=$nb_jours; $i++) {
			$lettres []= $this->_modele_fusion
				->setDataSource(array("session_formation" => $session_formation,
															"date_context" => $date_context,
															"date_jour" => new FusionDateContext()))
				->getContenuFusionne();
			$date_context->forwardOneDay();
		}

		return implode('<div style="page-break-after: always"></div>', $lettres);
	}
}




class FusionDateContext {
	protected $_current_date;

	public function __construct($datestr=null) {
		$this->_current_date = $this->dateStringToZendDate($datestr);
	}


	public function dateStringToZendDate($datestr) {
		return new Zend_Date($datestr, null, Zend_Registry::get('locale'));
	}


	public function forwardOneDay() {
		$this->_current_date->add(1, Zend_Date::DAY);		
		return $this;
	}


	public function getTexte() {
		return $this->_current_date->toString('d MMMM yyyy');
	}


	public function numberOfDaysTo($other_datestr) {
		if (!$other_datestr)
			return 0;

		$other_date = $this->dateStringToZendDate($other_datestr);
		$other_date->sub($this->_current_date);
		return ($other_date->toValue(Zend_Date::DAY)-1);
	}


	public function callGetterByAttributeName($attribute) {
		return call_user_func(array($this, 'get'.Storm_Inflector::camelize($attribute)));
	}
}




class SessionStagiairesFusionStrategy extends AbstractSessionFusionStrategy{
	public function getContenuFusionne($session_formation) {
		$lettres = array();
		$stagiaires = $session_formation->getStagiaires();
		foreach($stagiaires as $stagiaire)
			$lettres []= $this->_modele_fusion
													->setDataSource(array('session_formation' => $session_formation,
																								'stagiaire' => $stagiaire,
																								"date_jour" => new FusionDateContext()))
													->getContenuFusionne();


		return implode('<div style="page-break-after: always"></div>', $lettres);
	}
}

?>