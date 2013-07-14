<?php

if($argc != 2)
{
	echo "Arguments missing.\n\n";
	echo "Usage: php removePartnerEntries.php {partner id}\n";
	exit;
} 
$partnerId = $argv[1];

require_once(__DIR__ . '/../bootstrap.php');

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

