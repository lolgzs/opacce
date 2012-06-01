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

require_once ROOT_PATH.'application/modules/opac/controllers/AuthController.php';

class Telephone_AuthController extends AuthController {
	public function init() {
		parent::init();
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}

	public function boiteloginAction() {
		if ($this->_request->isPost())
			$this->_authenticate();
		$this->_redirect('/telephone/index');
	}


	public function loginAction() {
		$this->_loginCommon('/abonne');
		$this->render('login-reservation');
	}


	public function loginReservationAction() {
		$this->_loginCommon('/recherche/reservation');
		$this->view->id_notice = $this->_getParam('id');
	}



	protected function _loginCommon($redirectUrl) {
		if (Class_Users::getLoader()->hasIdentity()) {
			$this->_redirect($redirectUrl);
			return;
		}

		$form = $this->_getForm();
		if ($this->_request->isPost()) {
			if (!($error = $this->_authenticate())) {
				$this->_redirect($redirectUrl);
				return;
			}

			$this->_flashMessenger->addMessage($error);
			$this->_redirect($this->view->url(), array('prependBase' => false));
		}
		
		$this->view->error = $this->_flashMessenger->getMessages();
		$this->view->form = $form;
	}


	protected function _getForm() {
		$form = new ZendAfi_Form_Login();
		$form->setAction($this->view->url())
			->setAttrib('class', 'ui-grid-b');

		$form->getElement('username')
			->setAttrib('placeholder', $this->view->_('Identifiant'))
			->setAttrib('data-mini', 'true')
			->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'ui-block-a'));

		$form->getElement('password')
			->setAttrib('placeholder', $this->view->_('Mot de passe'))
			->setAttrib('data-mini', 'true')
			->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'ui-block-b'));

		$form->getElement('login')
			->setLabel($this->view->_('Se connecter'))
			->setAttrib('data-mini', 'true')
			->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'ui-block-c'));

		return $form;
	}
}
?>