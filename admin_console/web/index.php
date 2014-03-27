<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
    
defined('UI_INFRA_PATH')
    || define('UI_INFRA_PATH', realpath(dirname(__FILE__) . '/../../ui_infra'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../vendor/ZendFramework/library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

$configPath = realpath(APPLICATION_PATH . '/../configurations/admin.ini');
if(!file_exists($configPath))
{
	$configTemplatePath = realpath(APPLICATION_PATH . '/../configurations/admin.template.ini');
	$msg = "Please rename template file [$configTemplatePath] to admin.ini and replace the tokens";
	error_log($msg);
	die($msg);
}

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    $configPath
);
$application->bootstrap()
            ->run();

// Prevent opening from within an iFrame (for browsers that respect this header)
// (*) main.js contains a JavaScript counterpart for browsers that don't respect this header.
header("X-Frame-Options: DENY");