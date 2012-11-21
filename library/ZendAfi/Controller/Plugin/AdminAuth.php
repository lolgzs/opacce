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
//////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	Admin Authentication Plugin.  Verify that the Admin is logged in before accessing Controllers
// 					This plugin uses the rules defined in ZendAfi_Acl_AdminControllerRoles
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Plugin_AdminAuth extends Zend_Controller_Plugin_Abstract
{	
	function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $this->_request->getModuleName();
		$controller = $this->_request->getControllerName();
		$action = $this->_request->getActionName();
		$session = Zend_Registry::get('session');

		$auth = ZendAfi_Auth::getInstance();
		
		if (isset($session->baseUrl))
		{
			if($session->baseUrl != BASE_URL)
			{
				$session->baseUrl = BASE_URL;
				$auth->clearIdentity();
			}
		}

		
		// Entree dans fonctions admin
		if ($module == 'admin' and $controller != 'error')	{
			Zend_Controller_Front::getInstance()
				->getPlugin('Zend_Controller_Plugin_ErrorHandler')
				->setErrorHandlerModule('admin');

			if (!$user = Class_Users::getIdentity()) {
				$controller = 'auth';
				$action = 'login';
			} else if (!$this->userCanAccessAdminPage($user)) {
				$module = 'opac';
				$controller = 'index';
				$action = 'index';
			}
		} else 	{		
		// Entree dans opac on teste si le site a été désactivé
			if (Class_AdminVar::get("SITE_OK") == "0" and $module == 'opac')	{
				$controller = 'index';
				$action = 'sitedown';
			}

			if ((!$user = Class_Users::getIdentity()) && ($controller == "abonne")) {
				$controller = 'auth';
				$action = 'login';
			}
		}
		
		// Parametres du controller
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
	}


	protected function userCanAccessAdminPage($user) {
		$acl = new ZendAfi_Acl_AdminControllerRoles();
		$resource = $this->_request->getControllerName();
		$role = $user->getRole();

		// si la resource n'existe pas dans ZendAfi_Acl_AdminControllerRoles
		if (!$acl->has($resource)) $resource = null;
				
		// Test du role et redirection vers opac si pas autorisé
		return $acl->isAllowed($role, $resource);
	}
}

?>