<?php

//error_reporting(E_ALL^E_DEPRECATED);
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
ini_set('memory_limit', '2048M');
date_default_timezone_set('Europe/Paris');

set_include_path( realpath(dirname(__FILE__)).'/../php'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/../php/integration'
. PATH_SEPARATOR . realpath(dirname(__FILE__)).'/../php/classes'
. PATH_SEPARATOR . get_include_path());

chdir('..');

session_start();

$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_REQUEST['action'] = '';
$_SESSION['passe'] = 'admin_systeme';


include_once( "_init.php");


?>
