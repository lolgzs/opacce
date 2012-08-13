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
class ZendAfi_View_Helper_Admin_SubscribeUsers extends ZendAfi_View_Helper_BaseHelper {
	protected $_users = array();
	protected $_search = '';
	protected $_search_label = 'Rechercher des utilisateurs';
	protected $_submit_label = 'Ajouter les utilisateurs sélectionnés';
	protected $_by_right = 0;
	protected $_read_only = false;

	public function subscribeUsers($users, $search = '') {
		return $this
			->setUsers($users)
			->setSearch($search)
			->render();
	}


	public function setUsers($users) {
		$this->_users = $users;
		return $this;
	}


	public function setSearch($search) {
		$this->_search = $search;
		return $this;
	}


	public function setSearchLabel($label) {
		$this->_search_label = $label;
		return $this;
	}


	public function setSubmitLabel($label) {
		$this->_submit_label = $label;
		return $this;
	}


	public function filterByRight($right) {
		$this->_by_right = $right;
		return $this;
	}


	public function setReadOnly($read_only) {
		$this->_read_only = $read_only;
		return $this;
	}


	public function isReadOnly() {
		return $this->_read_only;
	}


	public function __toString() {
		return $this->render();
	}


	public function render() {
    $content = '<table><thead><tr class="soustitre">';
		foreach(array('Nom', 'Prénom', 'Identifiant', '') as $column)
			$content .= sprintf('<td>%s</td>', $column);
		$content .= '</tr></thead><tbody>';

		foreach($this->_users as $user) 
			$content .= $this->_renderUser($user);

		$content .= '</tbody></table>';

		if ($this->isReadOnly())
			return $content;

		return $content
			.$this->_findUsersForm($this->_search)->render()
			.$this->_subscribeUsersForm($this->_search)->render();
	}


	protected function _renderUser($user) {
		$delete_url = $this->view->url(array('delete' => $user->getId()));
		if ($_GET)
			$delete_url .= '?'.http_build_query($_GET);

		return sprintf('<tr class="%s"><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
									 'first',
									 $user->getNom(),
									 $user->getPrenom(),
									 $user->getLogin(),
									 $this->view->tagAnchor($delete_url,
																					$this->view->boutonIco("type=del")));
	}


	protected function _subscribeUsersForm($search) {
		$users_found = Class_Users::getLoader()->findAllLike($search, $this->_by_right);

		$form = $this->view
			->newForm(array('id' => 'user_subscribe'))
			->setAttrib('class' , 'form');

		$user_checkboxes = new Zend_Form_Element_MultiCheckbox('users');
		foreach($users_found as $user) 
			$user_checkboxes->addMultiOption($user->getId(),
																			 sprintf('%s %s - %s',
																								$user->getNom(),
																								$user->getPrenom(),
																								$user->getLogin()));

		return $form
			->addElement($user_checkboxes)
			->addElement('submit', 'submit', array('label' => $this->_submit_label));
		
	}

	protected function _findUsersForm($search) {
		return $this->view
			->newForm(array('id' => 'findusers'))
			->setMethod('get')
			->setAttrib('class', 'form')
			->setAction($this->view->url())
			->addElement('text', 'search', array('label' => $this->_search_label,
																					 'size' => 30,
																					 'value' => $search))
			->addElement('submit', 'submit', array('label' => $this->view->_('Rechercher')))
			->setDecorators(array('FormElements', 'Form'));
	}

}

?>