<?php
set_time_limit(0);

ini_set("memory_limit","700M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
require_once(ROOT_DIR . '/server_infra/kConf.php');
require_once(ROOT_DIR . '/server_infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

$include_path = 
	realpath(dirname(__FILE__).'/../vendor/ZendFramework/library') . PATH_SEPARATOR . 
	get_include_path();
	
set_include_path($include_path);

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "server_infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/classMap.cache');
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
