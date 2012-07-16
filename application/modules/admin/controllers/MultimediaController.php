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
		return array(
				'model' => array('class' => 'Class_Multimedia_Location', 'name' => 'site'),
				'messages' => array('successful_save' => 'Site %s sauvegardé'),
				'actions' => array('edit' => array('title' => 'Modifier un site multimédia'),
					                 'index' => array('title' => 'Sites multimédia')),
				'display_groups' => array(
					'config' => array(
						'legend' => 'Réservation',
						'elements' => array(
							'slot_size' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Réservation minimale (slot) *',
									'title'=> 'en minutes',
									'size'	=> 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits'))),
							'max_slots' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Réservation maximale *',
									'title' => 'en nombre de "slots"',
									'size' => 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits'))),
							'hold_delay_min' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Délai minimum de réservation *',
									'title' => 'en jours, 0 autorise les réservations le jour même',
									'size' => 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits'))),
							'hold_delay_max' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Délai maximum de réservation *',
									'title' => 'en jours, doit être supérieur au délai minimum',
									'size' => 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits', new ZendAfi_Validate_FieldGreater('hold_delay_min', 'Délai minimum de réservation')))),
							'auth_delay' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Délai de connection *',
									'title' => 'en minutes, passé ce délai la réservation est annulée',
									'size' => 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits'))),
							'autohold' => array(
								'element' => 'checkbox',
								'options' => array(
									'label' => 'Autoriser la réservation automatique *',
									'title' => 'quand un abonné se connecte sur un poste non réservé, une réservation lui est attribuée',
									'required' => true,
									'allowEmpty' => false)),
							'autohold_slots_max' => array(
								'element' => 'text',
								'options' => array(
									'label' => 'Réservation automatique maximale *',
									'title' => 'en nombre de "slots"',
									'size' => 4,
									'required' => true,
									'allowEmpty' => false,
									'validators' => array('digits'))),
						)),
					'planning' => array(
						'legend' => 'Planning',
						'elements' => array(
							'days' => array(
								'element' => 'multiCheckbox',
								'options' => array(
									'label' => 'Jours d\'ouverture *',
									'multioptions' => Class_Multimedia_Location::getLoader()->getPossibleDays(),
									'separator' => '',
								)
							),
							'open_hour' => array(
								'element' => 'select',
								'options' => array(
									'label' => 'Heure d\'ouverture *',
									'multioptions' => Class_Multimedia_Location::getLoader()->getPossibleHours(15),
									'required' => true,
								  'allowEmpty' => false,
									'validators' => array(new Zend_Validate_Regex('/[0-9]{2}:[0-9]{2}/')),
								)
							),
							'close_hour' => array(
								'element' => 'select',
								'options' => array(
									'label' => 'Heure de fermeture *',
									'title' => 'Doit être après l\'heure d\'ouverture',
									'multioptions' => Class_Multimedia_Location::getLoader()->getPossibleHours(15),
									'required' => true,
								  'allowEmpty' => false,
									'validators' => array(new Zend_Validate_Regex('/[0-9]{2}:[0-9]{2}/')),
								)
							)
						)
					)
				));
	}


	public function browseAction() {
		if (!$location = Class_Multimedia_Location::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_redirect('/admin/multimedia');
			return;
		}

		$devices = $location->getDevices();
		$this->view->subview = $this->view->partial('multimedia/browse.phtml',
			                                          array('titre' => sprintf('Postes du site multimédia "%s"', $location->getLibelle()),
																									    'devices' => $devices));
		$this->_forward('index');
	}
		

	protected function _postEditAction($model) {
		$this->view->titre = 'Modification du site multimédia "' . $this->view->escape($model->getLibelle()) . '"';
		$this->view->form->getElement('days')->setValue($model->getDaysAsArray());
	}
		

	/** Les données viennent d'un serveur multimédia, pas de suppression */
	public function deleteAction() {
		$this->_redirect('/admin/multimedia');
	}

	
	/** Les données viennent d'un serveur multimédia, pas d'ajout */
	public function addAction() {
		$this->_redirect('/admin/multimedia');
	}
}

?>
