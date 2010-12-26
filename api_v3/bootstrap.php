<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."alpha".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."kConf.php");
define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
require_once(KALTURA_API_PATH.DIRECTORY_SEPARATOR.'VERSION.php'); //defines KALTURA_API_VERSION


// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
KAutoloader::setClassMapFilePath(KAutoloader::buildPath(KALTURA_API_PATH, "cache", "KalturaClassMap.cache"));
//KAutoloader::dumpExtra();
KAutoloader::register();


// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York


// Logger
$loggerConfigPath = KALTURA_API_PATH.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}

KalturaLog::initLog($config);

// for places where kLog is used
kLog::setLogger(KalturaLog::getInstance());
