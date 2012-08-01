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
class Admin_UsergroupController extends Zend_Controller_Action {
	public function indexAction() {
		$this->view->titre = "Gestion des groupes d'utilisateurs";
		$this->view->groups = Class_UserGroup::getLoader()->findAllBy(array('order' => 'libelle'));
	}


	public function addAction() {
		$this->view->titre = "Ajouter un groupe d'utilisateurs";
		$new_group = Class_UserGroup::getLoader()->newInstance();

		$this->_setupGroupFormAndSave('add', $new_group);
	}


	public function editAction() {
		if (!$group = Class_UserGroup::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_redirect('admin/usergroup');
			return;
		}

		$this->view->titre = "Modifier le groupe d'utilisateurs: ".$group->getLibelle();
		$this->_setupGroupFormAndSave('edit', $group);
	}


	public function editmembersAction() {
		if (!$group = Class_UserGroup::getLoader()->find((int)$this->_getParam('id'))) {
			$this->_redirect('admin/usergroup');
			return;
		}

		if ($id_user_to_delete = $this->_getParam('delete')) {
			$group
				->removeUser(Class_Users::getLoader()->find($id_user_to_delete))
				->save();

			$redirect_url = '/admin/usergroup/editmembers/id/'.$group->getId();
			if ($_GET)
				$redirect_url .= '?'.http_build_query($_GET);
			$this->_redirect($redirect_url);
			return;
		}


		if ($this->_request->isPost() 
				&& ($ids_users_to_add = $this->_request->getPost('users'))) {
			foreach($ids_users_to_add as $id)
				$group->addUser(Class_Users::getLoader()->find($id));
			$group->save();
		}

		$this->view->getHelper('SubscribeUsers')
			->setUsers($group->getUsers())
			->setSearch($this->_getParam('search'));

		$this->view->titre = "Membres du groupe: ".$group->getLibelle();
	}


	public function deleteAction() {
		if ($group = Class_UserGroup::getLoader()->find((int)$this->_getParam('id')))
			$group->delete();

		$this->_redirect('admin/usergroup');
	}


	protected function _setupGroupFormAndSave($action, $group) {
		$form = $this->_groupForm($action, $group);
		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			$group
				->updateAttributes($this->_request->getPost())
				->save();

			$this->_redirect('admin/usergroup');
			return true;
		}

		$this->view->form = $form;
		$this->renderScript('usergroup/_form.phtml');
		return false;
	}


	protected function _groupForm($action, $group) {
		$form = $this->view
			->newForm(array('id' => 'usergroupform'))
			->setAction($this->view->url(array('action' => $action)));

		$form->addRequiredTextNamed('libelle')
			->setLabel('Libellé *');

		$elements = array('libelle');
 
		if (Class_AdminVar::isFormationEnabled()) {
			$form->addElement('multiCheckbox',
				                'rights',
				                 array('label' => 'Droits',
													      'multiOptions' => Class_UserGroup::getRightDefinitionList()));
			$elements[] = 'rights';
		}

		$form->addDisplayGroup($elements, 'usergroup', array('legend' => 'Groupe'));		

		if (Class_AdminVar::isMultimediaEnabled()) {
			$form->addRequiredTextNamed('max_day')
				->setLabel('Par jour *')
				->setValue(0)
				->setValidators(array('Digits'));
			
			$form->addRequiredTextNamed('max_week')
				->setLabel('Par semaine *')
				->setValue(0)
				->setValidators(array('Digits',
						                  new ZendAfi_Validate_FieldsGreater(array('max_day' => 'Par jour'), true)));

			$form->addRequiredTextNamed('max_month')
				->setLabel('Par mois *')
				->setValue(0)
				->setValidators(array('Digits',
						                  new ZendAfi_Validate_FieldsGreater(array(
																	'max_day' => 'Par jour',
																	'max_week' => 'Par semaine'),
																true)));

			$form->addDisplayGroup(
				array('max_day', 'max_week', 'max_month'),
				'multimedia',
				array('legend' => 'Quota de réservation multimédia (mn)'));
		}
		
		$form->populate($group->toArray());

		return $form;
	}
}
?>