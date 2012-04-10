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
class ZendAfi_View_Helper_BaseHelper {
	protected static $_translate;

	/** @var ZendAfi_Controller_Action_Helper_View */
	protected $view;

	public function __construct() {
		$this->view = new ZendAfi_Controller_Action_Helper_View();
	}

	/**
	 * @param string $libelle
	 * @return string
	 */
	public function traduire($libelle) {
		return $this->view->traduire($libelle);
	}

	/**
	 * @param Zend_View_Interface $view
	 */
	public function setView($view) {
		$this->view = $view;
	}

	/**
	 * @return Zend_View_Interface
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * @param string $arg
	 * @return array
	 */
	public function splitArg($arg) {
		$pos = strscan($arg, '=', 0);
		$value[0] = strLeft( $arg, $pos);
		$value[1] = strMid( $arg, $pos+1,1024);
		return $value;

	}

	/**
	 * @param string $js_function
	 * @return string
	 */
	public function addLoadEvent($js_function) {
		$html = '<script type="text/javascript">';
		$html .= '$(document).ready('.$js_function.')';
		$html .= '</script>';
		return $html;
	}

	/**
	 * @param string $js_function
	 * @return string
	 */
	public function addUnloadEvent($js_function) {
		$html = '<script type="text/javascript">';
		$html .= '$(document).unload('.$js_function.')';
		$html .= '</script>';
		return $html;
	}

	/**
	 * @return Zend_Translate
	 */
	public static function translate() {
		if (!isset(self::$_translate))
			self::$_translate = Zend_Registry::get('translate');

		return self::$_translate;
	}

}