<?php
chdir(__DIR__);

define("KALTURA_ROOT_PATH", realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "server_infra" . DIRECTORY_SEPARATOR . "kConf.php");

define("KALTURA_API_PATH", KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "api_v3");
define("KALTURA_PLUGIN_PATH", KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "plugins");

require_once(KALTURA_API_PATH . DIRECTORY_SEPARATOR . 'VERSION.php'); //defines KALTURA_API_VERSION

// Autoloader
require_once(KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . "infra" . DIRECTORY_SEPARATOR . "KAutoloader.php");
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "server_infra", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "lib", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_API_PATH, "services", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "generator"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_PLUGIN_PATH, "*"));
KAutoloader::setClassMapFilePath(kConf::get("cache_root_path") . '/generator/classMap.cache');
KAutoloader::register();

// Timezone
date_default_timezone_set(kConf::get("date_default_timezone")); // America/New_York

// Logger
kLoggerCache::InitLogger('generator');
