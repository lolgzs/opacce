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
require_once ROOT_PATH.'application/modules/opac/controllers/RechercheController.php';

class Telephone_RechercheController extends RechercheController {
	public function viewnoticeAction()  {
		$this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
		$this->view->actions = array($this->view->_('Notice détaillée') =>		  array('action' => 'detail'),
																 $this->view->_('Avis') =>								  array('action' => 'avis'),
																 $this->view->_('Exemplaires') =>					  array('action' => 'exemplaires'),
																 $this->view->_('Résumés, analyses') =>		  array('action' => 'resume'),
																 $this->view->_('Tags') =>								  array('action' => 'tags'),
																 $this->view->_('Biographies') =>					  array('action' => 'biographie'),
																 $this->view->_('Notices similaires') =>	  array('action' => 'similaires'),
																 $this->view->_('Ressources numériques') => array('action' => 'ressourcesnumeriques'),
																 );
	}


	public function grandeimageAction() {
		$this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	}


	public function exemplairesAction() {
		$this->view->notice = Class_Notice::getLoader()->find($this->_getParam('id'));
	}
}