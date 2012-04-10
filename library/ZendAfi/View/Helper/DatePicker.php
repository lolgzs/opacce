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
	protected function _renderScripts() {
		Class_ScriptLoader::getInstance()
			->addAdminScripts(array('calendar', 'format'))
			->addAdminStyleSheet('calendar');
	}

	/*
	 * @param $name name of the INPUT
	 * @param $varDate default date VALUE for the INPUT
	 * @param $minYear minimum year to display
	 * @param $maxYear maximum year to display
	 * @return html for the date picker
	 */
	public function datePicker($name, $varDate, $minYear, $maxYear)	{
		$this->_renderScripts();

		$locale = Zend_Registry::get('locale');

		if ($locale == 'en_US'){
			$dateFormat = 'MM/dd/yyyy';
			$style = 0;
		}else{
			$dateFormat = 'dd/MM/yyyy';
			$style = 1;
		}

		$lun = $this->traduire('lun.');
		$mar = $this->traduire('mar.');
		$mer = $this->traduire('mer.');
		$jeu = $this->traduire('jeu.');
		$ven = $this->traduire('ven.');
		$sam = $this->traduire('sam.');
		$dim = $this->traduire('dim.');
		$nomWeek = array($lun, $mar, $mer, $jeu, $ven, $sam, $dim);
		$jsonNomsWeek = Zend_Json::encode($nomWeek);
		$htmlJsonNomsWeek= str_replace('"',"'", $jsonNomsWeek);

		$janvier = $this->traduire('janvier');
		$fevrier = $this->traduire('février');
		$mars = $this->traduire('mars');
		$avril = $this->traduire('avril');
		$mai = $this->traduire('mai');
		$juin = $this->traduire('juin');
		$juillet = $this->traduire('juillet');
		$aout = $this->traduire('août');
		$septembre = $this->traduire('septembre');
		$octobre = $this->traduire('octobre');
		$novembre = $this->traduire('novembre');
		$decembre = $this->traduire('décembre');
		$nomsMonth = array($janvier, $fevrier, $mars, $avril, $mai, $juin, $juillet, $aout, $septembre, $octobre, $novembre, $decembre);
		$jsonNomsMonth = Zend_Json::encode($nomsMonth);
		$htmlJsonNomsMonth= str_replace('"',"'", $jsonNomsMonth);

		// format date if there is one
		if ($varDate != '') {
			try{
				$date =  new Zend_Date($varDate, Zend_Date::ISO_8601, $locale);
			}catch (Exception $e){
				$date =  new Zend_Date($varDate, $dateFormat, $locale);
			}

			$value = $date->toString($dateFormat);
		}else{
			$value = '';

		}

		$html = $this->view->formText($name, $value, array(
			'id' => 'date' . $name,
			'maxlength' => 10,
			'onchange' => "forDateFormat(this,'null','null','" . $dateFormat . "');"
		));

		$html .= $this->view->tagImg(URL_ADMIN_IMG . 'calendar/images/calendrierIcon.gif', array(
			'class' => 'calendarIcon',
			'style' => 'height:13px;width:14px',
			'onclick' => "showCalendar(document.getElementById('date" . $name . "'), '"
																	. URL_ADMIN_IMG . "calendar', "
																	. $htmlJsonNomsWeek . ", "
																	. $htmlJsonNomsMonth . ", "
																	. $style . ", "
																	. $minYear . ", "
																	. $maxYear . ", "
																	. "'null')",
		));

		return $html;

	}
}