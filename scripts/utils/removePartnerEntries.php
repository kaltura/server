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
//	entry::ENTRY_TYPE_AUTOMATIC,
//	entry::ENTRY_TYPE_BACKGROUND,
//	entry::ENTRY_TYPE_MEDIACLIP,
//	entry::ENTRY_TYPE_SHOW,
//	entry::ENTRY_TYPE_BUBBLES,
//	entry::ENTRY_TYPE_PLAYLIST,
	entry::ENTRY_TYPE_DATA,
//	entry::ENTRY_TYPE_LIVE_STREAM,
	entry::ENTRY_TYPE_DOCUMENT,
//	entry::ENTRY_TYPE_DVD,
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

