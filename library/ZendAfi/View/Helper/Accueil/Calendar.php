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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Class_Module_Calendar
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Calendar extends ZendAfi_View_Helper_Accueil_Base {

//---------------------------------------------------------------------
// Fabrique le html
//---------------------------------------------------------------------  
	public function getHtml()	{
		$this->titre = $this->view->tagAnchor(array('controller' => 'cms',
																								'action' => 'articleviewbydate',
																								'id_module' => $this->id_module,
																								'id_profil' => Class_Profil::getCurrentProfil()->getId()),
																					$this->preferences["titre"]);


		if ($this->preferences['rss_avis'])
			$this->rss_interne = $this->_getRSSurl('cms', 'calendarrss');
		

		$param = array();
		if (array_isset('display_date', $this->preferences))
					$param["DATE"] = $this->preferences['display_date'];
		$param["URL"]="";
		$param["ID_BIB"]=Class_Profil::getCurrentProfil()->getIdSite();
		$param["NB_NEWS"]=3;
		$param["ID_MODULE"] = $this->id_module;
		$param["ID_CAT"] = $this->preferences["id_categorie"];
		$param["SELECT_ID_CAT"] = array_isset("select_id_categorie", $this->preferences) ? $this->preferences["select_id_categorie"] : "all";
		$param["DISPLAY_CAT_SELECT"] = $this->preferences["display_cat_select"];
 		$param["EVENT_INFO"] = $this->preferences["display_event_info"];
		$param["ALEATOIRE"] = 1;
		$param["DISPLAY_NEXT_EVENT"] = array_key_exists('display_next_event', $this->preferences) ? $this->preferences["display_next_event"] : '1';

		$class_calendar = new Class_Calendar($param);
		$this->contenu = $class_calendar->rendHTML();


		return $this->getHtmlArray();
	}

}