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
		
		if (isset($session->baseUrl))
		{
			if($session->baseUrl != BASE_URL)
			{
				$session->baseUrl = BASE_URL;
				Zend_Auth::getInstance()->clearIdentity();
			}
		}
		$auth = Zend_Auth::getInstance();
		
		// Entree dans fonctions admin
		if ($module == 'admin' and $controller != 'error')
		{
			$acl = new ZendAfi_Acl_AdminControllerRoles();
	    // Un user est connecté
	    if($auth->hasIdentity())
			{
				$resource = $controller;
				$role = $auth->getIdentity()->ROLE;		
				
				// si la resource n'existe pas dans ZendAfi_Acl_AdminControllerRoles
				if (!$acl->has($resource)) $resource = null;
				
				// Test du role et redirection vers opac si pas autorisé
				if (!$acl->isAllowed($role, $resource))
				{
	        $module = 'opac';
					$controller = 'index';
					$action = 'index';
				}
			}
			// User non connecté on redirige vers le login
			else
			{ 
				$module = 'admin';
				$controller = 'auth';
				$action = 'login';
			}
		}
		
		// Entree dans opac on teste si le site a été désactivé
		else 
		{		
			if (Class_AdminVar::get("SITE_OK") == "0" and $module == 'opac')
			{
				$module = 'opac';
				$controller = 'index';
				$action = 'sitedown';
			}
		}
		
		// Parametres du controller
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
	}
}