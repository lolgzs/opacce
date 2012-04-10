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
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Widget de sélection des catégories
//////////////////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_Admin_TreeSelect extends ZendAfi_View_Helper_BaseHelper {
	function treeSelect($id_items, $id_categories, $default_visibility, $url_data_source, $form_selector){
		$this->id_items = $id_items;
		$this->id_categories = $id_categories;
		$this->default_visibility = $default_visibility;
		$this->url_data_source = $url_data_source;
		$this->form_selector = $form_selector;
		$this->setHeader();

		$this->view->headScript()->appendScript($this->getJSTreeSelect());

		return $this->renderContent();
	}


	function setHeader(){
		$this->view->headScript()
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.effects.core.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.core.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.widget.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.mouse.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.button.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.sortable.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.accordion.min.js')
			->appendFile(URL_ADMIN_JS.'treeselect/data_selector.js')
			->appendFile(URL_ADMIN_JS.'treeselect/treeselect.js');
		$this->view->headLink()
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.base.css')
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.afi.theme.css')
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.accordion.css')
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.button.css')
			->appendStylesheet(URL_ADMIN_JS.'treeselect/treeselect.css');

	}

	function getJSTreeSelect(){
		$js_id_items = str_replace('-', ',', $this->id_items);
		$js_id_categories = str_replace('-', ',', $this->id_categories);
		$js_default_visibility = $this->default_visibility ? "true" : "false";

		$content = <<<CONTENT
			var pack_ids = function(datas) {
					return \$.map(datas, function(d){return d.id}).join('-');
			};

			var showSelectionWidget = function(data){
					\$(".icon_loading").fadeOut('fast', function(){\$(this).remove()});
					\$(".treeselect").
							treeselect({ datas: data }).
							treeselect("selectItems", [$js_id_items]).
							treeselect("selectCategories", [$js_id_categories]).
							treeselect('toggleVisibility', $js_default_visibility);


					\$("$this->form_selector").submit(function(){
							\$(".treeselect").treeselect("readSelection", function(items, categories){
									\$("#id_categorie").val(pack_ids(categories));
									\$("#id_items").val(pack_ids(items));
							});
					});
			};

			\$(document).ready(function(){
					\$.getJSON("$this->url_data_source", 
										function(data){
												showSelectionWidget(data)});
			});
CONTENT;

		return $content;
	}

	
	function renderContent(){
		$url_icon = URL_ADMIN_IMG.'loading.gif';
		$content = <<<CONTENT
			<div class="icon_loading">
    		<img src="$url_icon" /> 
			</div>
			<div class="treeselect" style="display:none"></div>
			<input type="hidden" id="id_categorie" name="id_categorie" value="$this->id_categories" />
			<input type="hidden" id="id_items" name="id_items" value="$this->id_items"/>
CONTENT;
		return $content;
	}
}