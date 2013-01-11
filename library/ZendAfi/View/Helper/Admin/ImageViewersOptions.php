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
class ZendAfi_View_Helper_Admin_ImageViewersOptions extends ZendAfi_View_Helper_BaseHelper {
	const STYLE_TYPE_JAVA = 'java';
	const STYLE_TYPE_FLASH= 'flash';

	/** @var array */
	private $_styles = array(self::STYLE_TYPE_JAVA => array(
																													'diaporama' => 'Diaporama',
																													'booklet' => 'Livre'));

	/** @var array */
	private $_preferences;

	/** @var string */
	private $_propertiesPath;

	/** @var string */
	private $_valuesPath;

	/**
	 * @param array $preferences
	 * @return string
	 */
	public function imageViewersOptions($preferences) {
		$this->_preferences = $preferences;
		$this->view->headScript()
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.core.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.widget.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.mouse.min.js')
			->appendFile(URL_ADMIN_JS.'jquery_ui/jquery.ui.slider.min.js');

		$this->view->headLink()
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.base.css')
			->appendStylesheet(URL_ADMIN_JS.'jquery_ui/css/jquery.ui.afi.theme.css');


		$html = $this->_getComboStyles();
		
		$this->_ensureOneStyle();
		$html .= $this->_getProperties();

		return $html;
	}


	/** @return string */
	private function _getComboStyles() {
		return '<select name="style_liste" onchange="getId(\'styles_reload\').value=\'1\';document.forms[0].submit();">'
			. '<option value="none">Liste</option>'
			. $this->_getComboTypedStyles(self::STYLE_TYPE_JAVA,
																		$this->translate()->_('Objets java-script'))
			. $this->_getComboTypedStyles(self::STYLE_TYPE_FLASH, 
																		$this->translate()->_('Objets flash'))
			. '</select>'
			. '<input type="hidden" id="styles_reload" name="styles_reload" value="0">';
	}


	/** 
	 * @param string $styleKey
	 * @param string $groupLabel
	 * @return string 
	 */
	private function _getComboTypedStyles($styleKey, $groupLabel) {
		$html = '';

		if (!array_key_exists($styleKey, $this->_styles)
				or !is_array($this->_styles[$styleKey])) {
			return $html;
		}

		$html .= '<optgroup label="' . $groupLabel . '" style="font-style:normal;color:#FF6600">';

		foreach ($this->_styles[$styleKey] as $k => $v) {
			$current = (array_key_exists('style_liste', $this->_preferences) 
									&& $this->_preferences['style_liste'] == $k);
			$html .= '<option style="color:#666666" value="' . $k . '" ' . (($current) ? 'selected="selected"' : '') . '>' . $v . '</option>';
		}

		return $html;
	}


	/** @return bool */
	private function _hasProperties() {
		$defaultsValues = $this->_preferences['style_liste'] . '/defaults.ini';
		$defaultsProperties = $this->_preferences['style_liste'] . '/properties.phtml';

		if (file_exists(PATH_JAVA . $defaultsProperties)) {
			$this->_propertiesPath = PATH_JAVA . $defaultsProperties;
			$this->_valuesPath = PATH_JAVA . $defaultsValues;
			return true;
		}

		if (file_exists(PATH_FLASH . $defaultsProperties)) {
			$this->_propertiesPath = PATH_FLASH . $defaultsProperties;
			$this->_valuesPath = PATH_FLASH . $defaultsValues;
			return true;
		}

		return false;
	}


	/** @return string */
	private function _getProperties() {
		$html = '';
		if (!$this->_hasProperties()) {
			return $html;
		}

		// Bouton des proprietes
		$html .= '&nbsp;' . $this->view->tagImg(URL_ADMIN_IMG . 'ico/copier.gif', 
																						array('title' => $this->translate()->_("propriétés de l'objet"),
																									'style' => 'cursor:pointer',
																									'onclick' => "oProp=getId('objet_props'); if(oProp.style.display=='block') oProp.style.display='none'; else oProp.style.display='block'"));

		$html .= '<div id="objet_props" style="display:none;border:1px solid #7f9db9;min-height:15px;background-color:#ffffff;padding:2px;margin-top:3px">' 
			. '<div style="color:#3C5188;background-color:#eeeeee;padding:4px;margin-bottom:3px;">' 
			. $this->translate()->_('Propriétés de l\'objet') . '</div>';


		try {
			$defaultsValues = new Zend_Config_Ini($this->_valuesPath);
			foreach ($defaultsValues->toArray() as $k => $v) {
				$k = 'op_' . $k;
				if (!array_key_exists($k, $this->_preferences)) {
					$this->_preferences[$k] = $v;
				}
			}
		} catch (Zend_Config_Exception $e) {
			// silently fail			
		}

		$this->view->preferences = $this->_preferences;
		$this->view->addScriptPath(realpath(dirname($this->_propertiesPath)));
		$html .= $this->view->render(basename($this->_propertiesPath));

		return $html . '</div>';
	}


	private function _ensureOneStyle() {
		if (!array_key_exists('style_liste', $this->_preferences)
				|| ('' == $this->_preferences['style_liste'])) {
			$keys = array_keys($this->_styles[self::STYLE_TYPE_JAVA]);
			$first = reset($keys);
			$this->_preferences['style_liste'] = $first;
		}
	}
}