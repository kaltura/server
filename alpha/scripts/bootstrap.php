<?php
set_time_limit(0);

ini_set("memory_limit","700M");

define("KALTURA_ROOT_PATH", realpath(__DIR__ . '/../../'));
require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');
require_once(KALTURA_ROOT_PATH . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/classMap.cache');
KAutoloader::addExcludePath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "aws", "*")); // Do not load AWS files
KAutoloader::addExcludePath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "HTMLPurifier", "*")); // Do not load HTMLPurifier files
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

$loggerConfigPath = KALTURA_ROOT_PATH.'/configurations/logger.ini';
try
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	KalturaLog::initLog($config->scripts);
	KalturaLog::setContext(basename($_SERVER['SCRIPT_NAME']));
}
catch (Zend_Config_Exception $ex)
{
	
}
KalturaLog::info("Starting script");

KalturaLog::info("Initializing database...");
DbManager::setConfig(kConf::getDB());
DbManager::initialize();
KalturaLog::info("Database initialized successfully");
