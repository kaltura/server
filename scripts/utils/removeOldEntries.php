<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php removeOldEntries.php {days old}\n";
	exit;
} 
$daysOld = $argv[1];
$updatedAt = time() - ($daysOld * 24 * 60 * 60);

chdir(dirname(__FILE__));
require_once(dirname(__FILE__) . '/../bootstrap.php');

$typesToDelete = array(
//	entryType::AUTOMATIC,
//	entryType::BACKGROUND,
//	entryType::MEDIA_CLIP,
//	entryType::SHOW,
//	entryType::BUBBLES,
//	entryType::PLAYLIST,
//	entryType::DATA,
//	entryType::LIVE_STREAM,
//	entryType::DOCUMENT,
//	entryType::DVD,
);

$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$c = new Criteria();
$c->add(entryPeer::PARTNER_ID, 100, Criteria::GREATER_THAN);
$c->add(entryPeer::UPDATED_AT, $updatedAt, Criteria::LESS_THAN);
if(count($typesToDelete))
	$c->add(entryPeer::TYPE, $typesToDelete, Criteria::IN);

$entries = entryPeer::doSelect($c);

foreach($entries as $entry)
{
	KalturaLog::debug("Deletes entry [" . $entry->getId() . "]");
	myEntryUtils::deleteEntry($entry, $entry->getPartnerId());
}
KalturaLog::debug("Done");

