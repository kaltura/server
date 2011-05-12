<?php
set_time_limit(0);

ini_set("memory_limit","700M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../'));
require_once(ROOT_DIR . '/alpha/config/kConf.php');
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/scripts/classMap.cache');
KAutoloader::register();

date_default_timezone_set(kConf::get("date_default_timezone"));

$loggerConfigPath = dirname(__FILE__).'/logger.ini';
$config = new Zend_Config_Ini($loggerConfigPath);
KalturaLog::initLog($config);
KalturaLog::setContext("SCRIPT");
KalturaLog::info("Starting script");

KalturaLog::info("Initializing database...");
DbManager::setConfig(kConf::getDB());
DbManager::initialize();
KalturaLog::info("Database initialized successfully");
