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
class ZendAfi_View_Helper_Notice_LienReserver extends Zend_View_Helper_HtmlElement {
	public function getScriptDialog() {
		return '
			var popupDialog = null;

			var showPopWin = function(url, width, height, returnFunc, showCloseBox) {
				popupDialog = $(\'<iframe src="\'+url+\'" style="min-width:95%"></div>\').dialog({width: width, height: height, close: returnFunc});
			}


			var hidePopWin = function() {
				popupDialog.dialog(\'close\');
			}

			var closeDialogExemplaires = function () {
				$(\'#dialog_reserver\').dialog(\'close\').remove();
			}

			var openDialogExemplaires = function(id_notice) {
				$.ajax({url: "'.$this->view->url(['controller' => 'noticeajax', 'action' => 'exemplaires'], null, true).'/id_notice/"+id_notice})
				.done(function(data) {
						var dialog_reserver = $(\'<div id="dialog_reserver"></div>\')
							.html(data)
							.dialog({width: 800, 
										   modal: true, 
										   title: "'.$this->view->_("Exemplaires").'",
								       open: function() {
									             $(\'div.ui-widget-overlay\').click(closeDialogExemplaires);
									            }});

						$reserver_links = dialog_reserver.find(\'td.exemplaires:last-child img\');
						$.each($reserver_links, function(index, link) {
								link = $(link);
								var onclick = link.attr(\'onclick\');
								link.attr(\'onclick\', \'\');
								link.click(function() {
										closeDialogExemplaires();
										eval(onclick);
									});			
							});
					})
			}';
	}

	public function notice_LienReserver($id_notice) {
		Class_ScriptLoader::getInstance()->addInlineScript($this->getScriptDialog());
		return '<a href="#" onclick="openDialogExemplaires('.$id_notice.');return false">&nbsp;&nbsp;&nbsp;&raquo;&nbsp;'.$this->view->_('Réserver').'</a>';
	}
}

?>