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
/*
 * Classe de gestion de Date
 */

class Class_Date {

	private $_locale = null;			// Locale
	private $_localeDateFormat = null;	// Date format to be used, depends on Locale; Set in constructor

	function __construct()
	{
		$this->_locale = Zend_Registry::get('locale');

		if ($this->_locale == 'en_US'){
			$this->_localeDateFormat = 'MM-dd-yyyy';
		}else{
			$this->_localeDateFormat = 'dd-MM-yyyy';
		}
	}

	/*
	 * @param string $dateFormat Format used to generate the date
	 * @return string Today's date
	 * @return false if failed
	 */
	function DateDuJour($dateFormat)
	{
		try{
			$zendDate =  new Zend_Date();
			$dateDuJour = $zendDate->toString($dateFormat);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Date; Function: DateDuJour' . NL . $e->getMessage());
			$dateDuJour = false;
		}

		return $dateDuJour;
	}

	/*
	 * Uses Date Format = 'yyyy-MM-dd HH:mm:ss'
	 * @return string Today's date and time
	 */
	function DateTimeDuJour()
	{
		$zendDate =  new Zend_Date();
		return $zendDate->toString('yyyy-MM-dd HH:mm:ss');
	}
	
	/*
	 * @param string $varDate date to be localized 
	 * @param string $dateFormat Format used to generate the date
	 * @return string localized date
	 * @return false if failed
	 */
	function LocalizedDate($varDate, $dateFormat)
	{
		try{
			$zendDate =  new Zend_Date($varDate, $dateFormat, $this->_locale);
			$localizedDate = $zendDate->toString($this->_localeDateFormat);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Date; Function: LocalizedDate' . NL . $e->getMessage());
			$localizedDate = false;
		}

		return $localizedDate;
	}

	/*
	 * @param string $varDate date to be localized 
	 * @param string $dateFormat Format used to generate the date
	 * @return string localized date
	 * @return false if failed
	 */
	function RendDateSql($varDate)
	{
		try{
			$zendDate =  new Zend_Date($varDate, $this->_localeDateFormat, $this->_locale);
			$sqlDate = $zendDate->toString('-MM-dd');
		}catch (Exception $e){
			logErrorMessage('Class: Class_Date; Function: RendDateSql' . NL . $e->getMessage());
			$sqlDate = false;
		}

		return $sqlDate;
	}
	
	/*
	 * @param string $varDate1 date 
	 * @param string $varDate1 date
	 * @return int The difference in days between the 2 dates
	 */
	function SoustraitDates($varDate1, $varDate2)
	{

		$date1 = strtotime($varDate1);
		$date2 = strtotime($varDate2);

		$differenceDays = (integer)(($date1 - $date2) / (24*60*60));
		
		return $differenceDays;
	}

	/*
	 * @param string $varDate date 
	 * @param int $jours number of days
	 * @param string $dateFormat Format used to generate the date
	 * @return string new date = $varDate + $jours
	 * @return false if failed
	 */
	function AjouterJours($varDate, $jours, $dateFormat)
	{
		try{
			$zendDate =  new Zend_Date($varDate, $dateFormat, $this->_locale);
			$zendDate->add((int)$jours, Zend_Date::DAY);
			return $zendDate->toString($dateFormat);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Date; Function: AjouterJours' . NL . $e->getMessage());
			return false;
		}

	}
	
	/*
	 * @return int current time
	 */
	function getTime()
	{
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$time = $mtime;

		return $time;
	}
	
	/*
	 * @param int $t time
	 * @return string in format '11 h 40 min. 33 sec.'
	 */
	function getSecMinHTime($t)
	{
		$temps = "";
		$secondes = $t % 60;
		$minutes = (int)($t/60);
		$heures = (int)($minutes/60);
		$minutes = $minutes % 60;
		if( $heures > 0 )$temps = $heures . " h ";
		if( $minutes > 0 ) $temps .= $minutes . " min. ";
		$temps .= $secondes . " sec.";

		return $temps;
	}



	public static function isEndDateAfterStartDate($start_date, $end_date) {
		if (empty($start_date) || empty($end_date)) 
			return true;

		return self::compareDates($start_date, $end_date) <= 0;
	}


	public static function isEndDateAfterStartDateNotEmpty($start_date, $end_date) {
		if (empty($end_date)) 
			return false;

		if (empty($start_date))
			return true;

		return self::compareDates($start_date, $end_date) <= 0;
	}


	public static function compareDates($start_date, $end_date) {
		$locale = Zend_Registry::get('locale');
		$zstart = new Zend_Date($start_date, null, $locale);
		$zend = new Zend_Date($end_date, null, $locale);
		return $zstart->compare($zend);
	}


	public function humanDate($datestr, $format='d MMMM yyyy HH:mm:ss') {
		$date = new Zend_Date($datestr, null, Zend_Registry::get('locale'));
		return $date->toString($format);
	}
}