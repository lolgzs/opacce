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


class ZendAfi_View_Helper_Telephone_Tags_NoticeDetaillee extends ZendAfi_View_Helper_BaseHelper {
	public function noticeDetaillee($notice) {
		$fields = array(
										$this->view->_('Titre(s)') => $notice->getTitrePrincipal(),
										$this->view->_('Auteur(s)') => $notice->getAuteurPrincipal(),
										$this->view->_('Editeur(s)') => $notice->getEditeur(),
										$this->view->_('Collation') => $notice->getCollation(), 
										$this->view->_('Collection(s)') => $notice->getCollection(), 
										$this->view->_('Année') => $notice->getAnnee(), 
										$this->view->_('Isbn') => $notice->getIsbn(), 
										$this->view->_('Ean') => $notice->getEan(), 
										$this->view->_('Langue(s)') => $notice->getLanguesList(),
										$this->view->_('Notes(s)') => $notice->getNotes()
										);

		return $this->_fielsToHTMLTable(array_filter($fields));
	}


	public function _fielsToHTMLTable($fields) {
		$rows = array();
		foreach ($fields as $label => $field) 
			$rows [] = sprintf('<tr><td>%s : </td><td>%s</td></tr>',
												 $label,
												 $this->_formatField($field));
		return sprintf('<table class="notice_detail">%s</table>', implode('',$rows));
	}


	public function _formatField($field) {
		if (!$field) return '';
		if (!is_array($field)) return $field;

		$html = '<ul>'; 
		foreach($field as $subfield) 
			$html .= '<li>'.$subfield.'</li>';
		$html .= '<ul>'; 
		return $html;
	}
	
}

?>