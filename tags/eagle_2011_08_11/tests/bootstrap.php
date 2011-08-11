<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");

define("KALTURA_TESTS_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."tests");
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'alpha'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'kConf.php');

// Autoloader
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_TESTS_PATH, "base", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_TESTS_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_TESTS_PATH, "api", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_TESTS_PATH, "common", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_TESTS_PATH, "unitTests", "*"));

KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/tests/classMap.cache');
//KAutoloader::dumpExtra();
KAutoloader::register();

// Timezone
$timeZone = kConf::get("date_default_timezone");

$isTimeZone = substr_count($timeZone, '@') == 0; //no @ in a real time zone

if($isTimeZone)
	date_default_timezone_set($timeZone); // America/New_York

// Logger
$loggerConfigPath = KALTURA_TESTS_PATH.DIRECTORY_SEPARATOR."base".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}

KalturaLog::initLog($config);
KalturaLog::setContext("tests");

// for places where kLog is used
kLog::setLogger(KalturaLog::getInstance());

// set DB
DbManager::setConfig(kConf::getDB());
DbManager::initialize();