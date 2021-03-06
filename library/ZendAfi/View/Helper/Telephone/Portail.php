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
class ZendAfi_View_Helper_Telephone_Portail extends ZendAfi_View_Helper_Portail {
	public function init() {
		Class_ScriptLoader::getInstance()
	  
			->loadJQueryUI()
			->addJQueryReady('$(".app-icon").click(function(event){
						var target = $(event.target).parent();
						$(".app-icon").parent().not(target).removeClass("show-maximized");
						target.toggleClass("show-maximized");
						$("div[role=\"main\"]").toggleClass("one-block-visible", (0 < $(".show-maximized").size()));
						/* $(".app-icon").parent(":not(div." + $(this).parent("div").attr("class") + ")").effect("slide", {mode:"hide"}); */
						/* $(this).siblings(".contenu").toggle(); */
						/* $(this).hide(); */
						/* $(this).siblings(".titre").hide(); */
						/* $(this).siblings(".icon-retour").css("display", "block"); */
						/* //$(this).parent("div").css("float", "none"); */
						/* //$(this).parent("div").css("width", "90%"); */
					})');
	}
		
  
			/*	->addJQueryReady('$(".icon-retour").click(function(){
							$(this).siblings(".contenu").hide();
							})');*/
}