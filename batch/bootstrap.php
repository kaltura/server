<?php
/**
 * 
 * @package Scheduler
 */

require_once("../infra/bootstrap_base.php");
require_once(KALTURA_ROOT_PATH . '/alpha/config/kConf.php');

define("KALTURA_BATCH_PATH", KALTURA_ROOT_PATH . "/batch");

// Autoloader - override the autoloader defaults
require_once(KALTURA_INFRA_PATH . "/KAutoloader.php");
KAutoloader::setClassPath(array(
	KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra", "*"),
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
$loggerConfigPath = KALTURA_BATCH_PATH . "/logger.ini";

try // we don't want to fail when logger is not configured right
{
	$config = new Zend_Config_Ini($loggerConfigPath);
}
catch(Zend_Config_Exception $ex)
{
	$config = null;
}

KalturaLog::initLog($config);
KalturaLog::setContext("BATCH");
