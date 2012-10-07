<?php
// http://www.red-team-design.com/just-another-awesome-css3-buttons
// http://bestwebgallery.com/

// some shortcuts
define("DS", DIRECTORY_SEPARATOR);
define("PS", PATH_SEPARATOR);

// basic configuration
define('PATH_ROOT', realpath(dirname(__FILE__)));
define('PATH_CONFIG', PATH_ROOT . DS . 'config');
define('PATH_LIBRARIES', PATH_ROOT . DS . 'libs');

// include the application config
require_once (PATH_CONFIG . DS . "environment.config.php");

// include the basic Autoloader class
require_once (PATH_LIBRARIES . DS . "play4php" . DS . "Autoloader.php");

// register autoloader for the application and initialize the configuration file
$applicationLoader = Autoloader::getInstance();
$config = ConfigManager::getInstance();

// Add additional source folders
//$applicationLoader->addSourceDirs(array(
//				$config->getAppPath() . DS . 'utils'
//				));

$applicationLoader->registerAutoloader();

// begin routing
Router::getInstance()->dispatch();
