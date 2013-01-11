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
		$log = Class_Multimedia::getLog();
		$log->info('Push multimedia start');
		
		$this->_helper->getHelper('viewRenderer')->setNoRender();

		if (null == ($json = $this->_getParam('json'))) {
			$log->err('Missing json parameter');
			return;
		}

		if (null == ($sign = $this->_getParam('sign'))) {
			$log->err('Missing sign parameter');
			return;
		}
	 
		if (!($groups = json_decode($json))) {
			$log->err('Invalid json');
			return;
		}
		
		if (!Class_Multimedia::isValidHash($sign, $this->_getParam('json'))) {
			$log->err('Sign check failure');
			return;
		}

		$id_origine_postes = $this->createAllDevicesFromGroups($groups);
		$this->deleteAllPostesFromSiteWithoutIdOrigine($groups[0]->site->id, $id_origine_postes);
	}


	public function createAllDevicesFromGroups($groups) {
		$id_origine_postes = [];

		foreach ($groups as $group) {
			$location = Class_Multimedia_Location::getLoader()->fromJsonModel($group->site);
			$deviceGroup = Class_Multimedia_DeviceGroup::getLoader()->fromJsonModelWithLocation($group, $location);
			foreach ($group->postes as $poste) {
				$poste = Class_Multimedia_Device::getLoader()->fromJsonModelWithGroup($poste, $deviceGroup);
				$id_origine_postes[] = $poste->getIdOrigine();
			}
		}

		return $id_origine_postes;
	}


	public function deleteAllPostesFromSiteWithoutIdOrigine($id_site, $id_origine_postes) {
		$postes_to_delete = Class_Multimedia_Device::findAllBy(['where' => sprintf('id_origine not in(%s) and id_origine like \'%s-%%\'',
																																							 implode(',', array_map(function($id){return '\''.$id.'\'';},
																																																			$id_origine_postes)),
																																							 $id_site)]);
		foreach($postes_to_delete as $poste)
			$poste->delete();
	}
}
?>