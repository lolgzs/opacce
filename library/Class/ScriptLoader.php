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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Pseudo MVC pour pouvoir déclarer les scripts à être chargés dans le head
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_ScriptLoader {
	protected static $instance;
	protected $_script_lines;
	protected $_css_lines;
	protected $_should_load_amber;
	protected $_amber_files;
	protected $_amber_ready_scripts;

	/**
	 * @return ScriptLoader
	 */
	public static function getInstance() {
		if (!isset(self::$instance))
			self::$instance = self::newInstance();
		return self::$instance;
	}


	/**
	 * @return ScriptLoader
	 */
	public static function newInstance() {
		return new self();
	}


	public static function resetInstance() {
		self::$instance = null;
	}


	public function __construct() {
		$this->_script_lines = array();
		$this->_css_lines = array();
		$this->_should_load_amber = false;
	}


	/**
	 * @return ScriptLoader
	 */
	public function loadAmber() {
		$this->_should_load_amber = true;
		return $this;
	}


	/**
	 * @return ScriptLoader
	 */
	protected function _deferredLoadAmber() {
		if (!$this->_should_load_amber)
			return $this;
		$this->_should_load_amber = false;

		$this->addScript(AMBERURL.'src/js/amber.js');

		$deploy =  $this->isAmberModeDeploy();
		
		$amber_options = sprintf('{"home":"%s", "files":%s, "deploy":%s, "ready":%s}',
														 AMBERURL.'src/',
														 json_encode($this->_amberAdditionalFiles($deploy)),
														 $deploy?'true':'false',
														 sprintf('function(){$(function(){%s})}',
																		 implode(';', $this->getAmberReadyScripts())));

		$this->addInlineScript(sprintf('loadAmber(%s);', $amber_options));
		return $this;
	}


	/**
	 * @param $album_id id de l'album a afficher
	 * @param $selector selecteur jQuery où l'album doit être affiché
	 * @return ScriptLoader
	 */
	public function loadBooklet($album_id, $selector) {
		return $this
			->addAmberPackage('AFI')
			->addAmberReady(sprintf("smalltalk.BibNumAlbum._load_in_scriptsRoot_('%s.json', '%s', '%s')",
															BASE_URL.'/bib-numerique/album/id/'.$album_id,
															$selector,
															AMBERURL."afi/souvigny/"))
			->loadAmber();
	}


	/**
	 * @return ScriptLoader
	 */
	public function loadPrettyPhoto() {
		return $this
			->addStyleSheet(URL_ADMIN_JS.'prettyphoto/css/prettyPhoto')
			->addAdminScript('prettyphoto/js/jquery.prettyPhoto')
			->addJQueryReady('$("a[rel^=\'prettyPhoto\']").prettyPhoto({opacity: 0.20, social_tools: ""})');
	}


	/**
	 * @return ScriptLoader
	 */
	public function loadJQuery() {
		return $this->addScript(JQUERY);
	}


	/**
	 * @return ScriptLoader
	 */
	public function loadJQueryMobile() {
		return $this
			->addScript(BASE_URL.'/public/telephone/js/jquery.mobile-'.JQUERYMOBILE_VERSION.'.min')
			->addSkinStyleSheet('../jquerymobile/jquery.mobile-'.JQUERYMOBILE_VERSION.'.min');
	}


	/**
	 * @return ScriptLoader
	 */
	public function loadJQueryUI() {
		return $this->addScript(JQUERYUI);
	}


	/**
	 * @return ScriptLoader
	 */
	public function addJQueryReady($js) {
		return $this->addInlineScript(sprintf("$(function(){%s});", $js));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addInlineScript($script) {
		return $this->_scriptsAddLine(sprintf('<script type="text/javascript">%s</script>', $script));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addScript($file) {
		if (false === strpos($file, '.js'))
				$file .= '.js';
		return $this->_scriptsAddLine(sprintf('<script src="%s" type="text/javascript"></script>', $file));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addOPACScript($script) {
		return $this->addScript(BASE_URL."/public/opac/js/".$script);
	}


	/**
	 * @return ScriptLoader
	 */
	public function addOPACScripts($scripts) {
		foreach($scripts as $script)
			$this->addOPACScript($script);
		return $this;
	}


	/**
	 * @return ScriptLoader
	 */
	public function addAdminScript($script) {
		return $this->addScript(URL_ADMIN_JS.$script);
	}


	public function showNotifications() {
		$messenger = new Zend_Controller_Action_Helper_FlashMessenger();
		$messages = $messenger->getMessages();
		foreach($messages as $message)
			$this->notify($message);
		return $this;
	}


	/**
	 * @return ScriptLoader
	 */
	public function notify($message) {
		return $this
			->addScript(URL_ADMIN_JS.'notification/js/jquery_notification_v.1')
			->addStyleSheet(URL_ADMIN_JS.'notification/css/jquery_notification')
			->addJQueryReady(sprintf('showNotification(%s)',
															 json_encode(array('message' => $message, 
																								 'autoClose' => true, 
																								 'duration' => 10, 
																								 'type' => 'information'))));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addStyleSheet($file, $additional_attributes=null) {
		if (!is_array($additional_attributes))
			$additional_attributes = array();

		if (false === strpos($file, '.css'))
				$file .= '.css';

		$attributes = array_merge(array('type' => 'text/css', 
																		'rel' => 'stylesheet', 
																		'href' => $file,
																		'media' => 'screen'),
															$additional_attributes);
		
		$html_attributes = '';
		foreach($attributes as $name => $value)
			$html_attributes .= sprintf(' %s="%s" ', $name, $value);

		return $this->cssAddLine(sprintf('<link %s>', $html_attributes));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addOPACStyleSheet($file, $additional_attributes=null) {
		return $this->addStyleSheet(BASE_URL."/public/opac/css/".$file,
																$additional_attributes);
	}


	/**
	 * @return ScriptLoader
	 */
	public function addOPACStyleSheets($files, $additional_attributes=null) {
		foreach($files as $file)
			$this->addOPACStyleSheet($file, $additional_attributes);
		return $this;
	}


	/**
	 * ex: addSkinStyleSheet('blanc_sur_noir', array('rel' => 'alternate stylesheet',
	 *																						   'title' => $this->_('Blanc sur noir')))
	 * @return ScriptLoader
	 */
	public function addSkinStyleSheet($file, $additional_attributes=null) {
		return $this->addStyleSheet(URL_CSS.$file,
																$additional_attributes);
	}


	/**
	 * ex: addAdminStyleSheet('blanc_sur_noir', array('rel' => 'alternate stylesheet',
	 *																						   'title' => $this->_('Blanc sur noir')))
	 * @return ScriptLoader
	 */
	public function addAdminStyleSheet($file, $additional_attributes=null) {
		return $this->addStyleSheet(URL_ADMIN_CSS.$file,
																$additional_attributes);
	}


	/**
	 * @return ScriptLoader
	 */
	public function addSkinStyleSheets($files, $additional_attributes=null) {
		foreach($files as $file)
			$this->addSkinStyleSheet($file, $additional_attributes);
		return $this;
	}


	/**
	 * @return ScriptLoader
	 */
	public function addInlineStyle($css) {
		return $this->cssAddLine(sprintf('<style type="text/css">%s</style>', $css));
	}


	/**
	 * @return ScriptLoader
	 */
	public function addAdminScripts($scripts) {
		foreach($scripts as $script)
			$this->addAdminScript($script);
		return $this;
	}


	/**
	 * @return ScriptLoader
	 */	
	 public function _scriptsAddLine($line) {
		 $this->_script_lines []= $line;
		 return $this;
	 }


	/**
	 * @return ScriptLoader
	 */	
	 public function cssAddLine($line) {
		 $this->_css_lines []= $line;
		 return $this;
	 }


	/**
	 * @return Boolean
	 */	
	public function isAmberModeDeploy() {
		if (null == $amber = Zend_Registry::get('cfg')->get('amber'))
			return true;

		return !in_array($amber->get('deploy'),
										 array(false, 'false', 0, '0'),
										 true);
	}


	/**
	 * @return Array
	 */	
	public function &getAmberFiles() {
		if (!isset($this->_amber_files)) {
			$this->_amber_files = array('AFI-Core');
			if (!$this->isAmberModeDeploy()) {
				$this->_amber_files[]='AMock';
				$this->_amber_files[]='AMock-Tests';
			}
		}
		return $this->_amber_files;
	}


	/**
	 * @return Array
	 */	
	public function &getAmberReadyScripts() {
		if (!isset($this->_amber_ready_scripts))
			$this->_amber_ready_scripts = array(sprintf('smalltalk.Package._defaultCommitPathJs_("%s/opac/amber/commitJs")', BASE_URL),
																					sprintf('smalltalk.Package._defaultCommitPathSt_("%s/opac/amber/commitSt")', BASE_URL));
		return $this->_amber_ready_scripts;
	}


	/**
	 * @param String package
	 * @return Class_ScriptLoader
	 */	
	public function addAmberPackage($package) {
		if (in_array($package, $this->getAmberFiles()))
				return $this;

		array_push($this->getAmberFiles(), $package);
		if (!$this->isAmberModeDeploy())
			array_push($this->getAmberFiles(), $package.'-Tests');
		return $this;
	}


	/**
	 * @param String js
	 * @return Class_ScriptLoader
	 */	
	public function addAmberReady($js) {
		if (!in_array($js, $this->getAmberReadyScripts()))
			array_push($this->getAmberReadyScripts(), $js);
		return $this;
	}


	/**
	 * @return Boolean
	 */	
	protected function _amberAdditionalFiles($deploy) {
		$additional_files = array();
		foreach ($this->getAmberFiles() as $file) {
			if ($deploy) $file .= '.deploy';
			$additional_files []= sprintf('../../afi/js/%s.js', $file);
		}
		
		return $additional_files;
	}


	/**
	 * @return String
	 */
	public function styleSheetsHTML() {
		$this->_deferredLoadAmber();
		return implode('',array_unique($this->_css_lines));
	}


	/**
	 * @return ScriptLoader
	 */
	public function renderStyleSheets() {
		echo $this->styleSheetsHTML();
		return $this;
	}


	/**
	 * @return String
	 */
	public function javaScriptsHTML() {
		$this->_deferredLoadAmber();
		return	implode('',array_unique($this->_script_lines));
	}


	/**
	 * @return ScriptLoader
	 */
	public function renderJavaScripts() {
		echo $this->javaScriptsHTML();
		return $this;
	}


	/**
	 * @return String
	 */
	public function html() {
		return $this->styleSheetsHTML().$this->javaScriptsHTML();
	}
}

?>