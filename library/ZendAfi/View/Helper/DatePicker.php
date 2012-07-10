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
class ZendAfi_View_Helper_DatePicker extends ZendAfi_View_Helper_BaseHelper {
	/*
	 * @param $name name of the INPUT
	 * @param $varDate default date VALUE for the INPUT
	 * @param $minYear minimum year to display
	 * @param $maxYear maximum year to display
	 * @return html for the date picker
	 */
	public function datePicker($name, $varDate, $minYear, $maxYear)	{
		$options = array();
		$locale = Zend_Registry::get('locale');
		$options = array(
										 'dateFormat' => ($locale == 'en_US') ? 'mm/dd/yy' : 'dd/mm/yy',
										 'yearRange'	=> $minYear.':'.$maxYear,
										 'showOn'			=> 'both',
										 'buttonImage' => URL_ADMIN_IMG . 'calendar/images/calendrierIcon.gif',
										 'buttonImageOnly' => false);


		Class_ScriptLoader::getInstance()
			->addJQueryReady('$("#date'.$name.'").datepicker('.json_encode($options).');');


		$value = $this->formatDate($varDate, $locale);


		return $this->view->formText($name, $value, array(
			'id' => 'date' . $name,
			'maxlength' => 10
		));
	}


	public function formatDate($varDate, $locale) {
		if (!$varDate)
			return '';

		$dateFormat = ($locale == 'en_US') ? 'MM/dd/yyyy' : 'dd/MM/yyyy';
		try{
			$date =  new Zend_Date($varDate, Zend_Date::ISO_8601, $locale);
		}catch (Exception $e){
			$date =  new Zend_Date($varDate, $dateFormat, $locale);
		}

		return $date->toString($dateFormat);
	}
}