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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	Activation du profil et du skin
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Controller_Plugin_DefineURLs extends Zend_Controller_Plugin_Abstract {

	function preDispatch(Zend_Controller_Request_Abstract $request)	{
		$requested_module = $request->getModuleName();
		if (isTelephone() and ($requested_module != 'admin'))
			$requested_module = 'telephone';

		// Initialisation du profil
		$id_profil = 0;
		$profil = null;

		$_SESSION['previous_id_profil'] = isset($_SESSION['id_profil']) ? $_SESSION['id_profil'] : 1;

		if ($requested_module != 'admin')
			$id_profil=(int)$request->getParam('id_profil');

		if ($id_profil <= 0 and isset($_SESSION['id_profil']))
				$id_profil = intval($_SESSION["id_profil"]);
		
		if ($id_profil <= 0)
			$id_profil = 1; //portail

		if(!$profil = Class_Profil::getLoader()->find($id_profil))
			$profil = Class_Profil::getLoader()->findFirstBy(array('order' => 'id_profil'));


		if ($requested_module == 'telephone' and $profil->getBrowser() != 'telephone') {
			$first_profil_tel = Class_Profil::getLoader()->findFirstBy(array('BROWSER' => 'telephone'));
			if ($first_profil_tel)	$profil = $first_profil_tel;
		}


		Class_Profil::setCurrentProfil($profil);
		$module = $requested_module;
		if (('telephone' == $profil->getBrowser()) &&  ($requested_module != 'admin'))
			$module = 'telephone';

		if ($requested_module == 'telephone' && $profil->getBrowser() == 'opac')
			$module = 'opac';


		/**
		 * Si l'ouverture du profil nécessite un niveau d'accès et que
		 * le niveau requis est trop faible, on redirige sur la page de login
		 */
		if (!$profil->isPublic()) {
			if (!Zend_Auth::getInstance()->hasIdentity() or
					Zend_Auth::getInstance()->getIdentity()->ROLE_LEVEL < $profil->getAccessLevel()) {
				$request->setControllerName('auth');
				$request->setActionName('login');
				$module = 'admin';
			}
		}

		$request->setModuleName($module);


		$_SESSION["id_profil"] = $profil->getId();

		// Initialisation du skin
		$skin = $profil->getSkin();

		$skindir = "./public/".$module."/skins";
		if (!is_dir("$skindir/$skin")) $skin = 'original';

		Zend_Registry::set('path_templates', "./public/".$profil->getBrowser()."/skins/".$skin."/templates/");
		$url_skin = BASE_URL . substr($skindir,1)."/".$skin;

		$this->_defineConstants($module, $skindir."/".$skin."/", $url_skin);

		// Initialisation du filtre zone et bibliotheque pour l'admin
		if($module=="admin") {
			if (!array_key_exists('admin', $_SESSION))
				$_SESSION["admin"] = array("filtre_localisation" => array("id_zone" => 'ALL',
																																	"id_bib" => 'ALL'));
			$session=$_SESSION["admin"]["filtre_localisation"];


			$id_bib	= 'ALL';
			$id_zone= 'ALL';

			// Si role admin_site on force la zone et la bib
			if (
				(null !== ($user = Class_Users::getLoader()->getIdentity()))
				&& (null !== ($bib = $user->getBib()))
			) {
				$id_bib = $bib->getId();
				$id_zone = $bib->getIdZone();

			}	else	{
				// Zone
				$id_zone = $request->getParam('z');
				if($id_zone != "PORTAIL" and $id_zone != "ALL") $id_zone=intval($id_zone);
				if(!$id_zone) $id_zone=$session["id_zone"];

				// Bibliotheque
				$id_bib = $request->getParam('b');
				if($id_bib=="PORTAIL") $id_zone="PORTAIL";
				if($id_bib!="ALL" and $id_bib != "PORTAIL") $id_bib=intval($id_bib);
				if(!$id_bib) $id_bib=$session["id_bib"];
				if(intval($id_bib) > 0)
					$id_zone=fetchOne("select ID_ZONE from bib_c_site where ID_SITE=$id_bib");
			}

			// On remet dans la session
			if(!$id_zone) $id_zone="ALL";
			if(!$id_bib) $id_bib="ALL";
			$session["id_zone"]=$id_zone;
			$session["id_bib"]=$id_bib;
			$_SESSION["admin"]["filtre_localisation"]=$session;
		}
		//tracedebug($_SESSION,true);
	}


	protected function _defineConstants($module, $skindir, $url_skin) {
		if ($module !== 'admin' && $module !== 'telephone')
			$module = 'opac';
		$this
			->_defineConstant("URL_JS", BASE_URL . "/public/".$module."/js/")
			->_defineConstant("URL_SHARED_CSS", BASE_URL . "/public/".$module."/css/")
			->_defineConstant("URL_SHARED_IMG", BASE_URL . "/public/".$module."/images/")
			->_defineConstant("PATH_SKIN", $skindir)
			->_defineConstant("URL_IMG", $url_skin . "/images/")
			->_defineConstant("URL_CSS", $url_skin . "/css/")
			->_defineConstant("URL_HTML", $url_skin ."/html/");
	}


	protected function _defineConstant($name, $value) {
		if (!defined($name))
			define($name, $value);
		return $this;
	}

}