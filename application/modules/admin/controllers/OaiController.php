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
class Admin_OaiController extends Zend_Controller_Action {

	function indexAction() {
		$entrepot_id = $this->_getparam("entrepot_id");
		if ($entrepot_id) {
			$entrepot = Class_EntrepotOAI::getLoader()->find($entrepot_id);

			$oai_service = new Class_WebService_OAI();
			$oai_service->setOAIHandler($entrepot->getHandler());

			try {
				$this->view->oai_sets = $oai_service->getSets();
				$this->view->oai_set = $this->view->oai_sets[0];
			} catch (Exception $e) {
				$this->view->communication_error = $e->getMessage();
			}
		} 

		$this->view->entrepots = Class_EntrepotOAI::findAllAsArray();
		$this->view->entrepot_id = $entrepot_id;
		$this->view->titre = 'Entrepôts OAI';
	}


	function harvestAction() {
		$this->_helper->viewRenderer->setNoRender();

		$entrepot_id = $this->_getparam("entrepot_id");
		$resumption_token = $this->_getparam("resumption_token");
		$oai_set=$this->_getparam("oai_set");

		$entrepot = Class_EntrepotOAI::getLoader()->find($entrepot_id);
		$notice_oai = new Class_NoticeOAI();

		if ($resumption_token) {
			$token = new Class_WebService_ResumptionToken();
			$token->setToken($resumption_token);
			$next_token = $notice_oai->resumeHarvest($entrepot, $token);
		}	else {
			$next_token = $notice_oai->harvestSet($entrepot, $oai_set);
		}

		if ($next_token)
			echo $next_token->toJSON();
	}
}

?>