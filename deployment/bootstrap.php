<?php

set_time_limit(0);
ini_set("memory_limit","2048M");
if (!defined("KALTURA_ROOT_PATH"))			// may already be defined when invoked through kwidgetAction
	define("KALTURA_ROOT_PATH", realpath(__DIR__ . '/../'));
if (!defined("SF_ROOT_DIR"))				// may already be defined when invoked through kwidgetAction
	define('SF_ROOT_DIR', KALTURA_ROOT_PATH . '/alpha');
define("KALTURA_API_V3", true); // used for different logic in alpha libs

define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
require_once(KALTURA_API_PATH.DIRECTORY_SEPARATOR.'VERSION.php'); //defines KALTURA_API_VERSION
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'alpha'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'kConf.php');

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
require_once(ROOT_DIR . '/alpha/config/kConf.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

// Autoloader
require_once(KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/api_v3/classMap.cache');
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "nusoap", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

$loggerConfigPath = realpath(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "configurations" . DIRECTORY_SEPARATOR . "logger.ini");

try // we don't want to fail when logger is not configured right
{
	$config = new kZendConfigIni($loggerConfigPath);
	$deploy = $config->deploy;
	
	KalturaLog::initLog($deploy);
}
catch(Zend_Config_Exception $ex)
{
}

DbManager::setConfig(kConf::getDB());
DbManager::initialize();
