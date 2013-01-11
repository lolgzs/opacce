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

class ZendAfi_View_Helper_Accueil_Calendar extends ZendAfi_View_Helper_Accueil_Base {
	protected function _renderHeadScriptsOn($script_loader) {
		$script_loader->addJQueryReady($this->_getAjaxCalendarScript());
	}


	protected function _getAjaxCalendarScript() {
		return <<<SCRIPT

			var ajaxify_calendars = function () {
				var month_link = $("a.calendar_title_month_clickable:first-child, a.calendar_title_month_clickable:last-child");
				month_link.click(function(event) {
					var url = $(this).attr('href');
					if (url == '#')
						url = $(this).jqmData('href');
					$(this).parents(".calendar").load(url+' .calendar>div', 
																						ajaxify_calendars);
					event.preventDefault();
				});

				$('.calendar').bind('swiperight', 
														function () {
															$("a.calendar_title_month_clickable:first-child").click();
														}); 
				$('.calendar').bind('swipeleft', 
														function () {
															$("a.calendar_title_month_clickable:last-child").click();
														}); 


				$("form#calendar_select_categorie").change(function(event) {
					var url = $(this).attr('action');
					$(this).parents(".calendar").load(url, 
																						{'select_id_categorie':$(this).children('select').val(),
																						 'id_module':$(this).children('input').val()},
																						ajaxify_calendars);
				});
  	};
	ajaxify_calendars();
SCRIPT;
	}


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
		$param["NB_NEWS"]=(int)$this->preferences["nb_events"];
		$param["ID_MODULE"] = $this->id_module;
		$param["ID_CAT"] = $this->preferences["id_categorie"];
		$param["SELECT_ID_CAT"] = array_isset("select_id_categorie", $this->preferences) ? $this->preferences["select_id_categorie"] : "all";
		$param["DISPLAY_CAT_SELECT"] = array_isset("display_cat_select", $this->preferences) ? $this->preferences["display_cat_select"] : false;
 		$param["EVENT_INFO"] = $this->preferences["display_event_info"];
		$param["ALEATOIRE"] = 1;
		$param["DISPLAY_NEXT_EVENT"] = array_key_exists('display_next_event', $this->preferences) ? $this->preferences["display_next_event"] : '1';

		$class_calendar = new Class_Calendar($param);
		$this->contenu = $class_calendar->rendHTML();


		return $this->getHtmlArray();
	}

}