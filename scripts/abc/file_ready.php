<?php

if ($argc != 2)
	throw new Exception("Invalid argument. Usage: php new_file.php <full path>");

$ini = parse_ini_file("abc.ini");

require_once ($ini["CLIENT_LIBS"]."/php5/KalturaClient.php");
$incomingFilename = $argv[1];
$path_parts = pathinfo($incomingFilename);

$config = new KalturaConfiguration($ini["ABC_PARTNER_ID"]);
$config->serviceUrl = $ini["SERVICE_URL"];
$client = new KalturaClient($config);
$ks = $client->session->start($ini["ABC_ADMIN_SECRET"], "", KalturaSessionType :: ADMIN);
$client->setKs($ks);

// Get the entry
$filter = new KalturaMediaEntryFilter;
$filter->referenceIdEqual = $path_parts['basename'];
$filter->statusIn = implode(',', array(
	KalturaEntryStatus::ERROR_CONVERTING,
	KalturaEntryStatus::ERROR_IMPORTING,
	KalturaEntryStatus::IMPORT,
	KalturaEntryStatus::NO_CONTENT,
	KalturaEntryStatus::PENDING,
	KalturaEntryStatus::PRECONVERT,
	KalturaEntryStatus::READY,
));
$listResult = $client->media->listAction($filter);
echo "Entries found by referenceIdEqual:". ($listResult->totalCount) ."\r\n";
if ($listResult->totalCount == 0)
{
	throw new Exception('unexpected number of entries');
}
$entry = reset($listResult->objects);
echo "entry id: ". $entry->id."\r\n";
// Add or replace the content of the entry
$resource = new KalturaRemoteStorageResource();
$resource->storageProfileId = $ini["ABC_SOURCE_STORAGE_PROFILE_ID"];
$resource->url = $incomingFilename;

$client->media->updateContent($entry->id, $resource);
