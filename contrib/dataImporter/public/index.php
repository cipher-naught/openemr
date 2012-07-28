<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//Set OpenEMR includes
include_once("../../../interface/globals.php");
include_once("../../../library/api.inc");
require_once("../../../library/classes/ORDataObject.class.php");

//Zend_Controller_Front::run(realpath(dirname(__FILE__) . '/../application/controllers'));
//Question
if(!isset($_SESSION)) {
	session_start();
}
$application->bootstrap();
$application->run();