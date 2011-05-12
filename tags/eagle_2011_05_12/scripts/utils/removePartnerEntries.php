<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php removePartnerEntries.php {partner id}\n";
	exit;
} 
$partnerId = $argv[1];

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

$typesToDelete = array(
//	entryType::AUTOMATIC,
//	entryType::BACKGROUND,
//	entryType::MEDIA_CLIP,
//	entryType::SHOW,
//	entryType::BUBBLES,
//	entryType::PLAYLIST,
	entryType::DATA,
//	entryType::LIVE_STREAM,
	entryType::DOCUMENT,
//	entryType::DVD,
	);

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, $partnerId);
$c->add(entryPeer::TYPE, $typesToDelete, Criteria::IN);

$entries = entryPeer::doSelect($c);

foreach($entries as $entry)
{
	KalturaLog::debug("Deletes entry [" . $entry->getId() . "]");
	myEntryUtils::deleteEntry($entry, $partnerId);
}
KalturaLog::debug("Done");

