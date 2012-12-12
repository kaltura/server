<?php
/**
 * 
 * @package Scheduler
 */

chdir(__DIR__);
define('KALTURA_ROOT_PATH', realpath(__DIR__ . '/../'));
require_once(KALTURA_ROOT_PATH . '/server_infra/kConf.php');

define("KALTURA_BATCH_PATH", KALTURA_ROOT_PATH . "/batch");

// Autoloader - override the autoloader defaults
require_once(KALTURA_ROOT_PATH . "/infra/KAutoloader.php");
KAutoloader::setClassPath(array(
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "server_infra", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "*"),
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"),
	KAutoloader::buildPath(KALTURA_BATCH_PATH, "*"),
));

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*", "batch", "*"));

KAutoloader::setIncludePath(array(
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "ZendFramework", "library"),
));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/batch/classMap.cache');
KAutoloader::register();

// Logger
$loggerConfigPath = KALTURA_ROOT_PATH . "/configurations/logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
	KalturaLog::initLog($config->batch);
	KalturaLog::setContext("BATCH");
}
catch(Zend_Config_Exception $ex)
{
}

