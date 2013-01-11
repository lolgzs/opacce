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

class Class_Multimedia {
	const SEPARATOR = '--@--';
	const LOG_FILE = 'push.log';

	/** @var Class_Multimedia */
	protected static $_instance;

	/** @var Zend_Log */
	protected static $_log;


	/** @return Zend_Log */
	public static function getLog() {
		if (null == self::$_log)
			self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(PATH_TEMP . self::LOG_FILE));
		return self::$_log;
	}


	/**
	 * @param $log Zend_Log
	 */
	public static function setLog($log) {
		self::$_log = $log;
	}

		
	/** @return boolean */
	public static function isValidHash($hash, $content) {
		return self::getInstance()->isValidHashForContent($hash, $content);
	}


	/** @return boolean */
	public function isValidHashForContent($hash, $content) {
		return $hash == $this->getHashForContent($content);
	}


	/** @return Class_Multimedia */
	public static function getInstance() {
		if (null == self::$_instance)
			self::$_instance = new self();
		return self::$_instance;
	}


	/** @category testing */
	public static function setInstance($instance) {
		self::$_instance = $instance;
	}


	/** @return string */
	public function getHashForContent($content) {
		return md5($this->getFullContent($content));
	}


	/**
	 * @param $content string
	 * @return string
	 */
	public function getFullContent($content) {
		return $content . self::SEPARATOR . $this->getKey() . self::SEPARATOR . $this->getDate();
	}


	/** @return string */
	public function getKey() {
		return Class_AdminVar::get('MULTIMEDIA_KEY');
	}


	/** @return string formated date */
	public function getDate() {
		$oldTimeZone = date_default_timezone_get();
		date_default_timezone_set('UTC');
		$date = date('Y-m-d');
		date_default_timezone_set($oldTimeZone);
		return $date;
	}
}

?>