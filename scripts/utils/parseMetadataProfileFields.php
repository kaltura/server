<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php parseMetadataProfileFields.php {metadata profile id}\n";
	exit;
} 
$metadataProfileId = $argv[1];

set_time_limit(0);
ini_set("memory_limit","1024M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "plugins", "metadata", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$metadataProfile = MetadataProfilePeer::retrieveById($metadataProfileId);
if(!$metadataProfile)
{
	echo "Metadata Profile not found.\n";
	exit;
}

kMetadataManager::parseProfileSearchFields($metadataProfile);
