<?php
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."server_infra".DIRECTORY_SEPARATOR."bootstrap_base.php");

define("KALTURA_API_V3", true); // used for different logic in alpha libs

define("KALTURA_API_PATH", KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."api_v3");
require_once(KALTURA_API_PATH.DIRECTORY_SEPARATOR.'VERSION.php'); //defines KALTURA_API_VERSION
require_once (KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR.'server_infra'.DIRECTORY_SEPARATOR.'kConf.php');


// Autoloader
require_once(KALTURA_ROOT_PATH.DIRECTORY_SEPARATOR."infra".DIRECTORY_SEPARATOR."KAutoloader.php");
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/api_v3/classMap.cache');
if (!KAutoloader::loadClassMapFromCache())
{
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "server_infra", "*"));
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "alpha", "plugins", "*")); // needed for testmeDoc
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "*"));
	KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator")); // needed for testmeDoc
}
KAutoloader::register();


// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

// Logger
kLoggerCache::InitLogger('api_v3');
