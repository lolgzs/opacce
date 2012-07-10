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
class Push_MultimediaController extends Zend_Controller_Action {
	public function configAction() {
		$log = new Zend_Log(new Zend_Log_Writer_Stream(PATH_TEMP . 'push.log'));
		$log->info('Push multimedia start');
		
		$this->_helper->getHelper('viewRenderer')->setNoRender();

		if (!($groups = json_decode($this->_getParam('json')))
			|| !($sign = $this->_getParam('sign'))) {
			$log->err('Missing parameter');
			return;
		}
				
		if (!Class_Multimedia::isValidHash($sign, $this->_getParam('json'))) {
			$log->err('Sign check failure');
			return;
		}

		foreach ($groups as $group) {
			$location = Class_Multimedia_Location::getLoader()->fromJsonModel($group->site);
			$deviceGroup = Class_Multimedia_DeviceGroup::getLoader()->fromJsonModelWithLocation($group, $location);
			foreach ($group->postes as $poste)
				Class_Multimedia_Device::getLoader()->fromJsonModelWithGroup($poste, $deviceGroup);
		}
	}
}
?>