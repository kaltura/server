<?php

set_time_limit(0);
ini_set("memory_limit","2048M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
require_once(ROOT_DIR . '/alpha/config/kConf.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/deploy/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

$loggerConfigPath = realpath(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "configurations" . DIRECTORY_SEPARATOR . "logger.ini");

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	$deploy = $config->deploy;
	
	KalturaLog::initLog($deploy);
}
catch(Zend_Config_Exception $ex)
{
}

DbManager::setConfig(kConf::getDB());
DbManager::initialize();