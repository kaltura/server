<?php
require_once("..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");

define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
define("KALTURA_PLUGIN_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."plugins");
define("KALTURA_GENERATOR_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."generator");


// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_PLUGIN_PATH, "*"));
KAutoloader::setClassMapFilePath(KAutoloader::buildPath(KALTURA_GENERATOR_PATH, "cache","KalturaClassMap.cache"));
KAutoloader::register();


// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York


// Logger
$loggerConfigPath = KALTURA_GENERATOR_PATH.DIRECTORY_SEPARATOR."logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}

KalturaLog::initLog($config);
KalturaLog::setContext("GENERATOR");
