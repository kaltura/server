<?php

if($argc < 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php removeOldEntries.php {days old}\n";
	exit;
} 
$daysOld = $argv[1];
$dryRun = true;
if($argc > 2 && strtolower($argv[2]) == 'realrun')
	$dryRun = false;
	
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
 
$count = 0;
$entries = entryPeer::doSelect($c);
while($entries)
{
	$count += count($entries);
	foreach($entries as $entry)
	{
		kCurrentContext::$ks_partner_id = $entry->getPartnerId();
		kCurrentContext::$partner_id = $entry->getPartnerId();
		kCurrentContext::$master_partner_id = $entry->getPartnerId();
		
		KalturaLog::debug("Deletes entry [" . $entry->getId() . "]");
		KalturaStatement::setDryRun($dryRun);
		myEntryUtils::deleteEntry($entry, $entry->getPartnerId());
		KalturaStatement::setDryRun(false);
	}
	kEventsManager::flushEvents();
	kMemoryManager::clearMemory();
	$entries = entryPeer::doSelect($c);
}
KalturaLog::debug("Deleted [$count] entries");

