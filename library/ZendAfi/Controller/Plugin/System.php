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

class ZendAfi_Controller_Plugin_System extends Zend_Controller_Plugin_Abstract {
	function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!defined('SLOW_REQUEST_LOG') 
				|| !defined('SLOW_REQUEST_SYSTEM_TIME_LIMIT_SEC') 
				|| !defined('SLOW_REQUEST_USER_TIME_LIMIT_SEC'))
			return;

		$data = getrusage();
		$user_time_sec = $data['ru_utime.tv_sec'];
		$system_time_sec = $data['ru_stime.tv_sec'];
		if ($user_time_sec <= SLOW_REQUEST_USER_TIME_LIMIT_SEC  
				&& $system_time_sec <= SLOW_REQUEST_SYSTEM_TIME_LIMIT_SEC) return;

		$writer = new Zend_Log_Writer_Stream(SLOW_REQUEST_LOG);
		$logger = new Zend_Log($writer);
		
		$logger->info(sprintf('user: %02ds system: %02ds  %s',
													$user_time_sec,							
													$system_time_sec,
													$request->getRequestUri()));
	}
}

?>