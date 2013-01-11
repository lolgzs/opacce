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

class Admin_OuverturesController extends ZendAfi_Controller_Action {
	public function getRessourceDefinitions() {
		$hours_select = Class_Multimedia_Location::getLoader()->getPossibleHours(30);		

		$fields = ['id_site' => ['element' => 'hidden'],
							 'jour_semaine' => ['element' => 'select',
																	'options' => ['label' => $this->view->_('Répétition'),
																								'multiOptions' => [0 => $this->view->_('Aucune'),
																																	 1 => $this->view->_('Tous les lundis'),
																																	 2 => $this->view->_('Tous les mardis'),
																																	 3 => $this->view->_('Tous les mercredis'),
																																	 4 => $this->view->_('Tous les jeudis'),
																																	 5 => $this->view->_('Tous les vendredis'),
																																	 6 => $this->view->_('Tous les samedis'),
																																	 7 => $this->view->_('Tous les dimanches')]
																								]
																	],

							 'jour' => ['element' => 'datePicker', 
													'options' => ['label' => $this->view->_('Jour')]]];

		$field_labels = ['debut_matin' => $this->view->_('Début matinée'), 
										 'fin_matin' => $this->view->_('Fin matinée'), 
										 'debut_apres_midi' =>	$this->view->_('Début après-midi'), 
										 'fin_apres_midi' => $this->view->_('Fin après-midi')];

		foreach ($field_labels as $field => $label)
			$fields[$field] = ['element' => 'select', 'options' => ['label' => $label,
																															'multiOptions' => $hours_select]];

		return [
						'model' => ['class' => 'Class_Ouverture',
												'name' => 'ouverture',
												'scope' => 'id_site',
												'order' => ''],

						'sort' => ['Class_Ouverture', 'compare'],

						'messages' => ['successful_add' => 'Plage d\'ouverture %s ajoutée',
													 'successful_save' => 'Plage d\'ouverture %s sauvegardée',
													 'successful_delete' => 'Plage d\'ouverture %s supprimée'],

						'after_add' => function() {	$this->_redirectToIndex(); },
						'after_edit' => function() {	$this->_redirectToIndex(); },

						'display_groups' => ['plage_ouverture' => ['legend' => 'Plage d\'ouverture',
																											 'elements' => $fields
																											 ]
																 ]
						];
	}




	public function indexAction() {
		if (!$this->_getParam('id_site')) {
			$this->_redirect('admin/bib');
			return;
		}

		parent::indexAction();
		$this->formatTitreWithLibelleBib('%s: plages d\'ouverture');
	}


	public function editAction() {
		parent::editAction();
		$this->inputJourVisibleOnlyOnNoRepetition();
		$this->formatTitreWithLibelleBib('%s: modifier une plage d\'ouverture');
	}


	public function addAction() {
		parent::addAction();
		$this->inputJourVisibleOnlyOnNoRepetition();
		$this->formatTitreWithLibelleBib('%s: ajouter une plage d\'ouverture');
	}


	public function formatTitreWithLibelleBib($template) {
		$this->view->titre = $this->view->_($template, 
																				Class_Bib::find($this->_getParam('id_site'))->getLibelle());
	}


	public function inputJourVisibleOnlyOnNoRepetition() {
		Class_ScriptLoader::getInstance()
			->addInlineScript('formSelectToggleVisibilityForElement("#jour_semaine", "#ouverture tr:nth-child(3)", ["0"]);');
	}
}

?>