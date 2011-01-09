<?php 

//TODO:Fix this and the cache

////API bootstrap
//require_once (dirname(__FILE__). '/../../api_v3/bootstrap.php');

require_once(dirname(__FILE__) . "/../../infra/bootstrap_base.php");
require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');

define("KALTURA_TEST_PATH", KALTURA_ROOT_PATH . "/tests/base");

// Autoloader - override the autoloader defaults
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

KAutoloader::setClassPath(array(
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "lib", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "apps", "kaltura", "lib", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "tests", "unit_test", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "tests", "unit_test", "lib", "*"),
));

//Add the zend phpunit support 
KAutoloader::setIncludePath(array(
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library"),
));

//File path to entire server cache folder
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . 'unitTest/classMap.cache');

//File path to unit tests cache folder
//KAutoloader::setClassMapFilePath(dirname(__FILE__) . '/cache/classMap.cache');
KAutoloader::register();

//The kaltura client
require_once (dirname(__FILE__). '/lib/kalturaClient.php');

require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");

define("KALTURA_UNIT_TEST_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."tests".DIRECTORY_SEPARATOR."unit_test");

//Unit Test Project files (used in the zend phpunit library not needed for the command line)
//require_once ('/PHPUnit/Framework.php');
//require_once ('/PHPUnit/Util/Timer.php');

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

//Set the DB
DbManager::setConfig(kConf::getDB());
DbManager::initialize();


