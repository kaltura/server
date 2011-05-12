<?php

$partnerId = 1;

set_time_limit(0);
ini_set("memory_limit","700M");
error_reporting(E_ALL);
chdir(dirname(__FILE__));
define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "audit", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

$customData = myCustomData::fromString('a:3:{s:24:"defConversionProfileType";s:3:"med";s:22:"defaultAccessControlId";i:1;s:26:"defaultConversionProfileId";i:1;}');

$enabledPlugins = array();
$enabledPlugins[AuditPlugin::PLUGIN_NAME] = true;
$enabledPlugins[MetadataPlugin::PLUGIN_NAME] = true; 
$customData->put("enabledPlugins", $enabledPlugins);
$customData->put("enableVast", true);
$customData->put("liveEnabled", true);

echo $customData->toString();