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
class ZendAfi_View_Helper_TagModelTable extends Zend_View_Helper_HtmlElement {
	public function tagModelTable($models, $cols, $attribs, $actions, $id) {
		return '<table id="'.$id.'" class="models">'
			.$this->head($cols)
			.$this->tbody($models, $attribs, $actions)
			.'</table>';
	}


	public function head($cols) {
		$cols_html = 	'';
		foreach ($cols as $col) 
			$cols_html .= '<th>'.$col.'</th>';

		return '<thead><tr>'.$cols_html.'<th class="actions" style="width:50px;">'.$this->view->_('Actions').'</th></tr></thead>';
	}


	public function tbody($models, $attribs, $actions) {
		$rows = '';
		foreach($models as $model) {
			$cols = '';

			foreach ($attribs as $attrib)
				$cols .= '<td>'.$this->view->escape($model->callGetterByAttributeName($attrib)).'</td>';

			$rows .= '<tr>'.$cols.'<td>'.$this->renderModelActions($model, $actions).'</td></tr>';
		}

		return '<tbody>'.$rows.'</tbody>';
	}


	public function renderModelActions($model, $actions) {
		$html = '';
		foreach ($actions as $action) {
			$html .= $this->view->tagAnchor(array('action' => $action['action'],
																						'id' => $model->getId()),
																			$action['content']);
		}

		return $html;
	}
}

?>

