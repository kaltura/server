<?php
//
//require_once(dirname(__FILE__) . "/../../infra/bootstrap_base.php");
//require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
//
//define("KALTURA_TEST_PATH", KALTURA_ROOT_PATH . "/tests/base");
//
//// Autoloader - override the autoloader defaults
//require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
//KAutoloader::setClassPath(array(
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"),
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"),
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "lib", "*"),
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "apps", "kaltura", "lib", "*"),
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"),
////	KAutoloader::buildPath(KALTURA_ROOT_PATH, "tests", "base", "*"),
////	KAutoloader::buildPath(KALTURA_ROOT_PATH, "tests", "api", "*"),
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "tests", "unit_test", "lib", "*"),
//));
//
//KAutoloader::setIncludePath(array(
//	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library"),
//));
//
//KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/tests/classMap.cache');
//KAutoloader::register();
//
//// Timezone
//date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York
//
//// Logger
//$loggerConfigPath = KALTURA_TEST_PATH . "/config/logger.ini";
//
//try // we don't want to fail when logger is not configured right
//{
//	$config = new Zend_Config_Ini($loggerConfigPath);
//}
//catch(Zend_Config_Exception $ex)
//{
//	$config = null;
//}
//
//KalturaLog::initLog($config);
//KalturaLog::setContext("TESTS");
//
//// set DB
//DbManager::setConfig(kConf::getDB());
//DbManager::initialize();
