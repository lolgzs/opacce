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

require_once ROOT_PATH.'application/modules/opac/controllers/AbonneController.php';

class Telephone_AbonneController extends AbonneController {
	protected $_messenger;

	public function init() {
		parent::init();
		$this->_messenger = $this->_helper->getHelper('FlashMessenger');
	}


	public function ficheAction() {
		parent::ficheAction();
		$this->view->messages = $this->_messenger->getMessages();
	}


	public function cancelHoldAction() {
		$this->_helper->getHelper('ViewRenderer')->setLayoutScript('empty.phtml');
		$fiche_sigb = $this->_user->getFicheSigb();
		$this->view->user = $this->_user;

		if (isset($fiche_sigb['erreur'])) {
			$this->view->error = $fiche_sigb['erreur'];
			return;
		}

		$this->_detectReservation($fiche_sigb['fiche']->getReservations());

		if ($this->view->resa 
				&& $this->_getParam('confirmed')) {
			$sigb = new Class_CommSigb();
			$sigb->supprimerReservation($this->_user, $this->view->resa->getId());
			$this->_redirect('/abonne/fiche');
			return;
		}
	}


	public function prolongerpretAction() {
		$sigb = new Class_CommSigb();
		$result = $sigb->prolongerPret($this->_user, $this->_getParam('id_pret'));
		$this->_messenger->addMessage((1 == $result['statut']) ?
																	$this->view->_('Prêt prolongé') :
																	$result['erreur']);
		$this->_redirect('/abonne/fiche');
	}


	protected function _detectReservation($reservations) {
		foreach($reservations as $resa) {
			if ($resa->getId() == $this->_getParam('id')) {
				$this->view->resa = $resa;
				break;
			}
		}
	}
}

?>