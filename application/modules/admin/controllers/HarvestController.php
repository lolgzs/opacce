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

class Admin_HarvestController extends Zend_Controller_Action {
	public function arteVodAction() {
		if (!Class_AdminVar::isArteVodEnabled())
			$this->_redirect('/admin/index');

		$logger = new Zend_Log();
		$logger->addWriter(new Zend_Log_Writer_Stream('php://output'));
		ob_start();
		$logger->info('Début du moissonnage');
		
		$service = new Class_WebService_ArteVOD();
		$service->setLogger($logger);
		$service->harvest();

		$logger->info('Fin du moissonnage');
		
		$this->view->log = ob_get_clean();
	}


	public function arteVodBrowseAction() {
		$this->view->titre = $this->view->_('Moissonnage ArteVOD');
	}


	public function arteVodAjaxAction() {
		$this->_helper->viewRenderer->setNoRender();
		if (!Class_AdminVar::isArteVodEnabled())
			return;

		$service = new Class_WebService_ArteVOD();
		echo json_encode($service->harvestPage($this->_getParam('page', 1)));
	}
}
