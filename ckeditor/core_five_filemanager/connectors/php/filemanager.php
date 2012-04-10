<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Paris');

/**
 *	Filemanager PHP connector
 *
 *	filemanager.php
 *	use for ckeditor filemanager plug-in by Core Five - http://labs.corefive.com/Projects/FileManager/
 *
 *	@license	MIT License
 *	@author		Riaan Los <mail (at) riaanlos (dot) nl>
 *  @author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */



/**
 * Fast fix for afi-opac context
 * @author Patrick Barroca
 * @param string $path
 */
function opacTraversalProtect ($path) {
	$path = (string)$path;
	$parts = explode('/', $path);
	array_shift($parts);
	if (2 > count($parts)) {
		exit();
	}
	if ('userfiles' != $parts[1]) {
		exit();
	}
	if (in_array('..', $parts)) {
		exit();
	}
}


require_once('./inc/filemanager.inc.php');
require_once('filemanager.config.php');
require_once('filemanager.class.php');

if (isset($config['plugin']) && !empty($config['plugin'])) {
	$pluginPath = 'plugins' . DIRECTORY_SEPARATOR . $config['plugin'] . DIRECTORY_SEPARATOR;
	require_once($pluginPath . 'filemanager.' . $config['plugin'] . '.config.php');
	require_once($pluginPath . 'filemanager.' . $config['plugin'] . '.class.php');
	$className = 'Filemanager'.strtoupper($config['plugin']);
	$fm = new $className($config);
} else {
	$fm = new Filemanager($config);
}

$response = '';

if(!auth()) {
  $fm->error($fm->lang('AUTHORIZATION_REQUIRED'));
}

if(!isset($_GET)) {
  $fm->error($fm->lang('INVALID_ACTION'));
} else {

  if(isset($_GET['mode']) && $_GET['mode']!='') {

    switch($_GET['mode']) {
      	
      default:
				opacTraversalProtect($_GET['path']);
        $fm->error($fm->lang('MODE_ERROR'));
        break;

      case 'getinfo':
				opacTraversalProtect($_GET['path']);
        if($fm->getvar('path')) {
          $response = $fm->getinfo();
        }
        break;

      case 'getfolder':
				opacTraversalProtect($_GET['path']);        	
        if($fm->getvar('path')) {
          $response = $fm->getfolder();
        }
        break;

      case 'rename':

        if($fm->getvar('old') && $fm->getvar('new')) {
          $response = $fm->rename();
        }
        break;

      case 'delete':
				opacTraversalProtect($_GET['path']);
        if($fm->getvar('path')) {
          $response = $fm->delete();
        }
        break;

      case 'addfolder':
				opacTraversalProtect($_GET['path']);
        if($fm->getvar('path') && $fm->getvar('name')) {
          $response = $fm->addfolder();
        }
        break;

      case 'download':
				opacTraversalProtect($_GET['path']);
        if($fm->getvar('path')) {
          $fm->download();
        }
        break;
      case 'preview':
        if($fm->getvar('path')) {
          $fm->preview();
        }
        break;

    }

  } else if(isset($_POST['mode']) && $_POST['mode']!='') {

    switch($_POST['mode']) {
      	
      default:

        $fm->error($fm->lang('MODE_ERROR'));
        break;
        	
      case 'add':
        if($fm->postvar('currentpath')) {
					opacTraversalProtect($_POST['currentpath']);
          $response = $fm->add();
        }
        break;

    }

  }
}

echo json_encode($response);
die();

?>