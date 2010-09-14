<?php
chdir(dirname(__FILE__));
require_once("..".DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."bootstrap_base.php");
require_once(KALTURA_INFRA_PATH.DIRECTORY_SEPARATOR."KAutoloader.php");
define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::setClassMapFilePath(KAutoloader::buildPath("cache", "KalturaClassMap.cache"));
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

$loggerConfigPath = "logger.ini";
$config = new Zend_Config_Ini($loggerConfigPath);
KalturaLog::initLog($config);
KalturaLog::setContext("SCRIPT");
KalturaLog::info("Starting script");

KalturaLog::info("Initializing database...");
$dbManager = new DbManager();
$configPath = KALTURA_API_PATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "database.ini";
$dbManager->setConfig(new Zend_Config_Ini($configPath));
$dbManager->initialize();
KalturaLog::info("Database initialized successfully");
?>