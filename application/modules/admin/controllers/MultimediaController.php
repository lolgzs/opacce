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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301	 USA 
 */

class Admin_MultimediaController extends ZendAfi_Controller_Action {
	public function getRessourceDefinitions() {

		return [
				'model' => ['class' => 'Class_Multimedia_Location', 'name' => 'site'],

				'messages' => ['successful_save' => 'Site %s sauvegardé'],

				'actions' => ['edit' => ['title' => 'Modifier un site multimédia'],
											'index' => ['title' => 'Sites multimédia']],

				'display_groups' => ['localisation' => ['legend' => 'Localisation',
																								'elements' => $this->getLocalisationFields()],

														 'config' => ['legend' => 'Réservation',
																					'elements' => $this->getConfigFields()],

														 'config_auto' => ['legend' => 'Réservation automatique',
																							 'elements' => $this->getConfigAutoFields()]
														 ]
						];
	}


	public function getLocalisationFields() {
		$libelles = [];
		foreach (Class_Bib::findAllBy(['order' => 'libelle']) as $bib)
			$libelles[$bib->getId()] = $bib->getLibelle();
		
		return ['id_site' => ['element' => 'select',
													'options' => ['multioptions' => $libelles]]];
	}


	public function getConfigFields() {
		return ['slot_size' => ['element' => 'text',
														'options' => ['label' => 'Durée d\'un créneau (en minutes)',
																					'title'=> 'en minutes',
																					'size'	=> 4,
																					'required' => true,
																					'allowEmpty' => false,
																					'validators' => ['digits']]],

						'max_slots' => ['element' => 'text',
														'options' => ['label' => 'Nombre maximum de créneaux réservables simultanément',
																					'title' => 'en nombre de "slots"',
																					'size' => 4,
																					'required' => true,
																					'allowEmpty' => false,
																					'validators' => ['digits']]],

						'hold_delay_min' => ['element' => 'text',
																 'options' => ['label' => 'Nombre de jours au plus tard avant une réservation<br/> (0 pour résa le
jour même)',
																							 'title' => 'en jours, 0 autorise les réservations le jour même',
																							 'size' => 4,
																							 'required' => true,
																							 'allowEmpty' => false,
																							 'validators' => ['digits']]],

						'hold_delay_max' => ['element' => 'text',
																 'options' => [
																							 'label' => 'Nombre de jours au plus tôt avant une réservation<br/>(1 pour autoriser
les réservation pour le lendemain)',
																							 'title' => 'en jours, doit être supérieur au délai minimum',
																							 'size' => 4,
																							 'required' => true,
																							 'allowEmpty' => false,
																							 'validators' => ['digits', new ZendAfi_Validate_FieldGreater('hold_delay_min', 'Délai minimum de réservation')]]],

						'auth_delay' => ['element' => 'text',
														 'options' => ['label' => 'Délai de connexion avant d\'annuler une réservation (en minutes)',
																					 'title' => 'en minutes, passé ce délai la réservation est annulée',
																					 'size' => 4,
																					 'required' => true,
																					 'allowEmpty' => false,
																					 'validators' => ['digits']]]];
	}


	public function getConfigAutoFields() {
		return [
						'autohold' => ['element' => 'checkbox',
													 'options' => ['label' => 'Générer automatiquement une réservation à la connection à un poste
disponible',
																				 'title' => 'quand un abonné se connecte sur un poste non réservé, une réservation lui est attribuée',
																				 'required' => true,
																				 'allowEmpty' => false]],

						'autohold_min_time' => ['element' => 'text',
																		'options' => ['label' => 'Temps minimum de connexion avant la réservation suivante (en minutes)',
																									'title' => 'quand un abonné se connecte et qu\'une réservation est prévue dans quelques minutes, permet de définir si la réservation automatique peut s\'effectuer',
																									'size' => 4,
																									'required' => true,
																									'allowEmpty' => false,
																									'validators' => ['digits']]],

						'autohold_slots_max' => ['element' => 'text',
																		 'options' => ['label' => 'Durée de la réservation automatique (en nombre de créneaux)',
																									 'title' => 'en nombre de "slots"',
																									 'size' => 4,
																									 'required' => true,
																									 'allowEmpty' => false,
																									 'validators' => ['digits']]]];
	}


	public function browseAction() {
		if (!$location = Class_Multimedia_Location::find((int)$this->_getParam('id'))) {
			$this->_redirect('/admin/multimedia');
			return;
		}

		$devices = $location->getDevices();
		$this->view->subview = $this->view->partial('multimedia/browse.phtml',
			                                          ['titre' => sprintf('Postes du site multimédia "%s"', $location->getLibelle()),
																								 'devices' => $devices]);
		$this->_forward('index');
	}
		

	protected function _postEditAction($model) {
		$this->view->titre = 'Modification du site multimédia "' . $this->view->escape($model->getLibelle()) . '"';
	}
		

	/** Les données viennent d'un serveur multimédia, pas de suppression */
	public function deleteAction() {
		$this->_redirect('/admin/multimedia');
	}

	
	/** Les données viennent d'un serveur multimédia, pas d'ajout */
	public function addAction() {
		$this->_redirect('/admin/multimedia');
	}


	/** Affiche les réservations d'un device*/
	public function holdsAction() {
		if (!$device = Class_Multimedia_Device::find($this->_getParam('id'))) {
			$this->_redirect('/admin/multimedia/index');
			return;
		}

		$this->view->subview = $this->view->partial('multimedia/holds.phtml',
			                                          ['titre' => sprintf('%s (%s): réservations',
																																		$device->getLibelle(),
																																		$device->getOs()),
																								 'device' => $device]);
		$this->_forward('index');
	}
}

?>
