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
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		if (!$groups = json_decode($this->_getParam('json')))
			return;

		foreach ($groups as $group) {
			if (!$location = Class_Multimedia_Location::getLoader()->findFirstBy(array('id_origine' => (int)$group->site->id)))
				$location = Class_Multimedia_Location::getLoader()->newInstance()
					->setIdOrigine((int)$group->site->id);
						
			$location->setLibelle($group->site->libelle)
					->save();
			
			if (!$deviceGroup = Class_Multimedia_DeviceGroup::getLoader()->findFirstBy(array('id_origine' => (int)$group->id)))
				$deviceGroup = Class_Multimedia_DeviceGroup::getLoader()->newInstance()
					->setIdOrigine((int)$group->id);

			$deviceGroup
					->setLibelle($group->libelle)
					->setLocation($location)
					->save();

			foreach ($group->postes as $poste) {
				if (!$device = Class_Multimedia_Device::getLoader()->findFirstBy(array('id_origine' => (int)$poste->id)))
					$device = Class_Multimedia_Device::getLoader()->newInstance()
						->setIdOrigine((int)$poste->id);

				$device
						->setLibelle($poste->libelle)
						->setOs($poste->os)
						->setDeviceGroup($deviceGroup)
						->save();
			}
		}
	}
}
?>