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
	/** @var boolean */
	protected $_hasActions = false;
	/** @var int */
	protected $_cols_count = 0;

	public function tagModelTable($models, $cols, $attribs, $actions, $id, $group_by = null, $callbacks = []) {
		$this->_hasActions = 0 < count($actions);
		$this->_cols_count = count($attribs) + ($this->_hasActions ? 1 : 0);
		
		return '<table id="'.$id.'" class="models">'
			.$this->head($cols)
			.$this->tbody($models, $attribs, $actions, $group_by, $callbacks)
			.'</table>';
	}


	public function head($cols) {
		$cols_html = 	'';
		foreach ($cols as $col) 
			$cols_html .= '<th>'.$col.'</th>';

		$html = '<thead><tr>'.$cols_html;
		if (0 < $this->_hasActions)
			$html .= '<th class="actions" style="width:50px;">' . $this->view->_('Actions') . '</th>';
		$html .= '</tr></thead>';
		return $html;
	}


	public function tbody($models, $attribs, $actions, $group_by, $callbacks) {
		$rows = '';

		$groups = array();
		if (null != $group_by) {
			foreach ($models as $model) {
				$group = $model->callGetterByAttributeName($group_by);
				if (!array_key_exists($group, $groups))
					$groups[$group] = array();
				$groups[$group][] = $model;
			}
		} else {
			$groups['no_group'] = $models;
		}


		$rows = $this->renderGroupsAsTableRows($groups, $attribs, $actions, $callbacks);
		return '<tbody>'.$rows.'</tbody>';
	}


	public function renderGroupsAsTableRows($groups, $attribs, $actions, $callbacks) {
		$rows = '';

		foreach ($groups as $name => $groupModels) {
			if ('no_group' != $name && '' != $name)
				$rows .= '<tr><td style="background-color:#888;color:white;font-size:120%;padding:2px 10px;font-weight:bold;" colspan="' . $this->_cols_count . '">' . $this->view->escape($name) . '</td></tr>';

			$rows .= $this->renderModelsAsTableRows($groupModels, $attribs, $actions, $callbacks);
		}

		return $rows;
	}


	public function renderModelsAsTableRows($groupModels, $attribs, $actions, $callbacks) {
		$rows = '';
		foreach ($groupModels as $model)
			$rows .= $this->renderModelAsTableRow($model, $attribs, $actions, $callbacks);
		return $rows;
	}
	

	public function renderModelAsTableRow($model, $attribs, $actions, $callbacks) {
		$cols = '';

		$default_callback = function ($value) {return $this->view->escape($value);};
		
		foreach ($attribs as $attrib) {
			$callback = (array_key_exists($attrib, $callbacks)) ? $callbacks[$attrib] : $default_callback;
			$cols .= '<td>' . $callback($model->callGetterByAttributeName($attrib)) . '</td>';
		}

		$row = '<tr>'.$cols.'<td>';
		if ($this->_hasActions)
			$row .= $this->renderModelActions($model, $actions).'</td>';
		$row .= '</tr>';

		return $row;
	}


	/*
	 * @param Storm_Model_Abstract $model
	 * @param array of arrays of string / Closure $actions
	 * @return string
	 */
	public function renderModelActions($model, $actions) {
		$html = '';
		foreach ($actions as $action)
			$html .= $this->renderModelAction($model, $action);

		return $html;
	}


	/*
	 * @param Storm_Model_Abstract $model
	 * @param array of strings/closure or closure
	 * @return string
	 */
	public function renderModelAction($model, $action) {
		if (is_a($action, 'Closure'))
			return $action($model);

			
		$content = $action['content'];
		return  $this->view->tagAnchor(array('action' => $action['action'],
																				 'id' => $model->getId()),
																	 is_a($content, 'Closure') ? $content($model) : $content);
	}
}

?>

