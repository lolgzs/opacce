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

class Multimedia_DeviceLoader extends Storm_Model_Loader {
	/**
	 * @param $json_model stdClass
	 * @param $device_group Class_Multimedia_DeviceGroup
	 * @return Class_Multimedia_DeviceGroup
	 */
	public function fromJsonModelWithGroup($json_model, $device_group) {
		if (!$model = $this->findFirstBy(array('id_origine' => (int)$json_model->id)))
			$model = $this->newInstance()->setIdOrigine((int)$json_model->id);
		$model
				->setLibelle($json_model->libelle)
				->setOs($json_model->os)
				->setGroup($device_group)
				->save();
		return $model;
	}
}


class Class_Multimedia_Device extends Storm_Model_Abstract {
	protected $_loader_class = 'Multimedia_DeviceLoader';
	protected $_table_name = 'multimedia_device';
	protected $_belongs_to = array(
		'group' => array(
			'model' => 'Class_Multimedia_DeviceGroup',
			'referenced_in' => 'id_devicegroup'));
	
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}
}